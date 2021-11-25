<?php
/**
 * A basic example for rendering blur for an image attachment.
 */

// Image attachment's id.
$attachment_id = 16;

$prefix = 'image_blur_';

$image_size = 'full';

$key = $prefix . $image_size;

$mime = get_post_mime_type( $attachment_id );

// this value can be null in various cases, do null checking if necessary.
$blur_data = get_post_meta( $attachment_id, $key, true );

$src = "data:$mime;base64,$blur_data"; 
?>

<img src='<?php echo $src; ?>' />
