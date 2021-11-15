<?php
/**
 * Plugin Name: Image Blur
 */

require_once 'vendor/autoload.php';

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) or die;

$image_blur_plugin = new ImageBlur\Plugin();

register_deactivation_hook( __FILE__, array( $image_blur_plugin, 'deactivate' ) );
register_activation_hook( __FILE__, array( $image_blur_plugin, 'activate' ) );
