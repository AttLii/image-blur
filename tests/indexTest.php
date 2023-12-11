<?php

describe('index.php', function() {
    test('defines global variable', function() {
        include SRC_FOLDER . "/index.php";

        expect(defined('IMAGE_BLUR_PLUGIN_INIT'))->toBeTrue();
    });
});