<?php
namespace ImageBlur;

use ImageBlur\Constants;
use ImageBlur\Utils;
use ImageBlur\Repository\Image as ImageRepository;
use ImageBlur\Repository\ImageBlur as ImageBlurRepository;
use ImageBlur\Service\ImageManipulation as ImageManipulationService;
use ImageBlur\Parser\Attachment as AttachmentParser;

/**
 * Stop execution if not in Wordpress environment
 */
defined( 'WPINC' ) || die;

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
	 * @var ImageManipulationService
	 */
	public $image_manipulation_service;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->image_repository = new ImageRepository();
		$this->image_blur_repository  = new ImageBlurRepository();
		$this->image_manipulation_service = new ImageManipulationService();

		$this->add_hooks();
	}

	/**
	 * Add hooks in separate method, this is here for unit testing purposes.
	 */
	public function add_hooks(): void {
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_blur_for_attachment' ), 10, 2 );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'generate_blur_for_attachment' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'render_blur_data_in_edit_view' ), 10, 2 );
		add_action( 'delete_attachment', array( $this, 'remove_blurs_for_removed_attachment' ) );
	}

	/**
	 * Adds generated blur images to image's edit view for debugging purposes.
	 *
	 * @param array   $form_fields - array of existing form_fields.
	 * @param WP_Post $post - attachment as a post object.
	 * @return array $form_fields - modified form_fields parameter.
	 */
	public function render_blur_data_in_edit_view( $form_fields, $post ) {
		if ( $this->image_repository->is_image( $post->ID ) ) {
			$mime = get_post_mime_type( $post->ID );

			$sizes = $this->image_repository->get_all_image_sizes_with_default();

			foreach ( $sizes as $size ) {
				$key = Utils::add_plugin_prefix( $size );

				$value = "";
				$blur = $this->image_blur_repository->get( $post->ID, $size );
				if ( $blur ) {

					$form_fields[ $key ] = array(
						'input' => 'text',
						'value' => esc_url( "data:$mime;base64,$blur", array( "data" ) ),
						'label' => $size,
					);
				}
			}
		}
		return $form_fields;
	}

	/**
	 * Function is attached to wp_generate_attachment_metadata and wp_update_attachment_metadata filter.
	 * It generates downscaled and blurred version of the image to postmeta table.
	 *
	 * @param array $metadata - meta data information about the uploaded attachment.
	 * @param int   $id - id of the attachment.
	 * @return array $metadata - passed in metadata value.
	 */
	public function generate_blur_for_attachment( $metadata, $id ) {
		if ( $this->image_repository->is_image( $id ) ) {

			$this->image_blur_repository->clear( $id );

			$mime = $this->image_repository->get_mime_type( $id );
			list( $create, $output ) = $this->choose_funcs_for_mime_type( $mime );

			if ( function_exists( $create ) && function_exists( $output ) ) {
				list( 'basedir' => $basedir ) = wp_upload_dir();
				$sizes = AttachmentParser::parse_sizes_from_metadata( $metadata );
				foreach ( $sizes as $size => $path ) {
					$image = $create( "$basedir/$path" );
					$image = $this->image_manipulation_service->process_image( $mime, $image );

					ob_start();
					$output( $image );
					$contents = ob_get_clean();

					$data = base64_encode( $contents );
					$this->image_blur_repository->set( $id, $size, $data );
				}
			}
		}

		return $metadata;
	}

	/**
	 * Php opens and outputs images using different functions. In this method, we choose correct one using mime type.
	 * Remember to use function_exists() before using returned functions.
	 *
	 * @param string $mime - mime type (f.e. image/jpeg).
	 * @return array - array where first index is image open function and second index output function.
	 */
	public function choose_funcs_for_mime_type( string $mime ): array {
		$type = explode( '/', $mime )[1];
		return array( "imagecreatefrom$type", "image$type" );
	}

	/**
	 * A function that is run when this plugin is deactivated.
	 * It cleans post meta table from generated blur images.
	 */
	public function deactivate(): void {
		$ids = $this->image_repository->get_all_image_ids();
		foreach ( $ids as $id ) {
			$this->image_blur_repository->clear( $id );
		}
	}

	/**
	 * This runs when this plugin is activated.
	 * It generates blurs from each image in media library to all defined image sizes.
	 */
	public function activate(): void {
		$ids = $this->image_repository->get_all_image_ids();
		$sizes = $this->image_repository->get_all_image_sizes_with_default();
		foreach ( $ids as $id ) {
			$metadata = wp_get_attachment_metadata( $id );
			$this->generate_blur_for_attachment( $metadata, $id );
		}
	}

	/**
	 * Removes blur data for deleted image. This is added to delete_attachment action.
	 *
	 * @param int $id - attachment's ID.
	 */
	public function remove_blurs_for_removed_attachment( int $id ): void {
		if ( $this->image_repository->is_image( $id ) ) {
			$this->image_blur_repository->clear( $id );
		}
	}
}
