<?php

test(
	'non .webp is prosessable',
	function () {
		require SRC_FOLDER . 'index.php';
		expect( image_blur_is_unprosessable_image( 'jpg', "doesn't matter" ) )->toBe( false );
		expect( image_blur_is_unprosessable_image( 'gif', "doesn't matter" ) )->toBe( false );
		expect( image_blur_is_unprosessable_image( 'jpeg', "doesn't matter" ) )->toBe( false );
		expect( image_blur_is_unprosessable_image( 'png', "doesn't matter" ) )->toBe( false );
	}
);
