<?php

test(
	'defines global variable and adds expected hooks',
	function () {
		WP_Mock::expectFilterAdded( 'wp_generate_attachment_metadata', 'image_blur_generate_blur_for_attachment', 10, 2 );
		WP_Mock::expectFilterAdded( 'wp_update_attachment_metadata', 'image_blur_generate_blur_for_attachment', 10, 2 );
		WP_Mock::expectFilterAdded( 'attachment_fields_to_edit', 'image_blur_render_blur_to_edit_view', 10, 2 );
		WP_Mock::expectActionAdded( 'delete_attachment', 'image_blur_clear_blurs' );
		require SRC_FOLDER . 'index.php';
		expect( defined( 'IMAGE_BLUR_PLUGIN_INIT' ) )->toBeTrue();
	}
);
