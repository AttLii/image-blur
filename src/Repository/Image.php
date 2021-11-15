<?php
namespace ImageBlur\Repository;

use ImageBlur\Constants;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;


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
	 * Checks if passed in id belongs to an image attachment
	 *
	 * @param int $id - Attachment's id.
	 * @return bool - true if id is for image attachment.
	 */
	public function is_image( int $id ): bool {
		return wp_attachment_is_image( $id );
	}

	/**
	 * Gets all image ids
	 */
	public function get_all_image_ids(): array {
		return get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
	}

	/**
	 * Returns mime type for passed in attachment id.
	 *
	 * @param int $id - attachment's id.
	 * @return string - attachment's mime type.
	 */
	public function get_mime_type( int $id ): string {
		return get_post_mime_type( $id );
	}
}
