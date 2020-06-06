<?php

/**
 * 
 * @package ESIG_CFDS
 * @author  Approve me <arafatrahmank@gmail.com>
 */
if (!class_exists('ESIG_WPFORM')) :
class ESIG_WPFORM {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.1
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';
	
	

	/**
	 *
	 * Unique identifier for plugin.
	 *
	 * @since     0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'esig-wpform';

	/**
	 * Instance of this class.
	 *
	 * @since     1.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.1
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array($this, 'load_plugin_textdomain') );
                add_action( 'admin_init',array($this, 'esign_wpform_after_install') );
               
		
	
	}
        
        public function esign_wpform_after_install() {
		global $pagenow;
		
		if( ! is_admin() )
		return;
		
		// Delete the transient
		//delete_transient( '_esign_activation_redirect' );
		if(delete_transient( '_esign_wpform_redirect' )) 
		{
			wp_safe_redirect( admin_url( 'admin.php?page=esign-wpform-about' ));
			exit;
		}
	}
   
    
        
	
	/**
	 * Returns the plugin slug.
	 *
	 * @since     0.1
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @since     0.1
	 * @return    object    A single instance of this class.
	 */
	 
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since     0.1
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	 
	public static function activate( $network_wide ) {
		self::single_activate();
                set_transient( '_esign_wpform_redirect', true, 30 );
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since     0.1
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		self::single_deactivate();
	}

	

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since     0.1
	 */
	private static function single_activate() {
		//@TODO: Define activation functionality here
         if(get_option('WP_ESignature__WP_Forms_Digital_Signature_documentation'))
        {
            update_option('WP_ESignature__WP_Forms_Digital_Signature_documentation','');
            
        }
        else
        {
           
           add_option('WP_ESignature__WP_Forms_Digital_Signature_documentation','');
        }
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since     0.1
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since     0.1
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}
	
	
}
endif;
