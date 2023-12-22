<?php

test('no post meta found', function() {
    WP_Mock::userFunction('get_post_meta')->andReturn(false);
    WP_Mock::userFunction('delete_post_meta', [
        "times" => 0
    ]);
    require SRC_FOLDER . "index.php";
    image_blur_clear_blurs(1);
});