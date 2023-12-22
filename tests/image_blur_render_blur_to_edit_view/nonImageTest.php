<?php

test('edit view for non-image', function() {
    WP_Mock::userFunction('wp_attachment_is_image')->andReturn(false);
    WP_Mock::userFunction('get_post_mime_type', [
        "times" => 0
    ]);

    require SRC_FOLDER . "index.php";

    $post = new WP_Post();
    $post->ID = 69;
    $result = image_blur_render_blur_to_edit_view([], $post);

    expect($result)->toEqual([]);
});