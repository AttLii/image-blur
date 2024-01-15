<?php

test(
	'removes post metas that start with image_blur_',
	function () {
		WP_Mock::userFunction( 'get_post_meta' )->andReturn(
			array(
				'trash' => 'doesnt matter',
				'image_blur_full' => '1234',
				'image_blur_foo' => '5678',
			)
		);
		WP_Mock::userFunction(
			'delete_post_meta',
			array(
				'times' => 2,
			)
		);
		require SRC_FOLDER . 'index.php';
		image_blur_clear_blurs( 1 );
	}
);
