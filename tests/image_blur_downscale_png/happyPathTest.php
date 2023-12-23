<?php

test('resizes image to be 8px wide (default value)', function() {
    $file = realpath(dirname(__FILE__) . "/../assets/funImage.png");
    $gd_image = imagecreatefrompng($file);

    require SRC_FOLDER . "index.php";
    $downscaled = image_blur_downscale_png($gd_image);
    
    expect(imagesx($gd_image))->toBe(800);
    expect(imagesy($gd_image))->toBe(400);
    
    expect(imagesx($downscaled))->toBe(8);
    expect(imagesy($downscaled))->toBe(4);
});