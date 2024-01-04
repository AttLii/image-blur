<?php

test('resizes image to be wide as defined with the filter', function () {
    $gd_image = imagecreatefrompng(TEST_ASSET_FOLDER . "/funImage.png");

    WP_Mock::onFilter('image-blur-modify-width')->with(8)->reply(100);

    require SRC_FOLDER . "index.php";
    $downscaled = image_blur_downscale_png($gd_image);

    expect(imagesx($gd_image))->toBe(800);
    expect(imagesy($gd_image))->toBe(400);

    expect(imagesx($downscaled))->toBe(100);
    expect(imagesy($downscaled))->toBe(50);
});
