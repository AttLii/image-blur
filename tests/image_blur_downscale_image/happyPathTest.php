<?php

test('resizes image to be 8px wide (default value)', function () {
    $file = realpath(__DIR__ . "/../assets/funImage.jpg");
    $gd_image = imagecreatefromjpeg($file);

    require SRC_FOLDER . "index.php";
    $downscaled = image_blur_downscale_image($gd_image);

    expect(imagesx($gd_image))->toBe(800);
    expect(imagesy($gd_image))->toBe(400);

    expect(imagesx($downscaled))->toBe(8);
    expect(imagesy($downscaled))->toBe(4);
});
