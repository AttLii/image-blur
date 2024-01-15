=== Image Blur ===
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

= 1.1.0 (2021-12-02): =
- Add CDN support

= 1.2.0 (2022-01-10): =
- Add example script to support WPGraphQL plugin

= 1.2.1 (2022-01-28): =
- Update tested up to version to 5.9

= 1.2.2 (2022-02-28): =
- Fix the issue when the plugin is accidentally included to environment through mu-plugins and plugins.

= 2.0.0 (2023-01-26): =
- Test and confirm that plugin works with PHP 8.0 and WP 6.1.1. Update requires to meet these versions.

= 2.0.1 (2023-07-26): =
- Test and confirm that plugin works with PHP 8.1 and WP 6.3.

= 3.0.0 (2024-01-15): =
- Rewrite core to be more simpler.
- Test and confirm that plugin works with PHP 8.2 and WP 6.4.2.
- Test and confirm that plugin work with WP GraphQL plugin 1.19.0.

= 3.0.1 (2024-01-15): =
- Remove redundant some files and folders from production package.

= 3.0.2 (2024-01-15): =