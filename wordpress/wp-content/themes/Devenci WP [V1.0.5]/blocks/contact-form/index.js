( function ( wp ) {
	'use strict';

	var registerBlockType = wp.blocks.registerBlockType;
	var blockEditor = wp.blockEditor;
	var components = wp.components;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;

	registerBlockType( 'devenci/contact-form', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = blockEditor.useBlockProps( { className: 'contact-form-wrapper' } );

			var fields = [ 'Your Name', 'Your Email', 'Subject', 'Your Message' ].map( function ( label, index ) {
				var tag = index === 3 ? 'textarea' : 'input';
				return el(
					'div',
					{ className: 'form-group', key: label },
					el( tag, {
						placeholder: label,
						disabled: true,
						rows: index === 3 ? 4 : undefined,
					} )
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
						{ title: __( 'Contact Form', 'devenci' ), initialOpen: true },
						el( components.TextControl, {
							label: __( 'Button text', 'devenci' ),
							value: attributes.buttonText,
							onChange: function ( value ) {
								setAttributes( { buttonText: value } );
							},
						} ),
						el( 'p', {}, __( 'Messages are sent to the site administrator email address.', 'devenci' ) )
					)
				),
				el(
					'div',
					blockProps,
					el( 'form', { className: 'contact-form', onSubmit: function ( event ) { event.preventDefault(); } }, fields,
						el( 'button', { type: 'button', className: 'btn primary-btn', disabled: true }, attributes.buttonText )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
