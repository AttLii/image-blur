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
		$this->assertInstanceOf( ImageRepository::class, $this->repo );
	}

	public function testGetAllImageSizesMethod() {
		WP_Mock::userFunction(
			'get_intermediate_image_sizes',
			array(
				'return' => array(
					'thumbnail',
					'post-thumbnail',
					'foo',
				),
			)
		);

		$result = $this->repo->get_all_image_sizes();

		$this->assertEquals(
			$result,
			array(
				'thumbnail',
				'post-thumbnail',
				'foo',
			)
		);
	}

	public function testGetAllImageSizesWithDefaultMethod() {
		WP_Mock::userFunction(
			'get_intermediate_image_sizes',
			array(
				'return' => array(
					'thumbnail',
					'post-thumbnail',
					'foo',
				),
			)
		);

		$result = $this->repo->get_all_image_sizes_with_default();

		$this->assertEquals(
			$result,
			array(
				'thumbnail',
				'post-thumbnail',
				'foo',
				'full',
			)
		);
	}

	public function testIsImageMethod() {
		WP_Mock::userFunction(
			'wp_attachment_is_image',
			array(
				'return' => true,
			)
		);

		$result = $this->repo->is_image( 1 );
		$this->assertEquals( $result, true );
	}

	public function testGetAllImageIdsMethod() {
		WP_Mock::userFunction(
			'get_posts',
			array(
				'args' => array(
					array(
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'posts_per_page' => -1,
						'fields'         => 'ids',
					),
				),
				'return' => array( 1, 2, 3, 4 ),
			)
		);

		$result = $this->repo->get_all_image_ids();
		$this->assertEquals( $result, array( 1, 2, 3, 4 ) );
	}

	public function testGetMimeTypeMethod() {
		WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'return' => 'image/png',
			)
		);
		$result = $this->repo->get_mime_type( 1 );
		$this->assertEquals( $result, 'image/png' );
	}

	public function testGetUrlForSizeMethod() {
		WP_Mock::userFunction( "wp_get_attachment_image_url", array(
			"times" => 1,
			"with" => array( 1, "full" ),
			"return" => "https://cdn.client.com/wp-content/uploads/2021/12/merry-xmas.png"
		) );

		WP_Mock::userFunction( "wp_get_attachment_image_url", array(
			"times" => 1,
			"with" => array( 404, "weird-size-that-doesnt-exist" ),
			"return" => false
		) );

		$result = $this->repo->get_url_for_size( 1, 'full' );
		$this->assertEquals( $result, "https://cdn.client.com/wp-content/uploads/2021/12/merry-xmas.png" );

		$result = $this->repo->get_url_for_size( 404, 'weird-size-that-doesnt-exist' );
		$this->assertNull( $result );
	}
}
