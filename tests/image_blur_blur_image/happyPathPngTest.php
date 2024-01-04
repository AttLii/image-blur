<?php

test(
	'applies expected blur to the image',
	function () {
		$gd_image = imagecreatefrompng( TEST_ASSET_FOLDER . '/funImage.png' );

		require SRC_FOLDER . 'index.php';
		image_blur_blur_image( $gd_image );

		ob_start();
		imagepng( $gd_image );
		$contents = ob_get_clean();

		expect( base64_encode( $contents ) )->toMatchSnapshot();
	}
);
