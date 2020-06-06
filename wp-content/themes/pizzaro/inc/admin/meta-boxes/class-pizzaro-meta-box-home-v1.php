<?php
/**
 * Home v1 Metabox
 *
 * Displays the home v1 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Home_v1 Class.
 */
class Pizzaro_Meta_Box_Home_v1 {

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

		if ( $template_file !== 'template-homepage-v1.php' ) {
			return;
		}

		self::output_home_v1( $post );
	}

	private static function output_home_v1( $post ) {

		$home_v1 = pizzaro_get_home_v1_meta();

		?>
		<div class="panel-wrap meta-box-home">
			<ul class="home_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_home_v1_data_tabs', array(
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
					'tiles_block' => array(
						'label'  => esc_html__( 'Tiles Block', 'pizzaro' ),
						'target' => 'tiles_block',
						'class'  => array(),
					),
					'products_tabs' => array(
						'label'  => esc_html__( 'Products Tabs', 'pizzaro' ),
						'target' => 'products_tabs',
						'class'  => array(),
					),
					'products_sale_event' => array(
						'label'  => esc_html__( 'Products Sale Event', 'pizzaro' ),
						'target' => 'products_sale_event',
						'class'  => array(),
					),
					'products_block' => array(
						'label'  => esc_html__( 'Products Block', 'pizzaro' ),
						'target' => 'products_block',
						'class'  => array(),
					),
					'product_block' => array(
						'label'  => esc_html__( 'Product Block', 'pizzaro' ),
						'target' => 'product_block',
						'class'  => array(),
					),
					'features_list' => array(
						'label'  => esc_html__( 'Features List', 'pizzaro' ),
						'target' => 'features_list',
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
							'id'		=> '_home_v1_header_style',
							'label'		=> esc_html__( 'Header Style', 'pizzaro' ),
							'name'		=> '_home_v1[header_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Header', 'pizzaro' ),
								'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v1['header_style'] ) ? $home_v1['header_style'] : '',
						) );

						pizzaro_wp_select( array(
							'id'		=> '_home_v1_footer_style',
							'label'		=> esc_html__( 'Footer Style', 'pizzaro' ),
							'name'		=> '_home_v1[footer_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Footer', 'pizzaro' ),
								'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v1['footer_style'] ) ? $home_v1['footer_style'] : '',
						) );
					?>
				</div>
				<div class="options_group">
					<?php 
						$home_v1_blocks = array(
							'sdr'	=> esc_html__( 'Slider', 'pizzaro' ),
							'ti'	=> esc_html__( 'Tiles Block', 'pizzaro' ),
							'pt'	=> esc_html__( 'Products Tabs', 'pizzaro' ),
							'spe'	=> esc_html__( 'Products Sale Event', 'pizzaro' ),
							'pl'	=> esc_html__( 'Products Block', 'pizzaro' ),
							'sp'	=> esc_html__( 'Product Block', 'pizzaro' ),
							'fl'	=> esc_html__( 'Features List', 'pizzaro' ),
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
							<?php foreach( $home_v1_blocks as $key => $home_v1_block ) : ?>
							<tr>
								<td><?php echo esc_html( $home_v1_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_home_v1_' . $key . '_animation', 'label'=> '', 'name' => '_home_v1[' . $key . '][animation]', 'value' => isset( $home_v1['' . $key . '']['animation'] ) ? $home_v1['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_home_v1_' . $key . '_priority', 'label'=> '', 'name' => '_home_v1[' . $key . '][priority]', 'value' => isset( $home_v1['' . $key . '']['priority'] ) ? $home_v1['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_home_v1_' . $key . '_is_enabled', 'label' => '', 'name' => '_home_v1[' . $key . '][is_enabled]', 'value'=> isset( $home_v1['' . $key . '']['is_enabled'] ) ? $home_v1['' . $key . '']['is_enabled'] : '', ) ); ?></td>
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
						'id' 			=> '_home_v1_sdr_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shorcode for your slider here', 'pizzaro' ),
						'name'			=> '_home_v1[sdr][shortcode]',
						'value'			=> isset( $home_v1['sdr']['shortcode'] ) ? $home_v1['sdr']['shortcode'] : '',
					) );
				?>
				</div>
			</div><!-- /#slider_block -->
			
			<div id="tiles_block" class="panel pizzaro_options_panel">

				<?php pizzaro_wp_legend( esc_html__( 'Column 1', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][0][0][args][pre_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['pre_title'] ) ? $home_v1['ti']['tiles'][0][0]['args']['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][0][0][args][title]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['title'] ) ? $home_v1['ti']['tiles'][0][0]['args']['title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][0][0][args][sub_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['sub_title'] ) ? $home_v1['ti']['tiles'][0][0]['args']['sub_title'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][0][0][args][action_text]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['action_text'] ) ? $home_v1['ti']['tiles'][0][0]['args']['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][0][0][args][action_link]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['action_link'] ) ? $home_v1['ti']['tiles'][0][0]['args']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][0][0][args][condition]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['condition'] ) ? $home_v1['ti']['tiles'][0][0]['args']['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v1_ti_tiles_0_0_args_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v1[ti][tiles][0][0][args][bg_choice]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['bg_choice'] ) ? $home_v1['ti']['tiles'][0][0]['args']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v1_ti_tiles_0_0_args_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v1[ti][tiles][0][0][args][bg_image]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['bg_image'] ) ? $home_v1['ti']['tiles'][0][0]['args']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][0][0][args][bg_color]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['bg_color'] ) ? $home_v1['ti']['tiles'][0][0]['args']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_0_0_args_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][0][0][args][height]',
						'value'			=> isset( $home_v1['ti']['tiles'][0][0]['args']['height'] ) ? $home_v1['ti']['tiles'][0][0]['args']['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Column 2', 'pizzaro' ) ); ?>

				<?php pizzaro_wp_legend( esc_html__( 'Top', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][pre_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['pre_title'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['title'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][sub_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['sub_title'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['sub_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][description]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['description'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['description'] : esc_html__( 'for online orders in wendsdays', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][action_text]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['action_text'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][action_link]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['action_link'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][condition]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['condition'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v1_ti_tiles_1_0_args_banners_0_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][bg_choice]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['bg_choice'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v1_ti_tiles_1_0_args_banners_0_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][bg_image]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['bg_image'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][bg_color]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['bg_color'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_0_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][0][height]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['height'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][0]['height'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][pre_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['pre_title'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['title'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][sub_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['sub_title'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['sub_title'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][action_text]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['action_text'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][action_link]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['action_link'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][condition]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['condition'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v1_ti_tiles_1_0_args_banners_1_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][bg_choice]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['bg_choice'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v1_ti_tiles_1_0_args_banners_1_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][bg_image]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['bg_image'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][bg_color]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['bg_color'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_0_args_banners_1_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][1][0][args][banners][1][height]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['height'] ) ? $home_v1['ti']['tiles'][1][0]['args']['banners'][1]['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Bottom', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ),
						'name'			=> '_home_v1[ti][tiles][1][1][args][pre_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['pre_title'] ) ? $home_v1['ti']['tiles'][1][1]['args']['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][1][args][title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['title'] ) ? $home_v1['ti']['tiles'][1][1]['args']['title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][1][args][sub_title]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['sub_title'] ) ? $home_v1['ti']['tiles'][1][1]['args']['sub_title'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][1][args][action_text]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['action_text'] ) ? $home_v1['ti']['tiles'][1][1]['args']['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][1][args][action_link]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['action_link'] ) ? $home_v1['ti']['tiles'][1][1]['args']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v1[ti][tiles][1][1][args][condition]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['condition'] ) ? $home_v1['ti']['tiles'][1][1]['args']['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v1_ti_tiles_1_1_args_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v1[ti][tiles][1][1][args][bg_choice]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['bg_choice'] ) ? $home_v1['ti']['tiles'][1][1]['args']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v1_ti_tiles_1_1_args_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v1[ti][tiles][1][1][args][bg_image]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['bg_image'] ) ? $home_v1['ti']['tiles'][1][1]['args']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][1][1][args][bg_color]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['bg_color'] ) ? $home_v1['ti']['tiles'][1][1]['args']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_ti_tiles_1_1_args_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[ti][tiles][1][1][args][height]',
						'value'			=> isset( $home_v1['ti']['tiles'][1][1]['args']['height'] ) ? $home_v1['ti']['tiles'][1][1]['args']['height'] : '',
					) );
				?>
				</div>

			</div><!-- /#tiles_block -->

			<div id="products_tabs" class="panel pizzaro_options_panel">
				
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v1_pt_product_limit', 
						'label' 		=>  esc_html__( 'Products Limit', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Enter the number of products to show', 'pizzaro' ),
						'name'			=> '_home_v1[pt][product_limit]',
						'class'			=> 'product_limit',
						'size'			=> 2,
						'value'			=> isset( $home_v1['pt']['product_limit'] ) ? $home_v1['pt']['product_limit'] : 3,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v1_pt_product_columns', 
						'label' 		=>  esc_html__( 'Columns', 'pizzaro' ),
						'options'		=> array(
							'2'	=> '2',
							'3'	=> '3',
							'4'	=> '4',
							'5'	=> '5',
							'6'	=> '6',
						),
						'class'			=> 'columns_select',
						'default'		=> '4',
						'name'			=> '_home_v1[pt][product_columns]',
						'value'			=> isset( $home_v1['pt']['product_columns'] ) ? $home_v1['pt']['product_columns'] : 3,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v1_pt_active_tab', 
						'label' 		=>  esc_html__( 'Active Tab', 'pizzaro' ),
						'options'		=> array(
							'1'	=> '1',
							'2'	=> '2',
							'3'	=> '3',
						),
						'class'			=> 'columns_select',
						'default'		=> '2',
						'name'			=> '_home_v1[pt][default_active_tab]',
						'value'			=> isset( $home_v1['pt']['default_active_tab'] ) ? $home_v1['pt']['default_active_tab'] : '2',
					) );
				?>
				</div>

				<div class="options_group">
				<?php	
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v1_pt_tab_1_title', 
						'label' 		=> esc_html__( 'Tab #1 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Wraps', 'pizzaro' ),
						'name'			=> '_home_v1[pt][tabs][0][title]',
						'value'			=> isset( $home_v1['pt']['tabs'][0]['title'] ) ? $home_v1['pt']['tabs'][0]['title'] : esc_html__( 'Wraps', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v1_pt_tab_1_content',
						'label'			=> esc_html__( 'Tab #1 Content', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v1[pt][tabs][0][content]',
						'value'			=> isset( $home_v1['pt']['tabs'][0]['content'] ) ? $home_v1['pt']['tabs'][0]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v1_pt_tab_2_title', 
						'label' 		=> esc_html__( 'Tab #2 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Pizza Sets', 'pizzaro' ),
						'name'			=> '_home_v1[pt][tabs][1][title]',
						'value'			=> isset( $home_v1['pt']['tabs'][1]['title'] ) ? $home_v1['pt']['tabs'][1]['title'] : esc_html__( 'Pizza Sets', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v1_pt_tab_2_content',
						'label'			=> esc_html__( 'Tab #2 Content', 'pizzaro' ),
						'default'		=> 'best_selling_products',
						'name'			=> '_home_v1[pt][tabs][1][content]',
						'value'			=> isset( $home_v1['pt']['tabs'][1]['content'] ) ? $home_v1['pt']['tabs'][1]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v1_pt_tab_3_title', 
						'label' 		=> esc_html__( 'Tab #3 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Burgers', 'pizzaro' ),
						'name'			=> '_home_v1[pt][tabs][2][title]',
						'value'			=> isset( $home_v1['pt']['tabs'][2]['title'] ) ? $home_v1['pt']['tabs'][2]['title'] : esc_html__( 'Burgers', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v1_pt_tab_3_content',
						'label'			=> esc_html__( 'Tab #3 Content', 'pizzaro' ),
						'default'		=> 'sale_products',
						'name'			=> '_home_v1[pt][tabs][2][content]',
						'value'			=> isset( $home_v1['pt']['tabs'][2]['content'] ) ? $home_v1['pt']['tabs'][2]['content'] : '',
					) );
				?>
				</div>

			</div><!-- /#products_tabs -->

			<div id="products_sale_event" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_spe_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v1[spe][pre_title]',
						'value'			=> isset( $home_v1['spe']['pre_title'] ) ? $home_v1['spe']['pre_title'] : esc_html__( 'FREE DELIVERY WITH', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_spe_section_title', 
						'label' 		=> esc_html__( 'Section Title', 'pizzaro' ), 
						'name'			=> '_home_v1[spe][section_title]',
						'value'			=> isset( $home_v1['spe']['section_title'] ) ? $home_v1['spe']['section_title'] : esc_html__( 'PIZZA OF THE DAY', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_spe_price', 
						'label' 		=> esc_html__( 'Price', 'pizzaro' ), 
						'name'			=> '_home_v1[spe][price]',
						'value'			=> isset( $home_v1['spe']['price'] ) ? $home_v1['spe']['price'] : '9.99',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_spe_price_info', 
						'label' 		=> esc_html__( 'Price Info', 'pizzaro' ),
						'name'			=> '_home_v1[spe][price_info]',
						'value'			=> isset( $home_v1['spe']['price_info'] ) ? $home_v1['spe']['price_info'] : esc_html__( 'EACH', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_spe_product_ids', 
						'label' 		=> esc_html__( 'Product IDs', 'pizzaro' ),
						'name'			=> '_home_v1[spe][product_ids]',
						'value'			=> isset( $home_v1['spe']['product_ids'] ) ? $home_v1['spe']['product_ids'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_spe_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v1[spe][action_text]',
						'value'			=> isset( $home_v1['spe']['action_text'] ) ? $home_v1['spe']['action_text'] : esc_html__( 'Order Now', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_spe_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v1[spe][action_link]',
						'value'			=> isset( $home_v1['spe']['action_link'] ) ? $home_v1['spe']['action_link'] : '#',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v1_spe_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v1[spe][bg_choice]',
						'value'			=> isset( $home_v1['spe']['bg_choice'] ) ? $home_v1['spe']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v1_spe_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v1[spe][bg_image]',
						'value'			=> isset( $home_v1['spe']['bg_image'] ) ? $home_v1['spe']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_spe_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[spe][bg_color]',
						'value'			=> isset( $home_v1['spe']['bg_color'] ) ? $home_v1['spe']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_spe_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v1[spe][height]',
						'value'			=> isset( $home_v1['spe']['height'] ) ? $home_v1['spe']['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#products_sale_event -->

			<div id="products_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_pl_section_title',
						'label' 		=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v1[pl][section_title]',
						'value'			=> isset( $home_v1['pl']['section_title'] ) ? $home_v1['pl']['section_title'] : esc_html__( 'Goes Great With', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v1_pl_product_limit',
						'label'			=> esc_html__( 'Limit', 'pizzaro' ),
						'name'			=> '_home_v1[pl][product_limit]',
						'value'			=> isset( $home_v1['pl']['product_limit'] ) ? $home_v1['pl']['product_limit'] : 4,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v1_pl_product_columns',
						'label'			=> esc_html__( 'Columns', 'pizzaro' ),
						'name'			=> '_home_v1[pl][product_columns]',
						'value'			=> isset( $home_v1['pl']['product_columns'] ) ? $home_v1['pl']['product_columns'] : 4,
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v1_pl_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v1[pl][content]',
						'value'			=> isset( $home_v1['pl']['content'] ) ? $home_v1['pl']['content'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_block -->

			<div id="product_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id'			=> '_home_v1_sp_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v1[sp][section_title]',
						'value'			=> isset( $home_v1['sp']['section_title'] ) ? $home_v1['sp']['section_title'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v1_sp_product_id',
						'label'			=> esc_html__( 'Product ID', 'pizzaro' ),
						'name'			=> '_home_v1[sp][product_id]',
						'value'			=> isset( $home_v1['sp']['product_id'] ) ? $home_v1['sp']['product_id'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v1_sp_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v1[sp][bg_image]',
						'value'			=> isset( $home_v1['sp']['bg_image'] ) ? $home_v1['sp']['bg_image'] : '',
					) );

				?>
				</div>
			</div><!-- /#product_block -->

			<div id="features_list" class="panel pizzaro_options_panel">

				<?php pizzaro_wp_legend( esc_html__( 'Feature 1', 'pizzaro' ) ); ?>
				
				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_fl_1_icon',
						'label' 		=> esc_html__( 'Icon', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the icon for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][0][icon]',
						'value'			=> isset( $home_v1['fl']['features'][0]['icon'] ) ? $home_v1['fl']['features'][0]['icon'] : 'po po-best-quality',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_fl_1_label',
						'label' 		=> esc_html__( 'Text', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the text for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][0][label]',
						'value'			=> isset( $home_v1['fl']['features'][0]['label'] ) ? $home_v1['fl']['features'][0]['label'] : sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'Best Quality', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Feature 2', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_fl_2_icon',
						'label' 		=> esc_html__( 'Icon', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the icon for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][1][icon]',
						'value'			=> isset( $home_v1['fl']['features'][1]['icon'] ) ? $home_v1['fl']['features'][1]['icon'] : 'po po-on-time',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_fl_2_label',
						'label' 		=> esc_html__( 'Text', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the text for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][1][label]',
						'value'			=> isset( $home_v1['fl']['features'][1]['label'] ) ? $home_v1['fl']['features'][1]['label'] : sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'On Time', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Feature 3', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_fl_3_icon',
						'label' 		=> esc_html__( 'Icon', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the icon for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][2][icon]',
						'value'			=> isset( $home_v1['fl']['features'][2]['icon'] ) ? $home_v1['fl']['features'][2]['icon'] : 'po po-master-chef',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_fl_3_label',
						'label' 		=> esc_html__( 'Text', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the text for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][2][label]',
						'value'			=> isset( $home_v1['fl']['features'][2]['label'] ) ? $home_v1['fl']['features'][2]['label'] : sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'MasterChefs', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Feature 4', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v1_fl_4_icon',
						'label' 		=> esc_html__( 'Icon', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the icon for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][3][icon]',
						'value'			=> isset( $home_v1['fl']['features'][3]['icon'] ) ? $home_v1['fl']['features'][3]['icon'] : 'po po-ready-delivery',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v1_fl_4_label',
						'label' 		=> esc_html__( 'Text', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the text for your features here', 'pizzaro' ),
						'name'			=> '_home_v1[fl][features][3][label]',
						'value'			=> isset( $home_v1['fl']['features'][3]['label'] ) ? $home_v1['fl']['features'][3]['label'] : sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'Taste Food', 'pizzaro' ), esc_html__( 'Praesent pulvinar neque pellentesque mattis pretium.', 'pizzaro' ) ),
					) );
				?>
				</div>
				
			</div><!-- /#features_list -->
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_home_v1'] ) ) {
			$clean_home_v1_options = pizzaro_clean_kses_post( $_POST['_home_v1'] );
			update_post_meta( $post_id, '_home_v1_options',  serialize( $clean_home_v1_options ) );
		}	
	}
}