<?php

/*
 * generalsController
 * @since 1.0.1
 * @author Michael Medaglia
 * For use with static pages
 */

class WP_E_aboutsController extends WP_E_appController {

    public function __construct() {
        parent::__construct();
        $this->queueScripts();
        $this->equeueScripts();
        $this->settings = new WP_E_Setting();
        $this->document = new WP_E_Document();
        $this->user = new WP_E_User();
        $this->general = new WP_E_General();
    }

    public function calling_class() {
        return get_class();
    }

    private function queueScripts() {
        wp_enqueue_style('esig-about-style', ESIGN_ASSETS_DIR_URI . "/css/esign.about.css"); 
        wp_enqueue_style('esig-install-checklist-style', ESIGN_ASSETS_DIR_URI . "/css/esign.installcheck.css");
    }
    private function equeueScripts() {
       
       wp_enqueue_script('esig-install-checklist-scripts', ESIGN_ASSETS_DIR_URI . "/js/esign.installcheck.js");
       wp_localize_script('esig-install-checklist-scripts', 'esig_system_requirement', array('count' => count($this->get_system_requirement_array())));
        
    }

    public function index() {

        $template_data = array(
            "version_no" => esigGetVersion(),
            "ESIGN_ASSETS_URL" => ESIGN_ASSETS_DIR_URI,
        );

        $this->view->render('about', 'about', $template_data);
    }

    public function systeminfo($data_return = false) {
      
        $template_data = array();

        if (count($_POST) > 0) {

            $this->esig_sysinfo_download();
        }

        //new thing
        $template_data['hosting_info'] = $this->esig_get_host();
        $template_data['remote_post'] = $this->remote_post_working();
        $template_data['esign_pages'] = $this->esign_pages();

        // templates start here 
        if ($data_return) {
            return $template_data;
        }
        $this->view->render('about', 'systeminfo', $template_data);
    }

    /**
     * Get user host
     *
     * Returns the webhost this site is using if possible
     *
     * @since 1.4.0
     * @return mixed string $host if detected, false otherwise
     */
    public function esig_get_host() {
        $host = false;

        if (defined('WPE_APIKEY')) {
            $host = 'WP Engine';
        } elseif (defined('PAGELYBIN')) {
            $host = 'Pagely';
        } elseif (DB_HOST == 'localhost:/tmp/mysql5.sock') {
            $host = 'ICDSoft';
        } elseif (DB_HOST == 'mysqlv5') {
            $host = 'NetworkSolutions';
        } elseif (strpos(DB_HOST, 'ipagemysql.com') !== false) {
            $host = 'iPage';
        } elseif (strpos(DB_HOST, 'ipowermysql.com') !== false) {
            $host = 'IPower';
        } elseif (strpos(DB_HOST, '.gridserver.com') !== false) {
            $host = 'MediaTemple Grid';
        } elseif (strpos(DB_HOST, '.pair.com') !== false) {
            $host = 'pair Networks';
        } elseif (strpos(DB_HOST, '.stabletransit.com') !== false) {
            $host = 'Rackspace Cloud';
        } elseif (strpos(DB_HOST, '.sysfix.eu') !== false) {
            $host = 'SysFix.eu Power Hosting';
        } elseif (strpos($_SERVER['SERVER_NAME'], 'Flywheel') !== false) {
            $host = 'Flywheel';
        } else {
            // Adding a general fallback for data gathering
            $host = 'DBH: ' . DB_HOST . ', SRV: ' . $_SERVER['SERVER_NAME'];
        }

        return $host;
    }

    public function remote_post_working() {

        $params = array(
            'sslverify' => false,
            'timeout' => 60,
            'user-agent' => 'ESIGN/' . esigGetVersion(),
            'body' => array("cmd" => '_notify-validate')
        );

        $response = wp_remote_post('https://www.paypal.com/cgi-bin/webscr', $params);
        
        if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
            $WP_REMOTE_POST = true;
        } else {
            $WP_REMOTE_POST = false;
        }
        return $WP_REMOTE_POST;
    }

    public function esign_pages() {
        // esign pages start here 

        $pageID = WP_E_Sig()->setting->get_default_page();
        $core_html = null;
        if (!WP_E_Sig()->document->document_document_page_exists($pageID)) {


            $page_data = get_page($pageID);
            if ($page_data) :
                if (function_exists('has_shortcode')) {
                    if (has_shortcode($page_data->post_content, 'wp_e_signature')) {
                        $page_title = $page_data->post_title;
                        $permalink = get_permalink($page_data->ID);
                        //$permalink="post.php?post={$pageID}&action=edit";
                        $core_html .=$page_title . "\t" . $permalink . "\n\t\t\t";
                    }
                } else {
                    $core_html .= page_title . " \t\t " . $permalink . "No shortcode found\n\t\t\t";
                }
            endif;
        }
        global $wpdb;
        $table = $wpdb->prefix . 'esign_documents_stand_alone_docs';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $sad_page_id = $wpdb->get_col("SELECT page_id FROM {$table}");

            foreach ($sad_page_id as $page_id) {

                $page_data = get_page($page_id);
                if ($page_data) :
                    if (function_exists('has_shortcode')) {
                        if (has_shortcode($page_data->post_content, 'wp_e_signature_sad')) {
                            $page_title = $page_data->post_title;
                            $permalink = get_permalink($page_data->ID);
                            //$permalink="post.php?post={$page_id}&action=edit";
                            $core_html .= $page_title . "\t" . $permalink . "\n\t\t\t";
                        }
                    }
                endif;
            }
        }
        return $core_html;
    }

    /**
     * Generates a System Info download file
     *
     * @since       1.4.0
     * @return      void
     */
    public function esig_sysinfo_download() {

        nocache_headers();

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="wp-esignature-system-info.txt"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
       // header('Content-Length: ' . strlen($pdf_buffer));
        ob_clean();
        flush();
        echo wp_strip_all_tags($_POST['esig_system_info']);
        exit;
    }
    
    public function get_system_requirement_array(){
        $array = array(0 =>"memory_limit",1=> "php",2=>"wordpress",3=>"curl",4=>"remote",5=>"gd",6=>"asset_temp",7=>"mcrypt");
        return $array;
    }
    
    public function esig_requirement_checking(){
       //$content =  WP_E_Sig()->view->renderPartial('', false, false, '', ESIGN_PLUGIN_PATH . "/views/about/checklist-view-failed.php");
       //$content =  WP_E_Sig()->view->renderPartial('', false, false, '', ESIGN_PLUGIN_PATH . "/views/about/checklist-view-success.php");
        //echo $content;
        $system_index = (isset($_POST['esig_system_index']))?$_POST['esig_system_index'] : false; 
        $array = $this->get_system_requirement_array();
        global $is_esig_system_error; 
        $is_esig_system_error=false;
        $result = array();
        $result['display']='scroll';
        if($system_index){
            
            if(array_key_exists($system_index, $array)){
                if($array[$system_index] == "memory_limit"){ 
                    $result['content'] = "Checking memory limit at least 96M";
                    
                   
                }
                if($array[$system_index] == "php"){
                    $result['content'] = "Checking for php version...";
                   
                   
                }
                if($array[$system_index] == "wordpress"){
                    $result['content'] = "Checking for Wordpress version...";
                    
                   
                }  
                if($array[$system_index] == "curl"){ 
                    $result['content'] = "Checking for PHP Curl...";
                    
                }
                if($array[$system_index] == "mcrypt"){ 
                    $result['content'] = "Checking for PHP Openssl...";
                    
                }
                if($array[$system_index] == "remote"){
                    $result['content'] = "Checking for REMOTE GET/POST...";
                   
                }
                if($array[$system_index] == "gd"){
                    $result['content'] = "Checking for Php GD library...";
                   
                }
                if($array[$system_index] == "asset_temp"){
                    $result['content'] = "Checking for Assets/temps/ directory permission...";
                   
                }
                echo json_encode($result);
                return;
            }
        }
        
        
         $result['display'] = 'success';
         $content =  WP_E_Sig()->view->renderPartial('', false, false, '', ESIGN_PLUGIN_PATH . "/views/about/checklist-view-failed.php");
         if($is_esig_system_error){
          // $content =  WP_E_Sig()->view->renderPartial('', false, false, '', ESIGN_PLUGIN_PATH . "/views/about/checklist-view-failed.php");  
            $result['content'] = $content; 
         }
         else{
            $content =  WP_E_Sig()->view->renderPartial('', false, false, '', ESIGN_PLUGIN_PATH . "/views/about/checklist-view-success.php");  
            $result['content'] = $content;  
         }
        echo json_encode($result);
        return;
      
    }

}
