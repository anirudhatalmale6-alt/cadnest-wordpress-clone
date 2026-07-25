<?php
/**
 * Contact form handler.
 *
 * The reproduced form markup posts back to its own page. This validates the
 * nonce + honeypot, emails the site's configured recipient with wp_mail(), then
 * redirects back with a ?cad_sent flag that the_content turns into a banner.
 *
 * @package CADnest
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Recipient address for contact submissions (defaults to the admin email). */
function cadnest_contact_recipient() {
	$email = get_option( 'cadnest_contact_email' );
	if ( $email && is_email( $email ) ) {
		return $email;
	}
	return get_option( 'admin_email' );
}

add_action( 'template_redirect', 'cadnest_handle_contact' );

function cadnest_handle_contact() {
	if ( empty( $_POST['cadnest_nonce'] ) ) {
		return;
	}

	$back = wp_get_referer();
	if ( ! $back ) {
		$back = home_url( '/' );
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cadnest_nonce'] ) ), 'cadnest_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'cad_sent', '0', $back ) . '#contact-result' );
		exit;
	}

	// Honeypot: silently accept (so bots think they succeeded) but send nothing.
	if ( ! empty( $_POST['cad_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'cad_sent', '1', $back ) . '#contact-result' );
		exit;
	}

	$name    = isset( $_POST['cad_name'] ) ? sanitize_text_field( wp_unslash( $_POST['cad_name'] ) ) : '';
	$email   = isset( $_POST['cad_email'] ) ? sanitize_email( wp_unslash( $_POST['cad_email'] ) ) : '';
	$message = isset( $_POST['cad_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cad_message'] ) ) : '';

	$ok = false;
	if ( $name && is_email( $email ) && $message ) {
		$to      = cadnest_contact_recipient();
		$subject = 'New enquiry from ' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$body    = "You have a new message from your website contact form.\n\n"
			. "Name:  {$name}\n"
			. "Email: {$email}\n\n"
			. "Message:\n{$message}\n";
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . $name . ' <' . $email . '>',
		);
		$ok = wp_mail( $to, $subject, $body, $headers );
	}

	wp_safe_redirect( add_query_arg( 'cad_sent', $ok ? '1' : '0', $back ) . '#contact-result' );
	exit;
}
