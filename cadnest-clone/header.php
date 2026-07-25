<?php
/**
 * Theme header: <head>, opening <body>, and the shared site header markup.
 *
 * @package CADnest
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>
<div id="page-root" class="page-root">
<?php
// The shared header is stored once (identical on every page) and the asset
// token is swapped for this theme's real assets URL at render time.
$cad_assets = get_template_directory_uri() . '/assets';
$cad_header = @file_get_contents( get_template_directory() . '/parts/header.html' );
echo str_replace( '__ASSETBASE__', $cad_assets, $cad_header ); // phpcs:ignore
?>
