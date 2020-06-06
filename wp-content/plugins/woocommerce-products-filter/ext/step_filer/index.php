<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//29-10-2018
final class WOOF_EXT_STEP_FILTER extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'step_filter'; //should be defined!!

    public function __construct() {
        parent::__construct();
        add_shortcode("woof_step", array($this, 'woof_step'));
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {

        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);
        self::$includes['css']['woof_' . $this->folder_name . '_html_items'] = $this->get_ext_link() . 'css/' . $this->folder_name . '.css';
        self::$includes['js']['woof_step_filter_html_items'] = $this->get_ext_link() . 'js/step_filter.js';
        self::$includes['js_init_functions'][$this->folder_name] = 'woof_step_filter_html_items';
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-stat">
                <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                <span><?php _e("Step by step filter", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        $data = array();
        echo $WOOF->render_html($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function woof_step($args) {
        $shortcode_txt = "";
        if (isset($args['taxonomies'])) {
            $shortcode_txt .= " taxonomies=" . $args['taxonomies'];
        }
        if (isset($args['tax_only'])) {
            $shortcode_txt .= " tax_only=" . $args['tax_only'];
        }

        if (isset($args['tax_exclude'])) {
            $shortcode_txt .= " tax_exclude=" . $args['tax_exclude'];
        }

        if (isset($args['by_only'])) {
            $shortcode_txt .= " by_only=" . $args['by_only'];
        }

        if (isset($args['autohide'])) {
            $shortcode_txt .= " autohide=" . $args['autohide'];
        }

        if (isset($args['redirect'])) {
            $shortcode_txt .= " redirect=" . $args['redirect'];
        }

        if (isset($args['sid'])) {
            $shortcode_txt .= " sid=" . $args['sid'];
        }
        if (isset($args['dynamic_recount'])) {
            $shortcode_txt .= " dynamic_recount=" . $args['dynamic_recount'];
        }
        if (isset($args["by_step"])) {
            $shortcode_txt .= " by_step=" . $args["by_step"];
        } else {
            $shortcode_txt .= " by_step=''";
        }

        $data["hide"] = 1;
        if (isset($args['hide'])) {
            $data["hide"] = $args['hide'];
        }

        $data["autosubmit"] = 0;
        if (isset($args['autosubmit'])) {
            $data["autosubmit"] = $args['autosubmit'];
        }


        $data["next_btn_txt"] = __('Next', 'woocommerce-products-filter');
        if (isset($args["next_btn_txt"])) {
            $data["next_btn_txt"] = $args["next_btn_txt"];
        }
        $data["prev_btn_txt"] = __('Back', 'woocommerce-products-filter');
        if (isset($args["prev_btn_txt"])) {
            $data["prev_btn_txt"] = $args["prev_btn_txt"];
        }
        $data["filter_type"] = 1;
        if (isset($args["filter_type"])) {
            $data["filter_type"] = intval($args["filter_type"]);
            if ($data["filter_type"] > 2) {
                $data["filter_type"] = 2;
            }
        }



        $data["shortcode_woof"] = "[woof ajax_redraw=1 autosubmit=0  " . $shortcode_txt . "]";

        global $WOOF;
        return $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcode' . DIRECTORY_SEPARATOR . 'step_filter.php', $data);
    }

}

WOOF_EXT::$includes['applications']['step_filter'] = new WOOF_EXT_STEP_FILTER();
