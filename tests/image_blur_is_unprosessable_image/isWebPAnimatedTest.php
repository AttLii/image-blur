<?php

test('animated webp is unprosessable', function () {
    require SRC_FOLDER . "index.php";
    $path = TEST_ASSET_FOLDER . "/funImageAnimated.webp";
    expect(image_blur_is_unprosessable_image("webp", $path))->toBe(true);
});
