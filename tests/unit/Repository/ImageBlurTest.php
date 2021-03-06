<?php

use ImageBlur\Repository\ImageBlur as ImageBlurRepository;

final class ImageBlurTest extends WP_Mock\Tools\TestCase {
	public function setUp(): void {
		$this->repo = new ImageBlurRepository();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function testCanCreateInstanceOfClass() {
		$this->assertInstanceOf( ImageBlurRepository::class, $this->repo );
	}

	public function testSetMethod() {
		WP_Mock::userFunction(
			'update_post_meta',
			array(
				'args' => array(
					1,
					'image_blur_foo',
					'bar',
				),
			)
		);

		$this->repo->set( 1, 'foo', 'bar' );

		$this->assertTrue( true );
	}

	public function testGetMethod() {
		WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args' => array(
					1,
					'image_blur_foo',
					true,
				),
				'return' => 'baz',
			)
		);

		$result = $this->repo->get( 1, 'foo' );

		$this->assertEquals( $result, 'baz' );
	}

	public function testGetMethodWithoutDataInDB() {
		WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args' => array(
					1,
					'image_blur_doesnt_exist',
					true,
				),
				'return' => null,
			)
		);

		$result = $this->repo->get( 1, 'doesnt_exist' );

		$this->assertEquals( $result, '' );
	}

	public function testDeleteMethod() {
		WP_Mock::userFunction(
			'delete_post_meta',
			array(
				'args' => array( 1, 'image_blur_foo' ),
			)
		);

		$this->repo->delete( 1, 'foo' );

		$this->assertTrue( true );
	}

	public function testClearMethod() {
		$id = 1;

		WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args' => $id,
				'return' => array(
					'image_blur_thumbnail' => 'foo',
					'image_blur_large' => 'bar',
					'_wp_attachment' => 'foo',
					'wp_attachment' => 'bar',
				),
			)
		);

		WP_Mock::userFunction(
			'delete_post_meta',
			array(
				'args' => array( $id, 'image_blur_thumbnail' ),
			)
		);

		WP_Mock::userFunction(
			'delete_post_meta',
			array(
				'args' => array( $id, 'image_blur_large' ),
			)
		);

		$this->repo->clear( $id );

		$this->assertTrue( true );
	}

	public function testClearMethodWithBadId() {
		$id = 404;

		WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args' => $id,
				'return' => array(
					'_wp_attachment' => 'foo',
					'wp_attachment' => 'bar',
				),
			)
		);

		WP_Mock::userFunction(
			'delete_post_meta',
			array(
				'times' => 0,
			)
		);

		$this->repo->clear( $id );

		$this->assertTrue( true );
	}
}
