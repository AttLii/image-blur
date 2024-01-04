<?php

test(
	'activation and deactivation hook is called',
	function () {
		// this is defined in bootstrap-file, so we dont have to define it in every other test.
		global $IMAGE_BLUR_PLUGIN_INIT;
		unset( $IMAGE_BLUR_PLUGIN_INIT );

		WP_Mock::userFunction(
			'register_activation_hook',
			array(
				'times' => 1,
				'args'  => array(
					WP_Mock\Functions::type( 'string' ),
					WP_Mock\Functions::type( 'callable' ),
				),
			)
		);
		WP_Mock::userFunction(
			'register_deactivation_hook',
			array(
				'times' => 1,
				'args'  => array(
					WP_Mock\Functions::type( 'string' ),
					WP_Mock\Functions::type( 'callable' ),
				),
			)
		);

		require __DIR__ . '/../image-blur.php';

		// since all tests are run in isolation, doing the thing below doesnt fix anything at the moment.
		// leaving it here, so if in future we can run tests more conveniently
		// commenting also removes a warning
		// define('IMAGE_BLUR_PLUGIN_INIT', true);
	}
);
