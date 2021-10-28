<?php
namespace ImageBlur\Repository;

use ImageBlur\Constants;
use ImageBlur\Utils;

/**
 * Stop execution if not in Wordpress environment
 */
defined("WPINC") or die;

/**
 * Repository class for fetching and storing Image Blur related data for images
 */
class ImageBlur {

  public function set( int $id, string $size, string $data ): void {
    $meta_key = Utils::add_plugin_prefix($size);
    update_post_meta(
      $id,
      $meta_key,
      $data
    );
  }

  public function get( int $id, string $size ): string {
    $meta_key = Utils::add_plugin_prefix($size);
    return get_post_meta( $id, $meta_key, true ) ?? "";
  }

  public function delete( int $id, string $size ): void {
    $meta_key = Utils::add_plugin_prefix($size);
    delete_post_meta($id, $meta_key);
  }
}