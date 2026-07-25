<?php
/**
 * Fallback template. The site is built from static pages, so this simply
 * renders whatever content the current query returns.
 *
 * @package CADnest
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>
<main id="main">
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
else :
	echo '<div style="max-width:720px;margin:80px auto;padding:0 24px;font-family:\'Nunito Sans\',Arial,sans-serif;">';
	echo '<h1>Nothing found</h1><p>The page you are looking for could not be found.</p>';
	echo '<p><a href="' . esc_url( home_url( '/' ) ) . '">Return home</a></p></div>';
endif;
?>
</main>
<?php
get_footer();
