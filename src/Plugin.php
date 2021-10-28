<?php
namespace ImageBlur;

use ImageBlur\Constants;
use ImageBlur\Utils;
use ImageBlur\Repository\Image as ImageRepository;
use ImageBlur\Repository\ImageBlur as ImageBlurRepository;

/**
 * Stop execution if not in Wordpress environment
 */
defined("WPINC") or die;

/**
 * Main class of the plugin.
 */
class Plugin {

  /**
   * Instantiated image repository class.
   * 
   * @var ImageRepository
   */
  public $image_repository;

  /**
   * Instantiated image blur repository class.
   * 
   * @var ImageBlurRepository
   */
  public $image_blur_repository;


  public function __construct() {
    $this->image_repository = new ImageRepository();
    $this->image_blur_repository  = new ImageBlurRepository();

    $this->add_hooks();
  }

  public function add_hooks(): void {
    add_filter( "wp_generate_attachment_metadata", [ $this, "generate_blur_for_attachment" ], 10, 2);
    add_filter( 'attachment_fields_to_edit', [$this, "render_blur_data_in_edit_view"], 10, 2 );
  }

  /**
   * Adds generated blur images to image's edit view for debugging purposes.
   * 
   * @param array $form_fields - array of existing form_fields
   * @param WP_Post $post - attachment as a post object
   */
  public function render_blur_data_in_edit_view ( $form_fields, $post ) {
    if ( $this->image_repository->is_image($post->ID) ) {
      $sizes = $this->image_repository->get_all_image_sizes_with_default();
  
      foreach ($sizes as $size) {
        $key = Utils::add_plugin_prefix($size);
        $form_fields[$key] = [
          "input" => "text",
          "value" => $this->image_blur_repository->get($post->ID, $size), 
          "label" => $size,
        ];
      }
    }
    return $form_fields;
  }

  /**
   * Function is attached to wp_generate_attachment_metadata hook.
   * It generates downscaled and blurred version of the image to postmeta table.
   * 
   * @param array $meta_data - meta data information about uploaded attachment
   * @param int $id - id of the attachment
   */
  public function generate_blur_for_attachment( $meta_data, $id ) {
    if ( $this->image_repository->is_image($id) ) {
      $sizes = $meta_data["sizes"];
      
      // get upload dir's path on the server;
      $upload_dir_path = wp_upload_dir()["basedir"];
      
      // image's folder on the server
      $folder_path = dirname($upload_dir_path . "/" . $meta_data["file"]);

      // add default size
      $sizes[Constants::DEFAULT_IMAGE_SIZE] = array(
        "file" => wp_basename($meta_data["file"])
      );

      foreach ($sizes as $size => $size_data) {

        $content = file_get_contents($folder_path . "/" . $size_data["file"]);

        if ($content) {

          // generate FD Image object from the content
          $image = imagecreatefromstring($content);

          // apply resizing, -1 so we keep the aspect ratio
          $image = imagescale($image, 10, -1);

          // apply gaussian blur
          for ( $x = 0; $x <= 1; $x++ ) {
            imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
          }

          // capture image's content to a variable
          ob_start();
          imagejpeg($image);
          $contents = ob_get_contents();
          ob_end_clean();

          $data = "data:image/jpeg;base64," . base64_encode($contents);

          $this->image_blur_repository->set($id, $size, $data);
        } 
      }
    }

    return $meta_data;
  }

  /**
   * A function that is run when this plugin is deactivated. Cleans post meta table from generated blur images.
   */
  public function deactivate() {
    $ids = $this->image_repository->get_all_image_ids();
    $sizes = $this->image_repository->get_all_image_sizes_with_default();
    foreach ($ids as $id) {
      foreach ($sizes as $size) {
        $this->image_blur_repository->delete($id, $size);
      }
    }
  }
}
