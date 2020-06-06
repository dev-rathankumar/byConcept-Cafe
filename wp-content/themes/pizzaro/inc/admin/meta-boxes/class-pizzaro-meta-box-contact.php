<?php
/**
 * Contact Metabox
 *
 * Displays the contact meta box, tabbed, with several panels covering price, stock etc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pizzaro_Meta_Box_Contact Class.
 */
class Pizzaro_Meta_Box_Contact {

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

		if ( $template_file !== 'template-contactpage.php' ) {
			return;
		}

		self::output_contact( $post );
	}

	private static function output_contact( $post ) {

		$contact = pizzaro_get_contact_meta();

		?>
		<div class="panel-wrap meta-box-contact">
			<ul class="contact_data_tabs pz-tabs">
			<?php
				$product_data_tabs = apply_filters( 'pizzaro_contact_data_tabs', array(
					'general' => array(
						'label'  => esc_html__( 'General', 'pizzaro' ),
						'target' => 'general_block',
						'class'  => array(),
					),
					'contact_map' => array(
						'label'  => esc_html__( 'Contact Map', 'pizzaro' ),
						'target' => 'contact_map',
						'class'  => array(),
					),
					'contact_header' => array(
						'label'  => esc_html__( 'Contact Header', 'pizzaro' ),
						'target' => 'contact_header',
						'class'  => array(),
					),
					'contact_form_with_address' => array(
						'label'  => esc_html__( 'Contact Form With Address', 'pizzaro' ),
						'target' => 'contact_form_with_address',
						'class'  => array(),
					),
				) );
				foreach ( $product_data_tabs as $key => $tab ) {
					?><li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
						<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
					</li><?php
				}
				do_action( 'pizzaro_contact_write_panel_tabs' );
			?>
			</ul>
			<div id="general_block" class="panel pizzaro_options_panel">

				<div class="options_group">
					<?php 
						$contact_blocks = array(
							'cma'	=> esc_html__( 'Contact Map', 'pizzaro' ),
							'chr'	=> esc_html__( 'Contact Header', 'pizzaro' ),
							'cfa'	=> esc_html__( 'Contact Form with Address', 'pizzaro' ),
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
							<?php foreach( $contact_blocks as $key => $contact_block ) : ?>
							<tr>
								<td><?php echo esc_html( $contact_block ); ?></td>
								<td><?php pizzaro_wp_animation_dropdown( array(  'id' => '_contact_' . $key . '_animation', 'label'=> '', 'name' => '_contact[' . $key . '][animation]', 'value' => isset( $contact['' . $key . '']['animation'] ) ? $contact['' . $key . '']['animation'] : '', )); ?></td>
								<td><?php pizzaro_wp_text_input( array(  'id' => '_contact_' . $key . '_priority', 'label'=> '', 'name' => '_contact[' . $key . '][priority]', 'value' => isset( $contact['' . $key . '']['priority'] ) ? $contact['' . $key . '']['priority'] : 10, ) ); ?></td>
								<td><?php pizzaro_wp_checkbox( array( 'id' => '_contact_' . $key . '_is_enabled', 'label' => '', 'name' => '_contact[' . $key . '][is_enabled]', 'value'=> isset( $contact['' . $key . '']['is_enabled'] ) ? $contact['' . $key . '']['is_enabled'] : '', ) ); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

			</div><!-- /#general_block -->

			<div id="contact_map" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php
					pizzaro_wp_legend( esc_html__( 'Please add embed contact map in theme options panel.', 'pizzaro' ) );
				?>
				</div>
			</div><!-- /#contact_map -->

			<div id="contact_header" class="panel pizzaro_options_panel">
				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id' 			=> '_contact_chr_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title', 'pizzaro' ),
						'name'			=> '_contact[chr][title]',
						'value'			=> isset( $contact['chr']['title'] ) ? $contact['chr']['title'] : esc_html__( 'Contact Us', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_contact_chr_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the description', 'pizzaro' ),
						'name'			=> '_contact[chr][description]',
						'value'			=> isset( $contact['chr']['description'] ) ? $contact['chr']['description'] : esc_html__( 'We are a second-generation family business established in 1972', 'pizzaro' ),
					) );
				?>
				</div>
			</div><!-- /#contact_header -->

			<div id="contact_form_with_address" class="panel pizzaro_options_panel">
				<?php pizzaro_wp_legend( esc_html__( 'Form Shortcode', 'pizzaro' ) ); ?>

				<div class="options_group">
				<?php 
					pizzaro_wp_text_input( array( 
						'id' 			=> '_contact_cfa_form_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title for form', 'pizzaro' ),
						'name'			=> '_contact[cfa][form][title]',
						'value'			=> isset( $contact['cfa']['form']['title'] ) ? $contact['cfa']['form']['title'] : esc_html__( 'Leave us a Message', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_contact_cfa_form_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the description for form', 'pizzaro' ),
						'name'			=> '_contact[cfa][form][description]',
						'value'			=> isset( $contact['cfa']['form']['description'] ) ? $contact['cfa']['form']['description'] : esc_html__( 'Aenean massa diam, viverra vitae luctus sed, gravida eget est. Etiam nec ipsum porttitor, consequat libero eu, dignissim eros. Nulla auctor lacinia enim id mollis.', 'pizzaro' ),
					) );
					pizzaro_wp_text_input( array( 
						'id' 			=> '_contact_cfa_form_shortcode', 
						'label' 		=> esc_html__( 'Shortcode', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the shortcode for your form here', 'pizzaro' ),
						'name'			=> '_contact[cfa][form][shortcode]',
						'value'			=> isset( $contact['cfa']['form']['shortcode'] ) ? $contact['cfa']['form']['shortcode'] : '',
					) );
				?>
				</div>

				<?php pizzaro_wp_legend( esc_html__( 'Address Block', 'pizzaro' ) ); ?>

				<div class="options_group">
					<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_contact_cfa_address_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title for address', 'pizzaro' ),
						'name'			=> '_contact[cfa][address][title]',
						'value'			=> isset( $contact['cfa']['address']['title'] ) ? $contact['cfa']['address']['title'] : esc_html__( 'Our Address', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_contact_cfa_address_description', 
						'label' 		=> esc_html__( 'Description', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the description for address', 'pizzaro' ),
						'name'			=> '_contact[cfa][address][description]',
						'value'			=> isset( $contact['cfa']['address']['description'] ) ? $contact['cfa']['address']['description'] : sprintf( '%s<br>%s<br>%s', esc_html__( '17 Princess Road London, Greater London NW1 8JR, UK', 'pizzaro' ), esc_html__( 'Support (+800) 856 800 604', 'pizzaro' ), esc_html__( 'E-mail: info@pizzaro.com', 'pizzaro' ) ),
					) );
					?>
					</div>

					<?php pizzaro_wp_legend( esc_html__( 'Addresses Block', 'pizzaro' ) ); ?>
					<div class="options_group">
					<?php
					pizzaro_wp_text_input( array( 
						'id' 			=> '_contact_cfa_address_addresses_1_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title for address', 'pizzaro' ),
						'name'			=> '_contact[cfa][address][addresses][0][title]',
						'value'			=> isset( $contact['cfa']['address']['addresses'][0]['title'] ) ? $contact['cfa']['address']['addresses'][0]['title'] : esc_html__( 'Opening Hours', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_contact_cfa_address_addresses_1_address', 
						'label' 		=> esc_html__( 'Address', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the address for addresss', 'pizzaro' ),
						'name'			=> '_contact[cfa][address][addresses][0][address]',
						'value'			=> isset( $contact['cfa']['address']['addresses'][0]['address'] ) ? $contact['cfa']['address']['addresses'][0]['address'] : sprintf( '<ul><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">%s</span></li></ul>', esc_html__( 'Monday', 'pizzaro' ), esc_html__( 'Tuesday', 'pizzaro' ), esc_html__( 'Wednesday', 'pizzaro' ), esc_html__( 'Thursday', 'pizzaro' ), esc_html__( 'Friday', 'pizzaro' ), esc_html__( 'Saturday', 'pizzaro' ), esc_html__( 'Sunday', 'pizzaro' ), esc_html__( 'Closed', 'pizzaro' ) ),
					) );
					pizzaro_wp_text_input( array( 
						'id' 			=> '_contact_cfa_address_addresses_2_title', 
						'label' 		=> esc_html__( 'Title', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the title for address', 'pizzaro' ),
						'name'			=> '_contact[cfa][address][addresses][1][title]',
						'value'			=> isset( $contact['cfa']['address']['addresses'][1]['title'] ) ? $contact['cfa']['address']['addresses'][1]['title'] : esc_html__( 'Careers.', 'pizzaro' ),
					) );

					pizzaro_wp_textarea_input( array( 
						'id' 			=> '_contact_cfa_address_addresses_2_address', 
						'label' 		=> esc_html__( 'Address', 'pizzaro' ), 
						'placeholder' 	=> esc_html__( 'Enter the address for addresss', 'pizzaro' ),
						'name'			=> '_contact[cfa][address][addresses][1][address]',
						'value'			=> isset( $contact['cfa']['address']['addresses'][1]['address'] ) ? $contact['cfa']['address']['addresses'][1]['address'] : sprintf( '<p class="inner-right-md">%s<a href="mailto:contact@yourstore.com">%s</a></p>', esc_html__( 'If you are interested in employment opportunities at Pizzaro, please email us: ', 'pizzaro' ), 'contact@yourstore.com' ),
					) );
					?>
				</div>
			</div><!-- /#contact_form_with_address -->

		</div>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( isset( $_POST['_contact'] ) ) {
			$clean_contact_options = pizzaro_clean_kses_post( $_POST['_contact'] );
			update_post_meta( $post_id, '_contact_options',  addslashes( json_encode( $clean_contact_options ) ) );
		}	
	}
}