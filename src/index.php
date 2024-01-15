<?php
/**
 * Stop execution if not in Wordpress environment
 */

defined( 'WPINC' ) || die;

define( 'IMAGE_BLUR_PLUGIN_INIT', true );

function image_blur_clear_blurs( int $id ): void {
	$results = get_post_meta( $id );

	if ( ! is_array( $results ) ) {
		return;
	}

	foreach ( array_keys( $results ) as $key ) {
		if ( str_starts_with( $key, 'image_blur_' ) ) {
			delete_post_meta( $id, $key );
		}
	}
}

function image_blur_downscale_image( GdImage $image ): GdImage {
	$width = apply_filters( 'image-blur-modify-width', 8 );
	return imagescale( $image, $width );
}

function image_blur_blur_image( GdImage $image ): void {
	$strength = apply_filters( 'image-blur-modify-gaussian-blur-strength', 1 );
	for ( $i = 1; $i <= $strength; $i++ ) {
		imagefilter( $image, IMG_FILTER_GAUSSIAN_BLUR );
	}
}

function image_blur_downscale_png( GdImage $image ): GdImage {
	$width = imagesx( $image );
	$height = imagesy( $image );

	$new_image = imagecreatetruecolor( $width, $height );

	$new_image = image_blur_downscale_image( $new_image );
	imagealphablending( $new_image, false );
	imagesavealpha( $new_image, true );

	$ds_width = imagesx( $new_image );
	$ds_height = imagesy( $new_image );

	$transparency = imagecolorallocatealpha( $new_image, 255, 255, 255, 127 );
	imagefilledrectangle( $new_image, 0, 0, $ds_width, $ds_height, $transparency );

	imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $ds_width, $ds_height, $width, $height );

	return $new_image;
}

function image_blur_is_unprosessable_image( string $mimetype, string $image_path ): bool {
	if ( $mimetype !== 'webp' ) {
		return false;
	} else {
		// animated webp isn't supported
		$contents = file_get_contents( $image_path );
		return strpos( $contents, 'ANMF' ) !== false || strpos( $contents, 'ANIM' ) !== false;
	}
}

function image_blur_generate_blur_for_attachment( array $metadata, int $id ): array {
	if ( ! wp_attachment_is_image( $id ) ) {
		return $metadata;
	}

	image_blur_clear_blurs( $id );

	$mime = get_post_mime_type( $id );
	if ( ! $mime ) {
		return $metadata;
	}

	$type = explode( '/', $mime )[1];

	$create = 'imagecreatefrom' . $type;
	$output = 'image' . $type;
	if ( ! function_exists( $create ) || ! function_exists( $output ) ) {
		return $metadata;
	}

	$basedir = wp_upload_dir()['basedir'];
	$sizes = array( 'full' => $basedir . '/' . $metadata['file'] );
	if ( image_blur_is_unprosessable_image( $type, $sizes['full'] ) ) {
		return $metadata;
	}

	foreach ( get_intermediate_image_sizes() as $size ) {
		$info = image_get_intermediate_size( $id, $size );
		if ( $info ) {
			$sizes[ $size ] = $basedir . '/' . $info['path'];
		}
	}

	foreach ( $sizes as $size => $path ) {
		$image = $create( $path );

		if ( ! $image ) {
			continue;
		}

		$processed = $mime === 'image/png' ? image_blur_downscale_png( $image ) : image_blur_downscale_image( $image );
		image_blur_blur_image( $processed );

		ob_start();
		$output( $processed );
		$contents = ob_get_clean();

		update_post_meta(
			$id,
			'image_blur_' . $size,
			base64_encode( $contents )
		);
	}

	return $metadata;
}

function image_blur_render_blur_to_edit_view( array $form_fields, WP_Post $post ): array {
	if ( ! wp_attachment_is_image( $post->ID ) ) {
		return $form_fields;
	}

	$mime = get_post_mime_type( $post->ID );
	if ( ! $mime ) {
		return $form_fields;
	}

	$sizes = get_intermediate_image_sizes();
	$sizes[] = 'full';

	foreach ( $sizes as $size ) {
		$key = 'image_blur_' . $size;
		$blur = get_post_meta( $post->ID, $key, true );
		if ( ! $blur ) {
			continue;
		}

		$form_fields[ $key ] = array(
			'input' => 'text',
			'value' => esc_url( 'data:' . $mime . ';base64,' . $blur, array( 'data' ) ),
			'label' => $key,
		);
	}
	return $form_fields;
}

function image_blur_get_all_image_ids() {
	return get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
}

function image_blur_activate(): void {
	foreach ( image_blur_get_all_image_ids() as $id ) {
		$metadata = wp_get_attachment_metadata( $id );
		if ( ! $metadata ) {
			continue;
		}
		image_blur_generate_blur_for_attachment( $metadata, $id );
	}
}

function image_blur_deactivate(): void {
	foreach ( image_blur_get_all_image_ids() as $id ) {
		image_blur_clear_blurs( $id );
	}
}

add_filter( 'wp_generate_attachment_metadata', 'image_blur_generate_blur_for_attachment', 10, 2 );
add_filter( 'wp_update_attachment_metadata', 'image_blur_generate_blur_for_attachment', 10, 2 );
add_filter( 'attachment_fields_to_edit', 'image_blur_render_blur_to_edit_view', 10, 2 );
add_action( 'delete_attachment', 'image_blur_clear_blurs' );
