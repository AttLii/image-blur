<?php

use ImageBlur\Plugin;

final class PluginTest extends WP_Mock\Tools\TestCase {

	public function setUp(): void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		Mockery::close();
	}

	public function testCanCreateInstanceOfClass() {
		$this->assertInstanceOf( Plugin::class, new Plugin() );
	}

	public function testAddHooksMethod() {
		$plugin = new Plugin();
		WP_Mock::expectFilterAdded( 'wp_generate_attachment_metadata', array( $plugin, 'generate_blur_for_attachment' ), 10, 2 );
		WP_Mock::expectFilterAdded( 'wp_update_attachment_metadata', array( $plugin, 'generate_blur_for_attachment' ), 10, 2 );
		WP_Mock::expectFilterAdded( 'attachment_fields_to_edit', array( $plugin, 'render_blur_data_in_edit_view' ), 10, 2 );
		WP_Mock::expectActionAdded( 'delete_attachment', array( $plugin, 'remove_blurs_for_removed_attachment' ) );
		$plugin->add_hooks();

		$this->assertHooksAdded();
	}

	/**
	 * Test case for when post is not an image attachment
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRenderBlurDataInEditViewMethodWithBadAttachment() {
		Mockery::mock( 'overload:ImageBlur\Repository\Image' )
			->shouldReceive( 'is_image' )
			->with( 404 )
			->andReturn( false );

		$mock_post = new WP_Post();
		$mock_post->ID = 404;

		$plugin = new Plugin();
		$result = $plugin->render_blur_data_in_edit_view( array(), $mock_post );

		$this->assertEquals( $result, array() );
	}

	/**
	 * Test case for when environment doesnt have image sizes
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRenderBlurDataInEditViewMethodWithNoImageSizes() {
		$image_repository_mock = Mockery::mock( 'overload:ImageBlur\Repository\Image' );
		$image_repository_mock->shouldReceive( 'is_image' )
			->with( 1 )
			->andReturn( true );

		$image_repository_mock->shouldReceive( 'get_all_image_sizes_with_default' )
			->andReturn( array() );

		$mock_post = new WP_Post();
		$mock_post->ID = 1;

		$form_fields = array(
			array(
				'input' => 'text',
				'value' => 'some text field',
				'label' => 'label for the text fields',
			),
		);

		$plugin = new Plugin();
		$result = $plugin->render_blur_data_in_edit_view( $form_fields, $mock_post );

		$this->assertEquals( $result, $form_fields );
	}

	/**
	 * Test for case when everything goes as planned
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRenderBlurDataInEditViewMethod() {
		$image_repository_mock = Mockery::mock( 'overload:ImageBlur\Repository\Image' );
		$image_repository_mock->shouldReceive( 'is_image' )
			->with( 1 )
			->andReturn( true );

		$image_repository_mock->shouldReceive( 'get_all_image_sizes_with_default' )
			->andReturn(
				array(
					'thumbnail',
					'medium',
					'large',
				)
			);

		Mockery::mock( 'overload:ImageBlur\Repository\ImageBlur' )
			->shouldReceive( 'get' )
			->times( 3 )
			->withArgs(
				function ( $id, $size ) {
					if ( $id !== 1 ) {
						return false;
					}

					return in_array(
						$size,
						array(
							'thumbnail',
							'medium',
							'large',
						)
					);

				}
			)
			->andReturnUsing(
				function( $id, $size ) {
					return "blur for id ${id} with size ${size}";
				}
			);

		$mock_post = new WP_Post();
		$mock_post->ID = 1;

		$form_fields = array(
			'prefix' => array(
				'input' => 'text',
				'value' => 'some text field',
				'label' => 'label for the text fields',
			),
		);

		$plugin = new Plugin();
		$result = $plugin->render_blur_data_in_edit_view( $form_fields, $mock_post );

		$this->assertEquals(
			$result,
			array(
				'prefix' => array(
					'input' => 'text',
					'value' => 'some text field',
					'label' => 'label for the text fields',
				),
				'image_blur_thumbnail' => array(
					'input' => 'text',
					'value' => 'blur for id 1 with size thumbnail',
					'label' => 'thumbnail',
				),
				'image_blur_medium' => array(
					'input' => 'text',
					'value' => 'blur for id 1 with size medium',
					'label' => 'medium',
				),
				'image_blur_large' => array(
					'input' => 'text',
					'value' => 'blur for id 1 with size large',
					'label' => 'large',
				),
			)
		);
	}

	/**
	 * A test case for when id isn't for an image attachment
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testGenerateBlurForAttachmentMethodWithNonImageId() {
		Mockery::mock( 'overload:ImageBlur\Repository\Image' )
			->shouldReceive( 'is_image' )
			->with( 404 )
			->andReturn( false );

		$mock_meta_data = array(
			'file' => '2021/10/uploaded-file.jpg',
			'sizes' => array(),
		);

		$plugin = new Plugin();
		$plugin->generate_blur_for_attachment( $mock_meta_data, 404 );

		// Mockery::mock does assertion which phpunit doesnt recognize
		$this->assertTrue( true );
	}

	/**
	 * A test case for when id isn't for an image attachment
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testGenerateBlurForAttachmentMethod() {
		Mockery::mock( 'overload:ImageBlur\Repository\Image' )
			->shouldReceive( 'is_image' )
			->with( 404 )
			->andReturn( false );

		$mock_meta_data = array(
			'file' => '2021/10/uploaded-file.jpg',
			'sizes' => array(),
		);

		$plugin = new Plugin();
		$plugin->generate_blur_for_attachment( $mock_meta_data, 404 );

		// Mockery::mock does assertion which phpunit doesnt recognize
		$this->assertTrue( true );
	}

	/**
	 * test for remove_blurs_for_removed_attachment, when WP passes in attachment id that is not for image
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRemoveBlursForRemovedAttachmentMethodWithVideoId() {
		$video_id = 1337;
		Mockery::mock( 'overload:ImageBlur\Repository\Image' )
			->shouldReceive( 'is_image' )
			->with( $video_id )
			->andReturn( false );

		$plugin = new Plugin();
		$plugin->remove_blurs_for_removed_attachment( $video_id );

		$this->assertTrue( true );
	}

	/**
	 * test for remove_blurs_for_removed_attachment, when blur data deletion goes as planned
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRemoveBlursForRemovedAttachmentMethod() {
		$attachment_id = 1;

		Mockery::mock( 'overload:ImageBlur\Repository\Image' )
			->shouldReceive( 'is_image' )
			->with( $attachment_id )
			->andReturn( true );

		Mockery::mock( 'overload:ImageBlur\Repository\ImageBlur' )
			->shouldReceive( 'clear' )
			->with( $attachment_id );

		$plugin = new Plugin();
		$plugin->remove_blurs_for_removed_attachment( $attachment_id );

		$this->assertTrue( true );
	}

	/**
	 * test plugin deactivation
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testDeactivateMethod() {
		Mockery::mock( 'overload:ImageBlur\Repository\Image' )
			->shouldReceive( 'get_all_image_ids' )
			->andReturn( array( 1, 2, 3 ) );

		$mock_image_blur_repo = Mockery::mock( 'overload:ImageBlur\Repository\ImageBlur' );

		$mock_image_blur_repo
			->shouldReceive( 'clear' )
			->with( 1 );
		$mock_image_blur_repo
			->shouldReceive( 'clear' )
			->with( 2 );
		$mock_image_blur_repo
			->shouldReceive( 'clear' )
			->with( 3 );

		$plugin = new Plugin();
		$plugin->deactivate();

		$this->assertTrue( true );
	}

	/**
	 * test plugin activation
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testActivateMethod() {
		$metadata = array();
		$mock_image_repository = Mockery::mock( 'overload:ImageBlur\Repository\Image' );
		$mock_image_repository
			->shouldReceive( 'get_all_image_ids' )
			->andReturn( array( 1, 2, 3 ) );
		$mock_image_repository
			->shouldReceive( 'is_image' )
			->andReturn( false );
		$mock_image_repository
			->shouldReceive( 'get_all_image_sizes_with_default' )
			->andReturn( array() );

		WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'times' => 3,
				'return' => $metadata,
			)
		);

		$plugin = new Plugin();
		$plugin->activate();

		$this->assertTrue( true );
	}
	/**
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testChooseFuncsForMimeType() {
		$plugin = new Plugin();
		
		$this->assertEquals(
			$plugin->choose_funcs_for_mime_type("image/jpeg"),
			array(
				"imagecreatefromjpeg", "imagejpeg"
			)
		);
	}
}
