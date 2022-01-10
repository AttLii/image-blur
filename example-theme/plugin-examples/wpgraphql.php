<?php

// example usage
// {
//   mediaItems {
//    edges {
//      cursor
//      node {
//        sourceUrl
//        fullImageBlur: imageBlur(size: "full")
//        customSizeImageBlur: imageBlur(size: "my-custom-size")
//      }
//    }
//  }
// }

/**
 * This example shows how to add image blur data to wpgraphql media items.
 */
function add_image_blur_to_mediaitem() {
  register_graphql_field(
    'mediaItem', // type
    'imageBlur', // field
    array(
      'type' => 'String',
      'args' => array( 
          'size' => array(
          'type' => 'String'
        ),
      ),
      'resolve' => function($post, $args) {
        $image_blur_size = "image_blur_" . $args['size']; 
        return get_post_meta($post->ID, $image_blur_size, true);
      },
    )
  );
}

add_action( 'graphql_register_types', "add_image_blur_to_mediaitem"); 