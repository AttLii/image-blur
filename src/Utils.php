<?php
namespace ImageBlur;

use ImageBlur\Constants;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;

/**
 * A class that provides utility methods for this plugin
 */
class Utils {

	public static function add_plugin_prefix( string $str ): string {
		return Constants::PREFIX . $str;
	}

	public static function has_plugin_prefix( string $str ): bool {
		return substr( $str, 0, strlen( Constants::PREFIX ) ) === Constants::PREFIX;
	}
}
