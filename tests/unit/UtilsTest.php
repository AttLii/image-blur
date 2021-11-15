<?php

use ImageBlur\Utils;

final class UtilsTest extends WP_Mock\Tools\TestCase {
	public function setUp(): void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function testAddPluginPrefixMethod() {
		$result = Utils::add_plugin_prefix( 'foo' );
		$this->assertEquals( $result, 'image_blur_foo' );
	}

	public function testHasPluginPrefixMethod() {
		$result = Utils::has_plugin_prefix( 'image_blur_thumbnail' );
		$this->assertTrue( $result );

		$result = Utils::has_plugin_prefix( 'idonthaverightprefix' );
		$this->assertFalse( $result );
	}
}
