<?php
/**
 * Handles logic for the term thumbnail.
 *
 * @package PowerPack Elements
 * @since 1.4.13
 */

use PowerpackElements\Classes\PP_Admin_Settings;
use PowerpackElements\Classes\PP_Posts_Helper;

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PP_Taxonomy_Thumbnail.
 */
final class PP_Taxonomy_Thumbnail {
	/**
	 * Holds the value of setting field taxonomy_thumbnail_enable.
	 *
	 * @since 1.4.13
	 */
	static public $taxonomy_thumbnail_enable = 'disabled';

	/**
	 * Holds the value of setting field taxonomies.
	 *
	 * @since 1.4.13
	 */
	static public $taxonomies = array();

	/**
	 * Initializing.
	 *
	 * @since 1.4.13
	 */
	static public function init() {

		self::$taxonomy_thumbnail_enable = PP_Admin_Settings::get_option( 'pp_elementor_taxonomy_thumbnail_enable' );

		self::$taxonomies = PP_Admin_Settings::get_option( 'pp_elementor_taxonomy_thumbnail_taxonomies' );

		if ( ! self::$taxonomy_thumbnail_enable ) {
			return;
		}
		add_action( 'admin_init', __CLASS__ . '::taxonomy_thumbnail_hooks' );
		add_action( 'admin_print_scripts', __CLASS__ . '::taxonomy_admin_scripts' );
		add_action( 'admin_print_styles', __CLASS__ . '::taxonomy_admin_styles' );
	}

	static public function taxonomy_admin_scripts() {
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_script( 'pp-taxonomy-thumbnail-upload', POWERPACK_ELEMENTS_URL . '/assets/js/pp-taxonomy-thumbnail.js', array( 'jquery' ), null, false );
	}

	static public function taxonomy_admin_styles() {
		?>
		<style>
			.column-taxonomy_thumbnail {
				width: 80px;
			}
		</style>
		<?php
	}

	/**
	 * Dynamically create hooks for each taxonomy for edit page.
	 *
	 * Adds hooks for each taxonomy that the user has selected
	 * via settings page. These hooks
	 * enable the image interface on wp-admin/edit-tags.php.
	 *
	 * @since 1.4.13
	 */
	static public function taxonomy_thumbnail_hooks() {
		if ( !is_array(self::$taxonomies) ) {
			return;
		}
		if ( empty( self::$taxonomies ) ) {
			return;
		}
		$taxonomy_thumbnail_enable     = PP_Taxonomy_Thumbnail::$taxonomy_thumbnail_enable;
		$taxonomy_thumbnail_taxonomies = PP_Taxonomy_Thumbnail::$taxonomies;
		if ( 'enabled' === $taxonomy_thumbnail_enable ) {
			foreach ( self::$taxonomies as $taxonomy ) {
				add_filter( 'manage_' . $taxonomy . '_custom_column', __CLASS__ . '::taxonomy_thumbnail_taxonomy_rows', 15, 3 );
				add_filter( 'manage_edit-' . $taxonomy . '_columns', __CLASS__ . '::taxonomy_thumbnail_taxonomy_columns' );
				add_action( $taxonomy . '_edit_form_fields', __CLASS__ . '::taxonomy_thumbnail_edit_tag_form', 10, 2 );
				add_action( $taxonomy . '_add_form_fields', __CLASS__ . '::taxonomy_thumbnail_add_tag_form', 10 );
				add_action( 'edit_term', __CLASS__ . '::taxonomy_thumbnail_save_term', 10, 3 );
				add_action( 'create_term', __CLASS__ . '::taxonomy_thumbnail_save_term', 10, 3 );
			}
		}
	}

	/**
	 * Save Edited Term.
	 *
	 * @see taxonomy_thumbnail_hooks()
	 *
	 * @param array A list of columns.
	 * @return array List of columns with "Images" inserted after the checked.
	 * @since 1.4.13
	 */
	static public function taxonomy_thumbnail_save_term( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['taxonomy_thumbnail_id'] ) ) {
			update_term_meta( $term_id, 'taxonomy_thumbnail_id', sanitize_text_field( $_POST['taxonomy_thumbnail_id'] ) );
		}
	}


	/**
	 * Edit Term Columns.
	 *
	 * Insert a new column on wp-admin/edit-tags.php.
	 *
	 * @see taxonomy_thumbnail_hooks()
	 *
	 * @param array A list of columns.
	 * @return array List of columns with "Images" inserted after the checked.
	 * @since 1.4.13
	 */
	static public function taxonomy_thumbnail_taxonomy_columns( $original_columns ) {
		$new_columns = $original_columns;
		array_splice( $new_columns, 1 );
		$new_columns['taxonomy_thumbnail'] = esc_html__( 'Image', 'powerpack' );
		return array_merge( $new_columns, $original_columns );
	}

	/**
	 * Edit Term Rows.
	 *
	 * Create image control for each term row of wp-admin/edit-tags.php.
	 *
	 * @see taxonomy_thumbnail_hooks()
	 *
	 * @param string    Row.
	 * @param string    Name of the current column.
	 * @param int   Term ID.
	 * @return    string    @see taxonomy_thumbnail_control_image()
	 * @since 1.4.13
	 */
	static public function taxonomy_thumbnail_taxonomy_rows( $row, $column_name, $term_id ) {
		if ( 'taxonomy_thumbnail' === $column_name ) {
			$html = '<div id="taxonomy_thumbnail_preview">';
			$taxonomy_thumbnail_id = '';
			$taxonomy_thumbnail_id = get_term_meta( $term_id, 'taxonomy_thumbnail_id', true );
			if ( '' !== $taxonomy_thumbnail_id ) {
				$obj_taxonomy_thumbnail = wp_get_attachment_image_src( $taxonomy_thumbnail_id, 'thumbnail' );
				if ( ! empty( $obj_taxonomy_thumbnail ) ) {
					$taxonomy_thumbnail_img_url = $obj_taxonomy_thumbnail[0];

					$html .= '<img id="taxonomy_thumbnail_preview_img" width="50" height="50" src="' . $taxonomy_thumbnail_img_url . '" >';
				}
			}
			$html .= '</div>';
			return $row . $html;
		}
		return $row;
	}

	/**
	 * Edit Term Control.
	 *
	 * Create image control for wp-admin/edit-tag-form.php.
	 * Hooked into the $taxonomy. '_edit_form_fields' action.
	 *
	 * @param stdClass  Term object.
	 * @param string    Taxonomy slug.
	 * @since 1.4.13
	 */
	static public function taxonomy_thumbnail_edit_tag_form( $term, $taxonomy ) {
		$taxonomy = get_taxonomy( $taxonomy );
		$name     = __( 'term', 'powerpack' );
		if ( isset( $taxonomy->labels->singular_name ) )
			$name = strtolower( $taxonomy->labels->singular_name );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php print esc_html__( 'Featured Image', 'powerpack' ); ?></label></th>
			<td>
				<div id="taxonomy_thumbnail_preview">
				<?php
				$taxonomy_thumbnail_id = '';
				$taxonomy_thumbnail_id = get_term_meta( $term->term_id, 'taxonomy_thumbnail_id', true );
				if ( '' !== $taxonomy_thumbnail_id ) {
					$obj_taxonomy_thumbnail = wp_get_attachment_image_src( $taxonomy_thumbnail_id, 'thumbnail' );
					if ( ! empty( $obj_taxonomy_thumbnail ) ) {
						$taxonomy_thumbnail_img_url = $obj_taxonomy_thumbnail[0];
						?>
						<img id="taxonomy_thumbnail_preview_img" width="150" height="150" src="<?php echo $taxonomy_thumbnail_img_url; ?>" ><br>
						<?php
					}
				}
				?>
				</div>
				<input id="taxonomy_thumbnail_id" type="hidden" name="taxonomy_thumbnail_id" value="<?php echo $taxonomy_thumbnail_id; ?>" />
				<input id="upload_taxonomy_thumbnail_button" type="button" class="button button-primary" value="<?php echo esc_html__( 'Upload', 'powerpack' ); ?>" />
				<?php
				$delete_button_inline_css = 'display:none';
				if ( '' !== $taxonomy_thumbnail_id ) {
					$delete_button_inline_css = '';
				}
				?>
				<input style="<?php echo $delete_button_inline_css; ?>" id="delete_taxonomy_thumbnail_button" type="button" class="button button-danger" value="<?php echo esc_html__( 'Delete', 'powerpack' ); ?>" />
				<div class="clear"></div>
				<?php
				// translators: %1$s for label.
				?>
				<span class="description"><?php printf( esc_html__( 'Add an image from media library to this %1$s.', 'powerpack' ), esc_html( $name ) ); ?></span>
			</td>
		</tr>
		<?php
	}

	static public function taxonomy_thumbnail_add_tag_form( $taxonomy ) {
		$taxonomy = get_taxonomy( $taxonomy );
		$name     = __( 'term', 'powerpack' );
		if ( isset( $taxonomy->labels->singular_name ) ) {
			$name = strtolower( $taxonomy->labels->singular_name );
		}
		?>
		<div class="form-field term-thumbnail-wrap">
			<label for="description"><?php print esc_html__( 'Featured Image', 'powerpack' ); ?></label>
			<div id="taxonomy_thumbnail_preview">
			</div>
			<input id="taxonomy_thumbnail_id" type="hidden" name="taxonomy_thumbnail_id" value="" />
			<input id="upload_taxonomy_thumbnail_button" type="button" class="button button-primary" value="<?php echo esc_html__( 'Upload', 'powerpack' ); ?>" />
			<?php
				$delete_button_inline_css = 'display:none';
			?>
			<input style="<?php echo $delete_button_inline_css; ?>" id="delete_taxonomy_thumbnail_button" type="button" class="button button-danger" value="<?php echo esc_html__( 'Delete', 'powerpack' ); ?>" />
			<div class="clear"></div>
			<?php
				//translators: %1$s for label.
			?>
			<span class="description"><?php printf( esc_html__( 'Add an image from media library to this %1$s.', 'powerpack' ), esc_html( $name ) ); ?></span>
		</div>
		<?php
	}
}

// Initialize the class.
PP_Taxonomy_Thumbnail::init();
