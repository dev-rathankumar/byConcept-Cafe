<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://woosignal.com
 * @since      1.0.0
 *
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Json_Mobile_Api_Wpapp
 * @subpackage Json_Mobile_Api_Wpapp/includes
 * @author     WooSignal <support@woosignal.com>
 */
class Json_Mobile_Api_Wpapp_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Grab latest post title by an author!
	 *
	 * @param array $data Options for the function.
	 * @return string|null Post title for the latest,  * or null if none.
	 */
	function wpapp_create_nonce() {
		return $this->standardizePayload([
			'nonce' => wp_create_nonce('wpapp_json_api'),
			'root' => esc_url_raw(rest_url()),
			'expiry' => strtotime("+1 day", time())
		],'',200);
	}

	function wpapp_verify_nonce(WP_REST_Request $request) {
		$params = $request != null 
		? (array)$request->get_params()
		: null;

		if (!empty($params) && isset($params['nonce'])) {
			$nonce = $params['nonce'];
			return $this->standardizePayload([
				'is_valid' => boolval(wp_verify_nonce($nonce, 'wpapp_json_api'))
			]);
		} 
		return $this->standardizePayload([], 'invalid nonce', 500);
	}

	function wpapp_login_user(WP_REST_Request $request) {
		$params = $request != null 
		? (array)$request->get_params()
		: null;
		
		if (!isset($params['auth'])) {
			return $this->standardizePayload([], "Missing query 'auth' specify type e.g. ?auth=email or ?auth=username", 500);
		}
		if (!(($params['auth'] == "email") || ($params['auth'] == "username"))) {
			return $this->standardizePayload([], "Invalid value, the 'auth' query can only be 'email' or 'username'", 500);
		}
		
		$authType = $params['auth'];
		$requiredParams = ['nonce', 'password'];
		$requiredParams[] = $authType;

		if(isset($params['nonce'])) {
			
			// CHECK NONCE
			$nonce = $params['nonce'];
			if (wp_verify_nonce($nonce, 'wpapp_json_api')) {
				$check = null;
				if (isset($params[$authType]) && isset($params['password'])) {
					if ($authType == 'email') {
						$email = $params['email'];
						$password = $params['password'];
						$check = wp_authenticate_email_password(NULL, $email, $password);
					} else if ($authType == 'username') {
						$username = $params['username'];
						$password = $params['password'];
						$check = wp_authenticate_username_password(NULL, $username, $password);
					}
					if ($check instanceof WP_Error) {
						return $this->standardizePayload([], 'Error: ' . $check->get_error_message() . ". Code: " . $check->get_error_code(), 500);
					} else if ($check instanceof WP_User) {
						$token = $this->createNewToken();
						$res;

						if (isset($params['expiry'])) {
							$expiry = $params['expiry'];
							$res = $this->addNewTokenToDb($token, $check->ID, $expiry);
						} else {
							$res = $this->addNewTokenToDb($token, $check->ID);
						}
						return $this->standardizePayload([
							'user_token' => $res['token'],
							'expiry' => $res['expires_at']
						], '', 200);
					} else {
						return $this->standardizePayload(['error' => $check], 'Error, something went wrong.', 500);
					}
				} else {
					return $this->standardizePayload([], 'Invalid params', 500);
				}
				return $check;
			} else {
				return $this->standardizePayload([], 'Invalid nonce', 500);
			}
		}
		
		$msg = '';
		$missing = [];
		foreach ($requiredParams as $requiredParam) {
			if (empty($params[$requiredParam])) {
				$missing[] = "'$requiredParam'";
			}
		}
		if (count($missing) != 0) {
			$msg = 'Missing params ' . implode(", ", $missing);
		}
		return $this->standardizePayload([], $msg, 500);
	}

	function createNewToken() {
		return bin2hex(random_bytes(24));
	}

	function addNewTokenToDb($token, $userId, $expiry = null) {
		global $wpdb;
		$table = "{$wpdb->base_prefix}wpapp_tokens";

		$expiresAt = ($expiry != null ? date("Y-m-d H:i:s", strtotime($expiry)) : null);
		$hasSucceeded = $wpdb->insert($table, [
			'user_id' => $userId,
			'app_token' => $token,
			'is_active' => 1,
			'created_at' => date("Y-m-d H:i:s"),
			'expires_at' => $expiresAt,
		]);
		
		if ($hasSucceeded) {
			return ['token' => $token, 'expires_at' => ($expiry == null ? 0 : strtotime($expiresAt))];
		} else {
			return ['token' => '', 'expires_at' => 0];
		}
	}

	function clear_tokens_for_user($userId) {
		global $wpdb;
		$table = "{$wpdb->base_prefix}wpapp_tokens";
		$hasSucceeded = $wpdb->update($table, [
			'is_active' => 0
		], ['user_id' => $userId]);
		return $hasSucceeded;
	}

	function wpapp_register_new_user(WP_REST_Request $request) {
		$params = $request != null 
		? (array)$request->get_params()
		: null;

		$nonce = $params['nonce'];

		if (!wp_verify_nonce($nonce, 'wpapp_json_api')) {
			return $this->standardizePayload([], 'Invalid nonce', 500); 
		}
		$username = $params['username'];
		$email = $params['email'];
		$password = $params['password'];
		
		$user_id = username_exists($username);
		if ($user_id != false) {
			return $this->standardizePayload([], 'Username is taken', 520);
		}
		
		if (!$user_id && false == email_exists($email)) {
			$userId = wp_create_user($username, $password, $email);
			if ($userId instanceof WP_Error) {
				return $this->standardizePayload([], 'Error ' . $userId, 500); 
			}
			$token = $this->createNewToken();
			$res;
			if (isset($params['expiry'])) {
				$expiry = $params['expiry'];
				$res = $this->addNewTokenToDb($token, $userId, $expiry);
			} else {
				$res = $this->addNewTokenToDb($token, $userId);
			}
			return $this->standardizePayload([
				'user_token' => $res['token'],
				'expiry' => $res['expires_at']
			], '', 200); 
		} else {
			return $this->standardizePayload([], 'User already exists.' . $userId, 510);
		}

	}

	function wpapp_get_user_info(WP_REST_Request $request) {
		$this->validate_token($request);
		$token = $this->getBearerToken();

		$user = $this->get_user_for_token($token);
		if ($user instanceof WP_User) {

			return $this->standardizePayload([
				'id' => $user->ID,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'username' => get_userdata($user->ID)->user_login,
				'email' => $user->user_email,
				'avatar' => get_avatar_url($user->ID, ['size' => 250]),
				'created_at' => $user->get('user_registered')
			], '', 200);
		} else {
			return $this->standardizePayload([], 'Cannot find user', 500); 
		}
	}

	function wpapp_get_wc_user_info(WP_REST_Request $request) {
		$this->validate_token($request);
		$token = $this->getBearerToken();

		$user = $this->get_user_for_token($token);
		if ($user instanceof WP_User) {
			if ( class_exists( 'WooCommerce' ) ) {
				$customer = new WC_Customer( $user->ID );
				$rspPayload = [
					'first_name' => $customer->get_first_name(),
					'last_name' => $customer->get_last_name(),
					'display_name' => $customer->get_display_name(),
					'avatar' => $customer->get_avatar_url(),
					'shipping' => $customer->get_shipping(),
					'billing' => $customer->get_billing()
				]; 

				return $this->standardizePayload($rspPayload, '', 200); 
			}
			return $this->standardizePayload([], 'WooCommerce is not found', 500); 
		}
		return $this->standardizePayload([], $user, 500); 
	}

	function wpapp_update_wc_user_info(WP_REST_Request $request) {
		$this->validate_token($request);
		$token = $this->getBearerToken();

		$user = $this->get_user_for_token($token);
		if ($user instanceof WP_User) {
			if (class_exists('WooCommerce')) {
				$customer = new WC_Customer($user->ID);

				$params = $request != null 
				? (array)$request->get_params()
				: null;
				
				if (isset($params['first_name'])) {
					$customer->set_first_name($params['first_name']);
				}
				if (isset($params['last_name'])) {
					$customer->set_last_name($params['last_name']);
				}
				if (isset($params['display_name'])) {
					$customer->set_display_name($params['display_name']);
				}

				// BILLING
				if (isset($params['billing'])) {
					$billing = $params['billing'];
					if (isset($billing['first_name'])) {
						$customer->set_billing_first_name($billing['first_name']);
					}
					if (isset($billing['last_name'])) {
						$customer->set_billing_last_name($billing['last_name']);
					}
					if (isset($billing['company'])) {
						$customer->set_billing_company($billing['company']);
					}
					if (isset($billing['address_1'])) {
						$customer->set_billing_address_1($billing['address_1']);
					}
					if (isset($billing['address_2'])) {
						$customer->set_billing_address_2($billing['address_2']);
					}
					if (isset($billing['city'])) {
						$customer->set_billing_city($billing['city']);
					}
					if (isset($billing['state'])) {
						$customer->set_billing_state($billing['state']);
					}
					if (isset($billing['postcode'])) {
						$customer->set_billing_postcode($billing['postcode']);
					}
					if (isset($billing['country'])) {
						$customer->set_billing_country($billing['country']);
					}
					if (isset($billing['email'])) {
						$customer->set_billing_email($billing['email']);
					}
					if (isset($billing['phone'])) {
						$customer->set_billing_phone($billing['phone']);
					}
				}

				// SHIPPING
				if (isset($params['shipping'])) {
					$shipping = $params['shipping'];
					if (isset($shipping['first_name'])) {
						$customer->set_shipping_first_name($shipping['first_name']);
					}
					if (isset($shipping['last_name'])) {
						$customer->set_shipping_last_name($shipping['last_name']);
					}
					if (isset($shipping['company'])) {
						$customer->set_shipping_company($shipping['company']);
					}
					if (isset($shipping['address_1'])) {
						$customer->set_shipping_address_1($shipping['address_1']);
					}
					if (isset($shipping['address_2'])) {
						$customer->set_shipping_address_2($shipping['address_2']);
					}
					if (isset($shipping['city'])) {
						$customer->set_shipping_city($shipping['city']);
					}
					if (isset($shipping['state'])) {
						$customer->set_shipping_state($shipping['state']);
					}
					if (isset($shipping['postcode'])) {
						$customer->set_shipping_postcode($shipping['postcode']);
					}
					if (isset($shipping['country'])) {
						$customer->set_shipping_country($shipping['country']);
					}
					if (isset($shipping['email'])) {
						$customer->set_shipping_email($shipping['email']);
					}
					if (isset($shipping['phone'])) {
						$customer->set_shipping_phone($shipping['phone']);
					}	
				}
				$rsp = ($customer->save() == $user->ID);
				return $this->standardizePayload([], $rsp == 1 ? 'Updated' : '', 200); 
			}
			return $this->standardizePayload([], 'WooCommerce is not found', 500); 
		}
		return $this->standardizePayload([], $user, 500); 
	}

	function get_user_for_token($token) {
		global $wpdb;
		$table = "{$wpdb->base_prefix}wpapp_tokens";
		$res = $wpdb->get_results("
			SELECT ID, user_id 
			FROM $table
			WHERE app_token = '$token'
			AND is_active = '1' 
			AND expires_at IS NULL OR 
			app_token = '$token'
			AND is_active = '1' 
			AND expires_at >= NOW() ORDER BY created_at desc LIMIT 1");
		if (empty($res)) {
			return false;
		}
		return new WP_User(current($res)->user_id);
	}

	function validate_token(WP_REST_Request $request) {
		$token = $this->getBearerToken();
		if (empty($token)) {
			exit(json_encode($this->standardizePayload([], 'Invalid token', 500)));
		}
		$isValid = $this->is_token_valid($token);
		if ($isValid != true) {
			exit(json_encode($this->standardizePayload([], 'Invalid token', 500)));
		}
	}

	/** 
	 * Get header Authorization
	 * */
	function getAuthorizationHeader(){
		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		}
		else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { 
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} elseif (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();

			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		return $headers;
	}

    /**
 * get access token from header
 * */
    function getBearerToken() {
    	$headers = $this->getAuthorizationHeader();
    	if (!empty($headers)) {
    		if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
    			return $matches[1];
    		}
    	}
    	return null;
    }

    function wpapp_update_user_info(WP_REST_Request $request) {
    	$this->validate_token($request);
    	$token = $this->getBearerToken();
    	$user = $this->get_user_for_token($token);

    	if ($user instanceof WP_User) {
    		$params = $request != null 
    		? (array)$request->get_params()
    		: null;

    		$updateData = ['ID' => $user->ID];
    		foreach ($params as $key => $param) {
    			if (in_array($key, ['first_name', 'last_name']) && !empty($param)) {
    				$updateData[$key] = $param;
    			}
    		}

    		$rsp = wp_update_user($updateData);

    		if ($rsp instanceof WP_Error) {
    			return $this->standardizePayload([], 'Error: ' . $rsp->get_error_message() . ". Code: " . $rsp->get_error_code(), 500);
    		} else {
    			return $this->standardizePayload($updateData, 'Updated', 200);
    		}
    	} else {
    		return $this->standardizePayload([], 'Error: ' . $user->get_error_message() . ". Code: " . $user->get_error_code(), 500);
    	}

    }

    function wpapp_reset_password(WP_REST_Request $request) {
    	$this->validate_token($request);
    	$token = $this->getBearerToken();
    	$user = $this->get_user_for_token($token);

    	if ($user instanceof WP_User) {
    		$params = $request != null 
    		? (array)$request->get_params()
    		: null;

    		if (isset($params['password'])) {
    			wp_set_password($params['password'], $user->ID);
    			return $this->standardizePayload([], 'Updated', 200);
    		} else {
    			return $this->standardizePayload([], "Missing param 'password'", 500); 
    		}

    	} else if ($user instanceof WP_Error) {
    		return $this->standardizePayload([], 'Error: ' . $user->get_error_message() . ". Code: " . $user->get_error_code(), 500); 
    	} 
    	return $this->standardizePayload([], 'Error', 500); 
    }

    function is_token_valid($token) {
    	global $wpdb;
    	$table = "{$wpdb->base_prefix}wpapp_tokens";
    	$res = $wpdb->get_results("
    		SELECT ID, app_token 
    		FROM $table
    		WHERE app_token = '$token'
    		AND is_active = '1' 
    		AND expires_at IS NULL OR 
    		app_token = '$token'
    		AND is_active = '1' 
    		AND expires_at >= NOW() ORDER BY created_at desc LIMIT 1");
    	if (empty($res)) {
    		return false;
    	}
    	return (current($res)->app_token === $token) ? true : false;
    }

    function standardizePayload($result = [], $message = '', $status = 200) {
    	return [
    		'data' => $result,
    		'message' => $message,
    		'status' => $status
    	];
    }

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		add_action( 'rest_api_init', function() {
			$prefixAuthV1 = 'wpapp/auth/v1';
			$prefixApiV1 = 'wpapp/api/v1';
			$prefixWcV1 = 'wpapp/wc/v1';
			
			// AUTH
			
			register_rest_route( $prefixAuthV1, '/nonce', [
				'methods' => 'GET',
				'callback' => [$this, 'wpapp_create_nonce'],
			]);

			register_rest_route( $prefixAuthV1, '/verify', [
				'methods' => 'POST',
				'callback' => [$this, 'wpapp_verify_nonce'],
			]);

			// API

			register_rest_route( $prefixApiV1, '/user/register', [
				'methods' => 'POST',
				'callback' => [$this, 'wpapp_register_new_user'],
			]);

			register_rest_route( $prefixApiV1, '/user/login', [
				'methods' => 'POST',
				'callback' => [$this, 'wpapp_login_user'],
			]);

			register_rest_route( $prefixApiV1, '/user/info', [
				'methods' => 'GET',
				'callback' => [$this, 'wpapp_get_user_info'],
			]);

			register_rest_route( $prefixApiV1, '/update/user/info', [
				'methods' => 'POST',
				'callback' => [$this, 'wpapp_update_user_info'],
			]);

			register_rest_route( $prefixApiV1, '/update/user/password', [
				'methods' => 'POST',
				'callback' => [$this, 'wpapp_reset_password'],
			]);

			// WOOCOMMERCE

			register_rest_route( $prefixWcV1, '/user/info', [
				'methods' => 'GET',
				'callback' => [$this, 'wpapp_get_wc_user_info'],
			]);

			register_rest_route( $prefixWcV1, '/update/user/info', [
				'methods' => 'POST',
				'callback' => [$this, 'wpapp_update_wc_user_info'],
			]);

		} );
	}

}
