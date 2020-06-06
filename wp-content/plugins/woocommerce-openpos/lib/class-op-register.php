<?php
if(!class_exists('OP_Register'))
{
    class OP_Register{
        public $_post_type = '_op_register';
        public $_cashiers_meta_key = '_op_cashiers';
        public $_warehouse_meta_key = '_op_warehouse';
        public $_filesystem;
        public $_bill_data_path;
        public $_base_path;
        public function __construct()
        {
            if(!class_exists('WP_Filesystem_Direct'))
            {
                require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
                require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
            }
            $this->_filesystem = new WP_Filesystem_Direct(false);
            $this->_base_path =  WP_CONTENT_DIR.'/uploads/openpos';
            $this->_bill_data_path =  $this->_base_path.'/registers';
            $this->init();
        }
        function init(){
            // create openpos data directory

            if(!file_exists($this->_base_path))
            {
                $this->_filesystem->mkdir($this->_base_path);
            }

            if(!file_exists($this->_bill_data_path))
            {
                $this->_filesystem->mkdir($this->_bill_data_path);
            }
        }
        public function registers(){
            $result = array();

            $posts = get_posts([
                'post_type' => $this->_post_type,
                'post_status' => array('publish','draft'),
                'numberposts' => -1
                // 'order'    => 'ASC'
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
            }

        }
        public function save($params){

            $id  = 0;
            if(isset($params['id']) && $params['id'] > 0)
            {
                $id = $params['id'];
            }
            $warehouse_id = isset($params['warehouse']) ? $params['warehouse'] : 0;
            $args = array(
                'ID' => $id,
                'post_title' => $params['name'],
                'post_type' => $this->_post_type,
                'post_status' => $params['status'],
                'post_parent' => $warehouse_id
            );
            $post_id = wp_insert_post($args);
            if(!is_wp_error($post_id)){

                $cashiers = array();
                if(isset($params['cashiers']))
                {
                    $cashiers = $params['cashiers'];
                }
                $mode = isset($params['register_mode']) ? esc_attr($params['register_mode']) : 'cashier';
                update_post_meta($post_id,'_op_mode',$mode);
                update_post_meta($post_id,$this->_cashiers_meta_key,$cashiers);
                update_post_meta($post_id,$this->_warehouse_meta_key,$warehouse_id);
                return $post_id;
            }else{
                //there was an error in the post insertion,
                throw new Exception($post_id->get_error_message()) ;
            }
        }
        public function get($id)
        {
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
            $warehouse = get_post_meta($id,$this->_warehouse_meta_key,true);

            $cashiers = get_post_meta($id,$this->_cashiers_meta_key,true);
            $register_mode = get_post_meta($id,'_op_mode',true);
            if(!$register_mode)
            {
                $register_mode = 'cashier';
            }
            $status = $post->post_status;
            $result = array(
                'id' => $id,
                'name' => $name,
                'warehouse' => $warehouse,
                'cashiers' => $cashiers,
                'balance' => $this->cash_balance($id),
                'register_mode' => $register_mode,
                'status' => $status
            );

            return apply_filters('op_register_get_data',$result,$this);

        }
        public function cash_balance($register_id = 0){
            $option_key = $this->get_transaction_balance_key($register_id);
            return get_option($option_key,0);
        }
        public function get_transaction_balance_key($register_id = 0){
            $option_key = '_pos_cash_balance_'.$register_id;
            return $option_key;
        }
        public function get_order_meta_key(){
            $option_key = '_pos_order_cashdrawer';
            return $option_key;
        }
        public function get_transaction_meta_key(){
            $option_key = '_pos_transaction_cashdrawer';
            return $option_key;
        }
        public function addCashBalance($register_id = 0 ,$amount = 0){
            $current_balance = $this->cash_balance($register_id);
            $new_blance = $current_balance + $amount;
            update_option($this->get_transaction_balance_key($register_id),$new_blance);
        }
        public function transactions($register_id = 0){

        }
        public function update_bill_screen($session_data,$cart_data){
            $register_id = isset($session_data['login_cashdrawer_id']) ? $session_data['login_cashdrawer_id'] : 0;

            if($register_id)
            {
                $cart_data['session_user'] = $session_data['name'];
                $cart_data['register'] = $this->get($register_id);
                if(!isset($cart_data['items']))
                {
                    $cart_data['items'] = array();
                }
                if(!isset($cart_data['grand_total']))
                {
                    $cart_data['grand_total'] = 0;
                }

                $register_file = $this->bill_screen_file_path($register_id);
                $file_mode = apply_filters('op_file_mode',0755) ;
                if(file_exists($register_file))
                {
                    if ( defined( 'FS_CHMOD_FILE' ) ) {
                        $this->_filesystem->put_contents(
                            $register_file,
                            json_encode($cart_data)
                        );
                    }else{
                        $this->_filesystem->put_contents(
                            $register_file,
                            json_encode($cart_data),
                            $file_mode
                        );
                    }
                }else{
                    
                    $this->_filesystem->put_contents(
                        $register_file,
                        json_encode($cart_data),
                        $file_mode
                    );
                }
                
            }
        }
        public function bill_screen_file_path($register_id)
        {
            return $this->_bill_data_path.'/'.$register_id.'.json';
        }
        public function bill_screen_file_url($register_id)
        {
            $upload_dir = wp_upload_dir();
            $url = $upload_dir['baseurl'];
            $url = ltrim($url,'/');
            if(is_multisite()){
                $prefix = '/sites/'.get_current_blog_id();
                $url = str_replace($prefix, '',$url);
            }
            return $url.'/openpos/registers/'.$register_id.'.json';
        }
        public function bill_template(){
            $file_name = 'bill.txt';
            $file_path = OPENPOS_DIR.'/default/'.$file_name;
            if($this->_filesystem->is_file($file_path))
            {
                return $this->_filesystem->get_contents($file_path);
            }else{
                return '';
            }
        }
    }
}
?>