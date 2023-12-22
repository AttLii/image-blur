<?php 

test('applies expected blur to the image', function() {
    $file = dirname(__FILE__) . "/../assets/funImage.jpg";
    $gd_image = imagecreatefromjpeg($file);

    require SRC_FOLDER . "index.php";
    image_blur_blur_image($gd_image);

    ob_start();
    imagejpeg($gd_image);
    $contents = ob_get_clean();

    expect(base64_encode($contents))->toMatchSnapshot();
});