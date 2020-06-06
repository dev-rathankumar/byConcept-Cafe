<?php
/**
 * Home v3 Metabox
 *
 * Displays the home v3 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Home_v3 Class.
 */
class Pizzaro_Meta_Box_Home_v3 {

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

		if ( $template_file !== 'template-homepage-v3.php' ) {
			return;
		}

		self::output_home_v3( $post );
	}

	private static function output_home_v3( $post ) {

		$home_v3 = pizzaro_get_home_v3_meta();

		?>
		<div class="panel-wrap meta-box-home">
			<ul class="home_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_home_v3_data_tabs', array(
					'general' => array(
						'label'  => esc_html__( 'General', 'pizzaro' ),
						'target' => 'general_block',
						'class'  => array(),
					),
					'tiles_block' => array(
						'label'  => esc_html__( 'Tiles Block', 'pizzaro' ),
						'target' => 'tiles_block',
						'class'  => array(),
					),
					'banners1_block' => array(
						'label'  => esc_html__( 'Banners 1', 'pizzaro' ),
						'target' => 'banners1_block',
						'class'  => array(),
					),
					'banners2_block' => array(
						'label'  => esc_html__( 'Banners 2', 'pizzaro' ),
						'target' => 'banners2_block',
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
							'id'		=> '_home_v3_header_style',
							'label'		=> esc_html__( 'Header Style', 'pizzaro' ),
							'name'		=> '_home_v3[header_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Header', 'pizzaro' ),
								'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v3['header_style'] ) ? $home_v3['header_style'] : '',
						) );

						pizzaro_wp_select( array(
							'id'		=> '_home_v3_footer_style',
							'label'		=> esc_html__( 'Footer Style', 'pizzaro' ),
							'name'		=> '_home_v3[footer_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Footer', 'pizzaro' ),
								'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v3['footer_style'] ) ? $home_v3['footer_style'] : '',
						) );
					?>
				</div>
				<div class="options_group">
					<?php 
						$home_v3_blocks = array(
							'ti'	=> esc_html__( 'Tiles Block', 'pizzaro' ),
							'brs1'	=> esc_html__( 'Banners 1', 'pizzaro' ),
							'brs2'	=> esc_html__( 'Banners 2', 'pizzaro' ),
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
							<?php foreach( $home_v3_blocks as $key => $home_v3_block ) : ?>
							<tr>
								<td><?php echo esc_html( $home_v3_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_home_v3_' . $key . '_animation', 'label'=> '', 'name' => '_home_v3[' . $key . '][animation]', 'value' => isset( $home_v3['' . $key . '']['animation'] ) ? $home_v3['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_home_v3_' . $key . '_priority', 'label'=> '', 'name' => '_home_v3[' . $key . '][priority]', 'value' => isset( $home_v3['' . $key . '']['priority'] ) ? $home_v3['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_home_v3_' . $key . '_is_enabled', 'label' => '', 'name' => '_home_v3[' . $key . '][is_enabled]', 'value'=> isset( $home_v3['' . $key . '']['is_enabled'] ) ? $home_v3['' . $key . '']['is_enabled'] : '', ) ); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div><!-- /#general_block -->
			
			<div id="tiles_block" class="panel pizzaro_options_panel">

				<?php pizzaro_wp_legend( esc_html__( 'Column 1', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][0][0][args][pre_title]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['pre_title'] ) ? $home_v3['ti']['tiles'][0][0]['args']['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][0][0][args][title]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['title'] ) ? $home_v3['ti']['tiles'][0][0]['args']['title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][0][0][args][sub_title]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['sub_title'] ) ? $home_v3['ti']['tiles'][0][0]['args']['sub_title'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][0][0][args][action_text]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['action_text'] ) ? $home_v3['ti']['tiles'][0][0]['args']['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][0][0][args][action_link]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['action_link'] ) ? $home_v3['ti']['tiles'][0][0]['args']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][0][0][args][condition]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['condition'] ) ? $home_v3['ti']['tiles'][0][0]['args']['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_ti_tiles_0_0_args_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[ti][tiles][0][0][args][bg_choice]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['bg_choice'] ) ? $home_v3['ti']['tiles'][0][0]['args']['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_ti_tiles_0_0_args_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[ti][tiles][0][0][args][bg_image]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['bg_image'] ) ? $home_v3['ti']['tiles'][0][0]['args']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[ti][tiles][0][0][args][bg_color]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['bg_color'] ) ? $home_v3['ti']['tiles'][0][0]['args']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_0_0_args_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[ti][tiles][0][0][args][height]',
						'value'			=> isset( $home_v3['ti']['tiles'][0][0]['args']['height'] ) ? $home_v3['ti']['tiles'][0][0]['args']['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Column 2', 'pizzaro' ) ); ?>

				<?php pizzaro_wp_legend( esc_html__( 'Top (Store Locator)', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][0][args][title]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['title'] ) ? $home_v3['ti']['tiles'][1][0]['args']['title'] : esc_html__( 'FIND', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_sub_title', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][0][args][sub_title]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['sub_title'] ) ? $home_v3['ti']['tiles'][1][0]['args']['sub_title'] : wp_kses_post( __( 'AN PIZZARO`s', 'pizzaro' ) . '<br/>' . __( 'LOCATION NEAR YOU!', 'pizzaro' ) ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_icon_class', 
						'label' 		=> esc_html__( 'Icon Class', 'pizzaro' ),
						'name'			=> '_home_v3[ti][tiles][1][0][args][icon_class]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['icon_class'] ) ? $home_v3['ti']['tiles'][1][0]['args']['icon_class'] : 'po po-marker-hand-drawned',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_button_text', 
						'label' 		=> esc_html__( 'Button Text', 'pizzaro' ),
						'name'			=> '_home_v3[ti][tiles][1][0][args][button_text]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['button_text'] ) ? $home_v3['ti']['tiles'][1][0]['args']['button_text'] : esc_html__( 'Find', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_page_id', 
						'label' 		=> esc_html__( 'Store Locator Page ID', 'pizzaro' ),
						'name'			=> '_home_v3[ti][tiles][1][0][args][page_id]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['page_id'] ) ? $home_v3['ti']['tiles'][1][0]['args']['page_id'] : '',
						'placeholder'	=> esc_html__( 'Enter store locator page id where you added [wpsl] shortcode', 'pizzaro' )
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_ti_tiles_1_0_args_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[ti][tiles][1][0][args][bg_choice]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['bg_choice'] ) ? $home_v3['ti']['tiles'][1][0]['args']['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_ti_tiles_1_0_args_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[ti][tiles][1][0][args][bg_image]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['bg_image'] ) ? $home_v3['ti']['tiles'][1][0]['args']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[ti][tiles][1][0][args][bg_color]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['bg_color'] ) ? $home_v3['ti']['tiles'][1][0]['args']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_0_args_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[ti][tiles][1][0][args][height]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][0]['args']['height'] ) ? $home_v3['ti']['tiles'][1][0]['args']['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Bottom', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ),
						'name'			=> '_home_v3[ti][tiles][1][1][args][pre_title]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['pre_title'] ) ? $home_v3['ti']['tiles'][1][1]['args']['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][1][args][title]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['title'] ) ? $home_v3['ti']['tiles'][1][1]['args']['title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][1][args][sub_title]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['sub_title'] ) ? $home_v3['ti']['tiles'][1][1]['args']['sub_title'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][1][args][action_text]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['action_text'] ) ? $home_v3['ti']['tiles'][1][1]['args']['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][1][args][action_link]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['action_link'] ) ? $home_v3['ti']['tiles'][1][1]['args']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[ti][tiles][1][1][args][condition]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['condition'] ) ? $home_v3['ti']['tiles'][1][1]['args']['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_ti_tiles_1_1_args_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[ti][tiles][1][1][args][bg_choice]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['bg_choice'] ) ? $home_v3['ti']['tiles'][1][1]['args']['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_ti_tiles_1_1_args_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[ti][tiles][1][1][args][bg_image]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['bg_image'] ) ? $home_v3['ti']['tiles'][1][1]['args']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[ti][tiles][1][1][args][bg_color]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['bg_color'] ) ? $home_v3['ti']['tiles'][1][1]['args']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_ti_tiles_1_1_args_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[ti][tiles][1][1][args][height]',
						'value'			=> isset( $home_v3['ti']['tiles'][1][1]['args']['height'] ) ? $home_v3['ti']['tiles'][1][1]['args']['height'] : '',
					) );
				?>
				</div>

			</div><!-- /#tiles_block -->

			<div id="banners1_block" class="panel pizzaro_options_panel">
				
				<?php pizzaro_wp_legend( esc_html__( 'Banner 1', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][0][pre_title]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['pre_title'] ) ? $home_v3['brs1']['banners'][0]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][0][title]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['title'] ) ? $home_v3['brs1']['banners'][0]['title'] : wp_kses_post( '<strong>' . __( 'VEGGIE +', 'pizzaro' ) . '</strong>' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][0][sub_title]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['sub_title'] ) ? $home_v3['brs1']['banners'][0]['sub_title'] : wp_kses_post( '<strong>' . __( 'SPECIALS', 'pizzaro' ) . '</strong>' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v3[brs1][banners][0][description]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['description'] ) ? $home_v3['brs1']['banners'][0]['description'] : wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>9<span class="decimals">99</span></span></div>' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][0][action_text]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['action_text'] ) ? $home_v3['brs1']['banners'][0]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][0][action_link]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['action_link'] ) ? $home_v3['brs1']['banners'][0]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][0][condition]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['condition'] ) ? $home_v3['brs1']['banners'][0]['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_brs1_banners_0_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[brs1][banners][0][bg_choice]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['bg_choice'] ) ? $home_v3['brs1']['banners'][0]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_brs1_banners_0_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[brs1][banners][0][bg_image]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['bg_image'] ) ? $home_v3['brs1']['banners'][0]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs1][banners][0][bg_color]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['bg_color'] ) ? $home_v3['brs1']['banners'][0]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_0_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs1][banners][0][height]',
						'value'			=> isset( $home_v3['brs1']['banners'][0]['height'] ) ? $home_v3['brs1']['banners'][0]['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Banner 2', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][1][pre_title]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['pre_title'] ) ? $home_v3['brs1']['banners'][1]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][1][title]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['title'] ) ? $home_v3['brs1']['banners'][1]['title'] : esc_html__( 'iCED COFFEE', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][1][sub_title]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['sub_title'] ) ? $home_v3['brs1']['banners'][1]['sub_title'] : esc_html__( 'SUMMERS', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v3[brs1][banners][1][description]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['description'] ) ? $home_v3['brs1']['banners'][1]['description'] : wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>5<span class="decimals">99</span></span></div>' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][1][action_text]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['action_text'] ) ? $home_v3['brs1']['banners'][1]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][1][action_link]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['action_link'] ) ? $home_v3['brs1']['banners'][1]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][1][condition]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['condition'] ) ? $home_v3['brs1']['banners'][1]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_brs1_banners_1_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[brs1][banners][1][bg_choice]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['bg_choice'] ) ? $home_v3['brs1']['banners'][1]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_brs1_banners_1_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[brs1][banners][1][bg_image]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['bg_image'] ) ? $home_v3['brs1']['banners'][1]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs1][banners][1][bg_color]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['bg_color'] ) ? $home_v3['brs1']['banners'][1]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_1_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs1][banners][1][height]',
						'value'			=> isset( $home_v3['brs1']['banners'][1]['height'] ) ? $home_v3['brs1']['banners'][1]['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Banner 3', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][2][pre_title]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['pre_title'] ) ? $home_v3['brs1']['banners'][2]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][2][title]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['title'] ) ? $home_v3['brs1']['banners'][2]['title'] : esc_html__( 'CHICKEN WRAP', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][2][sub_title]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['sub_title'] ) ? $home_v3['brs1']['banners'][2]['sub_title'] : esc_html__( 'SUMMERS', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v3[brs1][banners][2][description]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['description'] ) ? $home_v3['brs1']['banners'][2]['description'] : wp_kses_post( '<div class="banner-price"><span class="amount"><span class="currency">$</span>3<span class="decimals">99</span></span></div>' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][2][action_text]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['action_text'] ) ? $home_v3['brs1']['banners'][2]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][2][action_link]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['action_link'] ) ? $home_v3['brs1']['banners'][2]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[brs1][banners][2][condition]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['condition'] ) ? $home_v3['brs1']['banners'][2]['condition'] : wp_kses_post( '<em>*</em>' . __( 'ONLY IN LOCAL', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_brs1_banners_2_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[brs1][banners][2][bg_choice]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['bg_choice'] ) ? $home_v3['brs1']['banners'][2]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_brs1_banners_2_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[brs1][banners][2][bg_image]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['bg_image'] ) ? $home_v3['brs1']['banners'][2]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs1][banners][2][bg_color]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['bg_color'] ) ? $home_v3['brs1']['banners'][2]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs1_banners_2_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs1][banners][2][height]',
						'value'			=> isset( $home_v3['brs1']['banners'][2]['height'] ) ? $home_v3['brs1']['banners'][2]['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#banners1_block -->

			<div id="banners2_block" class="panel pizzaro_options_panel">

				<?php pizzaro_wp_legend( esc_html__( 'Banner 1', 'pizzaro' ) ); ?>
				
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][0][pre_title]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['pre_title'] ) ? $home_v3['brs2']['banners'][0]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][0][title]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['title'] ) ? $home_v3['brs2']['banners'][0]['title'] : esc_html__( 'FLAVOR MENU', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][0][sub_title]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['sub_title'] ) ? $home_v3['brs2']['banners'][0]['sub_title'] : esc_html__( 'VEGGIE LOVER - HAND TOSTED', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v3[brs2][banners][0][description]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['description'] ) ? $home_v3['brs2']['banners'][0]['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][0][action_text]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['action_text'] ) ? $home_v3['brs2']['banners'][0]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][0][action_link]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['action_link'] ) ? $home_v3['brs2']['banners'][0]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][0][condition]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['condition'] ) ? $home_v3['brs2']['banners'][0]['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_brs2_banners_0_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[brs2][banners][0][bg_choice]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['bg_choice'] ) ? $home_v3['brs2']['banners'][0]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_brs2_banners_0_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[brs2][banners][0][bg_image]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['bg_image'] ) ? $home_v3['brs2']['banners'][0]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs2][banners][0][bg_color]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['bg_color'] ) ? $home_v3['brs2']['banners'][0]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_0_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs2][banners][0][height]',
						'value'			=> isset( $home_v3['brs2']['banners'][0]['height'] ) ? $home_v3['brs2']['banners'][0]['height'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Banner 2', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][1][pre_title]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['pre_title'] ) ? $home_v3['brs2']['banners'][1]['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][1][title]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['title'] ) ? $home_v3['brs2']['banners'][1]['title'] : esc_html__( 'BREAKFAST MENU', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][1][sub_title]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['sub_title'] ) ? $home_v3['brs2']['banners'][1]['sub_title'] : esc_html__( 'FROM 7 AM TO 11 AM', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v3[brs2][banners][1][description]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['description'] ) ? $home_v3['brs2']['banners'][1]['description'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][1][action_text]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['action_text'] ) ? $home_v3['brs2']['banners'][1]['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][1][action_link]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['action_link'] ) ? $home_v3['brs2']['banners'][1]['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v3[brs2][banners][1][condition]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['condition'] ) ? $home_v3['brs2']['banners'][1]['condition'] : '',
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v3_brs2_banners_1_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v3[brs2][banners][1][bg_choice]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['bg_choice'] ) ? $home_v3['brs2']['banners'][1]['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v3_brs2_banners_1_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v3[brs2][banners][1][bg_image]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['bg_image'] ) ? $home_v3['brs2']['banners'][1]['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs2][banners][1][bg_color]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['bg_color'] ) ? $home_v3['brs2']['banners'][1]['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v3_brs2_banners_1_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v3[brs2][banners][1][height]',
						'value'			=> isset( $home_v3['brs2']['banners'][1]['height'] ) ? $home_v3['brs2']['banners'][1]['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#banners2_block -->
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_home_v3'] ) ) {
			$clean_home_v3_options = pizzaro_clean_kses_post( $_POST['_home_v3'] );
			update_post_meta( $post_id, '_home_v3_options',  serialize( $clean_home_v3_options ) );
		}	
	}
}