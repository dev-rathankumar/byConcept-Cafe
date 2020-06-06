<?php
if(!class_exists('OP_Table'))
{
    class OP_Table{
        public $_post_type = '_op_table';
        public $_warehouse_meta_key = '_op_warehouse';
        public $_position_meta_key = '_op_table_position';
        public $_type_meta_key = '_op_table_type';
        public $_cost_meta_key = '_op_table_cost';
        public $_cost_type_meta_key = '_op_table_cost_type';
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
            $this->_bill_data_path =  $this->_base_path.'/tables';
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
            //upload all table with no position
            $posts = get_posts([
                'post_type' => $this->_post_type,
                'numberposts' => -1,
                'meta_query' => array(
                    array(
                        'key' => $this->_position_meta_key,
                        'compare' => 'NOT EXISTS' // this should work...
                    ),
                )
            ]);
            foreach ($posts as $post)
            {
                $post_id = $post->ID;
                update_post_meta($post_id,$this->_position_meta_key,0);
            }

        }
        public function tables($warehouse_id = -1,$is_front = false ){
            $result = array();
            if($warehouse_id >= 0)
            {
                $posts = get_posts([
                    'post_type' => $this->_post_type,
                    'post_status' => array('publish'),
                    'numberposts' => -1,
                    'order'     => 'ASC',
                    'meta_key' => $this->_position_meta_key,
                    'orderby'   => 'meta_value_num'
                ]);

                foreach($posts as $p)
                {
                    $tmp = $this->get($p->ID);
                    if($tmp['warehouse'] == $warehouse_id)
                    {
                        $result[] = $tmp;
                    }

                }
            }else{
                $posts = get_posts([
                    'post_type' => $this->_post_type,
                    'post_status' => array('publish','draft'),
                    'numberposts' => -1,
                    'order'     => 'ASC',
                    'meta_key' => $this->_position_meta_key,
                    'orderby'   => 'meta_value_num',
                ]);

                foreach($posts as $p)
                {
                    $result[] = $this->get($p->ID,$is_front);
                }
            }

            return $result;
        }
        public function takeawayTables($warehouse_id = -1 ){

            $result = array();
            if ($handle = opendir( $this->_bill_data_path)) {

                while (false !== ($entry = readdir($handle))) {

                    if ($entry != "." && $entry != ".." && strpos($entry,'takeaway') == 0) {

                        if(strpos($entry,'.json') > 0)
                        {
                            $table_id = str_replace('.json','',$entry);
                            $file_path = $this->_bill_data_path.'/'.$entry;
                            $data = $this->_filesystem->get_contents($file_path);

                            if($data)
                            {
                                $result_table = json_decode($data,true);
                                $desk = $result_table['desk'];


                                if(isset($desk['warehouse_id']))
                                {
                                    if(  $warehouse_id >= 0 && $desk['warehouse_id'] != $warehouse_id)
                                    {
                                        continue;
                                    }
                                    $result[] = array(
                                        'id' => $desk['id'],
                                        'name' => $desk['name'],
                                        'warehouse' => $desk['warehouse_id'],
                                        'position' => 0,
                                        'status' => 'publish',
                                        'dine_type' => 'takeaway',
                                    );
                                }


                            }
                        }
                    }
                }
                closedir($handle);
            }

            return $result;
            /*
            $result = array(
                'id' => $id,
                'name' => $name,
                'warehouse' => $warehouse,
                'position' => (int)$position,
                'status' => $status
            );
            */

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
            $position = isset($params['position']) ? (int)$params['position'] : 0;

            $type = isset($params['type']) ? $params['type'] : 'default';
            $cost = isset($params['cost']) ? $params['cost'] : 0;
            $cost_type = isset($params['cost_type']) ? $params['cost_type'] : 'hour';
            $args = array(
                'ID' => $id,
                'post_title' => $params['name'],
                'post_type' => $this->_post_type,
                'post_status' => $params['status'],
                'post_parent' => $warehouse_id
            );
            $post_id = wp_insert_post($args);
            if(!is_wp_error($post_id)){


                update_post_meta($post_id,$this->_warehouse_meta_key,$warehouse_id);
                update_post_meta($post_id,$this->_position_meta_key,$position);

                update_post_meta($post_id,$this->_type_meta_key,$type);
                update_post_meta($post_id,$this->_cost_meta_key,$cost);
                update_post_meta($post_id,$this->_cost_type_meta_key,$cost_type);

                return $post_id;
            }else{
                //there was an error in the post insertion,
                throw new Exception($post_id->get_error_message()) ;
            }
        }
        public function get($id,$is_front = false)
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
            $position = get_post_meta($id,$this->_position_meta_key,true);
            
            $type = get_post_meta($id,$this->_type_meta_key,true);
            $cost_type = get_post_meta($id,$this->_cost_type_meta_key,true);
            $cost = get_post_meta($id,$this->_cost_meta_key,true);

            if(!$cost)
            {
                $cost = 0;
            }
            if(!$cost_type)
            {
                $cost_type = 'hour';
            }
            if(!$type)
            {
                $type = 'default';
            }

            $status = $post->post_status;
            $result = array(
                'id' => $id,
                'name' => $name,
                'warehouse' => $warehouse,
                'position' => (int)$position,
                'type' => $type,
                'cost' => $cost,
                'cost_type' => $cost_type,
                'status' => $status
            );
            if($is_front)
            {
                $min_cost = $result['cost'] ;
                switch($result['cost_type'])
                {
                    case 'day':
                        $min_cost = $min_cost / ( 60 * 24 );
                        break;
                    case 'hour':
                        $min_cost = $min_cost / ( 60  );
                        break;
                }
                $result['cost_type'] = 'minute';
                $result['cost'] = 1 * $min_cost;
                
            }
            return  apply_filters('op_table_details',$result,$is_front);;
        }

        public function update_bill_screen($tables_data){

            if(!empty($tables_data))
            {
                foreach($tables_data as $table_key => $table_data)
                {
                    $table_id = str_replace('desk-','',$table_key);
                    $current_data = $this->bill_screen_data($table_id);
                    $allow_update = true;
                    if(isset($current_data['ver']) && isset($table_data['ver']))
                    {
                        if($current_data['ver'] >= $table_data['ver']  )
                        {
                            $allow_update = false;
                        }

                    }
                    if($allow_update)
                    {
                        $register_file = $this->bill_screen_file_path($table_id);
                        if(file_exists($register_file))
                        {
                            $this->_filesystem->delete($register_file);
                        }
                        $file_mode = apply_filters('op_file_mode',0755) ;
                        $this->_filesystem->put_contents(
                            $register_file,
                            json_encode($table_data),
                            apply_filters('op_file_mode',$file_mode) // predefined mode settings for WP files
                        );
                    }

                }
            }
        }
        public function update_table_bill_screen($table_id,$table_data,$table_type = 'dine_in'){
            if($table_type != 'dine_in')
            {
                $table_id = $table_type.'-'.$table_id;
            }
            $register_file = $this->bill_screen_file_path($table_id);
            $file_mode = apply_filters('op_file_mode',0755) ;
            if(file_exists($register_file))
            {
                if ( defined( 'FS_CHMOD_FILE' ) ) {
                    $this->_filesystem->put_contents(
                        $register_file,
                        json_encode($table_data)
                    );
                }else{
                    $this->_filesystem->put_contents(
                        $register_file,
                        json_encode($table_data),
                        $file_mode
                    );
                }
                
            }else{
                
                $this->_filesystem->put_contents(
                    $register_file,
                    json_encode($table_data),
                    $file_mode // predefined mode settings for WP files
                );
            }
            
        }
        public function bill_screen_file_path($table_id)
        {
            return $this->_bill_data_path.'/'.$table_id.'.json';
        }
        public function bill_screen_file_url($table_id)
        {
            $upload_dir = wp_upload_dir();
            $url = $upload_dir['baseurl'];
            $url = ltrim($url,'/');
            return $url.'/openpos/tables/'.$table_id.'.json';
        }
        public function bill_screen_data($table_id,$type='dine_in')
        {
            if($type != 'dine_in')
            {
                $table_id = $type.'-'.$table_id;
            }
            $file_path = $this->bill_screen_file_path($table_id);
          
            $data = $this->_filesystem->get_contents($file_path);
            $result = array();
            if($data)
            {
                $result = json_decode($data,true);
            }

            return $result;
        }
        public function tables_version($warehouse_id = -1){
            $result = array();
            if ($handle = opendir( $this->_bill_data_path)) {

                while (false !== ($entry = readdir($handle))) {

                    if ($entry != "." && $entry != "..") {

                        if(strpos($entry,'.json') > 0)
                        {
                            $table_id = str_replace('.json','',$entry);
                            $file_path = $this->_bill_data_path.'/'.$entry;
                            $data = $this->_filesystem->get_contents($file_path);
                            if($data)
                            {
                                $result_table = json_decode($data,true);
                                if($warehouse_id >= 0)
                                {

                                    if( isset($result_table['desk']['warehouse_id']) && $result_table['desk']['warehouse_id'] != $warehouse_id ){
                                        continue;
                                    }
                                }
                               
                                $version = isset($result_table['ver']) ? $result_table['ver'] : 0;
                                $result[$table_id] = $version;
                            }
                        }
                    }
                }
                closedir($handle);
            }
            return $result;
        }
        public function ready_dishes($warehouse_id = -1){
            $result = array();
            if ($handle = opendir( $this->_bill_data_path)) {

                while (false !== ($entry = readdir($handle))) {

                    if ($entry != "." && $entry != "..") {
                        $table_type = 'dine_in';

                        if(strpos($entry,'takeaway') == 0)
                        {
                            $table_type = 'takeaway';
                        }
                        if(strpos($entry,'.json') > 0)
                        {
                            $table_id = str_replace('.json','',$entry);
                            $file_path = $this->_bill_data_path.'/'.$entry;
                            $data = $this->_filesystem->get_contents($file_path);
                            if($data)
                            {
                                $result_table = json_decode($data,true);
                                $desk = $result_table['desk'];
                                if(isset($desk['warehouse_id']) && $desk['warehouse_id'] != $warehouse_id && $warehouse_id >= 0)
                                {
                                    continue;
                                }
                                $items = isset($result_table['items']) ? $result_table['items'] : array();
                                if(!empty($items))
                                {
                                    $table = isset($result_table['desk']) ? $result_table['desk'] : [];
                                    $table_name = isset($table['name']) ? $table['name'] : '';
                                    $table_id = isset($table['id']) ? $table['id'] : 0;
                                    foreach ($items as $_item)
                                    {
                                        if(isset($_item['done']) && $_item['done'] == 'ready')
                                        {
                                            $result[] = array(
                                                'id' => $_item['id'],
                                                'table_id' => $table_id,
                                                'table_name' => $table_name,
                                                'table_type' => $table_type,
                                                'item_name' => $_item['qty'].' x '.$_item['name']
                                            );
                                        }
                                    }

                                }

                            }
                        }
                    }
                }
                closedir($handle);
            }
            return $result;
        }
        public function removeJsonTable($table_id,$force = false){

            $file_path = $this->bill_screen_file_path($table_id);

            if($force)
            {
                $file_path = $this->_bill_data_path.'/takeaway-'.$table_id.'.json';
            }

            if(file_exists($file_path))
            {
                unlink($file_path);
            }
            
        }
        public function clear_takeaway(){
            $result = array();
            if ($handle = opendir( $this->_bill_data_path)) {

                while (false !== ($entry = readdir($handle))) {

                    if ($entry != "." && $entry != "..") {

                        if(strpos($entry,'.json') > 0)
                        {
                            if(strpos($entry,'takeaway') >= 0)
                            {


                                $file_path = $this->_bill_data_path.'/'.$entry;
                                unlink($file_path);
                            }


                        }
                    }
                }
                closedir($handle);
            }
            return $result;
        }
    }
}
?>