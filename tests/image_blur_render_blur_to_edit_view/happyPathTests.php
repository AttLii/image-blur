<?php

test(
	'edit view for non-image',
	function () {
		WP_Mock::userFunction( 'wp_attachment_is_image' )->andReturn( true );
		WP_Mock::userFunction( 'get_post_mime_type' )->andReturn( 'png' );
		WP_Mock::userFunction( 'get_intermediate_image_sizes' )->andReturn( array( 'custom-image-size' ) );
		WP_Mock::userFunction(
			'get_post_meta',
			array(
				'times' => 2,
				'return_in_order' => array( null, 'foo' ),
			)
		);

		require SRC_FOLDER . 'index.php';

		$post = new WP_Post();
		$post->ID = 69;
		$result = image_blur_render_blur_to_edit_view( array(), $post );

		expect( $result )->toEqual(
			array(
				'image_blur_full' => array(
					'input' => 'text',
					'value' => 'data:png;base64,foo',
					'label' => 'image_blur_full',
				),
			)
		);
	}
);
