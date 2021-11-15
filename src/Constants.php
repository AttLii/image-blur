<?php

namespace ImageBlur;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) or die;

/**
 * A class that holds constants values, that are used internally in this plugin
 */
class Constants {

	/**
	 * Wordpress doesn't have a name for original image size, so we use this throughout our plugin.
	 */
	const DEFAULT_IMAGE_SIZE = 'original';

	/**
	 * Prefix used on code level to give different keys plugin's namespace
	 */
	const PREFIX = 'image_blur_';
}
