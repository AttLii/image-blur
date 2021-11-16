<?php
/**
 * A basic example for rendering blur for an image attachment.
 */

// Image attachment's id.
$attachment_id = 16;

$prefix = 'image_blur_';

// a coined term for the default size.
$image_size = 'original';

$key = $prefix . $image_size;

// this value can be null in various cases, do null checking if necessary.
$blur_data = get_post_meta( $attachment_id, $key, true );
?>

<img src='<?php echo $blur_data; ?>' />
