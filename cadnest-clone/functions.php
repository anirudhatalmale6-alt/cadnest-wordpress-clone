<?php
/**
 * CADnest Clone theme functions.
 *
 * Serves each WordPress page from a captured, self-contained HTML snapshot so
 * the public site is a pixel-perfect replica of cadnestdesign.com, and ships a
 * one-click installer that recreates every original page with its exact slug.
 *
 * @package CADnest_Clone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CADNEST_VERSION', '1.0.0' );

/**
 * The original pages, in menu order.  Key = slug (== old URL), value = title.
 * The home page uses the reserved slug "home" and is set as the static front page.
 */
function cadnest_pages() {
	return array(
		'home'                                         => 'Home',
		'sample-plans'                                 => 'Sample Plans',
		'quote-request'                                => 'Quote Request',
		'contact-us'                                   => 'Contact Us',
		'drafting-services-for-contractors-los-angeles' => 'Drafting Services for Contractors Los Angeles',
		'drafting-services-pasadena-ca'                => 'Drafting Services Pasadena CA',
		'adu-plans-los-angeles'                        => 'ADU Plans Los Angeles',
		'as-built-plans-pasadena-los-angeles'          => 'As-Built Plans Pasadena Los Angeles',
		'room-addition-remodel-drafting-los-angeles'   => 'Room Addition & Remodel Drafting Los Angeles',
		'outsourced-cad-drafting-los-angeles'          => 'Outsourced CAD Drafting Los Angeles',
		'privacy-policy'                               => 'Privacy Policy',
	);
}

/**
 * Return the snapshot slug for the current request, or null if none applies.
 */
function cadnest_current_slug() {
	if ( is_admin() ) {
		return null;
	}
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( $slug && array_key_exists( $slug, cadnest_pages() ) ) {
			return $slug;
		}
	}
	return null;
}

/**
 * Serve the captured snapshot verbatim, with the asset base rewritten to this
 * theme's /assets URL.  Bypasses the normal template so the output is byte-for
 * byte the original markup.
 */
function cadnest_render_snapshot() {
	$slug = cadnest_current_slug();
	if ( ! $slug ) {
		return; // Let WordPress handle admin, feeds, 404s, etc.
	}

	$file = get_template_directory() . '/snapshots/' . $slug . '.html';
	if ( ! file_exists( $file ) ) {
		return;
	}

	$html = file_get_contents( $file );
	$html = str_replace( '__ASSETBASE__', get_template_directory_uri() . '/assets', $html );

	if ( ! headers_sent() ) {
		header( 'Content-Type: text/html; charset=UTF-8' );
		header( 'X-Cadnest-Clone: ' . CADNEST_VERSION );
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- captured static markup served verbatim by design.
	exit;
}
add_action( 'template_redirect', 'cadnest_render_snapshot', 0 );

/**
 * ---------------------------------------------------------------------------
 * Installer: recreate every original page with its exact slug.
 * ---------------------------------------------------------------------------
 */
function cadnest_install() {
	$created = array();
	$order   = 0;

	foreach ( cadnest_pages() as $slug => $title ) {
		$order++;
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing ) {
			// Keep menu order tidy but don't clobber anything else.
			wp_update_post(
				array(
					'ID'         => $existing->ID,
					'menu_order' => $order,
				)
			);
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '', // Content is rendered from the snapshot.
				'menu_order'   => $order,
				'comment_status' => 'closed',
				'ping_status'  => 'closed',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			$created[] = $slug;
		}
	}

	// Set the home page as the static front page.
	$home = get_page_by_path( 'home', OBJECT, 'page' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}

	// Ensure pretty permalinks so /slug URLs resolve.
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	if ( function_exists( 'flush_rewrite_rules' ) ) {
		flush_rewrite_rules();
	}

	return $created;
}

/**
 * Auto-run the installer once when the theme is activated.
 */
function cadnest_after_switch_theme() {
	cadnest_install();
	set_transient( 'cadnest_just_installed', 1, 60 );
}
add_action( 'after_switch_theme', 'cadnest_after_switch_theme' );

/**
 * Admin page with a manual "Install / repair pages" button (Appearance menu).
 */
function cadnest_admin_menu() {
	add_theme_page(
		'CADnest Clone',
		'CADnest Clone',
		'manage_options',
		'cadnest-clone',
		'cadnest_admin_page'
	);
}
add_action( 'admin_menu', 'cadnest_admin_menu' );

function cadnest_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$done = false;
	if ( isset( $_POST['cadnest_install'] ) && check_admin_referer( 'cadnest_install_action', 'cadnest_nonce' ) ) {
		cadnest_install();
		$done = true;
	}

	echo '<div class="wrap"><h1>CADnest Clone</h1>';
	if ( $done ) {
		echo '<div class="notice notice-success"><p>Pages installed / repaired successfully. All original URLs are now live.</p></div>';
	}
	echo '<p>This installer creates every original page with its exact slug so all indexed URLs keep working. It is safe to run again at any time &ndash; existing pages are left untouched.</p>';

	echo '<table class="widefat striped" style="max-width:760px;margin:16px 0;"><thead><tr><th>Page</th><th>URL</th><th>Status</th></tr></thead><tbody>';
	foreach ( cadnest_pages() as $slug => $title ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		$url  = ( 'home' === $slug ) ? home_url( '/' ) : home_url( '/' . $slug . '/' );
		$state = $page ? '<span style="color:#1a7f37;">&#10004; created</span>' : '<span style="color:#b32d2e;">&#10008; missing</span>';
		echo '<tr><td>' . esc_html( $title ) . '</td><td><a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a></td><td>' . $state . '</td></tr>';
	}
	echo '</tbody></table>';

	echo '<form method="post">';
	wp_nonce_field( 'cadnest_install_action', 'cadnest_nonce' );
	echo '<p><button type="submit" name="cadnest_install" class="button button-primary button-hero">Install / repair pages</button></p>';
	echo '</form>';
	echo '</div>';
}

/**
 * Admin notice right after activation, guiding the user to the installer.
 */
function cadnest_activation_notice() {
	if ( get_transient( 'cadnest_just_installed' ) ) {
		delete_transient( 'cadnest_just_installed' );
		$url = admin_url( 'themes.php?page=cadnest-clone' );
		echo '<div class="notice notice-success is-dismissible"><p><strong>CADnest Clone activated.</strong> All original pages were created automatically. <a href="' . esc_url( $url ) . '">View install status &raquo;</a></p></div>';
	}
}
add_action( 'admin_notices', 'cadnest_activation_notice' );

/**
 * WP-CLI command:  wp cadnest install
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'cadnest install',
		function () {
			$created = cadnest_install();
			WP_CLI::success( 'CADnest pages installed. New pages: ' . ( $created ? implode( ', ', $created ) : 'none (all already existed)' ) );
		}
	);
}
