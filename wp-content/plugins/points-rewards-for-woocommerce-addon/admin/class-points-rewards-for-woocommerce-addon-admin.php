<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Points_Rewards_For_Woocommerce_Addon
 * @subpackage Points_Rewards_For_Woocommerce_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Points_Rewards_For_Woocommerce_Addon
 * @subpackage Points_Rewards_For_Woocommerce_Addon/admin
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Points_Rewards_For_Woocommerce_Addon_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/points-rewards-for-woocommerce-addon-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/points-rewards-for-woocommerce-addon-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add a visit log section on admin panel. 
	 *
	 * @since    1.0.0
	 */
	public function mwb_wpr_add_admin_point_log_vising_link( $point_log ){
		if ( array_key_exists( 'recurring_points', $point_log ) ) {
			?>
			<div class="mwb_wpr_slide_toggle">
				<p class="mwb_wpr_view_log_notice mwb_wpr_common_slider" ><?php esc_html_e( 'Recurring Points', 'points-rewards-for-woocommerce' ); ?>
				  <a class ="mwb_wpr_open_toggle"  href="javascript:;"></a>
			 	</p>
			 	<div class="mwb_wpr_points_view"> 
					<table class = "form-table mwp_wpr_settings mwb_wpr_points_view mwb_wpr_common_table">
					  	<thead>
							<tr valign="top">
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Date & Time', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Point Status', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
							</tr>
						</thead>
						<?php
						foreach ( $point_log['recurring_points'] as $key => $value ) {
							?>
							<tr valign="top">
								<td class="forminp forminp-text"><?php echo esc_html( $value['date'] ); ?></td>
								<td class="forminp forminp-text"><?php echo '+' . esc_html( $value['recurring_points'] ); ?></td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
			</div>
			<?php
		}
		if ( array_key_exists( 'visit_link', $point_log ) ) {
			?>
			<div class="mwb_wpr_slide_toggle">
				<p class="mwb_wpr_view_log_notice mwb_wpr_common_slider" ><?php esc_html_e( 'Points earned via Visiting Link', 'points-rewards-for-woocommerce' ); ?>
				  <a class ="mwb_wpr_open_toggle"  href="javascript:;"></a>
			 	</p>
			 	<div class="mwb_wpr_points_view"> 
					<table class = "form-table mwp_wpr_settings mwb_wpr_points_view mwb_wpr_common_table">
					  	<thead>
							<tr valign="top">
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Date & Time', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Point Status', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Visited Links', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
							</tr>
						</thead>
						<?php
						foreach ( $point_log['visit_link'] as $key => $value ) {
							?>
							<tr valign="top">
								<td class="forminp forminp-text"><?php echo esc_html( $value['date'] ); ?></td>
								<td class="forminp forminp-text"><?php echo '+' . esc_html( $value['visit_link'] ); ?></td>
								<td class="forminp forminp-text"><?php echo esc_html( $value['href'] ); ?></td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
			</div>
			<?php
		}
		if ( array_key_exists( 'custom_points', $point_log ) ) {
			?>
			<div class="mwb_wpr_slide_toggle">
				<p class="mwb_wpr_view_log_notice mwb_wpr_common_slider" ><?php esc_html_e( 'Additional Points', 'points-rewards-for-woocommerce' ); ?>
				  <a class ="mwb_wpr_open_toggle"  href="javascript:;"></a>
			 	</p>
			 	<div class="mwb_wpr_points_view"> 
					<table class = "form-table mwp_wpr_settings mwb_wpr_points_view mwb_wpr_common_table">
					  	<thead>
							<tr valign="top">
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Date & Time', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Point Status', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
								<th scope="row" class="titledesc">
									<span class="nobr"><?php echo esc_html__( 'Reason', 'points-rewards-for-woocommerce' ); ?></span>
								</th>
							</tr>
						</thead>
						<?php
						foreach ( $point_log['custom_points'] as $key => $value ) {
							?>
							<tr valign="top">
								<td class="forminp forminp-text"><?php echo esc_html( $value['date'] ); ?></td>
								<td class="forminp forminp-text"><?php echo '+' . esc_html( $value['custom_points'] ); ?></td>
								<td class="forminp forminp-text"><?php echo esc_html( $value['reason'] ); ?></td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
			</div>
			<?php
		}	
	}

	/**
	 * Add settings to get input for recurring points. 
	 *
	 * @since    1.0.0
	 */
	public function mwb_wpra_assign_recurring_points( $user ){
		$mwb_wpra_assigned_points = get_user_meta( $user->ID, 'mwb_wpra_assigned_points', true );
		$mwb_wpra_reset_period = get_user_meta( $user->ID, 'mwb_wpra_reset_period', true );
		$mwb_wpra_enable_recurring_points = get_user_meta( $user->ID, 'mwb_wpra_enable_recurring_points', true );
		?>
		<h3><?php _e('Set Recurring Points','coupon-referral-program');?></h3>
		<table class="form-table">
			<tr>
				<th><label for="mwb_wpra_assigned_points"><?php _e('Set Points','coupon-referral-program');?></label></th>
				<td>
					<input id="mwb_wpra_assigned_points" class="mwb_crp_refree" name="mwb_wpra_assigned_points" type="number" min="0" value="<?php echo $mwb_wpra_assigned_points;?>" />
				</td>
			</tr>
			<tr>
				<th><label for="mwb_wpra_reset_period"><?php _e('Reset After','coupon-referral-program');?></label></th>
				<td>
					<input id="mwb_wpra_reset_period" class="mwb_crp_refree" name="mwb_wpra_reset_period" type="number" min="0" value="<?php echo $mwb_wpra_reset_period;?>" /><span><?php _e('Days','coupon-referral-program');?></span>
				</td>
			</tr>
		</table>
		<?php 

	}

	/**
	 * Save settings for recurring user points. 
	 *
	 * @since    1.0.0
	 */
	public function mwb_wpra_save_assigned_recurring_points( $user_id ) {
		if(isset($_POST['mwb_wpra_reset_period'])){
			update_user_meta( $user_id,'mwb_wpra_reset_period',$_POST['mwb_wpra_reset_period'] );
		}
		if ( ! isset( $_POST['mwb_wpra_enable_recurring_points'] ) ) {
			$_POST['mwb_wpra_enable_recurring_points'] = 'off';
		}
		update_user_meta( $user_id,'mwb_wpra_enable_recurring_points',$_POST['mwb_wpra_enable_recurring_points'] );

		$timestamp = get_user_meta( $user_id, 'recurring_points_expiration', true );
		if ( '' === $timestamp ) {
			$current_time = current_time( 'timestamp' );
			$reset_period = strtotime( '+'.$_POST['mwb_wpra_reset_period'].'days', $current_time );
			update_user_meta( $user_id,'recurring_points_expiration', $reset_period );
			update_user_meta( $user_id, 'mwb_wpr_points', $_POST['mwb_wpra_assigned_points'] );
			$this->mwb_wpra_recurring_point_log( $user_id, 'recurring_points', $_POST['mwb_wpra_assigned_points'] );
		}
		$args = array( $user_id );
		if ( ! wp_next_scheduled ( 'mwb_wpra_recurring_points_cron_schedule' ) ) {
			wp_schedule_event( time(), 'daily', 'mwb_wpra_recurring_points_cron_schedule', $args );
		}
	}

	public function mwb_wpra_recurring_points_cron_schedule_expiration( $user_id ){
		$response['result'] = false;
		$current_time = current_time( 'timestamp' );
		$timestamp = get_user_meta( $user_id, 'recurring_points_expiration', true );
		$timediff = $timestamp - $current_time;
		if( $timediff > 0 ) {
			$response['result'] = false;
			echo json_encode($response);
			die;
		}
		else
		{
			$user_total_points = get_user_meta( $user->ID, 'mwb_wpra_assigned_points', true );
			update_user_meta( $user_id, 'mwb_wpr_points', $user_total_points );
			$response['result'] = true;
			echo json_encode($response);
			die;
		}	
	}

	public function mwb_wpra_recurring_point_log( $user_id, $type, $points ){
		$today_date = date_i18n( 'Y-m-d h:i:sa' );
		if ( 'recurring_points' == $type ) {
			$mwb_points_sharing = get_user_meta( $user_id, 'points_details', true );
			if ( isset( $mwb_points_sharing[ $type ] ) && ! empty( $mwb_points_sharing[ $type ] ) ) {
				$custom_array = array(
					$type => $points,
					'date' => $today_date,
				);
				$mwb_points_sharing[ $type ][] = $custom_array;
			} else {
				if ( ! is_array( $mwb_points_sharing ) ) {
					$mwb_points_sharing = array();
				}
				$mwb_points_sharing[ $type ][] = array(
					$type => $points,
					'date' => $today_date,
				);
			}
			/*Update the user meta for the points details*/
			update_user_meta( $user_id, 'points_details', $mwb_points_sharing );
		}
	}
}
