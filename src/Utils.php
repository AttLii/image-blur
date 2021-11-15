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

	/**
	 * Adds plugin prefix to passed in string. This is used to add our own "namespace" to key values, that we store f.e. to database.
	 *
	 * @param string $str - string that needs plugin prefix.
	 * @return string - string with added plugin prefix.
	 */
	public static function add_plugin_prefix( string $str ): string {
		return Constants::PREFIX . $str;
	}

	/**
	 * Helper function that checks if passed in string has plugin prefix.
	 *
	 * @param string $str - string that needs checking.
	 * @return bool - has or has not the prefix.
	 */
	public static function has_plugin_prefix( string $str ): bool {
		return substr( $str, 0, strlen( Constants::PREFIX ) ) === Constants::PREFIX;
	}
}
