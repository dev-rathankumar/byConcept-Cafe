<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
 
 class WOOF_META_FILTER_SELECT extends WOOF_META_FILTER_TYPE {
    public $type='select';
    public $js_func_name= 'woof_init_meta_selects';
    public function __construct($key,$options,$woof_settings) {
        parent::__construct($key,$options,$woof_settings);
        $this->value_type=(isset($this->woof_settings['meta_filter'][$this->meta_key]['type']))?$this->woof_settings['meta_filter'][$this->meta_key]['type']:'string';
        $this->init();
    } 
    public  function init(){
        add_action('woof_print_html_type_options_' . $this->meta_key,array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' .$this->meta_key,array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer',array($this, 'wp_footer') );
        add_filter('woof_extensions_type_index',array($this, 'add_type_index'));
    } 
    public function wp_footer(){
        wp_enqueue_script( 'meta-select-js',  $this->get_meta_filter_link(). 'js/select.js', array('jquery'), '', true );
        wp_enqueue_style( 'meta-select-css',  $this->get_meta_filter_link(). 'css/select.css' );
    }    
     
    public function get_meta_filter_path(){
        return plugin_dir_path(__FILE__);
    }

    public function get_meta_filter_link(){
        return plugin_dir_url(__FILE__);
    }
    public function add_type_index($indexes){
        $indexes[]='"'.$this->type."_".$this->meta_key.'"' ;
        return $indexes;
        
    }
    protected function draw_additional_options(){
        $data=array();
        $data['key']=$this->meta_key;
        $data['settings']=$this->woof_settings;
        return $this->render_html($this->get_meta_filter_path().'/views/additional_options.php', $data);
    }
    public function woof_print_html_type_meta(){

        $data['meta_key']=$this->meta_key;
        $data['options']=$this->type_options;
        $data['meta_options']= (isset($this->type_options["options"]))?$this->type_options["options"]:"";
        $data['meta_settings']=(isset($this->woof_settings[$this->meta_key]))?$this->woof_settings[$this->meta_key]:"";
        if($this->woof_settings[$this->meta_key]["show"]){
            echo  $this->render_html($this->get_meta_filter_path().'/views/woof.php', $data);
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
    public function create_meta_query(){
        $curr_text="";
        $curr_index=$this->check_current_request();
        $options=(isset($this->type_options["options"]))?$this->type_options["options"]:"";
        $options=explode(',',$options);
        if(isset($options[intval($curr_index)-1])){
            $curr_text= $options[intval($curr_index)-1]; 
            $custom_title=explode('^',$options[intval($curr_index)-1],2);
            if(count($custom_title)>1){
                $curr_text=$custom_title[1];
            }
        }
        if($curr_text){           
            $meta=array(
                       'key' => $this->meta_key,
                       'value' => $curr_text,
                       'compare'=>'=',
                       'type'    => $this->value_type,
                   );  
                   
            return $meta;
        }else{
            return false;
        }
    }
    public function get_js_func_name(){
        return $this->js_func_name;
    }   
    public static function get_option_name($value,$key=NULL){
        $option_txt="";
        global $WOOF;
        if($key){
            $meta_key=str_replace("select_", "",$key);
            $options=explode(',',(isset($WOOF->settings['meta_filter'][$meta_key]["options"]))?$WOOF->settings['meta_filter'][$meta_key]["options"]:"");
            if(!empty($options) AND isset($options[intval($value-1)]) ){
                $op_title=explode('^',$options[intval($value)-1],2);
                if(count($op_title)>1){
                    $curr_title=$op_title[0];
                }else{
                    $curr_title=$options[intval($value)-1];
                }
                $option_txt=WOOF_HELPER::wpml_translate(null,$curr_title);
            }
        }
        return $option_txt;
    }    
}
