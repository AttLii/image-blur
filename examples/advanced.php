<?php
/**
 * Advanced example for rendering image blur for an image attachment, while main image is loading.
 */

// existing image attachment's id.
$id = 17;

$prefix = 'image_blur_';

$image_size = 'full';

$key = $prefix . $image_size;

// this value can be null in various cases, do null checking if necessary.
$blur_data = get_post_meta( $id, $key, true );

list($url, $width, $height) = wp_get_attachment_image_src( $id, $image_size );

?>
<div class="image-wrapper">
  <img
	class="image-blur"
	role="presentation"
	src="<?php echo $blur_data; ?>"
  />
  <img
	class="image"
	width="<?php echo $width; ?>"
	height="<?php echo $height; ?>"
	src="<?php echo $url; ?>"
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
	transition: 0.4s opacity linear 0.1s;
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
