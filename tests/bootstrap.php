<?php
define( 'SRC_FOLDER', dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR );
define( 'TEST_ASSET_FOLDER', __DIR__ . DIRECTORY_SEPARATOR . 'assets' );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
WP_Mock::bootstrap();

class WP_post {

	public int $ID;
}
