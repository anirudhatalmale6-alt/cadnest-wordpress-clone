<?php
/**
 * Admin screen (Appearance > CADnest): install / repair pages and set the
 * contact-form recipient email.
 *
 * @package CADnest
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_menu', function () {
	add_theme_page( 'CADnest', 'CADnest', 'manage_options', 'cadnest', 'cadnest_admin_page' );
} );

add_action( 'admin_init', function () {
	register_setting( 'cadnest_settings', 'cadnest_contact_email', array( 'sanitize_callback' => 'sanitize_email' ) );
} );

function cadnest_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$did_install = false;
	if ( isset( $_POST['cadnest_install'] ) && check_admin_referer( 'cadnest_install_action', 'cadnest_admin_nonce' ) ) {
		cadnest_install( isset( $_POST['cadnest_overwrite'] ) );
		$did_install = true;
	}

	echo '<div class="wrap"><h1>CADnest</h1>';

	if ( $did_install ) {
		echo '<div class="notice notice-success"><p>Pages installed / repaired. All original URLs are live.</p></div>';
	}

	// --- Contact recipient -------------------------------------------------
	echo '<h2>Contact form</h2>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'cadnest_settings' );
	$email = esc_attr( get_option( 'cadnest_contact_email', get_option( 'admin_email' ) ) );
	echo '<table class="form-table"><tr><th scope="row"><label for="cadnest_contact_email">Send enquiries to</label></th>';
	echo '<td><input type="email" id="cadnest_contact_email" name="cadnest_contact_email" value="' . $email . '" class="regular-text" placeholder="you@example.com"><p class="description">The contact form on the Home and Contact pages delivers here.</p></td></tr></table>';
	submit_button( 'Save email' );
	echo '</form>';

	// --- Page installer ----------------------------------------------------
	echo '<hr><h2>Pages &amp; URLs</h2>';
	echo '<p>Creates every original page with its exact slug so indexed URLs keep working. Safe to run again &ndash; your edits are never overwritten unless you tick the box below.</p>';

	echo '<table class="widefat striped" style="max-width:820px;margin:16px 0;"><thead><tr><th>Page</th><th>URL</th><th>Status</th></tr></thead><tbody>';
	foreach ( cadnest_pages() as $slug => $title ) {
		$page  = get_page_by_path( $slug, OBJECT, 'page' );
		$url   = ( 'home' === $slug ) ? home_url( '/' ) : home_url( '/' . $slug . '/' );
		$state = $page ? '<span style="color:#1a7f37;">&#10004; created</span>' : '<span style="color:#b32d2e;">&#10008; missing</span>';
		echo '<tr><td>' . esc_html( $title ) . '</td><td><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $url ) . '</a></td><td>' . $state . '</td></tr>'; // phpcs:ignore
	}
	echo '</tbody></table>';

	echo '<form method="post">';
	wp_nonce_field( 'cadnest_install_action', 'cadnest_admin_nonce' );
	echo '<p><label><input type="checkbox" name="cadnest_overwrite" value="1"> Reset page content back to the original design (discards edits to these pages)</label></p>';
	echo '<p><button type="submit" name="cadnest_install" class="button button-primary button-hero">Install / repair pages</button></p>';
	echo '</form>';

	echo '</div>';
}

/** One-time success notice after activation. */
add_action( 'admin_notices', function () {
	if ( get_transient( 'cadnest_just_installed' ) ) {
		delete_transient( 'cadnest_just_installed' );
		$url = admin_url( 'themes.php?page=cadnest' );
		echo '<div class="notice notice-success is-dismissible"><p><strong>CADnest activated.</strong> All original pages were created. <a href="' . esc_url( $url ) . '">Open settings &amp; set your contact email &raquo;</a></p></div>';
	}
} );
