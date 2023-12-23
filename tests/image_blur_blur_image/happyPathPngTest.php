<?php 

test('applies expected blur to the image', function() {
    $file = realpath(dirname(__FILE__) . "/../assets/funImage.png");
    $gd_image = imagecreatefrompng($file);

    require SRC_FOLDER . "index.php";
    image_blur_blur_image($gd_image);

    ob_start();
    imagepng($gd_image);
    $contents = ob_get_clean();

    expect(base64_encode($contents))->toMatchSnapshot();
});