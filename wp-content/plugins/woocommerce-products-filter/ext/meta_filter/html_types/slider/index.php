<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
 
 class WOOF_META_FILTER_SLIDER extends WOOF_META_FILTER_TYPE {
    public $type='slider';
    public $js_func_name="woof_init_meta_slider";
    public $range="1-100";
    public function __construct($key,$options,$woof_settings) {
        parent::__construct($key,$options,$woof_settings);
        $this->init();
    } 
    public  function init(){
        add_action('woof_print_html_type_options_' . $this->meta_key,array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' .$this->meta_key,array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer',array($this, 'wp_footer') ); 
        if(isset($this->woof_settings[$this->meta_key]['range'])){
            $this->range=$this->woof_settings[$this->meta_key]['range'];
        }else{
            $this->woof_settings[$this->meta_key]['range']="1-100";
            $this->woof_settings[$this->meta_key]['step']=1;
            $this->woof_settings[$this->meta_key]['prefix']=$this->woof_settings[$this->meta_key]['postfix']="";
        }
        add_filter('woof_extensions_type_index',array($this, 'add_type_index'));
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
    public function wp_footer(){
        wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion-rangeSlider/ion.rangeSlider.min.js', array('jquery'));
        wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css');   
        $ion_slider_skin = $this->woof_settings['ion_slider_skin'];
        wp_enqueue_style('ion.range-slider-skin', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.' . $ion_slider_skin . '.css');
        wp_enqueue_script( 'meta-slider-js',  $this->get_meta_filter_link(). 'js/slider.js', array('jquery'), '', true );
        wp_enqueue_style( 'meta-slider-css',  $this->get_meta_filter_link(). 'css/slider.css' );
    } 
    public function woof_print_html_type_meta(){
        $data['meta_key']=$this->meta_key;
        $data['options']=$this->type_options;
        $data['meta_settings']= $data['meta_options']= (isset($this->woof_settings[$this->meta_key]))?$this->woof_settings[$this->meta_key]:"";
        $data['range']=$this->range;
        if(isset($this->woof_settings[$this->meta_key]["show"]) AND $this->woof_settings[$this->meta_key]["show"]){
            echo  $this->render_html($this->get_meta_filter_path().'/views/woof.php', $data);
        }
    }    
    protected function draw_additional_options(){
        $data=array();
        $data['key']=$this->meta_key;
        $data['settings']=$this->woof_settings;
        return $this->render_html($this->get_meta_filter_path().'/views/additional_options.php', $data);
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
        $curr_request=$this->check_current_request();
        if($curr_request){  
            $curr_range=array();
            $curr_range=explode("-",$curr_request);
            $from=0;
            $to=0;
            $from= floatval($curr_range[0]); 
            if(count($curr_range)>1){
                $to= floatval($curr_range[1]);                 
            }else{
                $range=explode("-",$this->range,2);
                $to=$range[1];
            }
            
            $meta=array(
                'key' => $this->meta_key,
                'value'   => array( $from, $to ),
		'type'    => 'numeric',
                'compare'=>'BETWEEN',
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
        global $WOOF;
        $value_txt="";
        $prefix="";
        $postfix="";
        $arr_val= explode("-", $value,2);
        if(count($arr_val)>1){
            if($key){
                $meta_key=str_replace("slider_", "",$key);
                $prefix=(isset($WOOF->settings[$meta_key]['prefix']))?$WOOF->settings[$meta_key]['prefix']:"";
                $postfix=(isset($WOOF->settings[$meta_key]['postfix']))?$WOOF->settings[$meta_key]['postfix']:"";             
            }
           $value_txt = sprintf(__('from %s %s to %s %s', 'woocommerce-products-filter'),$prefix, $arr_val[0], $arr_val[1],$postfix); 
        }
        
        return $value_txt;
    }
}
