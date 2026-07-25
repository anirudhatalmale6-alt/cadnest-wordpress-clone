<?php
/**
 * Page installer: creates every original page as a REAL WordPress page, with its
 * exact original slug (so indexed URLs keep resolving) and with the reproduced
 * content loaded into post_content (fully editable in the block editor).
 *
 * @package CADnest
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Original pages in menu order. Key = slug (== old URL path), value = title.
 * The home page uses slug "home" and is set as the static front page.
 */
function cadnest_pages() {
	return array(
		'home'                                          => 'Home',
		'sample-plans'                                  => 'Sample Plans',
		'quote-request'                                 => 'Quote Request',
		'contact-us'                                    => 'Contact Us',
		'drafting-services-for-contractors-los-angeles' => 'Contractors',
		'drafting-services-pasadena-ca'                 => 'Drafting Services',
		'adu-plans-los-angeles'                         => 'Los Angeles ADU',
		'as-built-plans-pasadena-los-angeles'           => 'As-Built Plans',
		'room-addition-remodel-drafting-los-angeles'    => 'Room Addition & Remodel',
		'outsourced-cad-drafting-los-angeles'           => 'Outsourced CAD Drafting',
		'privacy-policy'                                => 'Privacy Policy',
	);
}

/**
 * Load a page's reproduced content (editable Custom-HTML blocks) from disk.
 */
function cadnest_page_content( $slug ) {
	$file = get_template_directory() . '/inc/pages/' . $slug . '.html';
	if ( ! is_readable( $file ) ) {
		return '';
	}
	return file_get_contents( $file ); // phpcs:ignore
}

/**
 * Create or refresh all pages.
 *
 * @param bool $overwrite When true, existing pages have their content refreshed
 *                        back to the original. Default false (never clobber the
 *                        client's edits once created).
 * @return array Slugs that were newly created.
 */
function cadnest_install( $overwrite = false ) {
	$created = array();
	$order   = 0;

	foreach ( cadnest_pages() as $slug => $title ) {
		$order++;
		$content  = cadnest_page_content( $slug );
		$existing = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $existing ) {
			$fields = array(
				'ID'         => $existing->ID,
				'menu_order' => $order,
			);
			// Only (re)fill content if asked, or if the page is currently empty.
			if ( $overwrite || '' === trim( (string) $existing->post_content ) ) {
				$fields['post_content'] = $content;
			}
			wp_update_post( $fields );
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_content'   => $content,
				'menu_order'     => $order,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			$created[] = $slug;
		}
	}

	// Static front page = Home.
	$home = get_page_by_path( 'home', OBJECT, 'page' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}

	// Pretty permalinks so /slug/ URLs resolve.
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	if ( function_exists( 'flush_rewrite_rules' ) ) {
		flush_rewrite_rules();
	}

	set_transient( 'cadnest_just_installed', 1, 60 );
	return $created;
}

/**
 * WP-CLI:  wp cadnest install  [--overwrite]
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'cadnest install',
		function ( $args, $assoc ) {
			$created = cadnest_install( isset( $assoc['overwrite'] ) );
			WP_CLI::success( 'CADnest pages installed. New pages: ' . ( $created ? implode( ', ', $created ) : 'none (all already existed)' ) );
		}
	);
}
