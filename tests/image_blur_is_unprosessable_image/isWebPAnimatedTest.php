<?php

test('animated webp is unprosessable', function() {
    require SRC_FOLDER . "index.php";
    $path = dirname(__FILE__) . "/../assets/funImageAnimated.webp";
    expect(image_blur_is_unprosessable_image("webp", $path))->toBe(true);
});