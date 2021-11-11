<?php
namespace ImageBlur;

use ImageBlur\Constants;
use ImageBlur\Utils;
use ImageBlur\Repository\Image as ImageRepository;
use ImageBlur\Repository\ImageBlur as ImageBlurRepository;
use ImageBlur\Service\ProcessImage as ProcessImageService;

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

  /**
   * Instantiated process image service class.
   * 
   * @var ProcessImageService
   */
  public $process_image_service;


  public function __construct() {
    $this->image_repository = new ImageRepository();
    $this->image_blur_repository  = new ImageBlurRepository();
    $this->process_image_service = new ProcessImageService();

    $this->add_hooks();
  }

  public function add_hooks(): void {
    add_filter( "wp_generate_attachment_metadata", [ $this, "generate_blur_for_attachment" ], 10, 2);
    add_filter( 'attachment_fields_to_edit', [ $this, "render_blur_data_in_edit_view" ], 10, 2 );
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
    if ( $this->image_repository->is_image( $id ) ) {
      $sizes = $meta_data["sizes"];
      
      $sizes[Constants::DEFAULT_IMAGE_SIZE] = array(
        "file" => wp_basename( $meta_data["file"] )
      );
      
      // get upload dir's path on the server;
      $upload_dir_path = wp_upload_dir()["basedir"];
      
      // image's folder on the server
      $folder_path = dirname( $upload_dir_path . "/" . $meta_data["file"] );

      foreach ($sizes as $size => $size_data) {
        $file_path = $folder_path . "/" . $size_data["file"];

        $content = file_get_contents( $file_path );
        if( $content === false ) continue;

        $mime = mime_content_type( $file_path );
        if( $mime === false ) continue;

        $image_process_function_ref = null;
        $image_output_function_ref = null;
        if ( $mime === "image/png" ) {
          $image_process_function_ref = array( $this->process_image_service, "process_png" );
          $image_output_function_ref = "imagepng";
        } else {
          $image_process_function_ref = array( $this->process_image_service, "process_image" );
          if ( $mime === "image/jpeg" ) {
            $image_output_function_ref = "imagejpeg";
          } else if ( $mime === "image/gif" ) {
            $image_output_function_ref = "imagegif";
          }
        }
        
        if ( $image_output_function_ref && $image_process_function_ref ) {
          $image = imagecreatefromstring( $content );
          $image = call_user_func($image_process_function_ref, $image);

          ob_start();
          $image_output_function_ref( $image );
          $contents = ob_get_contents();
          ob_end_clean();
  
          $data = "data:" . $mime . ";base64," . base64_encode( $contents );
  
          $this->image_blur_repository->set( $id, $size, $data );
        }
      }
    }

    // filter requires different hooks to return this value. We dont use this hooks for altering meta_datas value.
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
