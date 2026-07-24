<?php
/**
 * Local test router that emulates how the WordPress theme serves snapshots.
 * Run:  php -S 127.0.0.1:8971 router.php   (from project root)
 *   /              -> snapshots/home.html
 *   /<slug>        -> snapshots/<slug>.html
 *   /assets/...    -> static file from cadnest-clone/assets/...
 * __ASSETBASE__ is rewritten to /assets to mirror get_template_directory_uri().
 */
$theme  = __DIR__ . '/cadnest-clone';
$uri    = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

// Serve bundled assets.
if ( strpos( $uri, '/assets/' ) === 0 ) {
	$path = realpath( $theme . $uri );
	if ( $path && strpos( $path, realpath( $theme . '/assets' ) ) === 0 && is_file( $path ) ) {
		$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		$mime = array(
			'css' => 'text/css', 'js' => 'application/javascript', 'woff2' => 'font/woff2',
			'woff' => 'font/woff', 'ttf' => 'font/ttf', 'eot' => 'application/vnd.ms-fontobject',
			'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp',
		);
		// GoDaddy images have no extension; sniff.
		if ( isset( $mime[ $ext ] ) ) {
			header( 'Content-Type: ' . $mime[ $ext ] );
		} else {
			$fi = finfo_open( FILEINFO_MIME_TYPE );
			header( 'Content-Type: ' . ( finfo_file( $fi, $path ) ?: 'application/octet-stream' ) );
		}
		readfile( $path );
		return true;
	}
	http_response_code( 404 );
	return true;
}

// Map URL to snapshot.
$slug = trim( $uri, '/' );
if ( $slug === '' ) {
	$slug = 'home';
}
$file = $theme . '/snapshots/' . $slug . '.html';
if ( is_file( $file ) ) {
	$html = file_get_contents( $file );
	$html = str_replace( '__ASSETBASE__', '/assets', $html );
	header( 'Content-Type: text/html; charset=UTF-8' );
	echo $html;
	return true;
}
http_response_code( 404 );
echo '404';
return true;
