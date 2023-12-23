<?php

test('webp is prosessable', function() {
    require SRC_FOLDER . "index.php";
    $path = realpath(dirname(__FILE__) . "/../assets/funImage.webp");
    expect(image_blur_is_unprosessable_image("webp", $path))->toBe(false);
});