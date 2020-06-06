<?php
/**
 * Assign Points to Products Template
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ultimate_Woocommerce_Points_And_Rewards
 * @subpackage Ultimate_Woocommerce_Points_And_Rewards/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once MWB_RWPR_DIR_PATH . '/admin/partials/settings/class-points-rewards-for-woocommerce-settings.php';
include_once ULTIMATE_WOOCOMMERCE_POINTS_AND_REWARDS_DIR_PATH . '/admin/partials/settings/class-ultimate-woocommerce-points-rewards-admin-settings.php';
$settings_obj = new Ultimate_Woocommerce_Points_Rewards_Admin_Settings();

$mwb_product_purchase_points = array(
	array(
		'title' => __( 'Purchase through Points', 'ultimate-woocommerce-points-and-rewards' ),
		'type'  => 'title',
	),
	array(
		'title' => __( 'Enable Purchase through Points', 'ultimate-woocommerce-points-and-rewards' ),
		'type'  => 'checkbox',
		'desc'  => __( 'Purchase Products through Points', 'ultimate-woocommerce-points-and-rewards' ),
		'id'    => 'mwb_wpr_product_purchase_points',
		'desc_tip' => __( 'Check this box to enable puchasing products through points', 'ultimate-woocommerce-points-and-rewards' ),
		'default'   => 0,
	),
	array(
		'title' => __( 'Enable restrictions for above setting', 'ultimate-woocommerce-points-and-rewards' ),
		'type'  => 'checkbox',
		'desc'  => __( 'Allow some of the products for purchasing through points', 'ultimate-woocommerce-points-and-rewards' ),
		'id'    => 'mwb_wpr_restrict_pro_by_points',
		'desc_tip' => __( 'Check this box if you want to allow some of the products for purchasing through points not all', 'ultimate-woocommerce-points-and-rewards' ),
		'default'   => 0,
	),
	array(
		'title' => __( 'Select Product Category', 'ultimate-woocommerce-points-and-rewards' ),
		'id' => 'mwb_wpr_restrictions_for_purchasing_cat',
		'type' => 'search&select',
		'multiple' => 'multiple',
		'desc_tip' => __( 'Select those categories which you want to allow to customers for purchase that product through points.', 'ultimate-woocommerce-points-and-rewards' ),
		'options' => $settings_obj->mwb_wpr_get_category(),
	),
	array(
		'title' => __( 'Enter Text', 'ultimate-woocommerce-points-and-rewards' ),
		'type'  => 'text',
		'id'    => 'mwb_wpr_purchase_product_text',
		'class' => 'text_points mwb_wpr_new_woo_ver_style_text',
		'desc'  => __( 'Entered text will get displayed on single product page', 'ultimate-woocommerce-points-and-rewards' ),
		'desc_tip' => __( 'Entered text will get displayed on Single Product Page', 'ultimate-woocommerce-points-and-rewards' ),
		'default' => __( 'Use your Points for purchasing this Product', 'ultimate-woocommerce-points-and-rewards' ),
	),
	array(
		'title' => __( 'Purchase Points Conversion', 'ultimate-woocommerce-points-and-rewards' ),
		'type'  => 'number_text',
		'number_text' => array(
			array(
				'type'  => 'number',
				'id'    => 'mwb_wpr_purchase_points',
				'class'   => 'input-text wc_input_price mwb_wpr_new_woo_ver_style_text',
				'custom_attributes' => array( 'min' => '"1"' ),
				'desc_tip' => __(
					'Entered points will be converted to price.(i.e., how many points will be equivalent to the product price)',
					'ultimate-woocommerce-points-and-rewards'
				),
				'desc' => __( 'Points =', 'ultimate-woocommerce-points-and-rewards' ),
			),
			array(
				'type'  => 'text',
				'id'    => 'mwb_wpr_product_purchase_price',
				'class'   => 'input-text mwb_wpr_new_woo_ver_style_text wc_input_price',
				'custom_attributes' => array( 'min' => '"1"' ),
				'desc_tip' => __(
					'Entered points will be converted to price.(i.e., how many points will be equivalent to the product price)',
					'ultimate-woocommerce-points-and-rewards'
				),
				'default' => '1',
			),
		),
	),
	array(
		'title' => __( 'Make "Per Product Redemption" Readonly', 'ultimate-woocommerce-points-and-rewards' ),
		'type'  => 'checkbox',
		'desc'  => __( 'Readonly for Enter Number of Points for Redemption ', 'ultimate-woocommerce-points-and-rewards' ),
		'id'    => 'mwb_wpr_make_readonly',
		'desc_tip' => __( 'Check this box if you want to make the redemption box readonly(where end user can enter the number of points they want to redeem)', 'ultimate-woocommerce-points-and-rewards' ),
		'default'   => 0,
	),
	array(
		'type'  => 'sectionend',
	),
);
	$current_tab = 'mwb_wpr_save_product_purchase';
	$mwb_product_purchase_points = apply_filters( 'mwb_wpr_add_product_purchase_points', $mwb_product_purchase_points );
if ( isset( $_POST['mwb_wpr_save_product_purchase'] ) && isset( $_POST['mwb-wpr-nonce'] ) ) {
	$mwb_nonce = sanitize_text_field( wp_unslash( $_POST['mwb-wpr-nonce'] ) );
	if ( wp_verify_nonce( $mwb_nonce, 'mwb-wpr-nonce' ) ) {
		if ( 'mwb_wpr_save_product_purchase' == $current_tab ) {

			/* Save Settings and check is not empty*/
			$postdata = $settings_obj->check_is_settings_is_not_empty( $mwb_product_purchase_points, $_POST );
			/* End of the save Settings and check is not empty*/
			$general_settings_array = array();
			foreach ( $postdata as $key => $value ) {
				$general_settings_array[ $key ] = $value;
			}
			if ( is_array( $general_settings_array ) && ! empty( $general_settings_array ) ) {
				$general_settings_array = apply_filters( 'mwb_wpr_general_settings_save_option', $general_settings_array );
				update_option( 'mwb_wpr_product_purchase_settings', $general_settings_array );
			}
			$settings_obj->mwb_wpr_settings_saved();
			do_action( 'mwb_wpr_general_settings_save_option', $general_settings_array );
		}
	}
}
?>
<?php
$general_settings = get_option( 'mwb_wpr_product_purchase_settings', array() );
?>
	<?php
	if ( ! is_array( $general_settings ) ) :
		$general_settings = array();
endif;
	?>
	<div class="mwb_table">
		<div class="mwb_wpr_general_wrapper">
				<?php
				foreach ( $mwb_product_purchase_points as $key => $value ) {
					if ( 'title' == $value['type'] ) {
						?>
					<div class="mwb_wpr_general_row_wrap">
						<?php $settings_obj->mwb_rwpr_generate_heading( $value ); ?>
					<?php } ?>
					<?php if ( 'title' != $value['type'] && 'sectionend' != $value['type'] ) { ?>
				<div class="mwb_wpr_general_row">
						<?php $settings_obj->mwb_rwpr_generate_label( $value ); ?>
					<div class="mwb_wpr_general_content">
						<?php
						$settings_obj->mwb_rwpr_generate_tool_tip( $value );
						if ( 'checkbox' == $value['type'] ) {
							$settings_obj->mwb_rwpr_generate_checkbox_html( $value, $general_settings );
						}
						if ( 'number' == $value['type'] ) {
							$settings_obj->mwb_rwpr_generate_number_html( $value, $general_settings );
						}
						if ( 'multiple_checkbox' == $value['type'] ) {
							foreach ( $value['multiple_checkbox'] as $k => $val ) {
								$settings_obj->mwb_rwpr_generate_checkbox_html( $val, $general_settings );
							}
						}
						if ( 'text' == $value['type'] ) {
							$settings_obj->mwb_rwpr_generate_text_html( $value, $general_settings );
						}
						if ( 'textarea' == $value['type'] ) {
							$settings_obj->mwb_rwpr_generate_textarea_html( $value, $general_settings );
						}
						if ( 'number_text' == $value['type'] ) {
							foreach ( $value['number_text'] as $k => $val ) {
								if ( 'text' == $val['type'] ) {
									$settings_obj->mwb_rwpr_generate_text_html( $val, $general_settings );

								}
								if ( 'number' == $val['type'] ) {
									$settings_obj->mwb_rwpr_generate_number_html( $val, $general_settings );
									echo esc_html( get_woocommerce_currency_symbol() );
								}
							}
						}
						if ( 'search&select' == $value['type'] ) {
							$settings_obj->mwb_wpr_generate_searchSelect_html( $value, $general_settings );
						}
						?>
					</div>
				</div>
				<?php } ?>
					<?php if ( 'sectionend' == $value['type'] ) : ?>
				 </div>	
				<?php endif; ?>
			<?php } ?> 		
		</div>
	</div>
	<div class="clear"></div>
	<p class="submit">
		<input type="submit" value='<?php esc_html_e( 'Save changes', 'ultimate-woocommerce-points-and-rewards' ); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_product_purchase">
	</p>

<!-- Category Listing -->

<div class="mwb_table">
	<p class="mwb_wpr_section_notice"><?php esc_html_e( 'This is the category wise setting for purchase product from points only, enter some valid points for assigning, leave blank fields for removing assigned points', 'ultimate-woocommerce-points-and-rewards' ); ?></p>
	<div class="mwb_wpr_categ_details">
		<table class="form-table mwb_wpr_pro_points_setting mwp_wpr_settings">
			<tbody>
				<tr>
					<th class="titledesc"><?php esc_html_e( 'Categories', 'ultimate-woocommerce-points-and-rewards' ); ?></th>
					<th class="titledesc"><?php esc_html_e( 'Enter Points', 'ultimate-woocommerce-points-and-rewards' ); ?></th>
					<th class="titledesc"><?php esc_html_e( 'Assign/Remove', 'ultimate-woocommerce-points-and-rewards' ); ?></th>
				</tr>
				<?php
				$args = array( 'taxonomy' => 'product_cat' );
				$categories = get_terms( $args );
				if ( isset( $categories ) && ! empty( $categories ) ) {
					foreach ( $categories as $category ) {
						$catid = $category->term_id;
						$catname = $category->name;
						$mwb_wpr_purchase_categ_point = get_option( 'mwb_wpr_purchase_points_cat' . $catid, '' );
						?>
						<tr>
							<td><?php echo esc_html( $catname ); ?></td>
							<td><input type="number" min="1" name="mwb_wpr_purchase_points_per_categ" id="mwb_wpr_purchase_points_cat<?php echo esc_html( $catid ); ?>" value="<?php echo esc_html( $mwb_wpr_purchase_categ_point ); ?>" class="input-text mwb_wpr_new_woo_ver_style_text"></td>
							<td><input type="button" value='<?php esc_html_e( 'Submit', 'ultimate-woocommerce-points-and-rewards' ); ?>' class="button-primary woocommerce-save-button mwb_wpr_submit_purchase_points_per_category" name="mwb_wpr_submit_purchase_points_per_category" id="<?php echo esc_html( $catid ); ?>"></td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
