( function ( wp ) {
	'use strict';

	var registerBlockType = wp.blocks.registerBlockType;
	var blockEditor = wp.blockEditor;
	var components = wp.components;
	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	registerBlockType( 'devenci/portfolio-grid', {
		edit: function ( props ) {
			var blockProps = blockEditor.useBlockProps( { className: 'devenci-portfolio is-editor' } );
			var innerBlocks = wp.data.useSelect( function ( select ) {
				return select( 'core/block-editor' ).getBlocks( props.clientId );
			}, [ props.clientId ] );

			var filters = [ 'All' ];

			innerBlocks.forEach( function ( block ) {
				if ( block.name !== 'devenci/portfolio-item' ) {
					return;
				}
				var category = ( block.attributes.category || '' ).trim();
				if ( category && filters.indexOf( category ) === -1 ) {
					filters.push( category );
				}
			} );

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'portfolio-filter is-editor-preview' },
					filters.map( function ( label, index ) {
						return el(
							'button',
							{ key: label, type: 'button', className: 'filter-btn' + ( index === 0 ? ' active' : '' ), disabled: true },
							label
						);
					} )
				),
				el(
					'div',
					{ className: 'portfolio-grid' },
					el( blockEditor.InnerBlocks, {
						allowedBlocks: [ 'devenci/portfolio-item' ],
						template: [ [ 'devenci/portfolio-item' ] ],
					} )
				)
			);
		},
		save: function () {
			return el( blockEditor.InnerBlocks.Content );
		},
	} );
} )( window.wp );
