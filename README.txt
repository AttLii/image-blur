=== Image Blur ===
Contributors: attlii
Tags: media, image, blur
Requires at least: 5.6
Tested up to: 5.8
Stable tag: 1.0.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generates base64 encoded, downscaled and blurred versions of media library's images.

== Inspiration ==
After Wolt showcased their [Blurhash](https://blurha.sh/) technique, I wanted to make something similar to Wordpress. Instead of encoding images to base83 like the food delivery company, I went with base64 so development experience was easier. Using preferred encoding is on the roadmap. 

== How to start using the plugin ==
- Install and activate this plugin in your environment
  - The plugin should generate blurs for each image on the activation.
- Check an image's details in your media library. It should have listing of all generated blurs.
- To get a blur to a template, use `get_post_meta()`. Examples are at the end of this documentation.

== Frequently Asked Questions ==

= How to generate blurs for new image sizes? =

To regenerate blurs you can :
- use f.e. [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) plugin
- use [WP CLI](https://developer.wordpress.org/cli/commands/media/regenerate/) if your environment has it installed.
- reactive this plugin (not recommended on high traffic sites)

= This bloats database too much, how do I clean up DB from this mess? =

This plugin comes with clean up functionality, that is run when the plugin is deactivated.

For manual deletion (which shouldn't be needed), you can remove all `image_blur_` prefixed rows from `wp_postmeta` table.