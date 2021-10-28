<?php
namespace ImageBlur;

use ImageBlur\Constants;

/**
 * Stop execution if not in Wordpress environment
 */
defined("WPINC") or die;

/**
 * A class that provides utility methods for this plugin 
 */
class Utils {

  public static function add_plugin_prefix(string $str): string {
    return Constants::PREFIX . $str;
  }

}