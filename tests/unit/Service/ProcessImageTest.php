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
		$this->assertInstanceOf( ProcessImage::class, $this->service );
	}

	public function testProcessImageMethodToCallTransparentProcess() {
		$service_mock = Mockery::mock('ImageBlur\Service\ProcessImage[transparent_process]');
		$service_mock->shouldReceive("transparent_process")->andReturn("foo");

		$image = imagecreatefrompng("./tests/assets/gaussian-blur-unprocessed.png");

		$result = $service_mock->process_image("image/png", $image);
		$this->assertEquals($result, "foo");
	}

	public function testProcessImageMethodToCallGenericProcess() {
		$service_mock = Mockery::mock('ImageBlur\Service\ProcessImage[generic_process]');
		$service_mock->shouldReceive("generic_process")->andReturn("foo");

		$image = imagecreatefromjpeg("./tests/assets/gaussian-blur-unprocessed.jpeg");

		$result = $service_mock->process_image("image/jpeg", $image);
		$this->assertEquals($result, "foo");
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

	public function testGaussianBlurMethodWithPng() {
		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( 10 );

		$image = imagecreatefrompng( "./tests/assets/gaussian-blur-unprocessed.png" );
		$this->service->gaussian_blur( $image );

		ob_start();
		imagepng( $image );
		$processed_content = ob_get_clean();

		$this->assertEquals( sha1( $processed_content ), sha1_file( "./tests/assets/gaussian-blur-processed.png" ) );
	}

	public function testGaussianBlurMethodWithGif() {
		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( 10 );

		$image = imagecreatefromgif( "./tests/assets/gaussian-blur-unprocessed.gif" );

		$this->service->gaussian_blur( $image );

		ob_start();
		imagegif( $image );
		$processed_content = ob_get_clean();

		$this->assertEquals( sha1( $processed_content ), sha1_file( "./tests/assets/gaussian-blur-processed.gif" ) );
	}

	public function testGenericProcessMethodWithGif() {
		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( 3 );
		
		WP_Mock::onFilter( 'image-blur-modify-width' )
			->with( 8 )
			->reply( 15 );

		$image = imagecreatefromgif( "./tests/assets/process-image-unprocessed.gif" );
		$processed_image = $this->service->generic_process( $image );

		ob_start();
		imagegif( $processed_image );
		$processed_content = ob_get_clean();

		$this->assertEquals( sha1( $processed_content ), sha1_file( "./tests/assets/process-image-processed.gif" ) );
	}

	public function testGenericProcessMethodWithJpg() {
		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( 2 );
		
		WP_Mock::onFilter( 'image-blur-modify-width' )
			->with( 8 )
			->reply( 10 );

		$image = imagecreatefromjpeg( "./tests/assets/process-image-unprocessed.jpeg" );
		$processed_image = $this->service->generic_process( $image );

		ob_start();
		imagegif( $processed_image );
		$processed_content = ob_get_clean();

		$this->assertEquals( sha1( $processed_content ), sha1_file( "./tests/assets/process-image-processed.jpeg" ) );
	}

	/**
	 * Following three tests work in local but not in github actions. 
	 */

	 /*
		public function testProcessPngMethod() {
			WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
				->with( 1 )
				->reply( 2 );
			
			WP_Mock::onFilter( 'image-blur-modify-width' )
				->with( 8 )
				->reply( 10 );

			$content = file_get_contents( "./tests/assets/process-png-unprocessed.png" );
			$image = imagecreatefromstring( $content );
			$processed_image = $this->service->process_png( $image );

			ob_start();
			imagepng( $processed_image );
			$processed_content = ob_get_clean();

			$this->assertEquals( $processed_content, file_get_contents("./tests/assets/process-png-processed.png") );
		}
	 
	 public function testGaussianBlurWithJpgTest() {
		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( 5 );

		$content = file_get_contents( "./tests/assets/gaussian-blur-unprocessed.jpg" );
		$image = imagecreatefromstring( $content );

		$this->service->gaussian_blur( $image );

		ob_start();
		imagejpeg( $image );
		$processed_content = ob_get_clean();

		$this->assertEquals( sha1( $processed_content ), sha1_file( "./tests/assets/gaussian-blur-processed.jpg" ) );
	}

	public function testGaussianBlurWithJpegTest() {
		WP_Mock::onFilter( 'image-blur-modify-gaussian-blur-strength' )
			->with( 1 )
			->reply( 100 );

		$content = file_get_contents( "./tests/assets/gaussian-blur-unprocessed.jpeg" );
		$image = imagecreatefromstring( $content );

		$this->service->gaussian_blur( $image );

		ob_start();
		imagejpeg( $image );
		$processed_content = ob_get_clean();

		$this->assertEquals( sha1( $processed_content ), sha1_file( "./tests/assets/gaussian-blur-processed.jpeg" ) );
	} */
}
