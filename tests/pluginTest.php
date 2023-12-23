<?php

test('activation hook is called', function() {
    global $IMAGE_BLUR_PLUGIN_INIT;
    unset($IMAGE_BLUR_PLUGIN_INIT);

    WP_Mock::userFunction('register_activation_hook', ['times' => 1]);

    require dirname(__FILE__) . "/../image-blur.php";
    
    // since all tests are run in isolation, doing the thing below doesnt fix anything at the moment.
    // leaving it here, so if in future we can run tests more conveniently
    // commenting also removes a warning
    // define('IMAGE_BLUR_PLUGIN_INIT', true);
});