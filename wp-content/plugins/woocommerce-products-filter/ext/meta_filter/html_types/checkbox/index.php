<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
 
 class WOOF_META_FILTER_CHECKBOX extends WOOF_META_FILTER_TYPE {
    public $type='checkbox';
    protected $js_func_name="woof_init_meta_checkbox";
    public function __construct($key,$options,$woof_settings) {
        parent::__construct($key,$options,$woof_settings);
        $this->value_type=(isset($this->woof_settings['meta_filter'][$this->meta_key]['title']))?$this->woof_settings['meta_filter'][$this->meta_key]['title']:'string';
        $this->init();
    } 
    public  function init(){
        if(!isset($this->woof_settings[$this->meta_key]['search_option'])){
            $this->woof_settings[$this->meta_key]['search_option']=0;
        }
        if(!isset($this->woof_settings[$this->meta_key]['search_value'])){
            $this->woof_settings[$this->meta_key]['search_value']="";
        }
        
        add_action('woof_print_html_type_options_' . $this->meta_key,array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' .$this->meta_key,array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer',array($this, 'wp_footer') );
        add_action('wp_head',array($this, 'wp_head') );
        add_filter('woof_extensions_type_index',array($this, 'add_type_index'));
    } 
    public function wp_head(){
        ?>      
        <script type="text/javascript">
            if (typeof woof_lang_custom == 'undefined') {
                var woof_lang_custom = {};/*!!important*/
            }
            woof_lang_custom.<?php echo $this->type."_".$this->meta_key ?> = "<?php echo WOOF_HELPER::wpml_translate(null,$this->woof_settings['meta_filter'][$this->meta_key]['title']); ?>";
        </script>
        <?php
    }
    public function add_type_index($indexes){
        $indexes[]='"'.$this->type."_".$this->meta_key.'"' ;
        return $indexes;
        
    }

    public function wp_footer(){
         wp_enqueue_script( 'meta-checkbox-js',  $this->get_meta_filter_link(). 'js/checkbox.js', array('jquery'), '', true );
         //wp_enqueue_style( 'meta-checkbox-css',  $this->get_meta_filter_link(). 'css/checkbox.css' );
    }
    public function get_meta_filter_path(){
        return plugin_dir_path(__FILE__);
    }

    public function get_meta_filter_link(){
        return plugin_dir_url(__FILE__);
    }
    public function woof_print_html_type_meta(){
        $data['meta_key']=$this->meta_key;
        $data['options']=$this->type_options;
        $data['type']=(isset($this->woof_settings['meta_filter'][$this->meta_key]['type']))?$this->woof_settings['meta_filter'][$this->meta_key]['type']:'numeric';
        $data['search_option']=(isset($this->woof_settings[$this->meta_key]['search_option']))?$this->woof_settings[$this->meta_key]['search_option']:0;
        $data['search_value']=(isset($this->woof_settings[$this->meta_key]['search_value']))?$this->woof_settings[$this->meta_key]['search_value']:"";       
        $data['type']=(isset($this->woof_settings['meta_filter'][$this->meta_key]['type']))?$this->woof_settings['meta_filter'][$this->meta_key]['type']:'numeric';
        if($this->woof_settings[$this->meta_key]["show"]){
            echo  $this->render_html($this->get_meta_filter_path().'/views/woof.php', $data);
        }
    }   
    /*public function draw_meta_filter_structure(){
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
           
        </li><?php 
    }*/
    protected function draw_additional_options(){
        $data=array();
        $data['key']=$this->meta_key;
        $data['settings']=$this->woof_settings;
        $data['type']=(isset($this->woof_settings['meta_filter'][$this->meta_key]['type']))?$this->woof_settings['meta_filter'][$this->meta_key]['type']:'numeric';
        $data['search_option']=(isset($this->woof_settings[$this->meta_key]['search_option']))?$this->woof_settings[$this->meta_key]['search_option']:0;
        $data['search_value']=(isset($this->woof_settings[$this->meta_key]['search_value']))?$this->woof_settings[$this->meta_key]['search_value']:"";
        return $this->render_html($this->get_meta_filter_path().'/views/additional_options.php', $data);
    }
    public function create_meta_query(){
        $curr_text=$this->check_current_request();
        if($curr_text){ 
            $value=1;
            $search_option=(isset($this->woof_settings[$this->meta_key]['search_option']))?$this->woof_settings[$this->meta_key]['search_option']:0;
            if($search_option==0){
                 $type=(isset($this->woof_settings['meta_filter'][$this->meta_key]['type']))?$this->woof_settings['meta_filter'][$this->meta_key]['type']:'numeric';
                 $search_value=(isset($this->woof_settings[$this->meta_key]['search_value']))?$this->woof_settings[$this->meta_key]['search_value']:"";
                 if($type!='numeric' AND !empty($search_value)){
                     $value=$search_value;  
                 }
                $meta=array(
                           'key' => $this->meta_key,
                           'value' => $value,
                           'compare'=>'=',
                           'type'    => $this->value_type,
                       );   
            }else{   
                //EXISTS
                $meta=array(
                           'key' => $this->meta_key,
                           'compare'=>'EXISTS'
                       );    
            }    
            return $meta;
        }else{
            return false;
        }
    }
    protected function check_current_request(){
        global $WOOF;
        $request = $WOOF->get_request_data();
        if(isset($request[$this->type."_".$this->meta_key]) AND $request[$this->type."_".$this->meta_key]){
            return $request[$this->type."_".$this->meta_key];
        }
        return false;    
    }
    public function get_js_func_name(){
        return $this->js_func_name;
    }
    public static function get_option_name($value,$key=NULL){
        return false;
    }
}
