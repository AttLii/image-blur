<?php declare(strict_types=1);

use ImageBlur\Service\ProcessImage;
use Spatie\Snapshots\MatchesSnapshots;

final class ProcessImageTest extends WP_Mock\Tools\TestCase {
	/**
	 * Instantiated Process Image class
	 *
	 * @var ProcessImage
	 */
	public $service;

	use MatchesSnapshots;

	public function setUp(): void {
		WP_Mock::setUp();
		$this->service = new ProcessImage();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		Mockery::close();
	}

	public function testCanCreateInstanceOfClass() {
		$this->assertInstanceOf( ProcessImage::class, $this->service );
	}

	public function testChooseFuncsForMimeTypeMethod() {
		list( $process, $output ) = $this->service->choose_funcs_for_mime_type( 'unknown/mime' );
		$this->assertNull( $process );
		$this->assertNull( $output );

		list( $process, $output ) = $this->service->choose_funcs_for_mime_type( 'image/png' );
		$this->assertEquals( $process, array( $this->service, 'process_png' ) );
		$this->assertEquals( $output, 'imagepng' );

		list( $process, $output ) = $this->service->choose_funcs_for_mime_type( 'image/jpeg' );
		$this->assertEquals( $process, array( $this->service, 'process_image' ) );
		$this->assertEquals( $output, 'imagejpeg' );

		list( $process, $output ) = $this->service->choose_funcs_for_mime_type( 'image/gif' );
		$this->assertEquals( $process, array( $this->service, 'process_image' ) );
		$this->assertEquals( $output, 'imagegif' );
	}

	public function testDownscaleMethod() {
		WP_Mock::onFilter( 'image-blur-modify-width' )
			->with( 8 )
			->reply( 20 );
			
		$image = imagecreate(400, 600);
		$downscaled_image = $this->service->downscale( $image );

		$width = imagesx($downscaled_image);
		$height = imagesy($downscaled_image);
		
		$this->assertEquals($width, 20);
		$this->assertEquals($height, 30);
	}

	public function testGaussianBlurMethod() {
		$strength = 10;

		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( $strength );

		$test_image_content = file_get_contents( realpath( __DIR__ . "/../../../tests/assets/test-image-gaussian-blur.jpg") );
		
		$test_image = imagecreatefromstring( $test_image_content );
		$this->service->gaussian_blur( $test_image );

		ob_start();
		imagejpeg( $test_image );
		$test_image_content = ob_get_clean();

		$this->assertEquals( sha1( $test_image_content ), sha1_file( __DIR__ . "/../../../tests/assets/test-image-gaussian-blur-$strength.jpg" ) );
	}

/* 	public function testProcessImageMethod() {
		$mock_image_content = file_get_contents( realpath( __DIR__ . "/../../assets/test-image-process-image.jpg" ) );
		$image = imagecreatefromstring( $mock_image_content );
		$processed_image = $this->service->process_image($image);

		ob_start();
		imagejpeg( $processed_image );
		$mock_image_content = ob_get_contents();
		ob_end_clean();

		$expected_image_content = file_get_contents( realpath( __DIR__ . "/../../assets/test-image-process-image-processed.jpg" ) );
		$this->assertEquals($mock_image_content, $expected_image_content);
	}
	
	public function testProcessPngMethod() {
		$mock_image_content = file_get_contents( realpath( __DIR__ . "/../../assets/test-image-process-png.png" ) );
		$image = imagecreatefromstring( $mock_image_content );
		$processed_image = $this->service->process_png($image);

		ob_start();
		imagepng( $processed_image );
		$mock_image_content = ob_get_contents();
		ob_end_clean();

		$expected_image_content = file_get_contents( realpath( __DIR__ . "/../../assets/test-image-process-png-processed.png" ) );
		$this->assertEquals($mock_image_content, $expected_image_content);

	} */
}
