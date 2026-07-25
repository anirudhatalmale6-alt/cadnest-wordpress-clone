<?php
/**
 * 404 template.
 *
 * @package CADnest
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>
<main id="main">
	<div style="max-width:720px;margin:80px auto;padding:0 24px;font-family:'Nunito Sans',Arial,sans-serif;">
		<h1>Page not found</h1>
		<p>Sorry, the page you were looking for doesn&rsquo;t exist.</p>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Return to the homepage</a></p>
	</div>
</main>
<?php
get_footer();
