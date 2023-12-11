<?php

function get_dir_files($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            get_dir_files($path, $results);
        }
    }

    return $results;
}

describe('WPINC check', function () {
    $file_paths = get_dir_files(SRC_FOLDER);
    foreach($file_paths as $file_path) {
        if (str_ends_with($file_path, '.php')) {
            $dir_and_filename = explode(DIRECTORY_SEPARATOR . "src", $file_path);
            test("src" . $dir_and_filename[count($dir_and_filename) - 1], function () use ($file_path) {
                $content = file_get_contents($file_path);
                expect(strpos($content, "\ndefined( 'WPINC' ) || die;\n") !== false)->toBeTrue(); 
            });
        }
    }
});