<?php
/**
 * Home v6 Metabox
 *
 * Displays the home v6 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Home_v6 Class.
 */
class Pizzaro_Meta_Box_Home_v6 {

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

		if ( $template_file !== 'template-homepage-v6.php' ) {
			return;
		}

		self::output_home_v6( $post );
	}

	private static function output_home_v6( $post ) {

		$home_v6 = pizzaro_get_home_v6_meta();

		?>
		<div class="panel-wrap meta-box-home">
			<ul class="home_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_home_v6_data_tabs', array(
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
					'banners_block' => array(
						'label'  => esc_html__( 'Banners', 'pizzaro' ),
						'target' => 'banners_block',
						'class'  => array(),
					),
					'products_card1' => array(
						'label'  => esc_html__( 'Products Card 1', 'pizzaro' ),
						'target' => 'products_card1',
						'class'  => array(),
					),
					'products_card2' => array(
						'label'  => esc_html__( 'Products Card 2', 'pizzaro' ),
						'target' => 'products_card2',
						'class'  => array(),
					),
					'products_card3' => array(
						'label'  => esc_html__( 'Products Card 3', 'pizzaro' ),
						'target' => 'products_card3',
						'class'  => array(),
					),
					'products_card4' => array(
						'label'  => esc_html__( 'Products Card 4', 'pizzaro' ),
						'target' => 'products_card4',
						'class'  => array(),
					),
					'products_card5' => array(
						'label'  => esc_html__( 'Products Card 5', 'pizzaro' ),
						'target' => 'products_card5',
						'class'  => array(),
					),
					'products_card6' => array(
						'label'  => esc_html__( 'Products Card 6', 'pizzaro' ),
						'target' => 'products_card6',
						'class'  => array(),
					),
					'banner_block' => array(
						'label'  => esc_html__( 'Banner', 'pizzaro' ),
						'target' => 'banner_block',
						'class'  => array(),
					),
					'products_tabs' => array(
						'label'  => esc_html__( 'Products Tabs', 'pizzaro' ),
						'target' => 'products_tabs',
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
							'id'		=> '_home_v6_header_style',
							'label'		=> esc_html__( 'Header Style', 'pizzaro' ),
							'name'		=> '_home_v6[header_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Header', 'pizzaro' ),
								'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v6['header_style'] ) ? $home_v6['header_style'] : '',
						) );

						pizzaro_wp_select( array(
							'id'		=> '_home_v6_footer_style',
							'label'		=> esc_html__( 'Footer Style', 'pizzaro' ),
							'name'		=> '_home_v6[footer_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Footer', 'pizzaro' ),
								'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v6['footer_style'] ) ? $home_v6['footer_style'] : '',
						) );
					?>
				</div>
				<div class="options_group">
					<?php 
						$home_v6_blocks = array(
							'sdr'	=> esc_html__( 'Slider', 'pizzaro' ),
							'brs'	=> esc_html__( 'Banners', 'pizzaro' ),
							'pc1'	=> esc_html__( 'Products Card 1', 'pizzaro' ),
							'pc2'	=> esc_html__( 'Products Card 2', 'pizzaro' ),
							'pc3'	=> esc_html__( 'Products Card 3', 'pizzaro' ),
							'pc4'	=> esc_html__( 'Products Card 4', 'pizzaro' ),
							'pc5'	=> esc_html__( 'Products Card 5', 'pizzaro' ),
							'pc6'	=> esc_html__( 'Products Card 6', 'pizzaro' ),
							'br'	=> esc_html__( 'Banner', 'pizzaro' ),
							'pt'	=> esc_html__( 'Products Tabs', 'pizzaro' ),
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
							<?php foreach( $home_v6_blocks as $key => $home_v6_block ) : ?>
							<tr>
								<td><?php echo esc_html( $home_v6_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_home_v6_' . $key . '_animation', 'label'=> '', 'name' => '_home_v6[' . $key . '][animation]', 'value' => isset( $home_v6['' . $key . '']['animation'] ) ? $home_v6['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_home_v6_' . $key . '_priority', 'label'=> '', 'name' => '_home_v6[' . $key . '][priority]', 'value' => isset( $home_v6['' . $key . '']['priority'] ) ? $home_v6['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_home_v6_' . $key . '_is_enabled', 'label' => '', 'name' => '_home_v6[' . $key . '][is_enabled]', 'value'=> isset( $home_v6['' . $key . '']['is_enabled'] ) ? $home_v6['' . $key . '']['is_enabled'] : '', ) ); ?></td>
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
						'id' 			=> '_home_v6_sdr_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shorcode for your slider here', 'pizzaro' ),
						'name'			=> '_home_v6[sdr][shortcode]',
						'value'			=> isset( $home_v6['sdr']['shortcode'] ) ? $home_v6['sdr']['shortcode'] : '',
					) );
				?>
				</div>
			</div><!-- /#slider_block -->

			<div id="banners_block" class="panel pizzaro_options_panel">
				
				<?php pizzaro_wp_legend( esc_html__( 'Banner 1', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][0][pre_title]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['pre_title'] ) ? $home_v6['brs']['banners'][0]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][0][title]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['title'] ) ? $home_v6['brs']['banners'][0]['title'] : esc_html__( 'iCED COFFEE', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][0][sub_title]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['sub_title'] ) ? $home_v6['brs']['banners'][0]['sub_title'] : esc_html__( 'SUMMERS', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v6[brs][banners][0][description]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['description'] ) ? $home_v6['brs']['banners'][0]['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][0][action_text]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['action_text'] ) ? $home_v6['brs']['banners'][0]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][0][action_link]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['action_link'] ) ? $home_v6['brs']['banners'][0]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][0][condition]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['condition'] ) ? $home_v6['brs']['banners'][0]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v6_brs_banners_0_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v6[brs][banners][0][bg_choice]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['bg_choice'] ) ? $home_v6['brs']['banners'][0]['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_brs_banners_0_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v6[brs][banners][0][bg_image]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['bg_image'] ) ? $home_v6['brs']['banners'][0]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[brs][banners][0][bg_color]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['bg_color'] ) ? $home_v6['brs']['banners'][0]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_0_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[brs][banners][0][height]',
						'value'			=> isset( $home_v6['brs']['banners'][0]['height'] ) ? $home_v6['brs']['banners'][0]['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Banner 2', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][1][pre_title]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['pre_title'] ) ? $home_v6['brs']['banners'][1]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][1][title]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['title'] ) ? $home_v6['brs']['banners'][1]['title'] : esc_html__( 'CHICKEN WRAP', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][1][sub_title]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['sub_title'] ) ? $home_v6['brs']['banners'][1]['sub_title'] : esc_html__( 'SPECIALS', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v6[brs][banners][1][description]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['description'] ) ? $home_v6['brs']['banners'][1]['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][1][action_text]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['action_text'] ) ? $home_v6['brs']['banners'][1]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][1][action_link]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['action_link'] ) ? $home_v6['brs']['banners'][1]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][1][condition]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['condition'] ) ? $home_v6['brs']['banners'][1]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v6_brs_banners_1_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v6[brs][banners][1][bg_choice]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['bg_choice'] ) ? $home_v6['brs']['banners'][1]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_brs_banners_1_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v6[brs][banners][1][bg_image]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['bg_image'] ) ? $home_v6['brs']['banners'][1]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[brs][banners][1][bg_color]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['bg_color'] ) ? $home_v6['brs']['banners'][1]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_1_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[brs][banners][1][height]',
						'value'			=> isset( $home_v6['brs']['banners'][1]['height'] ) ? $home_v6['brs']['banners'][1]['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Banner 3', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][2][pre_title]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['pre_title'] ) ? $home_v6['brs']['banners'][2]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][2][title]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['title'] ) ? $home_v6['brs']['banners'][2]['title'] : wp_kses_post( '<span>' . __( 'ORDER', 'pizzaro' ) . '</span>' . __( ' ONLINE', 'pizzaro' ) ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][2][sub_title]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['sub_title'] ) ? $home_v6['brs']['banners'][2]['sub_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v6[brs][banners][2][description]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['description'] ) ? $home_v6['brs']['banners'][2]['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][2][action_text]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['action_text'] ) ? $home_v6['brs']['banners'][2]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][2][action_link]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['action_link'] ) ? $home_v6['brs']['banners'][2]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v6[brs][banners][2][condition]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['condition'] ) ? $home_v6['brs']['banners'][2]['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v6_brs_banners_2_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v6[brs][banners][2][bg_choice]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['bg_choice'] ) ? $home_v6['brs']['banners'][2]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_brs_banners_2_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v6[brs][banners][2][bg_image]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['bg_image'] ) ? $home_v6['brs']['banners'][2]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[brs][banners][2][bg_color]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['bg_color'] ) ? $home_v6['brs']['banners'][2]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_brs_banners_2_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[brs][banners][2][height]',
						'value'			=> isset( $home_v6['brs']['banners'][2]['height'] ) ? $home_v6['brs']['banners'][2]['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#banners_block -->

			<div id="products_card1" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pc1_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v6[pc1][section_title]',
						'value'			=> isset( $home_v6['pc1']['section_title'] ) ? $home_v6['pc1']['section_title'] : '',
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pc1_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pc1][content]',
						'value'			=> isset( $home_v6['pc1']['content'] ) ? $home_v6['pc1']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_pc1_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v6[pc1][image]',
						'value'			=> isset( $home_v6['pc1']['image'] ) ? $home_v6['pc1']['image'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_card1 -->

			<div id="products_card2" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pc2_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v6[pc2][section_title]',
						'value'			=> isset( $home_v6['pc2']['section_title'] ) ? $home_v6['pc2']['section_title'] : '',
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pc2_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pc2][content]',
						'value'			=> isset( $home_v6['pc2']['content'] ) ? $home_v6['pc2']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_pc2_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v6[pc2][image]',
						'value'			=> isset( $home_v6['pc2']['image'] ) ? $home_v6['pc2']['image'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_card2 -->

			<div id="products_card3" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pc3_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v6[pc3][section_title]',
						'value'			=> isset( $home_v6['pc3']['section_title'] ) ? $home_v6['pc3']['section_title'] : '',
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pc3_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pc3][content]',
						'value'			=> isset( $home_v6['pc3']['content'] ) ? $home_v6['pc3']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_pc3_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v6[pc3][image]',
						'value'			=> isset( $home_v6['pc3']['image'] ) ? $home_v6['pc3']['image'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_card3 -->

			<div id="products_card4" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pc4_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v6[pc4][section_title]',
						'value'			=> isset( $home_v6['pc4']['section_title'] ) ? $home_v6['pc4']['section_title'] : '',
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pc4_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pc4][content]',
						'value'			=> isset( $home_v6['pc4']['content'] ) ? $home_v6['pc4']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_pc4_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v6[pc4][image]',
						'value'			=> isset( $home_v6['pc4']['image'] ) ? $home_v6['pc4']['image'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_card4 -->

			<div id="products_card5" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pc5_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v6[pc5][section_title]',
						'value'			=> isset( $home_v6['pc5']['section_title'] ) ? $home_v6['pc5']['section_title'] : '',
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pc5_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pc5][content]',
						'value'			=> isset( $home_v6['pc5']['content'] ) ? $home_v6['pc5']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_pc5_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v6[pc5][image]',
						'value'			=> isset( $home_v6['pc5']['image'] ) ? $home_v6['pc5']['image'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_card5 -->

			<div id="products_card6" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pc6_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v6[pc6][section_title]',
						'value'			=> isset( $home_v6['pc6']['section_title'] ) ? $home_v6['pc6']['section_title'] : '',
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pc6_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pc6][content]',
						'value'			=> isset( $home_v6['pc6']['content'] ) ? $home_v6['pc6']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_pc6_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v6[pc6][image]',
						'value'			=> isset( $home_v6['pc6']['image'] ) ? $home_v6['pc6']['image'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_card6 -->

			<div id="banner_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_br_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v6[br][pre_title]',
						'value'			=> isset( $home_v6['br']['pre_title'] ) ? $home_v6['br']['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_br_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v6[br][title]',
						'value'			=> isset( $home_v6['br']['title'] ) ? $home_v6['br']['title'] : esc_html__( 'FREE DELIVERY IN YOUR CITY', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_br_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v6[br][sub_title]',
						'value'			=> isset( $home_v6['br']['sub_title'] ) ? $home_v6['br']['sub_title'] : esc_html__( 'ON ORDERS FOR OVER 68$', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v6_br_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v6[br][description]',
						'value'			=> isset( $home_v6['br']['description'] ) ? $home_v6['br']['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_br_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v6[br][action_text]',
						'value'			=> isset( $home_v6['br']['action_text'] ) ? $home_v6['br']['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_br_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v6[br][action_link]',
						'value'			=> isset( $home_v6['br']['action_link'] ) ? $home_v6['br']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_br_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v6[br][condition]',
						'value'			=> isset( $home_v6['br']['condition'] ) ? $home_v6['br']['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v6_br_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v6[br][bg_choice]',
						'value'			=> isset( $home_v6['br']['bg_choice'] ) ? $home_v6['br']['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v6_br_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v6[br][bg_image]',
						'value'			=> isset( $home_v6['br']['bg_image'] ) ? $home_v6['br']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_br_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[br][bg_color]',
						'value'			=> isset( $home_v6['br']['bg_color'] ) ? $home_v6['br']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v6_br_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v6[br][height]',
						'value'			=> isset( $home_v6['br']['height'] ) ? $home_v6['br']['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#banner_block -->

			<div id="products_tabs" class="panel pizzaro_options_panel">
				
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pt_product_limit', 
						'label' 		=>  esc_html__( 'Products Limit', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Enter the number of products to show', 'pizzaro' ),
						'name'			=> '_home_v6[pt][product_limit]',
						'class'			=> 'product_limit',
						'size'			=> 2,
						'value'			=> isset( $home_v6['pt']['product_limit'] ) ? $home_v6['pt']['product_limit'] : 8,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v6_pt_product_columns', 
						'label' 		=>  esc_html__( 'Columns', 'pizzaro' ),
						'options'		=> array(
							'2'	=> '2',
							'3'	=> '3',
							'4'	=> '4',
						),
						'class'			=> 'columns_select',
						'default'		=> '4',
						'name'			=> '_home_v6[pt][product_columns]',
						'value'			=> isset( $home_v6['pt']['product_columns'] ) ? $home_v6['pt']['product_columns'] : 4,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v6_pt_active_tab', 
						'label' 		=>  esc_html__( 'Active Tab', 'pizzaro' ),
						'options'		=> array(
							'1'	=> '1',
							'2'	=> '2',
							'3'	=> '3',
						),
						'class'			=> 'columns_select',
						'default'		=> '2',
						'name'			=> '_home_v6[pt][default_active_tab]',
						'value'			=> isset( $home_v6['pt']['default_active_tab'] ) ? $home_v6['pt']['default_active_tab'] : '2',
					) );
				?>
				</div>

				<div class="options_group">
				<?php	
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pt_tab_1_title', 
						'label' 		=> esc_html__( 'Tab #1 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Wraps', 'pizzaro' ),
						'name'			=> '_home_v6[pt][tabs][0][title]',
						'value'			=> isset( $home_v6['pt']['tabs'][0]['title'] ) ? $home_v6['pt']['tabs'][0]['title'] : esc_html__( 'Wraps', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pt_tab_1_content',
						'label'			=> esc_html__( 'Tab #1 Content', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v6[pt][tabs][0][content]',
						'value'			=> isset( $home_v6['pt']['tabs'][0]['content'] ) ? $home_v6['pt']['tabs'][0]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pt_tab_2_title', 
						'label' 		=> esc_html__( 'Tab #2 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Pizza Sets', 'pizzaro' ),
						'name'			=> '_home_v6[pt][tabs][1][title]',
						'value'			=> isset( $home_v6['pt']['tabs'][1]['title'] ) ? $home_v6['pt']['tabs'][1]['title'] : esc_html__( 'Pizza Sets', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pt_tab_2_content',
						'label'			=> esc_html__( 'Tab #2 Content', 'pizzaro' ),
						'default'		=> 'best_selling_products',
						'name'			=> '_home_v6[pt][tabs][1][content]',
						'value'			=> isset( $home_v6['pt']['tabs'][1]['content'] ) ? $home_v6['pt']['tabs'][1]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v6_pt_tab_3_title', 
						'label' 		=> esc_html__( 'Tab #3 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Burgers', 'pizzaro' ),
						'name'			=> '_home_v6[pt][tabs][2][title]',
						'value'			=> isset( $home_v6['pt']['tabs'][2]['title'] ) ? $home_v6['pt']['tabs'][2]['title'] : esc_html__( 'Burgers', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v6_pt_tab_3_content',
						'label'			=> esc_html__( 'Tab #3 Content', 'pizzaro' ),
						'default'		=> 'sale_products',
						'name'			=> '_home_v6[pt][tabs][2][content]',
						'value'			=> isset( $home_v6['pt']['tabs'][2]['content'] ) ? $home_v6['pt']['tabs'][2]['content'] : '',
					) );
				?>
				</div>

			</div><!-- /#products_tabs -->
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_home_v6'] ) ) {
			$clean_home_v6_options = pizzaro_clean_kses_post( $_POST['_home_v6'] );
			update_post_meta( $post_id, '_home_v6_options',  serialize( $clean_home_v6_options ) );
		}	
	}
}