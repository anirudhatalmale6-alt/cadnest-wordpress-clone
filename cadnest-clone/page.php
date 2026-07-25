<?php
/**
 * Default page template. Renders the page's (editable) WordPress content
 * inside the same <main id="main"> wrapper the original site used.
 *
 * @package CADnest
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>
<main id="main">
<?php
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
?>
</main>
<?php
get_footer();
