<?php
define('SRC_FOLDER', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR);

require_once dirname(__DIR__).'/vendor/autoload.php';
WP_Mock::activateStrictMode();
WP_Mock::bootstrap();