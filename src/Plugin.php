<?php
namespace ImageBlur;

use ImageBlur\Constants;
use ImageBlur\Utils;
use ImageBlur\Repository\Image as ImageRepository;
use ImageBlur\Repository\ImageBlur as ImageBlurRepository;
use ImageBlur\Service\ProcessImage as ProcessImageService;
use ImageBlur\Parser\Attachment as AttachmentParser;

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

  /**
   * Add hooks in separate method, this is here for unit testing purposes.
   */
  public function add_hooks(): void {
    add_filter( "wp_generate_attachment_metadata", [ $this, "generate_blur_for_attachment" ], 10, 2);
    add_filter( "attachment_fields_to_edit", [ $this, "render_blur_data_in_edit_view" ], 10, 2 );
    add_action( "delete_attachment", [ $this, "remove_blurs_for_removed_attachment" ] );
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
  public function generate_blur_for_attachment( $metadata, $id ) {
    if ( $this->image_repository->is_image( $id ) ) {

      $this->image_blur_repository->clear( $id );

      $mime = $this->image_repository->get_mime_type( $id );

      $sizes = AttachmentParser::parse_sizes_from_metadata( $metadata );
      
      list( $process_func, $output_func ) = $this->process_image_service->choose_funcs_for_mime_type( $mime );

      if ( $process_func !== null && $output_func !== null ) {

        list( "basedir" => $basedir ) = wp_upload_dir();

        foreach ( $sizes as $size => $path ) {
          $content = file_get_contents( "$basedir/$path" );
  
          $image = imagecreatefromstring( $content );
          $image = $process_func( $image );

          ob_start();
          $output_func( $image );
          $contents = ob_get_contents();
          ob_end_clean();
  
          $data = "data:" . $mime . ";base64," . base64_encode( $contents );
  
          $this->image_blur_repository->set( $id, $size, $data );
        }
      }
    }

    return $metadata;
  }

  /**
   * A function that is run when this plugin is deactivated.
   * It cleans post meta table from generated blur images.
   */
  public function deactivate(): void {
    $ids = $this->image_repository->get_all_image_ids();
    foreach ($ids as $id) {
      $this->image_blur_repository->clear($id);
    }
  }

  /**
   * A function that is run when this plugin is activated.
   * It generates blurs from each image in media library to all defined image sizes.
   */
  public function activate(): void {
    $ids = $this->image_repository->get_all_image_ids();
    $sizes = $this->image_repository->get_all_image_sizes_with_default();
    foreach ($ids as $id) {
      $metadata = wp_get_attachment_metadata($id);
      $this->generate_blur_for_attachment($metadata, $id);
    }
  }

  /**
   * A function that removes blur data for deleted image. This is added to delete_attachment action.
   * 
   * @param int $id - attachment's ID
   * @return void
   */
  public function remove_blurs_for_removed_attachment( int $id ): void {
    if ( $this->image_repository->is_image( $id ) ) {
      $this->image_blur_repository->clear($id);
    }
  }
}
