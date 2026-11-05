<?php
// Contact form AJAX handler

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle contact form submissions
function devenci_handle_contact_form() {
	check_ajax_referer( 'devenci_contact', 'devenci_nonce' );

	$honeypot = isset( $_POST['website'] ) ? sanitize_text_field( wp_unslash( $_POST['website'] ) ) : '';
	if ( '' !== $honeypot ) {
		wp_send_json_success( array( 'message' => __( 'Thanks for reaching out!', 'devenci' ) ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Please complete your name, a valid email address, and a message.', 'devenci' ) ),
			400
		);
	}

	$recipient = apply_filters( 'devenci_contact_recipient', get_option( 'admin_email' ) );
	$subject   = $subject ? $subject : sprintf( /* translators: %s: sender name. */ __( 'New portfolio message from %s', 'devenci' ), $name );

	$body = sprintf(
		/* translators: 1: sender name, 2: sender email, 3: message. */
		__( "Name: %1\$s\nEmail: %2\$s\n\n%3\$s", 'devenci' ),
		$name,
		$email,
		$message
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %1$s <%2$s>', $name, $email ),
	);

	$sent = wp_mail( $recipient, $subject, $body, $headers );

	if ( ! $sent ) {
		wp_send_json_error(
			array( 'message' => __( 'The message could not be sent. Please try again later.', 'devenci' ) ),
			500
		);
	}

	wp_send_json_success(
		array(
			'message' => sprintf( /* translators: %s: sender name. */ __( 'Thanks for reaching out, %s!', 'devenci' ), $name ),
		)
	);
}
add_action( 'wp_ajax_devenci_contact', 'devenci_handle_contact_form' );
add_action( 'wp_ajax_nopriv_devenci_contact', 'devenci_handle_contact_form' );
