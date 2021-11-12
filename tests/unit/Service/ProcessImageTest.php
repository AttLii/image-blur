<?php

use ImageBlur\Service\ProcessImage;

final class ProcessImageTest extends WP_Mock\Tools\TestCase {
  /**
   * Instantiated Process Image class
   * 
   * @var ProcessImage
   */
  public $service;

	public function setUp(): void {
		WP_Mock::setUp();
    $this->service = new ProcessImage();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		Mockery::close();
	}

	public function testCanCreateInstanceOfClass() {
		$this->assertInstanceOf(ProcessImage::class, $this->service);
	}

	public function testChooseFuncsForMimeTypeMethod() {
    list( $process, $output ) = $this->service->choose_funcs_for_mime_type("unknown/mime");
    $this->assertNull($process);
    $this->assertNull($output);

    list( $process, $output ) = $this->service->choose_funcs_for_mime_type("image/png");
    $this->assertEquals($process, [$this->service, "process_png" ]);
    $this->assertEquals($output, "imagepng");

    list( $process, $output ) = $this->service->choose_funcs_for_mime_type("image/jpeg");
    $this->assertEquals($process, [$this->service, "process_image" ]);
    $this->assertEquals($output, "imagejpeg");

    list( $process, $output ) = $this->service->choose_funcs_for_mime_type("image/gif");
    $this->assertEquals($process, [$this->service, "process_image" ]);
    $this->assertEquals($output, "imagegif");
	}
}