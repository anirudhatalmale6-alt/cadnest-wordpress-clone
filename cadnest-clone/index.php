<?php
/**
 * Fallback template.
 *
 * For the 11 cloned pages the output is produced earlier on `template_redirect`
 * (see functions.php) and this file is never reached. It only runs for requests
 * that have no snapshot (e.g. search, 404), so we render a minimal, valid page
 * that still links back into the cloned site.
 *
 * @package CADnest_Clone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php bloginfo( 'name' ); ?></title>
	<?php wp_head(); ?>
	<style>
		body{font-family:"Nunito Sans",Arial,sans-serif;margin:0;padding:64px 24px;text-align:center;color:#333;}
		a.button{display:inline-block;margin-top:16px;padding:12px 24px;background:#0a5;color:#fff;text-decoration:none;border-radius:4px;}
	</style>
</head>
<body <?php body_class(); ?>>
	<h1><?php bloginfo( 'name' ); ?></h1>
	<p>The page you were looking for could not be found.</p>
	<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Return home</a>
	<?php wp_footer(); ?>
</body>
</html>
