<?php
// Media Library imports for the original theme images

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Source images bundled with the theme for the one-time demo import
function devenci_media_manifest() {
	return array(
		'eth_icon' => array(
			'file'  => 'eth-icon.png',
			'title' => 'Dev Venci Ethereum icon',
			'alt'   => 'Ethereum diamond',
		),
	);
}

// Retrieve a media attachment ID from the import map
function devenci_media_id( $key ) {
	$ids = get_option( 'devenci_media_ids', array() );

	return isset( $ids[ $key ] ) ? (int) $ids[ $key ] : 0;
}

// Import all bundled images into the Media Library
function devenci_import_media() {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$ids      = get_option( 'devenci_media_ids', array() );
	$manifest = devenci_media_manifest();

	foreach ( $manifest as $key => $item ) {
		if ( ! empty( $ids[ $key ] ) && 'attachment' === get_post_type( $ids[ $key ] ) && wp_get_attachment_url( $ids[ $key ] ) ) {
			continue;
		}

		$source = get_theme_file_path( 'assets/images/' . $item['file'] );

		if ( ! file_exists( $source ) ) {
			continue;
		}

		$contents = file_get_contents( $source ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.

		if ( false === $contents ) {
			continue;
		}

		$upload = wp_upload_bits( $item['file'], null, $contents );

		if ( ! empty( $upload['error'] ) ) {
			continue;
		}

		$filetype = wp_check_filetype( $upload['file'] );

		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => $filetype['type'],
				'post_title'     => $item['title'],
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file'],
			0,
			true
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			continue;
		}

		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		wp_update_attachment_metadata( $attachment_id, $metadata );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $item['alt'] );

		$ids[ $key ] = (int) $attachment_id;
	}

	update_option( 'devenci_media_ids', $ids, false );

	return $ids;
}
