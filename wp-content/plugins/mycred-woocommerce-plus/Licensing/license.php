<?php
if ( ! class_exists( 'myCRED_Woo_plus_License' ) ) :
	final class myCRED_Woo_plus_License {

		// Plugin Version
		public $version = MYCRED_WOOPLUS_VERSION;

		// Plugin Slug
		public $slug    = MYCRED_WOOPLUS_SLUG;

		public $base    = MYCRED_WOOPLUS_THIS;

		// Instnace
		protected static $_instance = NULL;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {

			//license system
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ), 80 );
			add_filter( 'plugins_api', array( $this, 'plugin_api_call' ), 80, 3 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_view_info' ), 80, 3 );

		}

		/**
		 * Plugin Update Check
		 * @since 1.0
		 * @version 1.1
		 */
		public function check_for_plugin_update( $checked_data ) {

			global $wp_version;

			if ( empty( $checked_data->checked ) )
				return $checked_data;

			$args = array(
				'slug'    => $this->slug,
				'version' => $this->version,
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'     => 'version', 
					'request'    => serialize( $args ),
					'api-key'    => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			// Start checking for an update
			$response = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

			if ( ! is_wp_error( $response ) ) {

				$result = maybe_unserialize( $response['body'] );

				if ( is_object( $result ) && ! empty( $result ) )
					$checked_data->response[ $this->slug . '/' . $this->slug . '.php' ] = $result;

			}

			return $checked_data;

		}

		/**
		 * Plugin New Version Update
		 * @since 1.0
		 * @version 1.1
		 */
		public function plugin_api_call( $result, $action, $args ) {
		  
			global $wp_version;

			if ( ! isset( $args->slug ) || ( $args->slug != $this->slug ) )
				return $result;

			// Get the current version
			$args = array(
				'slug'    => $this->slug,
				'version' => $this->version,
				'site'    => site_url()
			);
			 
			$request_string = array(
				'body'       => array(
					'action'     => 'info', 
					'request'    => serialize( $args ),
					'api-key'    => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);
			

			$request = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );
			
			if ( ! is_wp_error( $request ) )
				$result = maybe_unserialize( $request['body'] );

			if ( ! empty( $result->license_expires ) )
				update_option( 'mycred-premium-' . $this->slug . '-expires', $result->license_expires );

			if ( ! empty( $result->license_renew ) )
				update_option( 'mycred-premium-' . $this->slug . '-renew',   $result->license_renew );

			return $result;

		}

		/**
		 * Plugin View Info
		 * @since 1.1
		 * @version 1.0
		 */
		public function plugin_view_info( $plugin_meta, $file, $plugin_data ) {

			if ( $file != plugin_basename( $this->base ) ) return $plugin_meta;

			if( function_exists('mycred_is_membership_active') && mycred_is_membership_active() ) {
				$addon_slug = array();
				foreach( $this->is_membership_plugin()['addons'] as $addons ) {
					$addon_slug[] = $addons['folder'];
				}
			}
			
			if( function_exists('mycred_is_membership_active') && mycred_is_membership_active() ) {
			
				$addon_slug = array();
				foreach( $this->is_membership_plugin()['addons'] as $addons ) {
					$addon_slug[] = $addons['folder'];
				}
			
				$plugin_folder = $this->get_folder_name();
			
				if( in_array($plugin_folder,$addon_slug ) ) {
					
					
					$plugin_meta = $this->membership_license( $plugin_meta, $file, $plugin_data );
				} else {
					$plugin_meta = $this->get_old_license( $plugin_meta, $file, $plugin_data );
				}
			} else {
				$plugin_meta = $this->get_old_license( $plugin_meta, $file, $plugin_data );
				
			}
			return apply_filters( 'mycred_plugin_info', $plugin_meta, $this->slug, $file, $plugin_data );

		}

		public function membership_license( $plugin_meta, $file, $plugin_data ) {
			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $this->slug .
				'&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( __( 'More information about this plugin', $this->slug ) ),
				esc_attr( 'myCred Wheel of Fortune' ),
				__( 'View details', $this->slug )
			);
		
			$expire_days = $this->is_membership_plugin()['order'][0]['expire'];
			if( $this->is_subscription_expired($expire_days) == false )
				$plugin_meta[] = 'Your License Expires in '.$this->calculate_license_expire_time($expire_days);
			else
				$plugin_meta[] = '<span style="color:red">Your membership has been expired </span><a href="https://www.mycred.me/my-account/subscriptions/" style="font-weight:bold">Renew Membership</a>';

			return $plugin_meta;
		}

		public function get_old_license( $plugin_meta, $file, $plugin_data ) {
			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $this->slug .
				'&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( __( 'More information about this plugin', $this->slug ) ),
				esc_attr( 'myCred Wheel of Fortune' ),
				__( 'View details', $this->slug )
			);

			$url     = str_replace( array( 'https://', 'http://' ), '', get_bloginfo( 'url' ) );
			$expires = get_option( 'mycred-premium-' . $this->slug . '-expires', '' );
			if(empty($expires)){
				$args = new stdClass;
				$args->slug = $this->slug;
				$args->version = $this->version;
				$args->site = site_url();
				
				$action = '';
				$result = '';   
				$this->plugin_api_call( $result, $action, $args );
				
				$expires = get_option( 'mycred-premium-' . $this->slug . '-expires', '' );
			}
			if ( $expires != '' ) {

				if ( $expires == 'never' )
					$plugin_meta[] = 'Unlimited License';

				elseif ( absint( $expires ) > 0 ) {

					$days = ceil( ( $expires - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
					if ( $days > 0 )
						$plugin_meta[] = sprintf(
							'License Expires in <strong%s>%s</strong>',
							( ( $days < 30 ) ? ' style="color:red;"' : '' ),
							sprintf( _n( '1 day', '%d days', $days ), $days )
						);

					$renew = get_option( 'mycred-premium-' . $this->slug . '-renew', '' );
					if ( $days < 30 && $renew != '' )
						$plugin_meta[] = '<a href="' . esc_url( $renew ) . '" target="_blank" class="">Renew License</a>';

				}

			} else {
				$plugin_meta[] = '<a href="http://mycred.me/about/terms/#product-licenses" target="_blank">No license found for - ' . $url . '</a>';
			}

			return $plugin_meta;
		}

		public function calculate_license_expire_time( $expire_date ) {
				
			$date1=date_create(date('Y-m-d'));
			$date2=date_create($expire_date);
			$diff=date_diff($date1,$date2);
			$sign = $diff->format("%R");
			return $diff->format("%a days");
		}

		public function is_subscription_expired($expire_date) {
			$date1=date_create(date('Y-m-d'));
			$date2=date_create($expire_date);

			if( $date1 > $date2 )
				return true;
			else
				return false;
		}

		public function is_membership_plugin() {
			
			$membership_detail = mycred_get_membership_details();
			return $membership_detail;
		}

		public function get_folder_name() {
			$plugin_folder = plugin_basename( $this->base );
			$plugin_folder = explode('/',$plugin_folder);
			return $plugin_folder[0];
		}
	}
endif;

function mycred_woo_plus_license() {
	return myCRED_Woo_plus_License::instance();
}
mycred_woo_plus_license();