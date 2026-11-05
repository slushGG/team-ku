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

	registerBlockType( 'devenci/member-card', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = blockEditor.useBlockProps( { className: 'member is-editor' } );
			var media = wp.data.useSelect( function ( select ) {
				return attributes.imageId ? select( 'core' ).getMedia( attributes.imageId ) : null;
			}, [ attributes.imageId ] );

			var image = media
				? el( 'img', { src: media.source_url, alt: attributes.imageAlt, className: 'member-img' } )
				: mediaUpload( attributes, setAttributes, function ( open ) {
						return el(
							'button',
							{
								type: 'button',
								className: 'member-card-placeholder',
								onClick: open.open,
							},
							__( 'Add member image', 'devenci' )
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
						{ title: __( 'Member Card', 'devenci' ), initialOpen: true },
						el( components.TextControl, {
							label: __( 'Portfolio URL', 'devenci' ),
							value: attributes.url,
							type: 'url',
							onChange: function ( value ) {
								setAttributes( { url: value } );
							},
							help: __( 'Select the member portfolio page or paste its URL.', 'devenci' ),
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
							label: __( 'Entrance delay', 'devenci' ),
							value: attributes.delay,
							onChange: function ( value ) {
								setAttributes( { delay: value } );
							},
							help: __( 'CSS time value, for example 0.6s.', 'devenci' ),
						} )
					)
				),
				el(
					'div',
					blockProps,
					image,
					el(
						'div',
						{ className: 'member-name' },
						el( blockEditor.InnerBlocks, {
							template: [ [ 'core/paragraph', { placeholder: 'Member name' } ] ],
							templateLock: false,
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
