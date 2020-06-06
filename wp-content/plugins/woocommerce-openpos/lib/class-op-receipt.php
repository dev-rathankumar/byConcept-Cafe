<?php
if(!class_exists('OP_Receipt'))
{
    class OP_Receipt{
        public $_post_type = '_op_receipt';
        
       
        public function __construct()
        {
           
            $this->init();
        }
        function init(){
            // create openpos data directory
            add_action( 'wp_ajax_openpos_update_receipt_template', array($this,'update_receipt_template') );
            add_action( 'wp_ajax_openpos_delete_receipt', array($this,'delete_receipt') );
            add_action( 'wp_ajax_openpos_update_receipt_content', array($this,'update_receipt_content') );
            add_action( 'wp_ajax_openpos_update_receipt_draft', array($this,'update_receipt_draft') );
            add_action( 'wp_ajax_openpos_update_receipt_preview', array($this,'receipt_preiew') );
            add_action( 'op_register_form_end', array($this,'op_register_form_end'),10,1 );
            add_action( 'op_register_save_after', array($this,'op_register_save_after'),10,3 );
           

            add_filter('op_get_login_cashdrawer_data',array($this,'op_setting_data'),10,1);
            

            
           
        }
        public function templates($status = ''){
            $result = array();

            if(!$status)
            {
                $statues = array('publish','draft');
            }else{
                $statues = array($status);
            }

            $posts = get_posts([
                'post_type' => $this->_post_type,
                'post_status' => $statues,
                'numberposts' => -1
            ]);
            foreach($posts as $p)
            {
                $result[] = $this->get($p->ID);
            }
            return $result;
        }
        public function delete($id){
            $post = get_post($id);
            if($post->post_type == $this->_post_type)
            {
                wp_trash_post( $id  );
                return true;
            }
            return false;
        }
        public function save($params){

            $id  = 0;
            if(isset($params['id']) && $params['id'] > 0)
            {
                $id = $params['id'];
            }
            
            $args = array(
                'ID' => $id,
                'post_title' => $params['name'],
                'post_type' => $this->_post_type,
                'post_status' => $params['status'],
                'post_parent' => 0
            );
            $post_id = wp_insert_post($args);
            if(!is_wp_error($post_id)){

                
                return $post_id;
            }else{
                //there was an error in the post insertion,
                throw new Exception($post_id->get_error_message()) ;
            }
        }
        public function get($id)
        {
            global $OPENPOS_CORE;
            $post = get_post($id);
            if(!$post)
            {
                return array();
            }
            if($post->post_type != $this->_post_type)
            {
                return array();
            }
            $name = $post->post_title;
            $created_by = get_the_author_meta('display_name',$post->post_author);

          
            $created_at_time = $post->post_date;
            $created_at = $OPENPOS_CORE->render_ago_date_by_time_stamp($created_at_time);
            $status = $post->post_status;
           
            $content = $post->post_content;
            $custom_css = get_post_meta($id,'custom_css',true);
            $paper_width = get_post_meta($id,'paper_width',true);
            $padding_top = get_post_meta($id,'padding_top',true);
            $padding_right = get_post_meta($id,'padding_right',true);
            $padding_bottom = get_post_meta($id,'padding_bottom',true);
            $padding_left = get_post_meta($id,'padding_left',true);
            
            $result = array(
                'id' => $id,
                'name' => $name,
                'created_by' => $created_by,
                'created_at' => $created_at,
                'status' => $status,
                'content' => $content,
                'custom_css' => $custom_css,
                'paper_width' => $paper_width,
                'padding_top' => $padding_top,
                'padding_right' => $padding_right,
                'padding_bottom' => $padding_bottom,
                'padding_left' => $padding_left
            );
        
            return apply_filters('op_receipt_template_get_data',$result,$this);
        }
        public function update_receipt_template(){

            $result = array(
                'status' => 0,
                'message' => 'Unknown message',
                'data' => array()
            );
            $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
            $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            $status = isset($_REQUEST['status'])  ? $_REQUEST['status'] : 'draft';
            if($name)
            {
                $params = array(
                    'id' => $id,
                    'name' => $name,
                    'status' => $status
                );
                $post_id = $this->save($params);
                if($post_id)
                {
                    $result['status'] = 1;
                }
            }else{
                $result['message'] = __('Please enter template name','openpos');
            }
            echo json_encode($result);
            exit;
        }
        public function delete_receipt(){
            $result = array(
                'status' => 0,
                'message' => 'Unknown message',
                'data' => array()
            );
            $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            if($this->delete($id))
            {
                $result['status'] = 1;
            }
            echo json_encode($result);
            exit;
        }
        public function update_receipt_content(){
            $result = array(
                'status' => 0,
                'message' => 'Unknown message',
                'data' => array()
            );
            $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            $template = $this->get($id);
            if(!empty($template))
            {
                $params = $_POST;
                foreach($params as $key => $val)
                {
                    if($key == "content")
                    {
                        $my_post = array();
                        $my_post['ID'] = $id;
                        $my_post['post_content'] = $val;
                        wp_update_post( $my_post );
                    }else{
                        if($key != 'id' )
                        {
                            update_post_meta($id,$key,$val);
                        }
                    }
                }
                update_post_meta($id,'_tmp_setting','');
                $result['status'] = 1;
            }else{
                $result['message'] = 'Template not found';
            }
            echo json_encode($result);
            exit;
        }
        public function update_receipt_draft(){
            $result = array(
                'status' => 0,
                'message' => 'Unknown message',
                'data' => array()
            );
            $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            $template = $this->get($id);
            if(!empty($template))
            {
                $params = $_POST;
                $key = "_tmp_setting";
                update_post_meta($id,$key,$params);
                $result['status'] = 1;
            }else{
                $result['message'] = 'Template not found';
            }
            echo json_encode($result);
            exit;
        }
        public function receipt_preiew(){

            $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            $template = $this->get($id);
            if(!empty($template))
            {
                wp_register_script('openpos.admin.receipt.ejs', OPENPOS_URL.'/assets/js/ejs.js',array('jquery'));

                $data = $this->get_template_preview_data($template);
                
                $is_review = true;
                require(OPENPOS_DIR.'templates/admin/print_receipt.php');
            }else{
               echo 'Template not found';
            }
            
            
            exit;
        }
        public function get_template_preview_data($template){
            $setting = $template;
            $html_header = '';

            $receipt_padding_top = $setting['padding_top'];

            $unit = 'in';
            $receipt_padding_right = $setting['padding_right'];
            $receipt_padding_bottom = $setting['padding_bottom'];
            $receipt_padding_left = $setting['padding_left'];
            $receipt_width = $setting['paper_width'];
            $receipt_css = $setting['custom_css'];
           
            $receipt_template = $setting['content'];
           

            $html_header = '<style type="text/css" media="print,screen">';
            $html_header .= 'body{ ';
            $html_header .= 'background: #FFEB3B;';   
            $html_header .=  '}';

            $html_header .= '#invoice-POS { ';
            $html_header .= 'padding:  '.$receipt_padding_top.$unit. ' ' . $receipt_padding_right.$unit .' '.$receipt_padding_bottom.$unit.' '.$receipt_padding_left.$unit.';';
            $html_header .= 'margin: 0 auto;';
            $html_header .= 'background: #fff;';
            $html_header .= 'width: '.$receipt_width.$unit.' ;';
            $html_header .=  '}';

            $html_header .= $receipt_css;
            $html_header .= '</style>';
           
            $html = '<div id="invoice-POS">';
            $html_body = html_entity_decode(esc_html($receipt_template));
            $html_body = trim(preg_replace('/\s\s+/', ' ', $html_body));
            $html .= $html_body;
            $html .= '</div>';
            $html = do_shortcode($html);
            $data = array(
                'setting' => $setting,
                'html_header' => $html_header,
                'html_body' =>   addslashes($html) ,
                'order_json' =>  ''
    
            );
            return $data;
        }
        public function get_register_template($register_id){
            $template_id = get_post_meta($register_id,'_op_receipt_template',true);
            if(!$template_id)
            {
                $template_id = 0;
            }
            return $this->get($template_id);
        }
        public function op_register_form_end($default){
            $templates = $this->templates('publish');
            $current_id = 0;
           
            if(isset($default['id']) && $default['id'])
            {
                $current_receipt = $this->get_register_template($default['id']);
                if($current_receipt && !empty($current_receipt))
                {
                    $current_id = $current_receipt['id'];
                }
            }
            
            ?>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo __( 'Receipt', 'openpos' ); ?></label>
                <div class="col-sm-4">
                    <select class="form-control" name="receipt_template">
                            <option value="0" <?php echo $current_id == 0 ? 'selected':''; ?>><?php echo __('Default Template','openpos'); ?></option>
                            <?php foreach($templates as $template): ?>
                                    <option value="<?php echo $template['id']; ?>"  <?php echo $current_id == $template['id'] ? 'selected':''; ?> ><?php echo $template['name']; ?></option>
                            <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php
        }
        public function op_register_save_after($id,$params,$op_register){
               
                if($id && isset($params['receipt_template']))
                {
                    update_post_meta($id,'_op_receipt_template',(int)$params['receipt_template']);
                }
        }
        public function op_setting_data($session_response_data){
            //receipt_full_template
            $login_cashdrawer_id = $session_response_data['login_cashdrawer_id'];
            if($login_cashdrawer_id)
            {
                $receipt = $this->get_register_template($login_cashdrawer_id);
                if($receipt && !empty($receipt))
                {
                    $session_response_data['setting']['receipt_full_template'] = $this->generate_full_receipt_template($receipt);
                }
            }
            return $session_response_data;
        }
        public function generate_full_receipt_template($receipt){

            $setting = $receipt;
            $html_header = '';
            $receipt_padding_top = $setting['padding_top'];
            $unit = 'in';
            $receipt_padding_right = $setting['padding_right'];
            $receipt_padding_bottom = $setting['padding_bottom'];
            $receipt_padding_left = $setting['padding_left'];
            $receipt_width = $setting['paper_width'];
            $receipt_css = $setting['custom_css'];
            $receipt_template = $setting['content'];
            $html_header = '<style type="text/css" media="print,screen">';
            $html_header .= '#invoice-POS { ';
            $html_header .= 'padding:  '.$receipt_padding_top.$unit. ' ' . $receipt_padding_right.$unit .' '.$receipt_padding_bottom.$unit.' '.$receipt_padding_left.$unit.';';
            $html_header .= 'margin: 0 auto;';
            $html_header .= 'background: #fff;';
            $html_header .= 'width: '.$receipt_width.$unit.' ;';
            $html_header .=  '}';
            $html_header .= $receipt_css;
            $html_header .= '</style>';
            $html = '<div id="invoice-POS">';
            $html_body = html_entity_decode(esc_html($receipt_template));
            $html_body = trim(preg_replace('/\s\s+/', ' ', $html_body));
            $html .= $html_body;
            $html .= '</div>';
            return '<html><head>'.$html_header.'</head><body style="margin:0;">'.$html.'</body></html>';
        }

    }
}
?>