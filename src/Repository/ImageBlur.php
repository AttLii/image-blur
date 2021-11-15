<?php
namespace ImageBlur\Repository;

use ImageBlur\Constants;
use ImageBlur\Utils;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;

/**
 * Repository class for fetching and storing Image Blur related data for image attachments
 */
class ImageBlur {

	/**
	 * Sets blur data specific attachment's size.
	 *
	 * @param int    $id - attachment's id.
	 * @param string $size - attachment's size.
	 * @param string $data - data that is stored.
	 **/
	public function set( int $id, string $size, string $data ): void {
		$meta_key = Utils::add_plugin_prefix( $size );
		update_post_meta(
			$id,
			$meta_key,
			$data
		);
	}

	/**
	 * Get blur data for corresponds to specific attachment's size.
	 *
	 * @param int    $id   - attachment's id.
	 * @param string $size - attachment's size.
	 * @return string      - Blur data. this returns empty string, if it's not found.
	 */
	public function get( int $id, string $size ): string {
		$meta_key = Utils::add_plugin_prefix( $size );
		return get_post_meta( $id, $meta_key, true ) ?? '';
	}

	/**
	 * Deletes blur data that corresponds to specific attachment's size.
	 *
	 * @param int    $id   - attachment's id.
	 * @param string $size - attachment's size.
	 */
	public function delete( int $id, string $size ): void {
		$meta_key = Utils::add_plugin_prefix( $size );
		delete_post_meta( $id, $meta_key );
	}

	/**
	 * Clears attachment's blur data.
	 *
	 * @param int $id - Attachment's id.
	 */
	public function clear( int $id ): void {
		$results = get_post_meta( $id );

		foreach ( array_keys( $results ) as $key ) {
			if ( Utils::has_plugin_prefix( $key ) ) {
				delete_post_meta( $id, $key );
			}
		}
	}
}
