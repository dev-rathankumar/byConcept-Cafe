<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
 
 class WOOF_META_FILTER_TEXTINPUT extends WOOF_META_FILTER_TYPE {
    public $type='textinput';
    protected $js_func_name="woof_init_meta_text_input"; 
    public function __construct($key,$options,$woof_settings) {
        parent::__construct($key,$options,$woof_settings);
        $this->value_type=(isset($this->woof_settings['meta_filter'][$this->meta_key]['type']))?$this->woof_settings['meta_filter'][$this->meta_key]['type']:'string';
        $this->init();
    } 
    
    public  function init(){
        if(!isset($this->woof_settings[$this->meta_key]['text_conditional'])){
            $this->woof_settings[$this->meta_key]['text_conditional']="LIKE";
        }     
        add_action('woof_print_html_type_options_' . $this->meta_key,array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' .$this->meta_key,array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer',array($this, 'wp_footer') );
        add_action('wp_head',array($this, 'wp_head') );
        add_filter('woof_extensions_type_index',array($this, 'add_type_index'));
        // it can works separatly
        //add_filter('woof_get_meta_query',array($this, 'woof_get_meta_query'));
    } 
    public function wp_head()
    {
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
         wp_enqueue_script( 'meta-textinput-js',  $this->get_meta_filter_link(). 'js/textinput.js', array('jquery'), '', true );
         wp_enqueue_style( 'meta-textinput-css',  $this->get_meta_filter_link(). 'css/textinput.css' );
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
        $data['loader_img']=WOOF_LINK.'img/eye-icon1.png';
        if(isset($this->woof_settings[$this->meta_key]) AND isset($this->woof_settings[$this->meta_key]["show"]) AND $this->woof_settings[$this->meta_key]["show"] ){
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
        $curr_text=$this->check_current_request();
        if($curr_text){
            $compare='LIKE';
            if(isset($this->woof_settings[$this->meta_key]["text_conditional"]) AND $this->woof_settings[$this->meta_key]["text_conditional"]=='='){
                $compare='=';
            }            
            $meta=array(
                       'key' => $this->meta_key,
                       'value' => $curr_text,
                       'compare'=>$compare,
                       'type'    => $this->value_type,
                   );    
            return $meta;
        }else{
            return false;
        }
    }

    public function woof_get_meta_query($meta_query){
        $meta=$this->create_meta_query();
        if($meta){
            $meta_query=array_merge($meta_query,array($meta));
        }
        return $meta_query;
    }
    public function get_js_func_name(){
        return $this->js_func_name;
    }
    public static function get_option_name($value,$key=NULL){
        return '"'.$value.'"';
    }
}
