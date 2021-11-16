=== Image Blur ===
Contributors: attlii
Tags: media, image, blur
Requires at least: 5.6
Tested up to: 5.8
Stable tag: 1.0.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generates base64 encoded, downscaled and blurred versions of media library's images, which can be used f.e. as a placeholder.

== Inspiration ==
After Wolt showcased their [Blurhash](https://blurha.sh/) technique, I wanted to make something similar to Wordpress. Instead of encoding images to base83 like the food delivery company, I went with base64 so development experience was easier. Using different encoding is on the roadmap. 

== How to start using the plugin ==
- Install and activate this plugin in your environment
  - The plugin should generate blurs for each image on the activation.
- Check an image's details in your media library. It should have listing of all generated blurs.
- To get a blur to a template, use `get_post_meta()`. Check examples in [plugin's repository](https://github.com/AttLii/image-blur)

== Frequently Asked Questions ==

= How to generate blurs for new image sizes? =

To regenerate blurs you can:
- use f.e. [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) plugin
- use [WP CLI](https://developer.wordpress.org/cli/commands/media/regenerate/) if your environment has it installed.
- reactive this plugin (not recommended on high traffic sites)

= This bloats my database way too much, how do I clean up DB from this mess? =

This plugin cleans up generated blurs when it is deactivated.

For manual deletion (which shouldn't be needed), you can remove all rows from `wp_postmeta` table that have `image_blur_` prefix in `meta_key` column.

== Customization ==

The plugin provides few filters, so developers can modify generated blur images to their needs. By default, generated images are 8 pixels wide and passed once through gaussian blur function, which will result in roughly 250-1000 characters long string. To change these, use following hooks:

`
function modify_image_blur_width( int $width ): int {
  return 15;
}
add_filter("image-blur-modify-width", "modify_image_blur_width");

function modify_gaussian_blur_strength( int $strength ): int {
  return 10;
}
add_filter("image-blur-modify-gaussian-blur-strength", "modify_gaussian_blur_strength");
`
