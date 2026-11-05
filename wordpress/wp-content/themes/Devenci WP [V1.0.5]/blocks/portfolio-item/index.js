( function ( wp ) {
	'use strict';

	var registerBlockType = wp.blocks.registerBlockType;
	var blockEditor = wp.blockEditor;
	var components = wp.components;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;

	function mediaUpload( attributes, setAttributes, render ) {
		return el(
			blockEditor.MediaUploadCheck,
			{},
			el( blockEditor.MediaUpload, {
				onSelect: function ( selected ) {
					setAttributes( {
						imageId: selected.id,
						imageAlt: selected.alt || selected.title || '',
					} );
				},
				allowedTypes: [ 'image' ],
				value: attributes.imageId,
				render: render,
			} )
		);
	}

	registerBlockType( 'devenci/portfolio-item', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = blockEditor.useBlockProps( { className: 'portfolio-item is-editor' } );
			var media = wp.data.useSelect( function ( select ) {
				return attributes.imageId ? select( 'core' ).getMedia( attributes.imageId ) : null;
			}, [ attributes.imageId ] );

			var preview = media
				? el( 'img', { src: media.source_url, alt: attributes.imageAlt, className: 'portfolio-img' } )
				: mediaUpload( attributes, setAttributes, function ( open ) {
						return el(
							'button',
							{
								type: 'button',
								className: 'portfolio-img-placeholder',
								onClick: open.open,
							},
							__( 'Add project image', 'devenci' )
						);
				  } );

			return el(
				Fragment,
				{},
				el(
					blockEditor.InspectorControls,
					{},
					el(
						components.PanelBody,
						{ title: __( 'Portfolio Item', 'devenci' ), initialOpen: true },
						el( components.TextControl, {
							label: __( 'Filter category', 'devenci' ),
							value: attributes.category,
							onChange: function ( value ) {
								setAttributes( { category: value } );
							},
							help: __( 'Items sharing a category are grouped under one filter button.', 'devenci' ),
						} ),
						mediaUpload( attributes, setAttributes, function ( open ) {
							return el(
								components.Button,
								{ variant: 'secondary', onClick: open.open },
								attributes.imageId ? __( 'Replace image', 'devenci' ) : __( 'Select image', 'devenci' )
							);
						} ),
						el( components.TextControl, {
							label: __( 'Image alt text', 'devenci' ),
							value: attributes.imageAlt,
							onChange: function ( value ) {
								setAttributes( { imageAlt: value } );
							},
						} ),
						el( components.TextControl, {
							label: __( 'Project link', 'devenci' ),
							value: attributes.link,
							type: 'url',
							onChange: function ( value ) {
								setAttributes( { link: value } );
							},
						} )
					)
				),
				el(
					'div',
					blockProps,
					preview,
					el(
						'div',
						{ className: 'portfolio-overlay is-editor' },
						el( blockEditor.InnerBlocks, {
							template: [
								[ 'core/heading', { level: 3, placeholder: 'Project title', className: 'portfolio-title' } ],
								[ 'core/paragraph', { placeholder: 'Project description', className: 'portfolio-category' } ],
							],
						} )
					)
				)
			);
		},
		save: function () {
			return el( blockEditor.InnerBlocks.Content );
		},
	} );
} )( window.wp );
