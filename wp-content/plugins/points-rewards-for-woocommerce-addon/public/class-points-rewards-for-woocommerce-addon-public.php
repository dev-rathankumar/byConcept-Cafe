<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Points_Rewards_For_Woocommerce_Addon
 * @subpackage Points_Rewards_For_Woocommerce_Addon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Points_Rewards_For_Woocommerce_Addon
 * @subpackage Points_Rewards_For_Woocommerce_Addon/public
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */

class Points_Rewards_For_Woocommerce_Addon_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Points_Rewards_For_Woocommerce_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Points_Rewards_For_Woocommerce_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/points-rewards-for-woocommerce-addon-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Points_Rewards_For_Woocommerce_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Points_Rewards_For_Woocommerce_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$mwb_wpr_addon = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'mwb_wpra_nonce' => wp_create_nonce( 'mwb-wpra-verify-nonce' ),
		);

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/points-rewards-for-woocommerce-addon-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'mwb_wpr_addon', $mwb_wpr_addon );
		wp_enqueue_script( $this->plugin_name );
	}
	public function mwb_wpra_add_point_on_visiting_link( ){
		check_ajax_referer( 'mwb-wpra-verify-nonce', 'mwb_nonce' );
		$visit_points = isset( $_POST['points'] ) ? intval( $_POST['points'] ) : 0;
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
		$href = isset( $_POST['href'] ) ? $_POST['href'] : '';
		$this->mwb_wpra_additional_update_points_log( $user_id, 'visit_link', $visit_points, $href );
	}
	public function mwb_wpra_additional_update_points_log( $user_id, $type, $points, $href ){
		$today_date = date_i18n( 'Y-m-d h:i:sa' );
		if ( 'visit_link' == $type ) {
			$mwb_points_sharing = get_user_meta( $user_id, 'points_details', true );
			$already_visited = $this->mwb_wpr_check_link_already_visited( $user_id, $href, $mwb_points_sharing );
			if( ! $already_visited ){
				if ( isset( $mwb_points_sharing[ $type ] ) && ! empty( $mwb_points_sharing[ $type ] ) ) {
					$custom_array = array(
						$type => $points,
						'date' => $today_date,
						'href' => $href,
					);
					$mwb_points_sharing[ $type ][] = $custom_array;
				} else {
					if ( ! is_array( $mwb_points_sharing ) ) {
						$mwb_points_sharing = array();
					}
					$mwb_points_sharing[ $type ][] = array(
						$type => $points,
						'date' => $today_date,
						'href' => $href,
					);
				}
				/*Update the user meta for the points details*/
				$mwb_wpr_args = array( $user_id, $mwb_points_sharing, $points);
				//as_schedule_single_action( time() + ( 60*60 ), 'update_user_points_for_vising_link', $mwb_wpr_args );
				wp_schedule_single_event( time() + ( 60*60 ), 'update_user_points_for_vising_link', $mwb_wpr_args );
			}
		}
	}
	public function update_user_points_for_vising_link_after_one_hour( $user_id, $mwb_points_sharing, $points ) {
		update_user_meta( $user_id, 'points_details', $mwb_points_sharing );
		$user_total_points = (int)get_user_meta( $user_id, 'mwb_wpr_points', true );
		if ( empty( $user_total_points ) ) {
			$user_total_points = 0;
		}
		$user_total_points = $user_total_points + $points;
		update_user_meta( $user_id, 'mwb_wpr_points', $user_total_points );
	}
	public function mwb_wpr_check_link_already_visited( $user_id, $href, $mwb_points_sharing ){
		$already_visited = false;
		if( array_key_exists('visit_link', $mwb_points_sharing ) && ! empty( $mwb_points_sharing['visit_link'] ) ) {
			$visited_link_data = $mwb_points_sharing['visit_link'];
			$visited_href = array_column($visited_link_data, 'href');
			$already_visited = in_array( $href , $visited_href );
		}
		return $already_visited;
	}
	public function mwb_wpr_additional_points_tab_for_visiting_link( $point_log ){
		if ( array_key_exists( 'recurring_points', $point_log ) ) {
			?>
			<div class="mwb_wpr_slide_toggle">
				<p class="mwb_wpr_view_log_notice mwb_wpr_common_slider"><?php esc_html_e( 'Recurring Points', 'points-rewards-for-woocommerce' ); ?><a class ="mwb_wpr_open_toggle"  href="javascript:;"></a></p>
				<table class="mwb_wpr_common_table">
					<thead>
						<tr>
							<th class="view-log-Date">
								<span class="nobr"><?php echo esc_html__( 'Date', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
							<th class="view-log-Status">
								<span class="nobr"><?php echo esc_html__( 'Point Status', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
						</tr>
					</thead>
					<?php
					foreach ( $point_log['recurring_points'] as $key => $value ) {
						?>
						<tr>
							<td><?php echo esc_html( mwb_wpr_set_the_wordpress_date_format( $value['date'] ) ); ?></td>
							<td><?php echo '+' . esc_html( $value['recurring_points'] ); ?></td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
			<?php
		}
		if ( array_key_exists( 'visit_link', $point_log ) ) {
			?>
			<div class="mwb_wpr_slide_toggle">
				<p class="mwb_wpr_view_log_notice mwb_wpr_common_slider"><?php esc_html_e( 'Points earned via Visiting Link', 'points-rewards-for-woocommerce' ); ?><a class ="mwb_wpr_open_toggle"  href="javascript:;"></a></p>
				<table class="mwb_wpr_common_table">
					<thead>
						<tr>
							<th class="view-log-Date">
								<span class="nobr"><?php echo esc_html__( 'Date', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
							<th class="view-log-Status">
								<span class="nobr"><?php echo esc_html__( 'Point Status', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
							<th class="view-log-Activity">
								<span class="nobr"><?php echo esc_html__( 'Visited Links', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
						</tr>
					</thead>
					<?php
					foreach ( $point_log['visit_link'] as $key => $value ) {
						?>
						<tr>
							<td><?php echo esc_html( mwb_wpr_set_the_wordpress_date_format( $value['date'] ) ); ?></td>
							<td><?php echo '+' . esc_html( $value['visit_link'] ); ?></td>
							<td><?php echo esc_html( $value['href'] ); ?></td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
			<?php
		}		
		if ( array_key_exists( 'custom_points', $point_log ) ) {
			?>
			<div class="mwb_wpr_slide_toggle">
				<p class="mwb_wpr_view_log_notice mwb_wpr_common_slider"><?php esc_html_e( 'Additional Points', 'points-rewards-for-woocommerce' ); ?><a class ="mwb_wpr_open_toggle"  href="javascript:;"></a></p>
				<table class="mwb_wpr_common_table">
					<thead>
						<tr>
							<th class="view-log-Date">
								<span class="nobr"><?php echo esc_html__( 'Date', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
							<th class="view-log-Status">
								<span class="nobr"><?php echo esc_html__( 'Point Status', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
							<th class="view-log-Status">
								<span class="nobr"><?php echo esc_html__( 'Reason', 'points-rewards-for-woocommerce' ); ?></span>
							</th>
						</tr>
					</thead>
					<?php
					foreach ( $point_log['custom_points'] as $key => $value ) {
						?>
						<tr>
							<td><?php echo esc_html( mwb_wpr_set_the_wordpress_date_format( $value['date'] ) ); ?></td>
							<td><?php echo '+' . esc_html( $value['custom_points'] ); ?></td>
							<td><?php echo esc_html( $value['reason'] ); ?></td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
			<?php
		}	
	}
	public function mwb_register_route() {
		register_rest_route('user_details/v1','show_user_data',array(
			'methods' => 'GET',
			'callback' => array( $this,'mwb_wpra_return_user_data')
			)
		);
		register_rest_route('update_user_details/v1','get_user_data',array(
			'methods' => 'POST',
			'callback' => array( $this,'mwb_wpra_update_user_points')
			)
		);
	}

	public function mwb_wpra_return_user_data( $request ){
		$user_id = $request->get_param('user_id');
		$user_total_points = (int)get_user_meta( $user_id, 'mwb_wpr_points', true );
		$points_details = get_user_meta( $user_id, 'points_details', true );
		$user_data = array(
			'user_id' => $user_id ,
			'points_log' => $points_details,
			'total_points' => $user_total_points,
		); 
		if( !empty( $user_data ) ){
			return new WP_REST_Response( $user_data, 200 );	
		}
		else{
			return new WP_Error( 'No Details', 'There is no record of any point by this user', array('status' => 404) );
		}
	}

	public function mwb_wpra_update_user_points( $request ){
		$user_id = $request->get_param('user_id');
		$points_to_add = $request->get_param('points_to_add');
		$reason_for_points = $request->get_param('reason_for_points');
		$this->mwb_wpra_update_points_in_log( $user_id, 'custom_points', $reason_for_points, $points_to_add );

	}

	public function mwb_wpra_update_points_in_log( $user_id, $type, $reason_for_points, $points ){
		$today_date = date_i18n( 'Y-m-d h:i:sa' );
		if ( 'custom_points' === $type ) {
			$mwb_points_sharing = get_user_meta( $user_id, 'points_details', true );
			if ( isset( $mwb_points_sharing[ $type ] ) && ! empty( $mwb_points_sharing[ $type ] ) ) {
					$custom_array = array(
						$type => $points,
						'date' => $today_date,
						'reason' => $reason_for_points,
					);
					$mwb_points_sharing[ $type ][] = $custom_array;
				} else {
					if ( ! is_array( $mwb_points_sharing ) ) {
						$mwb_points_sharing = array();
					}
					$mwb_points_sharing[ $type ][] = array(
						$type => $points,
						'date' => $today_date,
						'reason' => $reason_for_points,
					);
				}
			/*Update the user meta for the points details*/
			update_user_meta( $user_id, 'points_details', $mwb_points_sharing );
			$user_total_points = (int)get_user_meta( $user_id, 'mwb_wpr_points', true );
			if ( empty( $user_total_points ) ) {
				$user_total_points = 0;
			}
			$user_total_points = $user_total_points + $points;
			update_user_meta( $user_id, 'mwb_wpr_points', $user_total_points );
				
		}
	}
}
