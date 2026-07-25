<?php
/**
 * Theme footer: shared site footer markup, then closes the page.
 *
 * @package CADnest
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$cad_assets = get_template_directory_uri() . '/assets';
$cad_footer = @file_get_contents( get_template_directory() . '/parts/footer.html' );
echo str_replace( '__ASSETBASE__', $cad_assets, $cad_footer ); // phpcs:ignore
?>
</div><!-- /#page-root -->
<?php wp_footer(); ?>
</body>
</html>
