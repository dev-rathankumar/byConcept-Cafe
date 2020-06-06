<?php

class WP_E_Notice extends WP_E_Model {

    /** @var array $_data  */
    protected $_esigdata = array();

    /** @var bool $_dirty When something changes */
    protected $_change = false;
    protected $_customer_id;
    private static $instance;
    private $_cache_group = 'esig_notice_display';

    public function __construct() {
        parent::__construct();
        // $this->$_customer_id = $this->esig_session_id();
        // $this->_esigdata = $this->get_all();
    }

    public static function instance() {
        if (!isset(self::$instance) && !( self::$instance instanceof WP_E_Notice )) {
            self::$instance = new WP_E_Notice;
        }
        return self::$instance;
    }

    /**
     * Set a session variable
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {

        $this->_customer_id = $this->esig_session_id();
        $this->_esigdata = $this->get_all();

        if ($value !== $this->get($key)) {

            $this->_esigdata[$key] = maybe_serialize($value);
            $this->_change = true;
            $this->save_data();
        }
    }

    /**
     * save_data function.
     */
    public function save_data() {
        // Dirty if something changed - prevents saving nothing new
        if ($this->_change) {

            wp_cache_set('esig-' . $this->_customer_id, $this->_esigdata, $this->_cache_group, (24 * 60 * 60));

            if (false === get_transient('esig-' . $this->_customer_id)) {
                set_transient('esig-' . $this->esig_session_id(), maybe_serialize($this->_esigdata), 12 * 60);
            } else {
                delete_transient('esig-' . $this->_customer_id);

                set_transient('esig-' . $this->_customer_id, maybe_serialize($this->_esigdata), 12 * 60);
            }
            // Mark session clean after saving
            $this->_change = false;
        }
    }

    /**
     * Get a session variable
     *
     * @param string $key
     * @param  mixed $default used if the session variable isn't set
     * @return mixed value of session variable
     */
    public function get($key) {
        $key = sanitize_key($key);
        $notices = $this->get_all();
        return (is_array($notices) && array_key_exists($key, $notices)) ? $notices[$key] : false;
    }

    /**
     * Get a session variable
     *
     * @param string $key
     * @param  mixed $default used if the session variable isn't set
     * @return mixed value of session variable
     */
    public function get_all() {

        if (!empty($this->_esigdata)) {
            return maybe_unserialize($this->_esigdata);
        } else {
            $value = wp_cache_get('esig-' . $this->_customer_id, $this->_cache_group);
            if (!empty($value)) {
                return maybe_unserialize($value);
            } else {

                $value = get_transient('esig-' . $this->_customer_id);
                return maybe_unserialize($value);
            }
        }
    }

    public function esig_print_notice() {

        $this->_customer_id = $this->esig_session_id();
        // $this->_esigdata = $this->get_all();
        $all_notice = $this->get_all();

        $alert_msg = '';
        if (empty($all_notice)) {
            return false;
        }

        foreach ($all_notice as $key => $value) {

            $view = new WP_E_View();

            $data = array(
                "alert-type" => $key,
                "alert-msg" => $value,
            );

            $alert_msg .= $view->renderPartial('alert-msg', $data);
        }
        // deleting transient after printing notice 
        delete_transient('esig-' . $this->_customer_id);
        wp_cache_delete('esig-' . $this->_customer_id, $this->_cache_group);
        return $alert_msg;
    }

    /**
     * Generate a unique customer ID for guests, or return user ID if logged in.
     *
     * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
     *
     * @return int|string
     */
    public function esig_session_id() {

        if (is_user_logged_in()) {
            return get_current_user_id();
        }

        if (!isset($_COOKIE['esig_session_id'])) {
            require_once( ABSPATH . 'wp-includes/class-phpass.php');
            $hasher = new PasswordHash(8, false);

            $esig_session_id = md5($hasher->get_random_bytes(32));
            esig_setcookie('esig_session_id', $esig_session_id, 24 * 60 * 60);
            $this->_customer_id = $esig_session_id;
            return $esig_session_id;
        } else {
            return $_COOKIE['esig_session_id'];
        }
    }

    public static function set_error_dialog($name) {
        if (!headers_sent()) {
            esig_setcookie('esig-error-dialog', $name, 60 * 1);
        }
    }

    public static function get_error_dialog() {

        if (isset($_COOKIE['esig-error-dialog']) && !empty($_COOKIE['esig-error-dialog'])) {

            $error = $_COOKIE['esig-error-dialog'];
            //unset($_COOKIE['esig-error-dialog']);
            //setcookie('esig-error-dialog', '', time() - 3600, '/'); // empty value and old timestamp
            return $error;
        }

        return false;
    }

    public static function remove_error_dialog() {
        unset($_COOKIE['esig-error-dialog']);
        setcookie('esig-error-dialog', '', time() - 3600, COOKIEPATH);
    }

}
