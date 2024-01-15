<?php

test(
	'applies expected blur to the image',
	function () {
		require SRC_FOLDER . 'index.php';

		$gd_image = imagecreatefromjpeg( TEST_ASSET_FOLDER . '/funImage.jpg' );
		image_blur_blur_image( $gd_image );

		ob_start();
		imagejpeg( $gd_image );
		$contents = ob_get_clean();

		expect( base64_encode( $contents ) )->toMatchSnapshot();
	}
);
