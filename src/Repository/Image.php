<?php
namespace ImageBlur\Repository;

use ImageBlur\Constants;

/**
 * Stop execution if not in Wordpress environment
 */
defined("WPINC") or die;


/**
 * Repository for fetching Image related data from Wordpress
 */
class Image {

  /**
   * Returns all image size slugs registered to Wordpress environment
   */
  public function get_all_image_sizes(): array {
    return get_intermediate_image_sizes();
  }

  /**
   * Convenience function that returns all image sizes with added "default" size
   */
  public function get_all_image_sizes_with_default() {
    $sizes = $this->get_all_image_sizes();
    $sizes[] = Constants::DEFAULT_IMAGE_SIZE;

    return $sizes;
  }

  /**
   * Checks if passed in attachment id belongs to an image attachment
   */
  public function is_image(int $id): bool {
    return wp_attachment_is_image($id);
  }

  /**
   * Gets all image ids
   */
  public function get_all_image_ids(): array {
    return get_posts( array(
      "post_type"      => "attachment",
      "post_mime_type" => "image",
      "posts_per_page" => -1,
      "fields"         => "ids"
    ) );
  }
}