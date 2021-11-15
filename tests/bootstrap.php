<?php
require_once realpath( dirname( __FILE__ ) . '/../vendor/autoload.php' );
require_once 'mocks/WP_Post.php';
define( 'UNIT_TESTING', true );
WP_Mock::bootstrap();
