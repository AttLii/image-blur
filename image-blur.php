<?php
/**
 * Plugin Name:       Image Blur
 * Plugin URI:        https://github.com/AttLii/image-blur
 * Description:       Generates base64 encoded, downscaled and blurred versions of media library's images, which can be used f.e. as a placeholder.
 * Version:           2.0.2
 * Requires at least: 6.1.1
 * Tested up to:      6.4.2
 * Requires PHP:      8.3
 * Author:            Atte Liimatainen
 * Author URI:        https://github.com/AttLii
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;

if ( ! class_exists( 'ImageBlur\Plugin' ) ) {
	require_once 'vendor/autoload.php';
	$image_blur_plugin = new ImageBlur\Plugin();
	register_deactivation_hook( __FILE__, array( $image_blur_plugin, 'deactivate' ) );
	register_activation_hook( __FILE__, array( $image_blur_plugin, 'activate' ) );
}
