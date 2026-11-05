<?php
// Contact form render callback

$button_text = isset( $attributes['buttonText'] ) && '' !== $attributes['buttonText'] ? $attributes['buttonText'] : __( 'Send Message', 'devenci' );
?>
<div class="contact-form-wrapper">
	<form class="contact-form devenci-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="devenci_contact">
		<?php wp_nonce_field( 'devenci_contact', 'devenci_nonce', true, true ); ?>
		<div class="devenci-form-honeypot" aria-hidden="true">
			<label for="devenci-website"><?php esc_html_e( 'Website', 'devenci' ); ?></label>
			<input type="text" id="devenci-website" name="website" tabindex="-1" autocomplete="off">
		</div>
		<div class="form-group">
			<label class="screen-reader-text" for="devenci-name"><?php esc_html_e( 'Your Name', 'devenci' ); ?></label>
			<input type="text" id="devenci-name" name="name" placeholder="<?php esc_attr_e( 'Your Name', 'devenci' ); ?>" required>
			<span class="form-highlight" aria-hidden="true"></span>
		</div>
		<div class="form-group">
			<label class="screen-reader-text" for="devenci-email"><?php esc_html_e( 'Your Email', 'devenci' ); ?></label>
			<input type="email" id="devenci-email" name="email" placeholder="<?php esc_attr_e( 'Your Email', 'devenci' ); ?>" required>
			<span class="form-highlight" aria-hidden="true"></span>
		</div>
		<div class="form-group">
			<label class="screen-reader-text" for="devenci-subject"><?php esc_html_e( 'Subject', 'devenci' ); ?></label>
			<input type="text" id="devenci-subject" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'devenci' ); ?>">
			<span class="form-highlight" aria-hidden="true"></span>
		</div>
		<div class="form-group">
			<label class="screen-reader-text" for="devenci-message"><?php esc_html_e( 'Your Message', 'devenci' ); ?></label>
			<textarea id="devenci-message" name="message" placeholder="<?php esc_attr_e( 'Your Message', 'devenci' ); ?>" required></textarea>
			<span class="form-highlight" aria-hidden="true"></span>
		</div>
		<button type="submit" class="btn primary-btn icon-paper-plane">
			<span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
		</button>
		<p class="devenci-form-status" role="status" aria-live="polite"></p>
	</form>
</div>
