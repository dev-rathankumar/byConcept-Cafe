<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ultimate_Woocommerce_Points_And_Rewards
 * @subpackage Ultimate_Woocommerce_Points_And_Rewards/admin
 */

register_widget( 'Mwb_Wpr_Custom_Widget' );

/**
 * This class will add the widget in the woocommerce.
 *
 * @package    Ultimate_Woocommerce_Points_And_Rewards
 * @subpackage Ultimate_Woocommerce_Points_And_Rewards/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Mwb_Wpr_Custom_Widget extends WP_Widget {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$widget_options = array(
			'classname'   => 'mwb_wpr_custom_widget',
			'description' => 'My Point',
		);
		parent::__construct( 'mwb_wpr_custom_widget', 'Points and Rewards', $widget_options );
	}

	/**
	 * Custom Widget
	 *
	 * @name  widget
	 * @param array $args array of the arguments.
	 * @param array $instance instance of the class.
	 * @since    1.0.0
	 */
	public function widget( $args, $instance ) {

		extract( $args );//phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		$title                = apply_filters( 'widget_title', $instance['title'] );
		$mypoint              = isset( $instance['mypoint'] ) && ! empty( $instance['mypoint'] ) ? $instance['mypoint'] : __( 'Total Point:', 'ultimate-woocommerce-points-and-rewards' );
		$mycurrentlevel       = isset( $instance['mycurrentlevel'] ) && ! empty( $instance['mypoint'] ) ? $instance['mycurrentlevel'] : __( 'User Level:', 'ultimate-woocommerce-points-and-rewards' );
		$membershipexpiration = isset( $instance['membershipexpiration'] ) && ! empty( $instance['membershipexpiration'] ) ? $instance['membershipexpiration'] : __( 'Expiration Date:', 'ultimate-woocommerce-points-and-rewards' );
		$user_ID              = get_current_user_ID();
		if ( isset( $user_ID ) && ! empty( $user_ID ) ) {
			echo esc_html( $before_widget );
			if ( $title ) {
				echo esc_html( $before_title . $title . $after_title );
			}

				$get_points = (int) get_user_meta( $user_ID, 'mwb_wpr_points', true );
				echo '<p><strong>' . esc_html( $mypoint ) . ' ' . esc_html( $get_points ) . '</strong></p>';
				$user_level       = get_user_meta( $user_ID, 'membership_level', true );
				$mwb_wpr_mem_expr = get_user_meta( $user_ID, 'membership_expiration', true );
			if ( isset( $user_level ) && ! empty( $user_level ) ) {
				echo '<div><strong>' . esc_html( $mycurrentlevel ) . ' ' . esc_html( $user_level ) . '</strong></div>';
			}
			if ( isset( $mwb_wpr_mem_expr ) && ! empty( $mwb_wpr_mem_expr ) ) {
				echo '<div><strong>' . esc_html( $membershipexpiration ) . ' ' . esc_html( $mwb_wpr_mem_expr ) . '</strong></div>';
			}
			echo esc_html( $after_widget );
		}

	}

	/**
	 * Create Widget form
	 *
	 * @since 1.0.0
	 * @name form
	 * @param array $instance instance of the class.
	 */
	public function form( $instance ) {
		$title                = '';
		$mypoint              = '';
		$mycurrentlevel       = '';
		$membershipexpiration = '';
		if ( isset( $instance ) && null != $instance ) {
			$title                = esc_attr( $instance['title'] );
			$mypoint              = esc_attr( $instance['mypoint'] );
			$mycurrentlevel       = esc_attr( $instance['mycurrentlevel'] );
			$membershipexpiration = esc_attr( $instance['membershipexpiration'] );
		}
		?>
	 
	<p>
		<label for="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'ultimate-woocommerce-points-and-rewards' ); ?></label> 
		<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $title ); ?>" /></p> 
		<p>
		<label for="<?php echo esc_html( $this->get_field_id( 'mypoint' ) ); ?>"><?php esc_html_e( 'Total Point Text:', 'ultimate-woocommerce-points-and-rewards' ); ?></label> 
		<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'mypoint' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'mypoint' ) ); ?>" type="text" value="<?php echo esc_html( $mypoint ); ?>" /></p>
		<p>
		<label for="<?php echo esc_html( $this->get_field_id( 'mycurrentlevel' ) ); ?>"><?php esc_html_e( 'User Level:', 'ultimate-woocommerce-points-and-rewards' ); ?></label> 
		<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'mycurrentlevel' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'mycurrentlevel' ) ); ?>" type="text" value="<?php echo esc_html( $mycurrentlevel ); ?>" /></p>
		<p>
		<label for="<?php echo esc_html( $this->get_field_id( 'membershipexpiration' ) ); ?>"><?php esc_html_e( 'Expiration Text:', 'ultimate-woocommerce-points-and-rewards' ); ?></label> 
		<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'membershipexpiration' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'membershipexpiration' ) ); ?>" type="text" value="<?php echo esc_html( $membershipexpiration ); ?>" /></p>   
		<?php
	}

	/**
	 * THis function is used to updation.
	 *
	 * @name update
	 * @since 1.0.0
	 * @param array $new_instance array of the instance.
	 * @param array $old_instance array of the instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                         = $old_instance;
		$instance['title']                = strip_tags( $new_instance['title'] );
		$instance['mypoint']              = strip_tags( $new_instance['mypoint'] );
		$instance['mycurrentlevel']       = strip_tags( $new_instance['mycurrentlevel'] );
		$instance['membershipexpiration'] = strip_tags( $new_instance['membershipexpiration'] );
		return $instance;
	}
}
?>
