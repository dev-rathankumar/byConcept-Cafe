<?php
/**
 * Class to setup Taxonomies metabox customizations
 *
 * @package Pizzaro/WooCommerce
 */

class Pizzaro_Product_Taxonomies {

	public function __construct() {

		// Add scripts
		add_action( 'admin_enqueue_scripts',					array( $this, 'load_wp_media_files' ), 0 );

		// Add options for Product Categories
		add_action( "product_cat_add_form_fields",				array( $this, 'add_category_fields' ), 10 );
		add_action( "product_cat_edit_form_fields",				array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'create_term',								array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term',								array( $this, 'save_category_fields' ), 10, 3 );

		$food_type = pizzaro_get_food_type_taxonomy();
		if( ! empty( $food_type ) ) {
			
			// Add form
			add_action( "{$food_type}_add_form_fields",			array( $this, 'add_food_type_fields' ), 10 );
			add_action( "{$food_type}_edit_form_fields",		array( $this, 'edit_food_type_fields' ), 10, 2 );
			add_action( 'create_term',							array( $this, 'save_food_type_fields' ), 	10, 3 );
			add_action( 'edit_term',							array( $this, 'save_food_type_fields' ), 	10, 3 );
		}
	}

	/**
	 * Loads WP Media Files
	 *
	 * @return void
	 */
	public function load_wp_media_files() {
		wp_enqueue_media();
	}

	/**
	 * Product Category metabox fields.
	 *
	 * @return void
	 */
	public function add_category_fields() {
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Display Layout', 'pizzaro' ); ?></label>
			<select id="display_layout" class="form-control" name="display_layout">
				<option value=""><?php echo esc_html__( 'Default', 'pizzaro' ); ?></option>
				<option value="full-width"><?php echo esc_html__( 'Full Width', 'pizzaro' ); ?></option>
				<option value="left-sidebar"><?php echo esc_html__( 'Left Sidebar', 'pizzaro' ); ?></option>
				<option value="right-sidebar"><?php echo esc_html__( 'Right Sidebar', 'pizzaro' ); ?></option>
			</select>
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Display Style', 'pizzaro' ); ?></label>
			<select id="display_style" class="form-control" name="display_style">
				<option value=""><?php echo esc_html__( 'Default', 'pizzaro' ); ?></option>
				<option value="lite"><?php echo esc_html__( 'Lite', 'pizzaro' ); ?></option>
				<option value="dark"><?php echo esc_html__( 'Dark', 'pizzaro' ); ?></option>
			</select>
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Display View', 'pizzaro' ); ?></label>
			<select id="display_view" class="form-control" name="display_view">
				<option value=""><?php echo esc_html__( 'Default', 'pizzaro' ); ?></option>
				<option value="grid-view"><?php echo esc_html__( 'Grid', 'pizzaro' ); ?></option>
				<option value="list-view"><?php echo esc_html__( 'List', 'pizzaro' ); ?></option>
				<option value="list-no-image-view"><?php echo esc_html__( 'List without Image', 'pizzaro' ); ?></option>
			</select>
		</div>
		<?php
	}

	/**
	 * Edit Category metabox fields.
	 *
	 * @param mixed $term Term (product_cat) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_category_fields( $term, $taxonomy ) {

		$display_layout 	= get_term_meta( $term->term_id, 'display_layout', true );
		$display_style 		= get_term_meta( $term->term_id, 'display_style', true );
		$display_view 		= get_term_meta( $term->term_id, 'display_view', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Display Layout', 'pizzaro' ); ?></label></th>
			<td>
				<div class="form-group">
					<select id="display_layout" class="form-control" name="display_layout">
						<option value=""><?php echo esc_html__( 'Default', 'pizzaro' ); ?></option>
						<option value="full-width" <?php echo ( $display_layout == 'full-width'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'Full Width', 'pizzaro' ); ?></option>
						<option value="left-sidebar" <?php echo ( $display_layout == 'left-sidebar'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'Left Sidebar', 'pizzaro' ); ?></option>
						<option value="right-sidebar" <?php echo ( $display_layout == 'right-sidebar'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'Right Sidebar', 'pizzaro' ); ?></option>
					</select>
				</div>
				<div class="clear"></div>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Display Style', 'pizzaro' ); ?></label></th>
			<td>
				<div class="form-group">
					<select id="display_style" class="form-control" name="display_style">
						<option value=""><?php echo esc_html__( 'Default', 'pizzaro' ); ?></option>
						<option value="lite" <?php echo ( $display_style == 'lite'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'Lite', 'pizzaro' ); ?></option>
						<option value="dark" <?php echo ( $display_style == 'dark'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'Dark', 'pizzaro' ); ?></option>
					</select>
				</div>
				<div class="clear"></div>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Display View', 'pizzaro' ); ?></label></th>
			<td>
				<div class="form-group">
					<select id="display_view" class="form-control" name="display_view">
						<option value=""><?php echo esc_html__( 'Default', 'pizzaro' ); ?></option>
						<option value="grid-view" <?php echo ( $display_view == 'grid-view'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'Grid', 'pizzaro' ); ?></option>
						<option value="list-view" <?php echo ( $display_view == 'list-view'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'List', 'pizzaro' ); ?></option>
						<option value="list-no-image-view" <?php echo ( $display_view == 'list-no-image-view'  ? 'selected' : '' ); ?>><?php echo esc_html__( 'List without Image', 'pizzaro' ); ?></option>
					</select>
				</div>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save Category metabox fields.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function save_category_fields( $term_id, $tt_id, $taxonomy ) {

		if ( isset( $_POST['display_layout'] ) ) {
			update_woocommerce_term_meta( $term_id, 'display_layout', $_POST['display_layout'] );
		}

		if ( isset( $_POST['display_style'] ) ) {
			update_woocommerce_term_meta( $term_id, 'display_style', $_POST['display_style'] );
		}

		if ( isset( $_POST['display_view'] ) ) {
			update_woocommerce_term_meta( $term_id, 'display_view', $_POST['display_view'] );
		}

		delete_transient( 'wc_term_counts' );
	}

	/**
	 * Product Food Type metabox fields.
	 *
	 * @return void
	 */
	public function add_food_type_fields() {
		?>
		<div class="form-field">
			<label for="icon_class"><?php esc_html_e( 'Icon Class', 'pizzaro' ); ?></label>
			<input type="text" maxlength="28" value="" id="icon_class" name="icon_class">
		</div>
		<?php
	}

	/**
	 * Edit Food Type metabox fields.
	 *
	 * @param mixed $term Term (product_cat) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_food_type_fields( $term, $taxonomy ) {

		$icon_class = get_term_meta( $term->term_id, 'icon_class', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Icon Class', 'pizzaro' ); ?></label></th>
			<td>
				<div class="form-group">
					<input type="text" maxlength="28" value="<?php echo esc_attr( $icon_class ); ?>" id="icon_class" name="icon_class">
				</div>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save Food Type metabox fields.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function save_food_type_fields( $term_id, $tt_id, $taxonomy ) {

		if ( isset( $_POST['icon_class'] ) ) {
			update_woocommerce_term_meta( $term_id, 'icon_class', $_POST['icon_class'] );
		}

		delete_transient( 'wc_term_counts' );
	}

	/**
	 * Icon column added to food type admin.
	 *
	 * @param mixed $columns
	 * @return array
	 */
	public function product_food_type_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['icon'] = esc_html__( 'Icon', 'pizzaro' );

		unset( $columns['cb'] );

		unset( $columns['description'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Icon column value added to food type admin.
	 *
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function product_food_type_column( $columns, $column, $id ) {

		if ( $column == 'icon' ) {

			$icon_class 	= '';
			$icon_class 	= get_term_meta( $id, 'icon_class', true );

			$columns .= '<div style="width:15px;height:15px;font-size: 1.875em;"><i class="' . esc_attr( $icon_class ) . '"></i></div>';

		}

		return $columns;
	}
}

new Pizzaro_Product_Taxonomies;