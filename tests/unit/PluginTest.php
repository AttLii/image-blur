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
		$this->assertInstanceOf(Plugin::class, new Plugin());
  }

  public function testAddHooksMethod() {
		$plugin = new Plugin();
		WP_Mock::expectFilterAdded( 'wp_generate_attachment_metadata', [ $plugin, "generate_blur_for_attachment" ], 10, 2 );
		WP_Mock::expectFilterAdded( 'attachment_fields_to_edit', [ $plugin, "render_blur_data_in_edit_view" ], 10, 2 );
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
		Mockery::mock("overload:ImageBlur\Repository\Image")
			->shouldReceive("is_image")
			->with(404)
			->andReturn(false);

		$mock_post = new WP_Post();
		$mock_post->ID = 404;
		
		$plugin = new Plugin();
		$result = $plugin->render_blur_data_in_edit_view(array(), $mock_post);

		$this->assertEquals($result, array());
	}

	/**
	 * Test case for when environment doesnt have image sizes
	 * 
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRenderBlurDataInEditViewMethodWithNoImageSizes() {
		$image_repository_mock = Mockery::mock("overload:ImageBlur\Repository\Image");
		$image_repository_mock->shouldReceive("is_image")
			->with(1)
			->andReturn(true);
		
		$image_repository_mock->shouldReceive("get_all_image_sizes_with_default")
			->andReturn([]);

		$mock_post = new WP_Post();
		$mock_post->ID = 1;

		$form_fields = [
			[
				"input" => "text",
				"value" => "some text field",
				"label" => "label for the text fields"
			]
		];
		
		$plugin = new Plugin();
		$result = $plugin->render_blur_data_in_edit_view($form_fields, $mock_post);

		$this->assertEquals($result, $form_fields);
	}

	/**
	 * Test for case when everything goes as planned
	 * 
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testRenderBlurDataInEditViewMethod() {
		$image_repository_mock = Mockery::mock("overload:ImageBlur\Repository\Image");
		$image_repository_mock->shouldReceive("is_image")
			->with(1)
			->andReturn(true);
		
		$image_repository_mock->shouldReceive("get_all_image_sizes_with_default")
			->andReturn([
				"thumbnail",
				"medium",
				"large",
			]);

		Mockery::mock("overload:ImageBlur\Repository\ImageBlur")
			->shouldReceive("get")
			->times(3)
			->withArgs(function ($id, $size) {
				if ($id !== 1) {
					return false;
				}

				return in_array($size, [
					"thumbnail",
					"medium",
					"large",
				]);

    	})
			->andReturnUsing(function($id, $size) {
				return "blur for id ${id} with size ${size}";
			});
		

		$mock_post = new WP_Post();
		$mock_post->ID = 1;

		$form_fields = [
			"prefix" => [
				"input" => "text",
				"value" => "some text field",
				"label" => "label for the text fields"
			]
		];
		
		$plugin = new Plugin();
		$result = $plugin->render_blur_data_in_edit_view($form_fields, $mock_post);

		$this->assertEquals($result, [
			"prefix" => [
				"input" => "text",
				"value" => "some text field",
				"label" => "label for the text fields"
			],
			"image_blur_thumbnail" => [
				"input" => "text",
				"value" => "blur for id 1 with size thumbnail", 
				"label" => "thumbnail",
			],
			"image_blur_medium" => [
				"input" => "text",
				"value" => "blur for id 1 with size medium", 
				"label" => "medium",
			],
			"image_blur_large" => [
				"input" => "text",
				"value" => "blur for id 1 with size large", 
				"label" => "large",
			]
		]);
	}

	/**
	 * A test case for when id isn't for an image attachment
	 * 
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testGenerateBlurForAttachmentMethodWithNonImageId() {
		Mockery::mock("overload:ImageBlur\Repository\Image")
			->shouldReceive("is_image")
			->with(404)
			->andReturn(false);

		$mock_meta_data = array(
			"file" => "2021/10/uploaded-file.jpg",
			"sizes" => array()
		);

		$plugin = new Plugin();
		$plugin->generate_blur_for_attachment($mock_meta_data, 404);

		// Mockery::mock does assertion which phpunit doesnt recognize 
		$this->assertTrue(true);
	}

	/**
	 * A test case for when id isn't for an image attachment
	 * 
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testGenerateBlurForAttachmentMethod() {
		Mockery::mock("overload:ImageBlur\Repository\Image")
			->shouldReceive("is_image")
			->with(404)
			->andReturn(false);

		$mock_meta_data = array(
			"file" => "2021/10/uploaded-file.jpg",
			"sizes" => array()
		);

		$plugin = new Plugin();
		$plugin->generate_blur_for_attachment($mock_meta_data, 404);

		// Mockery::mock does assertion which phpunit doesnt recognize 
		$this->assertTrue(true);
	}
}