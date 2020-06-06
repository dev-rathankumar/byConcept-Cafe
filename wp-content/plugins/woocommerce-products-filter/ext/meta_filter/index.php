<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//12-06-2018
final class WOOF_META_FILTER extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'meta_filter'; //should be defined!!
    //***
    protected $excluded_meta = array();
    protected $meta_keys = array();
    //***
    public $meta_filters_obj = array();
    public $meta_filter_types = array();

    //***
    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {

        require_once $this->get_ext_path() . 'classes/woof_type_meta_filter.php';

        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);
        add_action('wp_footer', array($this, 'wp_footer'), 12);
        //ajax
        add_action('wp_ajax_woof_meta_get_keys', array($this, 'woof_meta_get_keys'));
        //add_action('wp_ajax_nopriv_woof_qt_update_file', array($this, 'create_data_search_files'));
        // Create meta query
        add_filter('woof_get_meta_query', array($this, 'woof_get_meta_query'));

        //option to add filter type 
        $this->meta_filter_types = array(
            'slider' => array(
                'key' => 'slider',
                'title' => __('Slider', 'woocommerce-products-filter'),
                'hide_if' => 'string',
                'show_options' => false,
            ),            
            'textinput' => array(
                'key' => 'textinput',
                'title' => __('Search by text', 'woocommerce-products-filter'),
                'hide_if' => 'no',
                'show_options' => false,
            ),
            'checkbox' => array(
                'key' => 'checkbox',
                'title' => __('Checkbox', 'woocommerce-products-filter'),
                'hide_if' => 'no',
                'show_options' => false,
            ),
            'select' => array(
                'key' => 'select',
                'title' => __('Drop-down', 'woocommerce-products-filter'),
                'hide_if' => 'no',
                'show_options' => true,
            ),
            'mselect' => array(
                'key' => 'mselect',
                'title' => __('Multi Drop-down', 'woocommerce-products-filter'),
                'hide_if' => 'no',
                'show_options' => true,
            ),
        );

        $this->meta_filter_types = apply_filters('woof_meta_filter_add_types', $this->meta_filter_types);
        global $WOOF;
        if (isset($this->woof_settings['meta_filter']) AND is_array($this->woof_settings['meta_filter'])) {
            foreach ($this->woof_settings['meta_filter'] as $key => $val) {
                if ($key == "__META_KEY__") {
                    continue;
                }

                $this->meta_keys[] = $val['meta_key'];
                //old code
                //add_action('woof_print_html_type_options_' . $key,array($this, 'woof_print_html_type_options_meta')); 
                //add_action('woof_print_html_type_' . $key,array($this, 'woof_print_html_type_meta'));
                //++++
                $this->conect_activate_meta_filter($key, $val);
            }
        }
        //add meta items to structure
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
    }

    public function conect_activate_meta_filter($key, $options) {
        $class_name = 'WOOF_META_FILTER_' . strtoupper($options['search_view']);
        require_once $this->get_ext_path() . 'html_types/' . $options['search_view'] . '/index.php';
        if (class_exists($class_name)) {
            $this->meta_filters_obj[$key] = new $class_name($key, $options, $this->woof_settings);
            self::$includes['js_init_functions']["meta_" . $options['search_view']] = $this->meta_filters_obj[$key]->get_js_func_name();
        }
    }

    public function wp_footer() {
        
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-meta-filter">
                <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                <span><?php _e("Meta Data", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        require_once $this->get_ext_path() . 'classes/woof_pds_cpt.php';
        if (class_exists('WOOF_PDS_CPT', false)) {
            $pds_cpt = new WOOF_PDS_CPT();
            $this->excluded_meta = array_merge($pds_cpt->get_internal_meta_keys(), $this->excluded_meta);
        }
        wp_enqueue_script('woof_qs_admin', $this->get_ext_link() . 'js/admin.js');
        //***
        global $WOOF;
        $data = array();

        $data['woof_settings'] = $this->woof_settings;
        $data['meta_types'] = $this->meta_filter_types;
        $data['metas'] = (isset($data['woof_settings']['meta_filter'])) ? $data['woof_settings']['meta_filter'] : array();

        echo $WOOF->render_html($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    // +++
    //ajax
    public function woof_meta_get_keys() {
        $res = '';

        require_once $this->get_ext_path() . 'classes/woof_pds_cpt.php';
        if (class_exists('WOOF_PDS_CPT', false)) {
            $pds_cpt=new WOOF_PDS_CPT();
            $this->excluded_meta = array_merge($pds_cpt->get_internal_meta_keys(), $this->excluded_meta);
        }
        $product_id = intval($_REQUEST['product_id']);
        if ($product_id > 0) {
            $a1 = array_keys(get_post_meta($product_id, '', true));
            $res = array_diff($a1, $this->excluded_meta);
        }

        die(json_encode(array_values($res)));
    }

    public function woof_add_items_keys($arr_keys) {
        if (!empty($this->meta_keys)) {
            $arr_keys = array_merge($arr_keys, $this->meta_keys);
        }
        return $arr_keys;
    }

    public function woof_print_html_type_options_meta() {//old
        $key = "";
        $key = str_replace('woof_print_html_type_options_', "", current_filter());
        global $WOOF;
        ?>
        <li data-key="<?php echo $key ?>" class="woof_options_li">

            <?php
            $show = 0;
            if (isset($this->woof_settings[$key]['show'])) {
                $show = $this->woof_settings[$key]['show'];
            }
            ?>

            <a href="#" class="help_tip woof_drag_and_drope" data-tip="<?php _e("drag and drope", 'woocommerce-products-filter'); ?>"><img src="<?php echo WOOF_LINK ?>img/move.png" alt="<?php _e("move", 'woocommerce-products-filter'); ?>" /></a>

            <strong style="display: inline-block; width: 176px;"><?php echo $this->woof_settings['meta_filter'][$key]['title'] ?>:</strong>

            <img class="help_tip" data-tip="<?php _e('Meta filter', 'woocommerce-products-filter') ?>" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16" width="16" />

            <div class="select-wrap">
                <select name="woof_settings[<?php echo $key ?>][show]" class="woof_setting_select">
                    <option value="0" <?php echo selected($show, 0) ?>><?php _e('No', 'woocommerce-products-filter') ?></option>
                    <option value="1" <?php echo selected($show, 1) ?>><?php _e('Yes', 'woocommerce-products-filter') ?></option>
                </select>
            </div>
            <input type="button" value="<?php _e('additional options', 'woocommerce-products-filter') ?>" data-key="<?php echo $key ?>" data-name="<?php echo $this->woof_settings['meta_filter'][$key]['title'] ?>" class="woof-button js_woof_options js_woof_options_<?php echo $key ?>" />
            <?php
            $data = array();
            $data['key'] = $key;
            $data['settings'] = $this->woof_settings;
            echo $WOOF->render_html($this->get_ext_path() . 'html_types/' . $this->woof_settings['meta_filter'][$key]['search_view'] . '/views/additional_options.php', $data);
            ?>      
        </li>
        <?php
    }

    public function woof_get_meta_query($meta_query) {
        $meta_filter_query = array();
        foreach ($this->meta_filters_obj as $obj) {
            $meta = $obj->create_meta_query();
            if ($meta) {
                $meta_filter_query[] = $meta;
            }
        }
        if (!empty($meta_filter_query)) {
            $meta_filter_query['relation'] = 'AND';
            $meta_query = array_merge($meta_query, $meta_filter_query);
        }
//        echo "<pre>";
//        var_dump($meta_query);
//        echo "</pre>";
        return $meta_query;
    }

    //compatibility with other extensions
    public static function get_meta_filter_name($request_key) {
        global $WOOF;
        foreach ($WOOF->settings['meta_filter'] as $item) {
            $key = $item['search_view'] . "_" . $item['meta_key'];
            if ($key == $request_key) {
                return WOOF_HELPER::wpml_translate(null, $item['title']);
            }
        }
        return false;
    }

    //compatibility with other extensions
    public static function get_meta_filter_option_name($request_key, $request_val) {
        global $WOOF;
        $option_name = "";
        foreach ($WOOF->settings['meta_filter'] as $item) {
            $key = $item['search_view'] . "_" . $item['meta_key'];
            if ($key == $request_key) {
                $class_name = "WOOF_META_FILTER_" . strtoupper($item['search_view']);
                if (class_exists($class_name)) {
                    $option_name = $class_name::get_option_name($request_val, $request_key);
                    return WOOF_HELPER::wpml_translate(null, $option_name);
                }
            }
        }

        return false;
    }

    // get html for  messenger
    public static function get_meta_title_messenger($request_val, $request_key) {
        $html = "";
        $title = self::get_meta_filter_name($request_key);
        $option = self::get_meta_filter_option_name($request_key, $request_val);
        if (!$title) {
            return $html;
        }
        $html = $title;
        if ($option) {
            $html .= ":" . $option;
        }
        return "<span class='woof_terms'>" . $html . "</span><br />";
    }

}

WOOF_EXT::$includes['applications']['meta_filter'] = new WOOF_META_FILTER();
