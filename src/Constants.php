<?php

namespace ImageBlur;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;

/**
 * A class that holds constants values, that are used internally in this plugin
 */
class Constants {

	/**
	 * Wordpress refers to default image size with this
	 */
	const DEFAULT_IMAGE_SIZE = 'full';

	/**
	 * Prefix used on code level to give different keys plugin's namespace
	 */
	const PREFIX = 'image_blur_';
}
