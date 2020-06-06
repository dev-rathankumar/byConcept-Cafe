<?php
/**
 * Home v2 Metabox
 *
 * Displays the home v2 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Home_v2 Class.
 */
class Pizzaro_Meta_Box_Home_v2 {

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

		if ( $template_file !== 'template-homepage-v2.php' ) {
			return;
		}

		self::output_home_v2( $post );
	}

	private static function output_home_v2( $post ) {

		$home_v2 = pizzaro_get_home_v2_meta();

		?>
		<div class="panel-wrap meta-box-home">
			<ul class="home_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_home_v2_data_tabs', array(
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
					'products_tab' => array(
						'label'  => esc_html__( 'Products Tab', 'pizzaro' ),
						'target' => 'products_tab',
						'class'  => array(),
					),
					'products_4_1' => array(
						'label'  => esc_html__( 'Products 4-1', 'pizzaro' ),
						'target' => 'products_4_1',
						'class'  => array(),
					),
					'product_block' => array(
						'label'  => esc_html__( 'Product Block', 'pizzaro' ),
						'target' => 'product_block',
						'class'  => array(),
					),
					'products_carousel' => array(
						'label'  => esc_html__( 'Product Carousel with Image', 'pizzaro' ),
						'target' => 'products_carousel',
						'class'  => array(),
					),
					// 'tiled_gallery' => array(
					// 	'label'  => esc_html__( 'Tiled Gallery', 'pizzaro' ),
					// 	'target' => 'tiled_gallery',
					// 	'class'  => array(),
					// ),
					'products_list' => array(
						'label'  => esc_html__( 'Products List', 'pizzaro' ),
						'target' => 'products_list',
						'class'  => array(),
					),
					'menu_card' => array(
						'label'  => esc_html__( 'Menu Card', 'pizzaro' ),
						'target' => 'menu_card',
						'class'  => array(),
					),
					'events_list' => array(
						'label'  => esc_html__( 'Events', 'pizzaro' ),
						'target' => 'events_list',
						'class'  => array(),
					),
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
							'id'		=> '_home_v2_header_style',
							'label'		=> esc_html__( 'Header Style', 'pizzaro' ),
							'name'		=> '_home_v2[header_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Header', 'pizzaro' ),
								'v1'		=> esc_html__( 'Header v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Header v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Header v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Header v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Header v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v2['header_style'] ) ? $home_v2['header_style'] : '',
						) );

						pizzaro_wp_select( array(
							'id'		=> '_home_v2_footer_style',
							'label'		=> esc_html__( 'Footer Style', 'pizzaro' ),
							'name'		=> '_home_v2[footer_style]',
							'options'	=> array(
								''			=> esc_html__( 'Default Footer', 'pizzaro' ),
								'v1'		=> esc_html__( 'Footer v1', 'pizzaro' ),
								'v2'		=> esc_html__( 'Footer v2', 'pizzaro' ),
								'v3'		=> esc_html__( 'Footer v3', 'pizzaro' ),
								'v4'		=> esc_html__( 'Footer v4', 'pizzaro' ),
								'v5'		=> esc_html__( 'Footer v5', 'pizzaro' ),
							),
							'value'		=> isset( $home_v2['footer_style'] ) ? $home_v2['footer_style'] : '',
						) );
					?>
				</div>
				<div class="options_group">
					<?php 
						$home_v2_blocks = array(
							'sdr'	=> esc_html__( 'Slider', 'pizzaro' ),
							'plgt'	=> esc_html__( 'Products Tab', 'pizzaro' ),
							'pfo'	=> esc_html__( 'Products 4-1', 'pizzaro' ),
							'sp'	=> esc_html__( 'Product Block', 'pizzaro' ),
							'pci'	=> esc_html__( 'Product Carousel with Image', 'pizzaro' ),
							// 'tg'	=> esc_html__( 'Tiled Gallery', 'pizzaro' ),
							'pl'	=> esc_html__( 'Products List', 'pizzaro' ),
							'mc'	=> esc_html__( 'Menu Card', 'pizzaro' ),
							'ent'	=> esc_html__( 'Events', 'pizzaro' ),
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
							<?php foreach( $home_v2_blocks as $key => $home_v2_block ) : ?>
							<tr>
								<td><?php echo esc_html( $home_v2_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_home_v2_' . $key . '_animation', 'label'=> '', 'name' => '_home_v2[' . $key . '][animation]', 'value' => isset( $home_v2['' . $key . '']['animation'] ) ? $home_v2['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_home_v2_' . $key . '_priority', 'label'=> '', 'name' => '_home_v2[' . $key . '][priority]', 'value' => isset( $home_v2['' . $key . '']['priority'] ) ? $home_v2['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_home_v2_' . $key . '_is_enabled', 'label' => '', 'name' => '_home_v2[' . $key . '][is_enabled]', 'value'=> isset( $home_v2['' . $key . '']['is_enabled'] ) ? $home_v2['' . $key . '']['is_enabled'] : '', ) ); ?></td>
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
						'id' 			=> '_home_v2_sdr_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shorcode for your slider here', 'pizzaro' ),
						'name'			=> '_home_v2[sdr][shortcode]',
						'value'			=> isset( $home_v2['sdr']['shortcode'] ) ? $home_v2['sdr']['shortcode'] : '',
					) );
				?>
				</div>
			</div><!-- /#slider_block -->

			<div id="products_tab" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					$tab_counts = isset( $home_v2['plgt']['tab_counts'] ) ? $home_v2['plgt']['tab_counts'] : 8;

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_plgt_product_limit',
						'label'			=> esc_html__( 'Limit', 'pizzaro' ),
						'name'			=> '_home_v2[plgt][product_limit]',
						'value'			=> isset( $home_v2['plgt']['product_limit'] ) ? $home_v2['plgt']['product_limit'] : 5,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_plgt_product_columns',
						'label'			=> esc_html__( 'Columns', 'pizzaro' ),
						'name'			=> '_home_v2[plgt][product_columns]',
						'value'			=> isset( $home_v2['plgt']['product_columns'] ) ? $home_v2['plgt']['product_columns'] : 5,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_plgt_tab_counts',
						'label'			=> esc_html__( 'Item Counts', 'pizzaro' ),
						'name'			=> '_home_v2[plgt][tab_counts]',
						'value'			=> $tab_counts,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v2_plgt_active_tab', 
						'label' 		=>  esc_html__( 'Active Tab', 'pizzaro' ),
						'options'		=> array(
							'1'	=> '1',
							'2'	=> '2',
							'3'	=> '3',
							'4'	=> '4',
							'5'	=> '5',
							'6'	=> '6',
							'7'	=> '7',
							'8'	=> '8',
						),
						'class'			=> 'columns_select',
						'default'		=> '2',
						'name'			=> '_home_v2[plgt][default_active_tab]',
						'value'			=> isset( $home_v2['plgt']['default_active_tab'] ) ? $home_v2['plgt']['default_active_tab'] : '2',
					) );

					?>
				</div>
				<?php

					for ( $i=0; $i < $tab_counts; $i++ ) {
						echo '<div class="options_group">';

						$title = esc_html__( 'Tab #', 'pizzaro' ) . ( $i+1 );
						pizzaro_wp_legend( $title );

						pizzaro_wp_textarea_input( array( 
							'id'			=> '_home_v2_plgt_tabs_' . ( $i+1 ) . '_title', 
							'label' 		=> esc_html__( 'Title', 'pizzaro' ),
							'name'			=> '_home_v2[plgt][tabs]['.$i.'][title]',
							'value'			=> isset( $home_v2['plgt']['tabs'][$i]['title'] ) ? $home_v2['plgt']['tabs'][$i]['title'] : '',
						) );

						pizzaro_wp_wc_shortcode( array( 
							'id'			=> '_home_v2_plgt_tabs_' . ( $i+1 ) . '_content', 
							'label' 		=> esc_html__( 'Products', 'pizzaro' ),
							'default'		=> 'recent_products',
							'name'			=> '_home_v2[plgt][tabs]['.$i.'][content]',
							'value'			=> isset( $home_v2['plgt']['tabs'][$i]['content'] ) ? $home_v2['plgt']['tabs'][$i]['content'] : '',
						) );

						echo '</div>';
					}

				?>
			</div><!-- /#products_tab -->

			<div id="products_4_1" class="panel pizzaro_options_panel">

				<div class="options_group">
				<?php

					pizzaro_wp_select( array( 
						'id'			=> '_home_v2_pfo_active_tab', 
						'label' 		=>  esc_html__( 'Active Tab', 'pizzaro' ),
						'options'		=> array(
							'1'	=> '1',
							'2'	=> '2',
							'3'	=> '3'
						),
						'class'			=> 'columns_select',
						'default'		=> '2',
						'name'			=> '_home_v2[pfo][default_active_tab]',
						'value'			=> isset( $home_v2['pfo']['default_active_tab'] ) ? $home_v2['pfo']['default_active_tab'] : '2',
					) );

					?>
				</div>

				<div class="options_group">
				<?php	
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_pfo_tab_1_title', 
						'label' 		=> esc_html__( 'Tab #1 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'American Grill', 'pizzaro' ),
						'name'			=> '_home_v2[pfo][tabs][0][title]',
						'value'			=> isset( $home_v2['pfo']['tabs'][0]['title'] ) ? $home_v2['pfo']['tabs'][0]['title'] : esc_html__( 'American Grill', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v2_pfo_tab_1_content',
						'label'			=> esc_html__( 'Tab #1 Content', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v2[pfo][tabs][0][content]',
						'value'			=> isset( $home_v2['pfo']['tabs'][0]['content'] ) ? $home_v2['pfo']['tabs'][0]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_pfo_tab_2_title', 
						'label' 		=> esc_html__( 'Tab #2 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Signature Pizzas', 'pizzaro' ),
						'name'			=> '_home_v2[pfo][tabs][1][title]',
						'value'			=> isset( $home_v2['pfo']['tabs'][1]['title'] ) ? $home_v2['pfo']['tabs'][1]['title'] : esc_html__( 'Signature Pizzas', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v2_pfo_tab_2_content',
						'label'			=> esc_html__( 'Tab #2 Content', 'pizzaro' ),
						'default'		=> 'best_selling_products',
						'name'			=> '_home_v2[pfo][tabs][1][content]',
						'value'			=> isset( $home_v2['pfo']['tabs'][1]['content'] ) ? $home_v2['pfo']['tabs'][1]['content'] : '',
					) );
				?>
				</div>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_pfo_tab_3_title', 
						'label' 		=> esc_html__( 'Tab #3 Title', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'House Salads', 'pizzaro' ),
						'name'			=> '_home_v2[pfo][tabs][2][title]',
						'value'			=> isset( $home_v2['pfo']['tabs'][2]['title'] ) ? $home_v2['pfo']['tabs'][2]['title'] : esc_html__( 'House Salads', 'pizzaro' ),
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v2_pfo_tab_3_content',
						'label'			=> esc_html__( 'Tab #3 Content', 'pizzaro' ),
						'default'		=> 'sale_products',
						'name'			=> '_home_v2[pfo][tabs][2][content]',
						'value'			=> isset( $home_v2['pfo']['tabs'][2]['content'] ) ? $home_v2['pfo']['tabs'][2]['content'] : '',
					) );
				?>
				</div>
			</div><!-- /#products_4_1 -->

			<div id="product_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_textarea_input( array( 
						'id'			=> '_home_v2_sp_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v2[sp][section_title]',
						'value'			=> isset( $home_v2['sp']['section_title'] ) ? $home_v2['sp']['section_title'] : sprintf( '<h3 class="pre-title">%s</h3><h2 class="title">%s<span>%s</span></h2><h4 class="sub-title">%s</h4>', esc_html__( 'The Original', 'pizzaro' ), esc_html__( 'Chicken', 'pizzaro' ), esc_html__( 'Burger', 'pizzaro' ), esc_html__( 'Bigger & Bolder', 'pizzaro' ) ),
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_sp_product_id',
						'label'			=> esc_html__( 'Product ID', 'pizzaro' ),
						'name'			=> '_home_v2[sp][product_id]',
						'value'			=> isset( $home_v2['sp']['product_id'] ) ? $home_v2['sp']['product_id'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v2_sp_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v2[sp][bg_image]',
						'value'			=> isset( $home_v2['sp']['bg_image'] ) ? $home_v2['sp']['bg_image'] : '',
					) );

				?>
				</div>
			</div><!-- /#product_block -->

			<div id="products_carousel" class="panel pizzaro_options_panel">

				<?php pizzaro_wp_legend( esc_html__( 'Product', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_pci_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v2[pci][section_title]',
						'value'			=> isset( $home_v2['pci']['section_title'] ) ? $home_v2['pci']['section_title'] : esc_html__( 'A little Hot or Cold?', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_pci_sub_title',
						'label'			=> esc_html__( 'Sub Title', 'pizzaro' ),
						'name'			=> '_home_v2[pci][sub_title]',
						'value'			=> isset( $home_v2['pci']['sub_title'] ) ? $home_v2['pci']['sub_title'] : esc_html__( 'Pick your own treatment', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_pci_product_limit',
						'label'			=> esc_html__( 'Limit', 'pizzaro' ),
						'name'			=> '_home_v2[pci][product_limit]',
						'value'			=> isset( $home_v2['pci']['product_limit'] ) ? $home_v2['pci']['product_limit'] : 12,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_pci_product_columns',
						'label'			=> esc_html__( 'Columns', 'pizzaro' ),
						'name'			=> '_home_v2[pci][product_columns]',
						'value'			=> isset( $home_v2['pci']['product_columns'] ) ? $home_v2['pci']['product_columns'] : 3,
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v2_pci_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v2[pci][content]',
						'value'			=> isset( $home_v2['pci']['content'] ) ? $home_v2['pci']['content'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v2_pci_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_home_v2[pci][image]',
						'value'			=> isset( $home_v2['pci']['image'] ) ? $home_v2['pci']['image'] : '',
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v2_pci_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v2[pci][bg_image]',
						'value'			=> isset( $home_v2['pci']['bg_image'] ) ? $home_v2['pci']['bg_image'] : '',
					) );

				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Category', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v2_pci_cat_orderby', 
						'label' 		=> esc_html__( 'Orderby', 'pizzaro' ), 
						'name'			=> '_home_v2[pci][cat_orderby]',
						'value'			=> isset( $home_v2['pci']['cat_orderby'] ) ? $home_v2['pci']['cat_orderby'] : 'name',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v2_pci_cat_order', 
						'label' 		=> esc_html__( 'Order', 'pizzaro' ), 
						'name'			=> '_home_v2[pci][cat_order]',
						'value'			=> isset( $home_v2['pci']['cat_order'] ) ? $home_v2['pci']['cat_order'] : 'ASC',
					) );

					pizzaro_wp_checkbox( array(
						'id' 			=> '_home_v2_pci_cat_hide_empty',
						'label' 		=> esc_html__( 'Hide Empty', 'pizzaro' ),
						'name' 			=> '_home_v2[pci][cat_hide_empty]',
						'value'			=> isset( $home_v2['pci']['cat_hide_empty'] ) ? $home_v2['pci']['cat_hide_empty'] : '',
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_home_v2_pci_cat_limit', 
						'label' 		=> esc_html__( 'Limit', 'pizzaro' ), 
						'name'			=> '_home_v2[pci][cat_limit]',
						'value'			=> isset( $home_v2['pci']['cat_limit'] ) ? $home_v2['pci']['cat_limit'] : 4,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_pci_cat_slugs',
						'label'			=> esc_html__( 'Slug', 'pizzaro' ),
						'name'			=> '_home_v2[pci][cat_slugs]',
						'default'		=> '',
						'value'			=> isset( $home_v2['pci']['cat_slugs'] ) ? $home_v2['pci']['cat_slugs'] : '',
						'placeholder'	=> esc_html__( 'Enter slugs separated by comma', 'pizzaro' )
					) );

				?>
				</div>
			</div><!-- /#products_carousel -->
			
			<div id="tiled_gallery" class="panel pizzaro_options_panel">

				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_tg_columns', 
						'label' 		=>  esc_html__( 'Columns', 'pizzaro' ),
						'placeholder' 	=> esc_html__( 'Enter the number of columns to show', 'pizzaro' ),
						'name'			=> '_home_v2[tg][columns]',
						'value'			=> isset( $home_v2['tg']['columns'] ) ? $home_v2['tg']['columns'] : 3,
					) );

					pizzaro_wp_select( array( 
						'id'			=> '_home_v2_tg_type', 
						'label' 		=>  esc_html__( 'Type', 'pizzaro' ),
						'options'		=> array(
							'rectangular'	=> esc_html__( 'Rectangular', 'pizzaro' ),
							'square'		=> esc_html__( 'Square', 'pizzaro' ),
							'circle'		=> esc_html__( 'Circle', 'pizzaro' ),
						),
						'default'		=> 'rectangular',
						'name'			=> '_home_v2[tg][type]',
						'value'			=> isset( $home_v2['tg']['type'] ) ? $home_v2['tg']['type'] : 'rectangular',
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_tg_orderby',
						'label'			=> esc_html__( 'Orderby', 'pizzaro' ),
						'name'			=> '_home_v2[tg][orderby]',
						'value'			=> isset( $home_v2['tg']['orderby'] ) ? $home_v2['tg']['orderby'] : 'rand',
					) );

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_tg_include',
						'label'			=> esc_html__( 'Include', 'pizzaro' ),
						'name'			=> '_home_v2[tg][include]',
						'value'			=> isset( $home_v2['tg']['include'] ) ? $home_v2['tg']['include'] : '',
					) );

				?>
				</div>

			</div><!-- /#tiled_gallery -->

			<div id="products_list" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_pl_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v2[pl][section_title]',
						'value'			=> isset( $home_v2['pl']['section_title'] ) ? $home_v2['pl']['section_title'] : esc_html__( 'Hot Italian Pasta Week', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_pl_product_limit',
						'label'			=> esc_html__( 'Limit', 'pizzaro' ),
						'name'			=> '_home_v2[pl][product_limit]',
						'value'			=> isset( $home_v2['pl']['product_limit'] ) ? $home_v2['pl']['product_limit'] : 4,
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_pl_product_columns',
						'label'			=> esc_html__( 'Columns', 'pizzaro' ),
						'name'			=> '_home_v2[pl][product_columns]',
						'value'			=> isset( $home_v2['pl']['product_columns'] ) ? $home_v2['pl']['product_columns'] : 2,
					) );

					pizzaro_wp_wc_shortcode( array( 
						'id' 			=> '_home_v2_pl_content',
						'label'			=> esc_html__( 'Products', 'pizzaro' ),
						'default'		=> 'recent_products',
						'name'			=> '_home_v2[pl][content]',
						'value'			=> isset( $home_v2['pl']['content'] ) ? $home_v2['pl']['content'] : '',
					) );

				?>
				</div>
			</div><!-- /#products_list -->

			<div id="menu_card" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					$menu_counts = isset( $home_v2['mc']['menu_counts'] ) ? $home_v2['mc']['menu_counts'] : 6;

					pizzaro_wp_textarea_input( array( 
						'id'			=> '_home_v2_mc_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v2[mc][section_title]',
						'value'			=> isset( $home_v2['mc']['section_title'] ) ? $home_v2['mc']['section_title'] : esc_html__( 'For Breakfast', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id'			=> '_home_v2_mc_pre_title',
						'label'			=> esc_html__( 'Pre Title', 'pizzaro' ),
						'name'			=> '_home_v2[mc][pre_title]',
						'value'			=> isset( $home_v2['mc']['pre_title'] ) ? $home_v2['mc']['pre_title'] : esc_html__( 'This week local menu', 'pizzaro' ),
					) );

					pizzaro_wp_upload_image( array(
						'id'			=> '_home_v2_mc_bg_image',
						'label'			=> esc_html__( 'Background Image', 'pizzaro' ),
						'name'			=> '_home_v2[mc][bg_image]',
						'value'			=> isset( $home_v2['mc']['bg_image'] ) ? $home_v2['mc']['bg_image'] : '',
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_mc_menu_counts',
						'label'			=> esc_html__( 'Item Counts', 'pizzaro' ),
						'name'			=> '_home_v2[mc][menu_counts]',
						'value'			=> $menu_counts,
					) );

					for ( $i=0; $i < $menu_counts; $i++ ) {

						$title = esc_html__( 'Menu Card Item #', 'pizzaro' ) . ( $i+1 );
						pizzaro_wp_legend( $title );

						pizzaro_wp_text_input( array( 
							'id'			=> '_home_v2_mc_menus_' . ( $i+1 ) . '_title', 
							'label' 		=> esc_html__( 'Title', 'pizzaro' ),
							'name'			=> '_home_v2[mc][menus]['.$i.'][title]',
							'value'			=> isset( $home_v2['mc']['menus'][$i]['title'] ) ? $home_v2['mc']['menus'][$i]['title'] : '',
						) );

						pizzaro_wp_text_input( array( 
							'id'			=> '_home_v2_mc_menus_' . ( $i+1 ) . '_price', 
							'label' 		=> esc_html__( 'Price', 'pizzaro' ),
							'name'			=> '_home_v2[mc][menus]['.$i.'][price]',
							'value'			=> isset( $home_v2['mc']['menus'][$i]['price'] ) ? $home_v2['mc']['menus'][$i]['price'] : '',
						) );

						pizzaro_wp_textarea_input( array( 
							'id'			=> '_home_v2_mc_menus_' . ( $i+1 ) . '_description', 
							'label' 		=> esc_html__( 'Description', 'pizzaro' ),
							'name'			=> '_home_v2[mc][menus]['.$i.'][description]',
							'value'			=> isset( $home_v2['mc']['menus'][$i]['description'] ) ? $home_v2['mc']['menus'][$i]['description'] : '',
						) );
					}

				?>
				</div>
			</div><!-- /#menu_card -->

			<div id="events_list" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php

					pizzaro_wp_text_input( array( 
						'id'			=> '_home_v2_ent_section_title',
						'label'			=> esc_html__( 'Section Title', 'pizzaro' ),
						'name'			=> '_home_v2[ent][section_title]',
						'value'			=> isset( $home_v2['ent']['section_title'] ) ? $home_v2['ent']['section_title'] : esc_html__( 'UPCOMING EVENTS', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array(
						'id'			=> '_home_v2_ent_pre_title',
						'label'			=> esc_html__( 'Pre Title', 'pizzaro' ),
						'name'			=> '_home_v2[ent][pre_title]',
						'value'			=> isset( $home_v2['ent']['pre_title'] ) ? $home_v2['ent']['pre_title'] : esc_html__( 'DONT MISS ANY OF', 'pizzaro' ),
					) );

				?>
				</div>
			</div><!-- /#events_list -->
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_home_v2'] ) ) {
			$clean_home_v2_options = pizzaro_clean_kses_post( $_POST['_home_v2'] );
			update_post_meta( $post_id, '_home_v2_options',  serialize( $clean_home_v2_options ) );
		}	
	}
}