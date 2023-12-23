<?php 

test('removes blurs from all images', function() {
    WP_Mock::userFunction('get_posts', ['times' => 1])->andReturn([1,2,3,4]);

    // this is called in the clear-function, doing early return since that is the easiest way to assert that it is called.
    WP_Mock::userFunction('get_post_meta', [
        'times' => 4,
    ])->andReturn(false);

    
    require SRC_FOLDER . "index.php";
    image_blur_deactivate();
});