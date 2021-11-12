<?php

use ImageBlur\Parser\Attachment;

final class AttachmentTest extends WP_Mock\Tools\TestCase {

	public function setUp(): void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		Mockery::close();
	}

  public function testParseSizesFromMetadataMethod() {
    $data = [
      "file" => "2021/11/Vector-1.png",
      "sizes" => [
        "thumbnail" => [
          "file" => "Vector-1-thumbnail.png"
        ],
        "large" => [
          "file" => "Vector-1-large.png"
        ]
      ]
    ];
    $result = Attachment::parse_sizes_from_metadata($data);

    $this->assertEquals(
      $result,
      [
        "original" => '2021/11/Vector-1.png',
        "thumbnail" => '2021/11/Vector-1-thumbnail.png',
        "large" => '2021/11/Vector-1-large.png'
      ]
    );
  }
}