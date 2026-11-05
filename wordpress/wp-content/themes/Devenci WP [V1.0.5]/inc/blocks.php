<?php
// Custom block registration

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register a dedicated block category for the theme blocks
function devenci_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'devenci',
				'title' => __( 'Dev Venci', 'devenci' ),
				'icon'  => 'screenoptions',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'devenci_block_categories' );

// Register the theme's blocks
function devenci_register_blocks() {
	$blocks = array(
		'member-card',
		'skill-bar',
		'portfolio-grid',
		'portfolio-item',
		'contact-form',
	);

	foreach ( $blocks as $block ) {
		$path = get_theme_file_path( 'blocks/' . $block );
		if ( file_exists( $path . '/block.json' ) ) {
			register_block_type( $path );
		}
	}
}
add_action( 'init', 'devenci_register_blocks' );

// Render an attachment image with a graceful fallback
function devenci_attachment_image( $attachment_id, $size = 'large', $class_name = '', $alt = '' ) {
	$attachment_id = (int) $attachment_id;

	if ( ! $attachment_id ) {
		return '';
	}

	$attr = array(
		'loading' => 'lazy',
		'alt'     => $alt,
	);

	if ( $class_name ) {
		$attr['class'] = $class_name;
	}

	return wp_get_attachment_image( $attachment_id, $size, false, $attr );
}
