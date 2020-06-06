<?php
/**
 * About v1 Metabox
 *
 * Displays the about v1 meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_About Class.
 */
class Pizzaro_Meta_Box_About {

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

		if ( $template_file !== 'template-aboutpage.php' ) {
			return;
		}

		self::output_about( $post );
	}

	private static function output_about( $post ) {

		$about = pizzaro_get_about_meta();

		?>
		<div class="panel-wrap meta-box-about">
			<ul class="about_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_about_data_tabs', array(
					'general' => array(
						'label'  => esc_html__( 'General', 'pizzaro' ),
						'target' => 'general_block',
						'class'  => array(),
					),
					'features' => array(
						'label'  => esc_html__( 'Features', 'pizzaro' ),
						'target' => 'features_block',
						'class'  => array(),
					),
					'process' => array(
						'label'  => esc_html__( 'Basics', 'pizzaro' ),
						'target' => 'basics_block',
						'class'  => array(),
					),
					'brands' => array(
						'label'  => esc_html__( 'Brands', 'pizzaro' ),
						'target' => 'brands_block',
						'class'  => array(),
					)
				) );
				foreach ( $product_data_tabs as $key => $tab ) {
					?><li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
						<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
					</li><?php
				}
				do_action( 'pizzaro_about_write_panel_tabs' );
			?>
			</ul>
			<div id="general_block" class="panel pizzaro_options_panel">

				<div class="options_group">
					<?php 
						$about_blocks = array(
							'fl'	=> esc_html__( 'Features', 'pizzaro' ),
							'bas'	=> esc_html__( 'Basics', 'pizzaro' ),
							'brs'	=> esc_html__( 'Brands', 'pizzaro' ),
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
							<?php foreach( $about_blocks as $key => $about_block ) : ?>
							<tr>
								<td><?php echo esc_html( $about_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_about_' . $key . '_animation', 'label'=> '', 'name' => '_about[' . $key . '][animation]', 'value' => isset( $about['' . $key . '']['animation'] ) ? $about['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_about_' . $key . '_priority', 'label'=> '', 'name' => '_about[' . $key . '][priority]', 'value' => isset( $about['' . $key . '']['priority'] ) ? $about['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_about_' . $key . '_is_enabled', 'label' => '', 'name' => '_about[' . $key . '][is_enabled]', 'value'=> isset( $about['' . $key . '']['is_enabled'] ) ? $about['' . $key . '']['is_enabled'] : '', ) ); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

			</div><!-- /#general_block -->
			
			<div id="features_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id' 			=> '_about_fl_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title for features', 'pizzaro' ),
						'name'			=> '_about[fl][title]',
						'value'			=> isset( $about['fl']['title'] ) ? $about['fl']['title'] : esc_html( 'About Us', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_about_fl_subtitle', 
						'label' 		=> esc_html__( 'Subtitle', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the subtitle for features', 'pizzaro' ),
						'name'			=> '_about[fl][subtitle]',
						'value'			=> isset( $about['fl']['subtitle'] ) ? $about['fl']['subtitle'] : esc_html( 'We are a second-generation family business established in 1972', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_about_fl_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shortcode for your features here', 'pizzaro' ),
						'name'			=> '_about[fl][shortcode]',
						'value'			=> isset( $about['fl']['shortcode'] ) ? $about['fl']['shortcode'] : '',
					) );
				?>
				</div>
			</div><!-- /#features_block -->

			<div id="basics_block" class="panel pizzaro_options_panel">

				<?php pizzaro_wp_legend( esc_html__( 'Banner', 'pizzaro' ) ); ?>
				
				<div class="options_group">
				<?php
					pizzaro_wp_upload_image( array(
						'id'			=> '_about_bas_image',
						'label'			=> esc_html__( 'Image', 'pizzaro' ),
						'name'			=> '_about[bas][image]',
						'value'			=> isset( $about['bas']['image'] ) ? $about['bas']['image'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Block 1', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_about_bas_1_block',
						'label' 		=> esc_html__( 'Content', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the content here', 'pizzaro' ),
						'name'			=> '_about[bas][blocks][block_1]',
						'value'			=> isset( $about['bas']['blocks']['block_1'] ) ? $about['bas']['blocks']['block_1'] : wp_kses_post( '<h2>' . esc_html__( 'Pizza Basics', 'pizzaro' ) . '</h2>' ),
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Block 2', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_about_bas_2_block',
						'label' 		=> esc_html__( 'Content', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the content here', 'pizzaro' ),
						'name'			=> '_about[bas][blocks][block_2]',
						'value'			=> isset( $about['bas']['blocks']['block_2'] ) ? $about['bas']['blocks']['block_2'] : wp_kses_post( '<p>' . esc_html__( 'Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum.  Aliquam non tincidunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat.', 'pizzaro' ) . '</p>' ),
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Block 3', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php
					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_about_bas_3_block',
						'label' 		=> esc_html__( 'Content', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the content here', 'pizzaro' ),
						'name'			=> '_about[bas][blocks][block_3]',
						'value'			=> isset( $about['bas']['blocks']['block_3'] ) ? $about['bas']['blocks']['block_3'] : wp_kses_post( '<p>' . esc_html__( 'Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum.  Aliquam non tincidunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat.', 'pizzaro' ) . '</p>' ),
					) );
				?>
				</div>
				
			</div><!-- /#basics_block -->

			<div id="brands_block" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id' 			=> '_about_brs_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title', 'pizzaro' ),
						'name'			=> '_about[brs][title]',
						'value'			=> isset( $about['brs']['title'] ) ? $about['brs']['title'] : esc_html__( 'We delivering Pizzas for', 'pizzaro' ),
					) );

					pizzaro_wp_text_input( array( 
						'id' 			=> '_about_brs_image_ids', 
						'label' 		=> esc_html__( 'Image Ids', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the image ids for your brands', 'pizzaro' ),
						'name'			=> '_about[brs][image_ids]',
						'value'			=> isset( $about['brs']['image_ids'] ) ? $about['brs']['image_ids'] : '',
					) );
				?>
				</div>
			</div><!-- /#brands_block -->
		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_about'] ) ) {
			$clean_about_options = pizzaro_clean_kses_post( $_POST['_about'] );
			update_post_meta( $post_id, '_about_options',  addslashes( json_encode( $clean_about_options ) ) );
		}	
	}
}