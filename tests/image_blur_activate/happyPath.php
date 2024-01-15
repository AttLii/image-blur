<?php

test(
	'applies expected blur to the images',
	function () {
		WP_Mock::userFunction( 'get_posts', array( 'times' => 1 ) )->andReturn( array( 1, 2, 3, 4 ) );
		WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'times' => 4,
				'return_in_order' => array( null, array( 'foo' ), array( 'bar' ), array( 'baz' ) ),
			)
		);

		// this is called in the main function, doing early return since that is the easiest way to assert that it is called.
		WP_Mock::userFunction(
			'wp_attachment_is_image',
			array(
				'times' => 3,
			)
		)->andReturn( false );

		require SRC_FOLDER . 'index.php';
		image_blur_activate();
	}
);
