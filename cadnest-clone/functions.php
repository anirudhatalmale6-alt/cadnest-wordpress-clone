<?php
/**
 * CADnest theme functions.
 *
 * A custom theme reproducing the CADnest website with REAL, editable WordPress
 * page content. Design CSS is enqueued; page text lives in the editor (and is
 * readable by SEO plugins such as Yoast). No third-party platform runtime ships
 * with this theme.
 *
 * @package CADnest
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'CADNEST_VERSION', '2.0.0' );

/** Base URL used inside page content / header / footer for bundled assets. */
function cadnest_assets_uri() {
	return get_template_directory_uri() . '/assets';
}

/**
 * Theme setup.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'post-thumbnails' );
	register_nav_menus( array( 'primary' => __( 'Primary Menu', 'cadnest' ) ) );
} );

/**
 * Enqueue the design CSS (self-contained) and the small, license-clean JS.
 */
add_action( 'wp_enqueue_scripts', function () {
	$uri = get_template_directory_uri();

	// 1) Design system. Background images are inline data-URIs, so it is fully
	//    self-contained; @font-face urls resolve relative to the file's folder.
	wp_enqueue_style(
		'cadnest-base',
		$uri . '/assets/-_-/common/styles/style.Bdx8pZMZ.css',
		array(),
		CADNEST_VERSION
	);

	// 2) Section + page styling (the union of the original inline <style> blocks).
	wp_enqueue_style(
		'cadnest-site',
		$uri . '/assets/site.css',
		array( 'cadnest-base' ),
		CADNEST_VERSION
	);

	// Small notices used by the contact form.
	wp_add_inline_style(
		'cadnest-site',
		'.cadnest-note{max-width:760px;margin:24px auto;padding:14px 18px;border-radius:6px;font-family:"Nunito Sans",Arial,sans-serif;font-size:16px;}' .
		'.cadnest-note.ok{background:#e7f6ec;color:#1b6b38;border:1px solid #b6e2c4;}' .
		'.cadnest-note.err{background:#fdecec;color:#a12626;border:1px solid #f2c2c2;}' .
		'.navigation-list-more.cadnest-more-open{display:block;}'
	);

	// 3) Clean, self-written JS (overflow / mobile navigation). No vendor runtime.
	wp_enqueue_script(
		'cadnest-theme',
		$uri . '/js/theme.js',
		array(),
		CADNEST_VERSION,
		true
	);
} );

/**
 * Render-time replacements inside page content:
 *  - swap the asset token for the real assets URL,
 *  - inject a real nonce field into the contact form.
 */
add_filter( 'the_content', function ( $html ) {
	if ( false !== strpos( $html, '__ASSETBASE__' ) ) {
		$html = str_replace( '__ASSETBASE__', cadnest_assets_uri(), $html );
	}
	if ( false !== strpos( $html, 'CADNEST_NONCE' ) ) {
		$nonce = wp_nonce_field( 'cadnest_contact', 'cadnest_nonce', true, false );
		$html  = str_replace( '<!--CADNEST_NONCE-->', $nonce, $html );
	}
	return $html;
}, 8 );

/**
 * Show a success / error banner just above the contact form after a submission.
 */
add_filter( 'the_content', function ( $html ) {
	if ( isset( $_GET['cad_sent'] ) && false !== strpos( $html, 'cadnest-form' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$ok   = '1' === $_GET['cad_sent']; // phpcs:ignore
		$msg  = $ok
			? 'Thanks &mdash; your message has been sent. We&rsquo;ll be in touch shortly.'
			: 'Sorry, we couldn&rsquo;t send your message. Please check the fields and try again.';
		$note = '<div class="cadnest-note ' . ( $ok ? 'ok' : 'err' ) . '" id="contact-result">' . $msg . '</div>';
		$html = preg_replace( '/(<form[^>]*cadnest-form)/', $note . '$1', $html, 1 );
	}
	return $html;
}, 9 );

require get_template_directory() . '/inc/installer.php';
require get_template_directory() . '/inc/contact.php';
require get_template_directory() . '/inc/admin.php';

/**
 * Run the page installer automatically the first time the theme is activated.
 */
add_action( 'after_switch_theme', 'cadnest_install' );
