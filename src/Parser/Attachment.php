<?php
namespace ImageBlur\Parser;

use ImageBlur\Constants;

/**
 * A class for parsing attachment specific data
 */
class Attachment {

	/**
	 * Parses image attachment's size data to more clearer and concise for our use case.
	 * This also appends default image path to the array, which is also in metadata.
	 *
	 * @param array $metadata - metadata passed to wp_generate_attachment_metadata hook
	 * @return array - array where key is the slug of image size and value path inside uploads folder to the image.
	 */
	public static function parse_sizes_from_metadata( $metadata ): array {
		$file = $metadata['file'];
		$file_dir = dirname( $file );

		$sizes[ Constants::DEFAULT_IMAGE_SIZE ] = $file;

		foreach ( $metadata['sizes'] as $size => $size_data ) {
			$sizes[ $size ] = $file_dir . '/' . $size_data['file'];
		}

		return $sizes;
	}
}
