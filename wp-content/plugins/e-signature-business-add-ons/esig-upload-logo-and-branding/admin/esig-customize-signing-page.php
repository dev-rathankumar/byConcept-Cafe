<?php

/**
 * Description of esig-customize-signing-page
 *
 * @author Abu shoaib
 */

if(!class_exists("Esig_branding_signing_page")):
class Esig_branding_signing_page{
    
        const HEADER_COLOR= "esig-front-header-color" ;
        const FOOTER_COLOR = "esig-front-footer-color" ; 
    
        public function __construct(){
            //add_filter('esig-branding-before-content', array($this, 'display_signing_branding_view'), 10, 1);
            //add_filter('esig-header-color', array($this, 'display_header_color'));
           // add_filter('esig-footer-color', array($this, 'display_footer_color'));
        }
        
        private static function save_header_color($header_color){
            WP_E_Sig()->setting->set_generic(self::HEADER_COLOR,$header_color);
        }
        
        private static function save_footer_color($footer_color){
            WP_E_Sig()->setting->set_generic(self::FOOTER_COLOR,$footer_color);
        }
        
        public static function get_header_color(){
            
            $header_color = WP_E_Sig()->setting->get_generic(self::HEADER_COLOR);
            if($header_color){
                return $header_color;
            }
            
            return '#23282e';
        }
        
        public static function get_footer_color(){
            
            $footer_color =  WP_E_Sig()->setting->get_generic(self::FOOTER_COLOR);
            
            if($footer_color){
                return $footer_color;
            }
            
            return '#23282e';
        }
        
        public function display_header_color(){
            echo 'style="background:'. self::get_header_color() .';"';
        }
        
        public function display_footer_color(){
            echo 'style="background:'. self::get_footer_color() .';"';
        }
        
        public function display_signing_branding_view($esig_branding_more_content){
            
             if (count($_POST) > 0 && isset($_POST['branding_submit']) && $_POST['branding_submit'] == 'Save Settings') {
                 self::save_header_color($_POST['esig-front-header-color']);
                 self::save_footer_color($_POST['esig-front-footer-color']);
             }
            
            $view_template = dirname(__FILE__) . "/view/signing-page-view.php";
            $esig_branding_more_content .= WP_E_Sig()->view->renderPartial('','', false, '', $view_template);

            return $esig_branding_more_content;
            
        }
        
}
endif ;

// initialize 
//new Esig_branding_signing_page();
