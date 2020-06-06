<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
 
abstract class  WOOF_META_FILTER_TYPE {
    protected $type_options=array();
    protected $woof_settings=array();
    protected $type="";
    protected $meta_key="";
    public $value_type='';
    public function __construct($key,$options,$woof_settings){
        $this->meta_key=$key;
        $this->type_options=$options;
        $this->woof_settings=$woof_settings;
    }
    abstract public function init();

    abstract public function get_meta_filter_path();

    abstract public function get_meta_filter_link(); 
    
    abstract public function create_meta_query();
    
    public function get_js_func_name(){
        return false;
    }
    
    protected function draw_additional_options(){
        return "";
    }
    public function draw_meta_filter_structure(){
      ?><li data-key="<?php echo $this->meta_key ?>" class="woof_options_li">
                <?php
                $show = 0;
                if (isset($this->woof_settings[$this->meta_key]['show'])) {
                    $show = $this->woof_settings[$this->meta_key]['show'];
                }
                ?>
                <a href="#" class="help_tip woof_drag_and_drope" data-tip="<?php _e("drag and drope", 'woocommerce-products-filter'); ?>"><img src="<?php echo WOOF_LINK ?>img/move.png" alt="<?php _e("move", 'woocommerce-products-filter'); ?>" /></a>

                <strong style="display: inline-block; width: 176px;"><?php echo $this->woof_settings['meta_filter'][$this->meta_key]['title'] ?>:</strong>

                <img class="help_tip" data-tip="<?php _e('Meta filter', 'woocommerce-products-filter') ?>" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16" width="16" />

                <div class="select-wrap">
                    <select name="woof_settings[<?php echo  $this->meta_key ?>][show]" class="woof_setting_select">
                        <option value="0" <?php echo selected($show, 0) ?>><?php _e('No', 'woocommerce-products-filter') ?></option>
                        <option value="1" <?php echo selected($show, 1) ?>><?php _e('Yes', 'woocommerce-products-filter') ?></option>
                    </select>
                </div>
            <input type="button" value="<?php _e('additional options', 'woocommerce-products-filter') ?>" data-key="<?php echo $this->meta_key ?>" data-name="<?php echo $this->woof_settings['meta_filter'][$this->meta_key]['title'] ?>" class="woof-button js_woof_options js_woof_options_<?php echo $this->meta_key ?>" />
             <?php 
             echo $this->draw_additional_options();
       ?></li><?php   
    }
    public function  woof_print_html_type_meta(){
        echo "<h1>",$this->meta_key,"</h1>";
    }    
    public function render_html($pagepath, $data = array()) {
        if(isset($data['pagepath'])){
            unset($data['pagepath']);
        }
        if (is_array($data) AND ! empty($data)) {
            extract($data);
        }    

        $pagepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath);
        ob_start();
        include($pagepath);
        return ob_get_clean();
    }

}
