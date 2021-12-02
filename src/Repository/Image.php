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
	 *
	 * @return array - array of defined image size slugs.
	 */
	public function get_all_image_sizes(): array {
		return get_intermediate_image_sizes();
	}

	/**
	 * Convenience function that returns all image sizes with added "default" size.
	 *
	 * @return array - array of defined image size slugs with default size.
	 */
	public function get_all_image_sizes_with_default() {
		$sizes = $this->get_all_image_sizes();
		$sizes[] = Constants::DEFAULT_IMAGE_SIZE;

		return $sizes;
	}

	/**
	 * Checks if passed in id belongs to an image attachment.
	 *
	 * @param int $id - Attachment's id.
	 * @return bool - true if id is for image attachment.
	 */
	public function is_image( int $id ): bool {
		return wp_attachment_is_image( $id );
	}

	/**
	 * Gets all image ids.
	 *
	 * @return array - array of integers.
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
	 * @return string|bool - attachment's mime type.
	 */
	public function get_mime_type( int $id ) {
		return get_post_mime_type( $id );
	}

	/**
	 * Returns remote url for the attachment's id and size
	 *
	 * @param int    $id - attachment's id.
	 * @param string $size - slug of the image size.
	 * @return null|string - attachment's mime type.
	 */
	public function get_url_for_size( int $id, string $size ): ?string {
		return wp_get_attachment_image_url( $id, $size ) ?: null;
	}
}
