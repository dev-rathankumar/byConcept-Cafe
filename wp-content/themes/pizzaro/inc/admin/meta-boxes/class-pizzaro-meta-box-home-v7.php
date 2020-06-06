<?php
/**
 * Home v7 Metabox
 *
 * Displays the home v7 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Home_v7 Class.
 */
class Pizzaro_Meta_Box_Home_v7 {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		global $post, $thepostid;

		wp_nonce_field( 'pizzaro_save_data', 'pizzaro_meta_nonce' );

		$thepostid 		= $post->ID;
		$template_file 	= get_post_meta( $thepostid, '_wp_page_template', true );

		if ( $template_file !== 'template-homepage-v7.php' ) {
			return;
		}

		self::output_home_v7( $post );
	}

	private static function output_home_v7( $post ) {

		$home_v7 = pizzaro_get_home_v7_meta();

		?>
		<div class="panel-wrap meta-box-home">
			<ul class="home_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_home_v7_data_tabs', array(
					'general' => array(
						'label'  => esc_html__( 'General', 'pizzaro' ),
						'target' => 'general_block',
						'class'  => array(),
					),
					'slider' => array(
						'label'  => esc_html__( 'Slider', 'pizzaro' ),
						'target' => 'slider_block',
						'class'  => array(),
					),
					'coupon_block' => array(
						'label'  => esc_html__( 'Coupon', 'pizzaro' ),
						'target' => 'coupon_block',
						'class'  => array(),
					),
					'sale_product' => array(
						'label'  => esc_html__( 'Sale Product', 'pizzaro' ),
						'target' => 'sale_product',
						'class'  => array(),
					),
					'banner_post' => array(
						'label'  => esc_html__( 'Banner with Post', 'pizzaro' ),
						'target' => 'banner_post',
						'class'  => array(),
					),
					'store_search' => array(
						'label'  => esc_html__( 'Store Locator', 'pizzaro' ),
						'target' => 'store_search',
						'class'  => array(),
					),
					'posts_block' => array(
						'label'  => esc_html__( 'Posts Block', 'pizzaro' ),
						'target' => 'posts_block',
						'class'  => array(),
					),
					'newsletter_block' => array(
						'label'  => esc_html__( 'Newsletter Block', 'pizzaro' ),
						'target' => 'newsletter_block',
						'class'  => array(),
					)
				) );
				foreach ( $product_data_tabs as $key => $tab ) {
					?><li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
						<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
					</li><?php
				}
				do_action( 'pizzaro_home_write_panel_tabs' );
			?>
			</ul>
			<div id="general_block" class="panel pizzaro_options_panel">
				<div class="options_group">
					<?php 
						pizzaro_wp_select( array(
							'id'		=> '_home_v7_header_style',
							'label'		=> esc_html__( 'Header Style', 'pizzaro' ),
							'name'		=> '_home_v7[header_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Header', 'pizzaro' ),
								'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v7['header_style'] ) ? $home_v7['header_style'] : '',
						) );

						pizzaro_wp_select( array(
							'id'		=> '_home_v7_footer_style',
							'label'		=> esc_html__( 'Footer Style', 'pizzaro' ),
							'name'		=> '_home_v7[footer_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Footer', 'pizzaro' ),
								'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v7['footer_style'] ) ? $home_v7['footer_style'] : '',
						) );
					?>
				</div>
				<div class="options_group">
					<?php 
						$home_v7_blocks = array(
							'sdr'	=> esc_html__( 'Slider', 'pizzaro' ),
							'cn'	=> esc_html__( 'Coupon', 'pizzaro' ),
							'sa'	=> esc_html__( 'Sale Product', 'pizzaro' ),
							'brwp'	=> esc_html__( 'Banner with Post', 'pizzaro' ),
							'ss'	=> esc_html__( 'Store Locator', 'pizzaro' ),
							'rp'	=> esc_html__( 'Posts Block', 'pizzaro' ),
							'nl'	=> esc_html__( 'Newsletter Block', 'pizzaro' ),
						);
					?>
					<table class="general-blocks-table widefat striped">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Block', 'pizzaro' ); ?></th>
								<th><?php echo esc_html__( 'Animation', 'pizzaro' ); ?></th>
								<th><?php echo esc_html__( 'Priority', 'pizzaro' ); ?></th>
								<th><?php echo esc_html__( 'Enabled ?', 'pizzaro' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $home_v7_blocks as $key => $home_v7_block ) : ?>
							<tr>
								<td><?php echo esc_html( $home_v7_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_home_v7_' . $key . '_animation', 'label'=> '', 'name' => '_home_v7[' . $key . '][animation]', 'value' => isset( $home_v7['' . $key . '']['animation'] ) ? $home_v7['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_home_v7_' . $key . '_priority', 'label'=> '', 'name' => '_home_v7[' . $key . '][priority]', 'value' => isset( $home_v7['' . $key . '']['priority'] ) ? $home_v7['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_home_v7_' . $key . '_is_enabled', 'label' => '', 'name' => '_home_v7[' . $key . '][is_enabled]', 'value'=> isset( $home_v7['' . $key . '']['is_enabled'] ) ? $home_v7['' . $key . '']['is_enabled'] : '', ) ); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div><!-- /#general_block -->
			
			<div id="slider_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_sdr_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shorcode for your slider here', 'pizzaro' ),
						'name'			=> '_home_v7[sdr][shortcode]',
						'value'			=> isset( $home_v7['sdr']['shortcode'] ) ? $home_v7['sdr']['shortcode'] : '',
					) );
				?>
				</div>
			</div><!-- /#slider_block -->

			<div id="coupon_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_cn_coupon_code', 
						'label' 		=> esc_html__( 'Coupon Code', 'pizzaro' ),
						'name'			=> '_home_v7[cn][coupon_code]',
						'value'			=> isset( $home_v7['cn']['coupon_code'] ) ? $home_v7['cn']['coupon_code'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_cn_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v7[cn][pre_title]',
						'value'			=> isset( $home_v7['cn']['pre_title'] ) ? $home_v7['cn']['pre_title'] : esc_html__( 'CRUST PIZZA', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_cn_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v7[cn][title]',
						'value'			=> isset( $home_v7['cn']['title'] ) ? $home_v7['cn']['title'] : esc_html__( 'BIG MEAL DEAL WITH PIZZA AND ICED COLA CUP', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_cn_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v7[cn][sub_title]',
						'value'			=> isset( $home_v7['cn']['sub_title'] ) ? $home_v7['cn']['sub_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_cn_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v7[cn][description]',
						'value'			=> isset( $home_v7['cn']['description'] ) ? $home_v7['cn']['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_cn_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v7[cn][action_text]',
						'value'			=> isset( $home_v7['cn']['action_text'] ) ? $home_v7['cn']['action_text'] : esc_html__( 'CLICK TO USE THE COUPON', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_cn_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v7[cn][action_link]',
						'value'			=> isset( $home_v7['cn']['action_link'] ) ? $home_v7['cn']['action_link'] : '#',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_cn_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[cn][bg_choice]',
						'value'			=> isset( $home_v7['cn']['bg_choice'] ) ? $home_v7['cn']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v7_cn_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v7[cn][bg_image]',
						'value'			=> isset( $home_v7['cn']['bg_image'] ) ? $home_v7['cn']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_cn_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[cn][bg_color]',
						'value'			=> isset( $home_v7['cn']['bg_color'] ) ? $home_v7['cn']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_cn_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[cn][height]',
						'value'			=> isset( $home_v7['cn']['height'] ) ? $home_v7['cn']['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#coupon_block -->
			
			<div id="sale_product" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_sa_section_title', 
						'label' 		=> esc_html__( 'Section Title', 'pizzaro' ), 
						'name'			=> '_home_v7[sa][section_title]',
						'value'			=> isset( $home_v7['sa']['section_title'] ) ? $home_v7['sa']['section_title'] : wp_kses_post( __( 'GRAND ', 'pizzaro' ) . '<span>' . __( 'ITALIANO', 'pizzaro' ) . '</span>' ),
						'placeholder'	=> esc_html__( 'Enter title or leave blank to display product title.', 'pizzaro' )
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_sa_button_text',
						'label'			=> esc_html__( 'Button Text', 'pizzaro' ),
						'name'			=> '_home_v7[sa][button_text]',
						'value'			=> isset( $home_v7['sa']['button_text'] ) ? $home_v7['sa']['button_text'] : esc_html__( 'Check the Deal', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_sa_product_id',
						'label'			=> esc_html__( 'Product ID', 'pizzaro' ),
						'name'			=> '_home_v7[sa][product_id]',
						'value'			=> isset( $home_v7['sa']['product_id'] ) ? $home_v7['sa']['product_id'] : '',
						'placeholder'	=> esc_html__( 'Enter product id or leave blank.', 'pizzaro' )
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v7_sa_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v7[sa][bg_image]',
						'value'			=> isset( $home_v7['sa']['bg_image'] ) ? $home_v7['sa']['bg_image'] : '',
					) );

				?>
				</div>
			</div><!-- /#sale_product -->

			<div id="banner_post" class="panel pizzaro_options_panel">
				
				<?php pizzaro_wp_legend( esc_html__( 'Banner', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_brwp_banner_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v7[brwp][banner][pre_title]',
						'value'			=> isset( $home_v7['brwp']['banner']['pre_title'] ) ? $home_v7['brwp']['banner']['pre_title'] : esc_html__( 'JOIN TO OUR', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_brwp_banner_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v7[brwp][banner][title]',
						'value'			=> isset( $home_v7['brwp']['banner']['title'] ) ? $home_v7['brwp']['banner']['title'] : esc_html__( 'RAVING FANS', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_brwp_banner_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v7[brwp][banner][sub_title]',
						'value'			=> isset( $home_v7['brwp']['banner']['sub_title'] ) ? $home_v7['brwp']['banner']['sub_title'] : esc_html__( '& GET FREE FOOD AND OTHER INSIDER-ONLY TREATS TO YOUR INBOX', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v7_brwp_banner_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v7[brwp][banner][description]',
						'value'			=> isset( $home_v7['brwp']['banner']['description'] ) ? $home_v7['brwp']['banner']['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_banner_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v7[brwp][banner][action_text]',
						'value'			=> isset( $home_v7['brwp']['banner']['action_text'] ) ? $home_v7['brwp']['banner']['action_text'] : esc_html__( 'BECOME FACEBOOK FAN', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_banner_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v7[brwp][banner][action_link]',
						'value'			=> isset( $home_v7['brwp']['banner']['action_link'] ) ? $home_v7['brwp']['banner']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_banner_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v7[brwp][banner][condition]',
						'value'			=> isset( $home_v7['brwp']['banner']['condition'] ) ? $home_v7['brwp']['banner']['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_brwp_banner_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[brwp][banner][bg_choice]',
						'value'			=> isset( $home_v7['brwp']['banner']['bg_choice'] ) ? $home_v7['brwp']['banner']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v7_brwp_banner_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v7[brwp][banner][bg_image]',
						'value'			=> isset( $home_v7['brwp']['banner']['bg_image'] ) ? $home_v7['brwp']['banner']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_banner_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[brwp][banner][bg_color]',
						'value'			=> isset( $home_v7['brwp']['banner']['bg_color'] ) ? $home_v7['brwp']['banner']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_banner_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[brwp][banner][height]',
						'value'			=> isset( $home_v7['brwp']['banner']['height'] ) ? $home_v7['brwp']['banner']['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Post', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_brwp_post_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v7[brwp][post][section_title]',
						'value'			=> isset( $home_v7['brwp']['post']['section_title'] ) ? $home_v7['brwp']['post']['section_title'] : esc_html__( 'Summer Taste', 'pizzaro' ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_brwp_post_post_choice',
						'label'			=> esc_html__( 'Post Choice', 'pizzaro' ),
						'options'		=> array(
							'random'	=> esc_html__( 'Random Posts', 'pizzaro' ),
							'recent'	=> esc_html__( 'Most Recent Posts', 'pizzaro' ),
							'specific'	=> esc_html__( 'Specify by ID', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[brwp][post][post_choice]',
						'value'			=> isset( $home_v7['brwp']['post']['post_choice'] ) ? $home_v7['brwp']['post']['post_choice'] : 'random',
					) );
					
					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_brwp_post_post_id', 
						'label' 		=>  esc_html__( 'Post ID', 'pizzaro' ),
						'name'			=> '_home_v7[brwp][post][post_id]',
						'wrapper_class'	=> 'show_if_specific hide',
						'value'			=> isset( $home_v7['brwp']['post']['post_id'] ) ? $home_v7['brwp']['post']['post_id'] : '',
						'placeholder'	=> esc_html__( 'Enter post id', 'pizzaro' )
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_brwp_post_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[brwp][post][bg_choice]',
						'value'			=> isset( $home_v7['brwp']['post']['bg_choice'] ) ? $home_v7['brwp']['post']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v7_brwp_post_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v7[brwp][post][bg_image]',
						'value'			=> isset( $home_v7['brwp']['post']['bg_image'] ) ? $home_v7['brwp']['post']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_post_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[brwp][post][bg_color]',
						'value'			=> isset( $home_v7['brwp']['post']['bg_color'] ) ? $home_v7['brwp']['post']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_brwp_post_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[brwp][post][height]',
						'value'			=> isset( $home_v7['brwp']['post']['height'] ) ? $home_v7['brwp']['post']['height'] : '',
					) );

				?>
				</div>

			</div><!-- /#banner_post -->

			<div id="store_search" class="panel pizzaro_options_panel">
				
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id'			=> '_home_v7_ss_title',
						'label'			=> esc_html__( 'Title', 'pizzaro' ),
						'name'			=> '_home_v7[ss][title]',
						'value'			=> isset( $home_v7['ss']['title'] ) ? $home_v7['ss']['title'] : esc_html__( 'FIND A', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id'			=> '_home_v7_ss_sub_title',
						'label'			=> esc_html__( 'Sub Title', 'pizzaro' ),
						'name'			=> '_home_v7[ss][sub_title]',
						'value'			=> isset( $home_v7['ss']['sub_title'] ) ? $home_v7['ss']['sub_title'] : wp_kses_post( __( 'PIZZARO RESTAURANT', 'pizzaro' ) . '<br/>' . __( 'NEAR YOU', 'pizzaro' ) ),
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_ss_icon_class',
						'label'			=> esc_html__( 'Icon Class', 'pizzaro' ),
						'name'			=> '_home_v7[ss][icon_class]',
						'value'			=> isset( $home_v7['ss']['icon_class'] ) ? $home_v7['ss']['icon_class'] : 'po po-marker-hand-drawned',
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_ss_button_text',
						'label'			=> esc_html__( 'Button Text', 'pizzaro' ),
						'name'			=> '_home_v7[ss][button_text]',
						'value'			=> isset( $home_v7['ss']['button_text'] ) ? $home_v7['ss']['button_text'] : esc_html__( 'See on map', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_ss_page_id', 
						'label' 		=>  esc_html__( 'Store Locator Page ID', 'pizzaro' ),
						'name'			=> '_home_v7[ss][page_id]',
						'value'			=> isset( $home_v7['ss']['page_id'] ) ? $home_v7['ss']['page_id'] : '',
						'placeholder'	=> esc_html__( 'Enter store locator page id where you added [wpsl] shortcode', 'pizzaro' )
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_ss_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[ss][bg_choice]',
						'value'			=> isset( $home_v7['ss']['bg_choice'] ) ? $home_v7['ss']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v7_ss_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v7[ss][bg_image]',
						'value'			=> isset( $home_v7['ss']['bg_image'] ) ? $home_v7['ss']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_ss_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[ss][bg_color]',
						'value'			=> isset( $home_v7['ss']['bg_color'] ) ? $home_v7['ss']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v7_ss_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[ss][height]',
						'value'			=> isset( $home_v7['ss']['height'] ) ? $home_v7['ss']['height'] : '',
					) );

				?>
				</div>

			</div><!-- /#store_search -->

			<div id="posts_block" class="panel pizzaro_options_panel">
				
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_rp_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v7[rp][section_title]',
						'value'			=> isset( $home_v7['rp']['section_title'] ) ? $home_v7['rp']['section_title'] : esc_html__( 'Read Our Blog', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v7_rp_pre_title',
						'label'			=> esc_html__( 'Pre Title', 'pizzaro' ),
						'name'			=> '_home_v7[rp][pre_title]',
						'value'			=> isset( $home_v7['rp']['pre_title'] ) ? $home_v7['rp']['pre_title'] : esc_html__( 'Our Latest Posts', 'pizzaro' ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_rp_post_choice',
						'label'			=> esc_html__( 'Post Choice', 'pizzaro' ),
						'options'		=> array(
							'random'	=> esc_html__( 'Random Posts', 'pizzaro' ),
							'recent'	=> esc_html__( 'Most Recent Posts', 'pizzaro' ),
							'specific'	=> esc_html__( 'Specify by ID', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[rp][post_choice]',
						'value'			=> isset( $home_v7['rp']['post_choice'] ) ? $home_v7['rp']['post_choice'] : 'random',
					) );
					
					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_rp_post_id', 
						'label' 		=>  esc_html__( 'Post ID', 'pizzaro' ),
						'name'			=> '_home_v7[rp][post_id]',
						'wrapper_class'	=> 'show_if_specific hide',
						'value'			=> isset( $home_v7['rp']['post_id'] ) ? $home_v7['rp']['post_id'] : '',
						'placeholder'	=> esc_html__( 'Enter post ids separated by comma', 'pizzaro' )
					) );

				?>
				</div>

			</div><!-- /#posts_block -->

			<div id="newsletter_block" class="panel pizzaro_options_panel">

				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_nl_title',
						'label'			=> esc_html__( 'Title', 'pizzaro' ),
						'name'			=> '_home_v7[nl][title]',
						'default'		=> esc_html__( 'Sign up for Newsletter', 'pizzaro' ),
						'value'			=> isset( $home_v7['nl']['title'] ) ? $home_v7['nl']['title'] : esc_html__( 'Subscribe to Newsletter', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_nl_marketing_text',
						'label'			=> esc_html__( 'Marketing Text', 'pizzaro' ),
						'name'			=> '_home_v7[nl][marketing_text]',
						'default'		=> esc_html__( 'Stay up to date', 'pizzaro' ),
						'value'			=> isset( $home_v7['nl']['marketing_text'] ) ? $home_v7['nl']['marketing_text'] : esc_html__( 'Subscribe to receive our weekly Hot Promotions every Monday!', 'pizzaro' ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v7_nl_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v7[ti][tiles][1][1][args][bg_choice]',
						'value'			=> isset( $home_v7['nl']['bg_choice'] ) ? $home_v7['nl']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v7_nl_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v7[nl][bg_image]',
						'wrapper_class'	=> 'show_if_image hide',
						'value'			=> isset( $home_v7['nl']['bg_image'] ) ? $home_v7['nl']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_nl_bg_color',
						'label'			=> esc_html__( 'Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[nl][bg_color]',
						'value'			=> isset( $home_v7['nl']['bg_color'] ) ? $home_v7['nl']['bg_color'] : '#e5e2db',
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v7_nl_height',
						'label'			=> esc_html__( 'Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v7[nl][height]',
						'value'			=> isset( $home_v7['nl']['height'] ) ? $home_v7['nl']['height'] : 460,
					) );
					
				?>
				</div>

			</div><!-- /#newsletter_block -->

		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_home_v7'] ) ) {
			$clean_home_v7_options = pizzaro_clean_kses_post( $_POST['_home_v7'] );
			update_post_meta( $post_id, '_home_v7_options',  serialize( $clean_home_v7_options ) );
		}	
	}
}