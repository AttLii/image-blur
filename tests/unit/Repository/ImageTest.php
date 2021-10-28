<?php

use ImageBlur\Repository\Image as ImageRepository;

final class ImageTest extends WP_Mock\Tools\TestCase {
	public function setUp(): void {
    $this->repo = new ImageRepository();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function testCanCreateInstanceOfClass() {
		$this->assertInstanceOf(ImageRepository::class, $this->repo);
	}

  public function testGetAllImageSizesMethod() {
    WP_Mock::userFunction("get_intermediate_image_sizes", array(
      "return" => array(
        "thumbnail",
        "post-thumbnail",
        "foo"
      )
    ));

    $result = $this->repo->get_all_image_sizes();
    
    $this->assertEquals($result, array(
      "thumbnail",
      "post-thumbnail",
      "foo"
    ));
  }

  public function testGetAllImageSizesWithDefaultMethod() {
    WP_Mock::userFunction("get_intermediate_image_sizes", array(
      "return" => array(
        "thumbnail",
        "post-thumbnail",
        "foo"
      )
    ));

    $result = $this->repo->get_all_image_sizes_with_default();
    
    $this->assertEquals($result, array(
      "thumbnail",
      "post-thumbnail",
      "foo",
      "original"
    ));
  }

  public function testIsImageMethod() {
    WP_Mock::userFunction("wp_attachment_is_image", array(
      "return" => true
    ));

    $result = $this->repo->is_image(1);
    $this->assertEquals($result, true);
  }

  public function testGetAllImageIdsMethod() {
    WP_Mock::userFunction("get_posts", array(
      "args" => array(
        array(
          "post_type"      => "attachment",
          "post_mime_type" => "image",
          "posts_per_page" => -1,
          "fields"         => "ids"
        )
      ),
      "return" => array(1, 2, 3, 4)
    ));

    $result = $this->repo->get_all_image_ids();
    $this->assertEquals($result, array(1, 2, 3, 4));
  }
}