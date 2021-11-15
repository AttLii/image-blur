<?php
namespace ImageBlur\Service;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;

class ProcessImage {

	/**
	 * Php processes and outputs image data in different ways depending on the mime type.
	 * These two functions are chosen in this method and returned as an array.
	 * This can also return null for both or either functions, when mime type not supported.
	 *
	 * @param string $mime_type
	 * @return array - array where first index is processing function and second output function
	 */
	public function choose_funcs_for_mime_type( string $mime_type ): array {
		$process_func = null;
		$output_func = null;

		if ( $mime_type === 'image/png' ) {
			$process_func = array( $this, 'process_png' );
			$output_func = 'imagepng';
		} else if ( $mime_type === 'image/jpeg' ) {
			$process_func = array( $this, 'process_image' );
			$output_func = 'imagejpeg';
		} else if ( $mime_type === 'image/gif' ) {
			$process_func = array( $this, 'process_image' );
			$output_func = 'imagegif';
		}

		return array(
			$process_func,
			$output_func,
		);
	}

	/**
	 * downscales passed in image while keeping aspect ratio to defined value and returns new downscaled image
	 */
	public function downscale( $image ) {
		$width = apply_filters( 'image-blur-modify-width', 8 );
		return imagescale( $image, $width );
	}

	/**
	 * Adds gaussian blur to passed in image. Blur's strength is applied using same function over and over again to the image object.
	 */
	public function gaussian_blur( $image ): void {
		$strength = apply_filters( 'image-blur-modify-blur-strength', 1 );
		for ( $i = 1; $i <= $strength; $i++ ) {
			imagefilter( $image, IMG_FILTER_GAUSSIAN_BLUR );
		}
	}

	/**
	 * PNG images have unique ability to be transparent, so we need to apply wanted changes with this specific function.
	 */
	public function process_png( $image ) {
		$width = imagesx( $image );
		$height = imagesy( $image );

		// create empty copy of passed in image using true color
		$new_image = imagecreatetruecolor( $width, $height );

		// downscale and apply needed alpha and blending
		$new_image = $this->downscale( $new_image );
		imagealphablending( $new_image, false );
		imagesavealpha( $new_image, true );

		$ds_width = imagesx( $new_image );
		$ds_height = imagesy( $new_image );

		// fill copy with transparent rectangle
		$transparency = imagecolorallocatealpha( $new_image, 255, 255, 255, 127 );
		imagefilledrectangle( $new_image, 0, 0, $ds_width, $ds_height, $transparency );

		// paste image inside the copy
		imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $ds_width, $ds_height, $width, $height );

		$this->gaussian_blur( $new_image );

		return $new_image;
	}

	public function process_image( $image ) {
		$image = $this->downscale( $image );
		$this->gaussian_blur( $image );
		return $image;
	}
}
