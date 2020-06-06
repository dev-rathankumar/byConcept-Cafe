<?php
/**
 * Home v5 Metabox
 *
 * Displays the home v5 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Home_v5 Class.
 */
class Pizzaro_Meta_Box_Home_v5 {

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

		if ( $template_file !== 'template-homepage-v5.php' ) {
			return;
		}

		self::output_home_v5( $post );
	}

	private static function output_home_v5( $post ) {

		$home_v5 = pizzaro_get_home_v5_meta();

		?>
		<div class="panel-wrap meta-box-home">
			<ul class="home_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_home_v5_data_tabs', array(
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
					'products_categories' => array(
						'label'  => esc_html__( 'Products Categories', 'pizzaro' ),
						'target' => 'products_categories',
						'class'  => array(),
					),
					'products_tabs' => array(
						'label'  => esc_html__( 'Products Tabs', 'pizzaro' ),
						'target' => 'products_tabs',
						'class'  => array(),
					),
					'banner_block' => array(
						'label'  => esc_html__( 'Banner', 'pizzaro' ),
						'target' => 'banner_block',
						'class'  => array(),
					),
					'products_block' => array(
						'label'  => esc_html__( 'Products Block', 'pizzaro' ),
						'target' => 'products_block',
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
							'id'		=> '_home_v5_header_style',
							'label'		=> esc_html__( 'Header Style', 'pizzaro' ),
							'name'		=> '_home_v5[header_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Header', 'pizzaro' ),
								'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v5['header_style'] ) ? $home_v5['header_style'] : '',
						) );

						pizzaro_wp_select( array(
							'id'		=> '_home_v5_footer_style',
							'label'		=> esc_html__( 'Footer Style', 'pizzaro' ),
							'name'		=> '_home_v5[footer_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Footer', 'pizzaro' ),
								'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v5['footer_style'] ) ? $home_v5['footer_style'] : '',
						) );
					?>
				</div>
				<div class="options_group">
					<?php 
						$home_v5_blocks = array(
							'sdr'	=> esc_html__( 'Slider', 'pizzaro' ),
							'pc'	=> esc_html__( 'Products Categories', 'pizzaro' ),
							'pt'	=> esc_html__( 'Products Tabs', 'pizzaro' ),
							'br'	=> esc_html__( 'Banner', 'pizzaro' ),
							'pl'	=> esc_html__( 'Products Block', 'pizzaro' ),
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
							<?php foreach( $home_v5_blocks as $key => $home_v5_block ) : ?>
							<tr>
								<td><?php echo esc_html( $home_v5_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_home_v5_' . $key . '_animation', 'label'=> '', 'name' => '_home_v5[' . $key . '][animation]', 'value' => isset( $home_v5['' . $key . '']['animation'] ) ? $home_v5['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_home_v5_' . $key . '_priority', 'label'=> '', 'name' => '_home_v5[' . $key . '][priority]', 'value' => isset( $home_v5['' . $key . '']['priority'] ) ? $home_v5['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_home_v5_' . $key . '_is_enabled', 'label' => '', 'name' => '_home_v5[' . $key . '][is_enabled]', 'value'=> isset( $home_v5['' . $key . '']['is_enabled'] ) ? $home_v5['' . $key . '']['is_enabled'] : '', ) ); ?></td>
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
						'id' 			=> '_home_v5_sdr_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shorcode for your slider here', 'pizzaro' ),
						'name'			=> '_home_v5[sdr][shortcode]',
						'value'			=> isset( $home_v5['sdr']['shortcode'] ) ? $home_v5['sdr']['shortcode'] : '',
					) );
				?>
				</div>
			</div><!-- /#slider_block -->

			<div id="products_categories" class="panel pizzaro_options_panel">

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_pc_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v5[pc][section_title]',
						'value'			=> isset( $home_v5['pc']['section_title'] ) ? $home_v5['pc']['section_title'] : esc_html__( 'Explore Menu', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_pc_pre_title',
						'label'			=> esc_html__( 'Pre Title', 'pizzaro' ),
						'name'			=> '_home_v5[pc][pre_title]',
						'value'			=> isset( $home_v5['pc']['pre_title'] ) ? $home_v5['pc']['pre_title'] : esc_html__( 'Pick your taste', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_pc_orderby', 
						'label' 		=> esc_html__( 'Orderby', 'pizzaro' ), 
						'name'			=> '_home_v5[pc][orderby]',
						'value'			=> isset( $home_v5['pc']['orderby'] ) ? $home_v5['pc']['orderby'] : 'name',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_pc_order', 
						'label' 		=> esc_html__( 'Order', 'pizzaro' ), 
						'name'			=> '_home_v5[pc][order]',
						'value'			=> isset( $home_v5['pc']['order'] ) ? $home_v5['pc']['order'] : 'ASC',
					) );

					pizzaro_wp_checkbox( array(
						'id' 			=> '_home_v5_pc_hide_empty',
						'label' 		=> esc_html__( 'Hide Empty', 'pizzaro' ),
						'name' 			=> '_home_v5[pc][hide_empty]',
						'value'			=> isset( $home_v5['pc']['hide_empty'] ) ? $home_v5['pc']['hide_empty'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_pc_limit', 
						'label' 		=> esc_html__( 'Limit', 'pizzaro' ), 
						'name'			=> '_home_v5[pc][limit]',
						'value'			=> isset( $home_v5['pc']['limit'] ) ? $home_v5['pc']['limit'] : 4,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_pc_slugs',
						'label'			=> esc_html__( 'Slug', 'pizzaro' ),
						'name'			=> '_home_v5[pc][slugs]',
						'default'		=> '',
						'value'			=> isset( $home_v5['pc']['slugs'] ) ? $home_v5['pc']['slugs'] : '',
						'placeholder'	=> esc_html__( 'Enter slugs separated by comma', 'pizzaro' )
					) );

				?>
				</div>

			</div><!-- /#products_categories-->

			<div id="products_tabs" class="panel pizzaro_options_panel">
				
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v5_pt_product_limit', 
						'label' 		=>  esc_html__( 'Products Limit', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Enter the number of products to show', 'pizzaro' ),
						'name'			=> '_home_v5[pt][product_limit]',
						'class'			=> 'product_limit',
						'size'			=> 2,
						'value'			=> isset( $home_v5['pt']['product_limit'] ) ? $home_v5['pt']['product_limit'] : 3,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v5_pt_product_columns', 
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
						'name'			=> '_home_v5[pt][product_columns]',
						'value'			=> isset( $home_v5['pt']['product_columns'] ) ? $home_v5['pt']['product_columns'] : 3,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v5_pt_active_tab', 
						'label' 		=>  esc_html__( 'Active Tab', 'pizzaro' ),
						'options'		=> array(
							'1'	=> '1',
							'2'	=> '2',
							'3'	=> '3',
						),
						'class'			=> 'columns_select',
						'default'		=> '2',
						'name'			=> '_home_v5[pt][default_active_tab]',
						'value'			=> isset( $home_v5['pt']['default_active_tab'] ) ? $home_v5['pt']['default_active_tab'] : '2',
					) );
				?>
				</div>

				<div class="options_group">
				<?php	
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v5_pt_tab_1_title', 
						'label' 		=> esc_html__( 'Tab #1 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Wraps', 'pizzaro' ),
						'name'			=> '_home_v5[pt][tabs][0][title]',
						'value'			=> isset( $home_v5['pt']['tabs'][0]['title'] ) ? $home_v5['pt']['tabs'][0]['title'] : esc_html__( 'Wraps', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v5_pt_tab_1_content',
						'label'			=> esc_html__( 'Tab #1 Content', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v5[pt][tabs][0][content]',
						'value'			=> isset( $home_v5['pt']['tabs'][0]['content'] ) ? $home_v5['pt']['tabs'][0]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v5_pt_tab_2_title', 
						'label' 		=> esc_html__( 'Tab #2 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Pizza Sets', 'pizzaro' ),
						'name'			=> '_home_v5[pt][tabs][1][title]',
						'value'			=> isset( $home_v5['pt']['tabs'][1]['title'] ) ? $home_v5['pt']['tabs'][1]['title'] : esc_html__( 'Pizza Sets', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v5_pt_tab_2_content',
						'label'			=> esc_html__( 'Tab #2 Content', 'pizzaro' ),
						'default'		=> 'best_selling_products',
						'name'			=> '_home_v5[pt][tabs][1][content]',
						'value'			=> isset( $home_v5['pt']['tabs'][1]['content'] ) ? $home_v5['pt']['tabs'][1]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v5_pt_tab_3_title', 
						'label' 		=> esc_html__( 'Tab #3 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Burgers', 'pizzaro' ),
						'name'			=> '_home_v5[pt][tabs][2][title]',
						'value'			=> isset( $home_v5['pt']['tabs'][2]['title'] ) ? $home_v5['pt']['tabs'][2]['title'] : esc_html__( 'Burgers', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v5_pt_tab_3_content',
						'label'			=> esc_html__( 'Tab #3 Content', 'pizzaro' ),
						'default'		=> 'sale_products',
						'name'			=> '_home_v5[pt][tabs][2][content]',
						'value'			=> isset( $home_v5['pt']['tabs'][2]['content'] ) ? $home_v5['pt']['tabs'][2]['content'] : '',
					) );
				?>
				</div>

			</div><!-- /#products_tabs -->

			<div id="banner_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v5_br_pre_title', 
						'label' 		=> esc_html__( 'Pre Title', 'pizzaro' ), 
						'name'			=> '_home_v5[br][pre_title]',
						'value'			=> isset( $home_v5['br']['pre_title'] ) ? $home_v5['br']['pre_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v5_br_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'name'			=> '_home_v5[br][title]',
						'value'			=> isset( $home_v5['br']['title'] ) ? $home_v5['br']['title'] : esc_html__( 'STUDENT HAPPY HOURS', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v5_br_subtitle', 
						'label' 		=> esc_html__( 'Sub Title', 'pizzaro' ), 
						'name'			=> '_home_v5[br][sub_title]',
						'value'			=> isset( $home_v5['br']['sub_title'] ) ? $home_v5['br']['sub_title'] : '',
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_home_v5_br_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ),
						'name'			=> '_home_v5[br][description]',
						'value'			=> isset( $home_v5['br']['description'] ) ? $home_v5['br']['description'] : sprintf( '<div class="price"><span>$</span>8</div><div class="text">%s</div>', esc_html__( 'FOR PIZZA ON MONDAYS', 'pizzaro' ) ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_br_action_text', 
						'label' 		=> esc_html__( 'Action Text', 'pizzaro' ), 
						'name'			=> '_home_v5[br][action_text]',
						'value'			=> isset( $home_v5['br']['action_text'] ) ? $home_v5['br']['action_text'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_br_action_link', 
						'label' 		=> esc_html__( 'Action Link', 'pizzaro' ), 
						'name'			=> '_home_v5[br][action_link]',
						'value'			=> isset( $home_v5['br']['action_link'] ) ? $home_v5['br']['action_link'] : '#',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_br_condition', 
						'label' 		=> esc_html__( 'Condition', 'pizzaro' ), 
						'name'			=> '_home_v5[br][condition]',
						'value'			=> isset( $home_v5['br']['condition'] ) ? $home_v5['br']['condition'] : wp_kses_post( '<em>*</em>' . __( 'Only in Mondays in local from 8:00 am to 9:00 pm.', 'pizzaro' ) ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v5_br_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v5[br][bg_choice]',
						'value'			=> isset( $home_v5['br']['bg_choice'] ) ? $home_v5['br']['bg_choice'] : 'image',
					) );


					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v5_br_bg_image',
						'label'			=> esc_html__( 'Banner Image', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_image hide',
						'name'			=> '_home_v5[br][bg_image]',
						'value'			=> isset( $home_v5['br']['bg_image'] ) ? $home_v5['br']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_br_bg_color', 
						'label' 		=> esc_html__( 'Background Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v5[br][bg_color]',
						'value'			=> isset( $home_v5['br']['bg_color'] ) ? $home_v5['br']['bg_color'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v5_br_height', 
						'label' 		=> esc_html__( 'Background Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v5[br][height]',
						'value'			=> isset( $home_v5['br']['height'] ) ? $home_v5['br']['height'] : '',
					) );
				?>
				</div>
			</div><!-- /#banner_block -->

			<div id="products_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v5_pl_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v5[pl][section_title]',
						'value'			=> isset( $home_v5['pl']['section_title'] ) ? $home_v5['pl']['section_title'] : esc_html__( 'Todays Delicious Pasta', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_pl_product_limit',
						'label'			=> esc_html__( 'Limit', 'pizzaro' ),
						'name'			=> '_home_v5[pl][product_limit]',
						'value'			=> isset( $home_v5['pl']['product_limit'] ) ? $home_v5['pl']['product_limit'] : 6,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_pl_product_columns',
						'label'			=> esc_html__( 'Columns', 'pizzaro' ),
						'name'			=> '_home_v5[pl][product_columns]',
						'value'			=> isset( $home_v5['pl']['product_columns'] ) ? $home_v5['pl']['product_columns'] : 6,
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v5_pl_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v5[pl][content]',
						'value'			=> isset( $home_v5['pl']['content'] ) ? $home_v5['pl']['content'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_block -->

			<div id="newsletter_block" class="panel pizzaro_options_panel">

				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_nl_title',
						'label'			=> esc_html__( 'Title', 'pizzaro' ),
						'name'			=> '_home_v5[nl][title]',
						'default'		=> esc_html__( 'Sign up for Newsletter', 'pizzaro' ),
						'value'			=> isset( $home_v5['nl']['title'] ) ? $home_v5['nl']['title'] : esc_html__( 'Subscribe to Newsletter', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_nl_marketing_text',
						'label'			=> esc_html__( 'Marketing Text', 'pizzaro' ),
						'name'			=> '_home_v5[nl][marketing_text]',
						'default'		=> esc_html__( 'Stay up to date', 'pizzaro' ),
						'value'			=> isset( $home_v5['nl']['marketing_text'] ) ? $home_v5['nl']['marketing_text'] : esc_html__( 'Subscribe to receive our weekly Hot Promotions every Monday!', 'pizzaro' ),
					) );

					pizzaro_wp_select( array(
						'id'			=> '_home_v5_nl_bg_choice',
						'label'			=> esc_html__( 'Background Choice', 'pizzaro' ),
						'options'		=> array(
							'image'	=> esc_html__( 'Image', 'pizzaro' ),
							'color'	=> esc_html__( 'Color', 'pizzaro' ),
						),
						'class'			=> 'show_hide_select',
						'name'			=> '_home_v5[nl][bg_choice]',
						'value'			=> isset( $home_v5['nl']['bg_choice'] ) ? $home_v5['nl']['bg_choice'] : 'image',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v5_nl_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v5[nl][bg_image]',
						'wrapper_class'	=> 'show_if_image hide',
						'value'			=> isset( $home_v5['nl']['bg_image'] ) ? $home_v5['nl']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_nl_bg_color',
						'label'			=> esc_html__( 'Color', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v5[nl][bg_color]',
						'value'			=> isset( $home_v5['nl']['bg_color'] ) ? $home_v5['nl']['bg_color'] : '#e5e2db',
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v5_nl_height',
						'label'			=> esc_html__( 'Height', 'pizzaro' ),
						'wrapper_class'	=> 'show_if_color hide',
						'name'			=> '_home_v5[nl][height]',
						'value'			=> isset( $home_v5['nl']['height'] ) ? $home_v5['nl']['height'] : 460,
					) );
					
				?>
				</div>

			</div><!-- /#newsletter_block -->
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_home_v5'] ) ) {
			$clean_home_v5_options = pizzaro_clean_kses_post( $_POST['_home_v5'] );
			update_post_meta( $post_id, '_home_v5_options',  serialize( $clean_home_v5_options ) );
		}	
	}
}