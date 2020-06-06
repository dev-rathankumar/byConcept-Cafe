<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//18-04-2016
final class WOOF_EXT_QUERY_SAVE extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'query_save'; //your custom key here
    public $index = '';
    public $html_type_dynamic_recount_behavior = 'none';
    protected $user_meta_key = 'woof_user_search_query';
    public $search_count=2;

    public function __construct() {
	parent::__construct();	
	//***
	if (isset($this->woof_settings["query_save"]['search_count']) AND ! empty($this->woof_settings["query_save"]['search_count'])) {
	    $this->search_coun = (int) $this->woof_settings["query_save"]['search_count'];
	}
	
	$this->init();
    }

    public function get_ext_path() {
	return plugin_dir_path(__FILE__);
    }

    public function get_ext_link() {
	return plugin_dir_url(__FILE__);
    }

    public function woof_add_items_keys($keys) {
	$keys[] = $this->html_type;
	return $keys;
    }

    public function init() {
	add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
	add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
	add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
	add_action('wp_head', array($this, 'wp_head'), 999);
	// Ajax  action
	add_action('wp_ajax_woof_save_query_add_query', array($this, 'woof_add_query'));
	add_action('wp_ajax_nopriv_woof_save_query_add_query', array($this, 'woof_add_query'));
	add_action('wp_ajax_woof_save_query_remove_query', array($this, 'woof_remove_query'));
	add_action('wp_ajax_nopriv_woof_save_query_remove_query', array($this, 'woof_remove_query'));
	//+++

        // add shortcode
        add_shortcode('woof_save_query',array($this,'woof_save_query'));
       
	self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
	self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
	self::$includes['js_init_functions'][$this->html_type] = 'woof_init_save_query';
    }


    //settings page hook
    public function woof_print_html_type_options() {
	global $WOOF;
	echo $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
	    'key' => $this->html_type,
	    "woof_settings" => get_option('woof_settings', array())
		)
	);
    }

    public function woof_add_query() {
	global $WOOF,$wpdb,$wp_query;

	if (! isset($_POST['link']) OR ! isset($_POST['user_id'])) {
	    die();
	}

	//***

	$data = array();
	$sanit_user_id = sanitize_key($_POST['user_id']);
	if ($sanit_user_id < 1) {
	    die(); //if user id - wrong!!!
	}

               
	$key = uniqid('woofms_'); // Create   key for this subscr
	$data['key'] = $key;
        
	$data['user_id'] = $sanit_user_id;
	$data['link'] = esc_url($_POST['link']);
	$data['get'] =$this->woof_get_html_terms($this->sanitaz_array_r($_POST['get_var']));
	$saved_q = get_user_meta($data['user_id'], $this->user_meta_key, true);
        $data['request'] =$this->sanitazed_sql_query(base64_decode($WOOF->storage->get_val("woof_pm_request_".$data['user_id'])));
        // If the request has banned operators or is empty
        if(!$data['request'] OR empty($data['request'])){
             die();
        }
        //+++
        //Remove limit frim request
        $pos = stripos($data['request'], "LIMIT");
        if($pos){
            $data['request']=substr($data['request'],0,$pos);
        }
	if (count($saved_q) >= $this->search_count) {
	    die('count is max'); // Check limit count on backend
	}
        //+++
	$data['date'] = time();
        
        $data['title']=__('My query', 'woocommerce-products-filter');
        if(isset($_POST['query_title']) AND $_POST['query_title']){
           $data['title']= sanitize_text_field($_POST['query_title']);
        }

	$saved_q[$key] = $data;
	update_user_meta($data['user_id'], $this->user_meta_key, $saved_q);
	//for Ajax redraw
	$cont = $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'item_list_query.php', $data);
	//die(json_encode($data));
	die($cont);
    }

    public function woof_remove_query() {
	if (!isset($_POST['key']) OR ! isset($_POST['user_id'])) {
	    die('No data!');
	}

	$user_id = sanitize_key($_POST['user_id']);
	$key = sanitize_key($_POST['key']);
	$subscr = get_user_meta($user_id, $this->user_meta_key, true);
	unset($subscr[$key]);
	update_user_meta($user_id, $this->user_meta_key, $subscr);
	$arg = array('key' => $key);
	die(json_encode($arg));
    }



    //it create  html for tooltip and list of the terms in email
    public function woof_get_html_terms($args) {
	$html = "";
       
	$not_show = array( 'swoof','paged','orderby', 'min_price', 'max_price', 'woof_author','page');
	if (isset($args['min_price'])) {
	    $price_text = sprintf(__('Price - from %s to %s', 'woocommerce-products-filter'), $args['min_price'], $args['max_price']);
	    $price_text .= '<br />';
	    $html .= '<span class="woof_subscr_price">' . $price_text . '</span>';
	}
	if (isset($args['woof_author'])) {
	    $ids = explode(',', $args['woof_author']);
	    $auths = "";
	    foreach ($ids as $auth) {
		$auths .= " " . get_userdata((int) $auth)->display_name;
	    }
	    $html .= "<span class='woof_author_name'>" . $auths . "</span><br />";
	}
 
	foreach ($args as $key => $val) {
            
	    if (in_array($key, $not_show)) {
		continue;
	    }
            
            if(class_exists('WOOF_META_FILTER')){
                $meta_title=WOOF_META_FILTER::get_meta_title_messenger($val, $key);
                //var_dump($meta_title);
                if(!empty($meta_title) AND $meta_title){
                    $html .= $meta_title; 
                    
                    continue;
                }
            }
            $tax=get_taxonomy($key);
            if(is_object($tax)){
                $name = $tax->labels->name;
                if (!empty($name)) {
                    $name .= ": ";
                }
                $name .= $val;
                $html .= "<span class='woof_terms'>" . $name . "</span><br />";                
            }

	}
	if (empty($html)) {
	   $html = __('None', 'woocommerce-products-filter');
	}
        //var_dump($html);
	return $html;
    }

    // Recursive sanitaze arrais
    public function sanitaz_array_r($arr) {
	$newArr = array();
	foreach ($arr as $key => $value) {
	    $newArr[WOOF_HELPER::escape($key)] = ( is_array($value) ) ? $this->sanitaz_array_r($value) : WOOF_HELPER::escape($value);
	}
	return $newArr;
    }

    public function wp_head() {
	?>
	<script type="text/javascript">
	    var woof_confirm_lang = "<?php _e('Are you sure?', 'woocommerce-products-filter') ?>";
	</script>
	<?php
    }
    public function  woof_save_query($args){
        $data=shortcode_atts(array(
	    'in_filter' => 0
			), $args);
        global $WOOF;
        return $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes'. DIRECTORY_SEPARATOR.'woof_save_query.php',$data);
    }

    public function  sanitazed_sql_query($sql){
        $conditional_operator=array('TRUNCATE','DELETE','UPDATE','INSERT','REPLACE','CREATE');
        foreach($conditional_operator as $operator){
            $result=stripos($sql,$operator);
            if($result!==false){
                return false;
                break;
            }
        }
        return $sql;
    }
}

WOOF_EXT::$includes['html_type_objects']['query_save'] = new WOOF_EXT_QUERY_SAVE();
