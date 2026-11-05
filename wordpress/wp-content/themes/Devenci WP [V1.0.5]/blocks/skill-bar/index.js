( function ( wp ) {
	'use strict';

	var registerBlockType = wp.blocks.registerBlockType;
	var blockEditor = wp.blockEditor;
	var components = wp.components;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;

	registerBlockType( 'devenci/skill-bar', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = blockEditor.useBlockProps( { className: 'skill-item' } );

			return el(
				Fragment,
				{},
				el(
					blockEditor.InspectorControls,
					{},
					el(
						components.PanelBody,
						{ title: __( 'Skill', 'devenci' ), initialOpen: true },
						el( components.RangeControl, {
							label: __( 'Percentage', 'devenci' ),
							value: attributes.percent,
							min: 0,
							max: 100,
							step: 1,
							onChange: function ( value ) {
								setAttributes( { percent: value || 0 } );
							},
						} )
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'skill-info' },
						el( blockEditor.RichText, {
							tagName: 'span',
							className: 'skill-label',
							value: attributes.label,
							placeholder: __( 'Skill name', 'devenci' ),
							onChange: function ( value ) {
								setAttributes( { label: value } );
							},
						} ),
						el( 'span', { className: 'skill-value' }, attributes.percent + '%' )
					),
					el(
						'div',
						{ className: 'skill-bar' },
						el( 'div', {
							className: 'skill-progress',
							style: { width: attributes.percent + '%' },
						} )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
