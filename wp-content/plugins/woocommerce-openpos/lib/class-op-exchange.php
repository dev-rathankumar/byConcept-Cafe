<?php
if(!class_exists('OP_Exchange'))
{
    class OP_Exchange{
        public $_post_type = '_op_exchange';
        public $_cashier_meta_key = '_op_cashier_id';
        public $_base_path;
        public function __construct()
        {

            $this->init();
        }
        function init(){
            //exchange html order
            add_filter( 'woocommerce_admin_order_items_after_fees', array( $this, 'exchange_woocommerce_admin_order_items_after_fees' ),10,1 );
        }

        public function exchange_woocommerce_admin_order_items_after_fees($order_id){
            $exchanges = $this->exchanges($order_id);
            ?>
            </tbody>
            <tbody id="order_exchange_line_items">
            <?php
            include(OPENPOS_DIR.'templates/admin/woocommerce/order_exchanges.php');
            ?>
            </tbody>
            <?php
        }

        public function exchanges($order_id){
            $result = array();

            $posts = get_posts([
                'post_type' => $this->_post_type,
                'post_status' => array('publish','draft'),
                'numberposts' => -1,
                'post_parent' => $order_id
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
        public function save($order_id,$exchange,$session_data){
            global $op_warehouse;
            $id  = 0;
            if(isset($exchange['id']) && $exchange['id'] > 0)
            {
                $id = $exchange['id'];
            }

            $cashier_id = isset($session_data['user_id']) ? $session_data['user_id'] : 0;
            $reason = isset($exchange['reason']) ? $exchange['reason'] :'';


            $exchange_title = sprintf( __( 'Exchange &ndash; %s', 'openpos' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'openpos' ) ) );
            $args = array(
                'ID' => 0,
                'post_title' => $exchange_title,
                'post_content' => $reason,
                'post_type' => $this->_post_type,
                'post_status' => isset($exchange['status']) ? $exchange['status'] : 'publish',
                'post_parent' => $order_id,
                'post_author' => $cashier_id
            );
            $post_id = wp_insert_post($args);
            if(!is_wp_error($post_id)){
                $warehouse_id = isset($session_data['login_warehouse_id']) ? $session_data['login_warehouse_id'] : 0;
                $register_id = isset($session_data['login_cashdrawer_id']) ? $session_data['login_cashdrawer_id'] : 0;
                $local_id = $id;
                $fee_amount = isset($exchange['fee_amount']) ? $exchange['fee_amount'] : 0;
                $refund_total = isset($exchange['refund_total']) ? $exchange['refund_total'] : 0;
                $addition_total = isset($exchange['addition_total']) ? $exchange['addition_total'] : 0;
                $created_at_time = isset($exchange['created_at_time']) ? $exchange['created_at_time'] : (time() * 1000);
                $return_items = isset($exchange['return_items']) ? $exchange['return_items'] :array();
                $exchange_items = isset($exchange['exchange_items']) ? $exchange['exchange_items'] :array();

                update_post_meta($post_id,'_warehouse_id',$warehouse_id);
                update_post_meta($post_id,'_register_id',$register_id);
                update_post_meta($post_id,'_local_id',$local_id);
                update_post_meta($post_id,'_local_created_time',$created_at_time);
                update_post_meta($post_id,'_fee_amount',$fee_amount);
                update_post_meta($post_id,'_refund_total',$refund_total);
                update_post_meta($post_id,'_addition_total',$addition_total);


                update_post_meta($post_id,'_return_items',$return_items);
                update_post_meta($post_id,'_exchange_items',$exchange_items);


                foreach($exchange_items as $item)
                {
                    if(isset($item['product']) && isset($item['product']['id']))
                    {
                        $product_id = $item['product']['id'];
                        $qty = $item['qty'];
                        //start reduct qty
                        if($warehouse_id > 0 && $product_id && $qty > 0)
                        {
                            $current_qty = $op_warehouse->get_qty($warehouse_id,$product_id);
                            $new_qty = $current_qty - $qty;
                            $op_warehouse->set_qty($warehouse_id,$product_id,$new_qty);
                        }
                        //end reduct qty
                    }
                }

                return $post_id;
            }else{
                //there was an error in the post insertion,
                throw new Exception($post_id->get_error_message()) ;
            }
        }
        public function get($id)
        {
            $post = get_post($id);
            if($post->post_type != $this->_post_type)
            {
                return array();
            }
            $cashier_id = $post->post_author;
            $user = get_userdata($cashier_id);
            $_addition_total = get_post_meta($id,'_addition_total',true);
            $reason = $post->post_content;
            $exchange_items = get_post_meta($id,'_exchange_items',true);
            $result = array(
                'id' => $id,
                'title' => $post->post_title,
                'addition_amount' => $_addition_total > 0 ? $_addition_total : 0,
                'by' => $user->nickname,
                'user_id' => $cashier_id,
                'reason' => $reason,
                'exchange_items' => $exchange_items
            );

            return $result;
        }


    }
}
?>