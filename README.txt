=== Image Blur ===
Contributors: attlii
Tags: media, image, blur
Requires at least: 5.6
Tested up to: 5.8.2
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generates base64 encoded, downscaled and blurred versions of media library's images, which can be used f.e. as a placeholder.

== Inspiration ==
After Wolt showcased their [Blurhash](https://blurha.sh/) technique, I wanted to make something similar to Wordpress. Instead of encoding images to base83 like the food delivery company does, I went with base64 so development experience was easier.

== How to start using the plugin ==
- Install and activate this plugin in your environment
  - The plugin should generate blurs for each image on the activation.
- Check an image's details in your media library. It should have listing of all generated blurs.
- To get a blur to a template, use `get_post_meta()`. Check example theme in [plugin's repository](https://github.com/AttLii/image-blur)

== Frequently Asked Questions ==

= How to generate blurs for new image sizes? =

To regenerate blurs you can:
- use f.e. [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) plugin
- use [WP CLI](https://developer.wordpress.org/cli/commands/media/regenerate/) if your environment has it installed.
- reactive this plugin (not recommended on high traffic sites)

= How do I clear DB from blur data? =

This plugin cleans up generated blurs when it is deactivated.

For manual deletion, you can remove all rows from `wp_postmeta` table that have `image_blur_` prefix in `meta_key` column.

= Which image formats are supported? =

During development phase, this plugin was tested with .jpg, .jpeg, .gif, .png and .webp, which are the default accepted image types to media library.

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

== Changelog ==

= 1.0.0 (2021-11-30): =
- Initial release

= 1.0.1 (2021-11-30): =
- Remove development related files from plugin directory