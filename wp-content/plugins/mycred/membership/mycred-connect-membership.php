<?php
/**
 * Class to connect mycred with membership
 * 
 * @since 1.0
 * @version 1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'myCRED_Connect_Membership' ) ) :
    Class myCRED_Connect_Membership {

        /**
		 * Construct
		 */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'mycred_membership_menu' ) );
            add_action( 'init', array( $this, 'add_styles' ) );
            add_action( 'mycred_admin_init', array( $this, 'mycred_review_notice' ) );
        }

        function add_styles() {

            wp_register_style('admin-subscription-css', plugins_url( 'assets/css/admin-subscription.css', myCRED_THIS ), array(), '1.1', 'all');
            wp_enqueue_style('admin-subscription-css');
        }

        /**
		 * Register membership menu
		 */
        public function mycred_membership_menu() {
            add_submenu_page( 'mycred', 'Membership', 'Membership<span class="mycred-membership-menu-label">New</span>', 'manage_options', 'mycred-membership',array($this,'mycred_membership_callback'));
        }

        /**
		 * Membership menu callback
		 */
        public function mycred_membership_callback() {
            $user_id = get_current_user_id();
            $this->mycred_save_license();
            $membership_key = get_option( 'mycred_membership_key' );
            if( !isset( $membership_key )  && !empty( $membership_key ) )
                $membership_key = '';
            ?>
            <div class="wrap">
                <h1><?php _e( 'myCred Membership Club', 'mycred' ); ?></h1>
                <div class="mmc_welcome">
                    <div class="mmc_welcome_content">
                        <div class="mmc_title"><?php _e( 'Welcome to myCred Membership Club', 'mycred' ); ?></div>
						<form action="#" method="post">
							<input type="text" name="mmc_lincense_key" class="mmc_lincense_key" placeholder="<?php _e( 'Add Your Membership License', 'mycred' ); ?>" value="<?php echo $membership_key?>">
							<input type="submit" class="mmc_save_license button-primary" value="Save"/>
							<div class="mmc_license_link"><a href="#"><span class="dashicons dashicons-editor-help"></span><?php _e('Click here to get your Membership License','mycred') ?></a></div>
						</form>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
		 * Saving user membership key
		 */
        public function mycred_save_license() {
            
            if( !isset($_POST['mmc_lincense_key']) ) return;

            $license_key = sanitize_text_field( $_POST['mmc_lincense_key'] );
            if( isset( $license_key ) ) {
                update_option( 'mycred_membership_key', $license_key );
            }
        }

        /**
         * myCred Review Dialog
         */
        public function mycred_review_notice() {
            
            $this->mycred_review_dismissal();
            $this->mycred_review_prending();

            $review_dismissal = get_site_option( 'mycred_review_dismiss' );
            if ( 'yes' == $review_dismissal ) {
                return;
            }

            $activation_time = get_site_option( 'mycred_active_time' );
            if ( ! $activation_time ) {

                $activation_time = time();
                add_site_option( 'mycred_active_time', $activation_time );
            }

            // Show notice after 7 Days.
            if ( ( time() - $activation_time ) > 604800 ) {
                add_action( 'admin_notices', array( $this, 'mycred_review_notice_content' ) );
            }
            
        }

        public function mycred_review_dismissal() {

            if ( ! is_admin() ||
            ! current_user_can( 'manage_options' ) ||
            ! isset( $_GET['_wpnonce'] ) ||
            ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'mycred-review-nonce' ) ||
            ! isset( $_GET['mycred_review_dismiss'] ) ) {

                return;
            }

            update_site_option( 'mycred_review_dismiss', 'yes' );
        }

        public function mycred_review_prending() {

            if ( ! is_admin() ||
            ! current_user_can( 'manage_options' ) ||
            ! isset( $_GET['_wpnonce'] ) ||
            ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'mycred-review-nonce' ) ||
            ! isset( $_GET['mycred_review_later'] ) ) {

                return;
            }

            // Reset Time
            update_site_option( 'mycred_active_time', time() );

        }

        public function mycred_review_notice_content() {

            $scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
            $url         = $_SERVER['REQUEST_URI'] . $scheme . 'mycred_review_dismiss=yes';
            $dismiss_url = wp_nonce_url( $url, 'mycred-review-nonce' );

            $later       = $_SERVER['REQUEST_URI'] . $scheme . 'mycred_review_later=yes';
            $later_url   = wp_nonce_url( $later, 'mycred-review-nonce' );

            ?>
            <div class="mycred-review-notice">
                <div class="mycred-review-thumbnail">
                    <img src="<?php echo plugins_url( 'assets/images/about/badge.png', myCRED_THIS ); ?>" alt="">
                </div>
                <div class="mycred-review-text">
                    <h3><?php _e( 'Your Feedback Please?', 'mycred' ); ?></h3>
                    <p><?php _e( 'We hope you had a pleasant experience of using myCred points management system. It will be highly appreciated if you leave us your valuable feedback on Wordpress.org.', 'mycred' ); ?></p>
                    <ul class="mycred-review-ul">
                        <li>
                            <a href="https://wordpress.org/support/plugin/mycred/reviews/?filter=5" target="_blank">
                                <span class="dashicons dashicons-external"></span>
                                <?php _e( 'Sure, why not?', 'mycred' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $dismiss_url; ?>">
                                <span class="dashicons dashicons-smiley"></span>
                                <?php _e( 'I have already provided my feedback', 'mycred' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $later_url; ?>">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                <?php _e( 'Ummm, maybe later!', 'mycred' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $dismiss_url; ?>">
                                <span class="dashicons dashicons-dismiss"></span>
                                <?php _e( 'Never show again', 'mycred' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="https://mycred.me/membership/">
                                <span class="dashicons dashicons-businessman"></span>
                                <?php _e( 'Explore Membership Plans', 'mycred' ); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
        }

    }
endif;

$myCRED_Connect_Membership = new myCRED_Connect_Membership();