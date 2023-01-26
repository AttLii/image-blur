<?php
/**
 * This is a template to render a single attachment with image blur plugin.
 */

// existing image attachment's id.
$attachment_id = get_the_ID();

// prefix or namespace used by image blur plugin to store data to post meta table.
$prefix = 'image_blur_';

// needed image size.
$image_size = 'full';

$key = $prefix . $image_size;

$mime = get_post_mime_type( $attachment_id );

// this value can be null in various cases, do null checking if necessary.
$blur_data = get_post_meta( $attachment_id, $key, true );

list( $url, $width, $height ) = wp_get_attachment_image_src( $attachment_id, $image_size );

$blur_src = "data:$mime;base64,$blur_data";

?>
<div class="image-wrapper">
  <img
		class="image-blur"
		role="presentation"
		src="<?= esc_url( $blur_src, array( 'data' ) ); ?>"
  />
  <img
		class="image"
		width="<?= esc_attr( $width ); ?>"
		height="<?= esc_attr( $height ); ?>"
		src="<?= esc_url( $url ); ?>"
  />
</div>

<style>
  .image-wrapper {
		position: relative;
		display: inline-block;
  }

  .image-blur {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		transition: 0.4s opacity linear 0.4s;
		opacity: 1;
  }

  .js-hide {
		opacity: 0;
  }
</style>

<script>
  const blur = document.querySelector(".image-blur");
  const image = document.querySelector(".image");
  image.addEventListener("load", () => blur.classList.add("js-hide"))
</script>
