<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;

//***
if (is_user_logged_in() AND isset($WOOF->settings['query_save']) ) {

    $query_count = 2;
    $p = "";
    $adding_class="";
    if($in_filter!=true){
        $adding_class="query_save_shortcode";
    }
    //var_dump($WOOF->settings['query_save']);
    if (isset($WOOF->settings['query_save'])AND ! empty($WOOF->settings['query_save'])) {
	$query_count = $WOOF->settings['query_save']['search_count'];
	$p = $WOOF->settings['query_save']['label'];
        $placeholder=$WOOF->settings['query_save']['placeholder'];
        $btn_label=$WOOF->settings['query_save']['btn_label'];
    }

    $cur_user_id = get_current_user_id();
    //if($cur_user_id==0)return;
    $user_data_queries = get_user_meta($cur_user_id, 'woof_user_search_query', true);
    ?>
    <div data-css-class="woof_query_save_container" class="woof_query_save_container woof_container <?php echo $adding_class ?>">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
    	<<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
            <?php
		echo WOOF_HELPER::wpml_translate(null, $p);
		?> 
    	</<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
	    <?php
	    if (!is_array($user_data_queries)) {
		$user_data_queries = array();
	    }
	    ?>
    	<div class="woof_query_save_list">
    	    <ul> 
		    <?php
		    $counter = 1;
		    foreach ($user_data_queries as $data) {
			//echo date('D, d M Y H:i:s',$data['date']),"*****",$data['count'],"<br>";
			echo $this->render_html(WOOF_EXT_PATH . 'query_save/views/item_list_query.php', $data);
			$counter++;
		    }
		    ?>
    	    </ul>
    	</div> 
	    <?php
            $show_btn=false;
	    $get_array = $WOOF->get_request_data();
            //var_dump($real_query);
            if(isset($WOOF->settings['items_order'])){
                $key_array=explode(',',$WOOF->settings['items_order']);
                $real_query=array_intersect(array_keys($get_array),$key_array);
                if(count($real_query)){
                    $show_btn=true;
                }

                foreach ($WOOF->settings['meta_filter'] as $item) {
                    $key = $item['search_view'] . "_" . $item['meta_key'];
                    if (in_array($key,array_keys($get_array))) {
                        $show_btn=true;
                    }
                }
            }
            
            if ($show_btn ) { // hide btn without search query
		$visible = 'none';
		if ($query_count > count($user_data_queries)) {
		    $visible = 'block';
		}
		?>
		<div class="woof_add_query_count" style="display: <?php echo $visible ?>" >
                    <input name="title_query_save"  type="text"  class="woof_save_query_title"  value="" placeholder="<?php echo $placeholder ?>" >
                    <div class="woof_query_save_title_error"><?php _e('Please fill the title field.', 'woocommerce-products-filter') ?></div>
                    <input name="add_query_save" data-count="<?php echo $query_count ?>" type="button" class="woof_add_query_save" data-user="<?php echo $cur_user_id ?>" value="<?php echo $btn_label ?>"  >
		</div>
        
	    <?php } ?>
            <?php if (isset($this->settings['query_save']['notes_for_customer']) AND ! empty($this->settings['query_save']['notes_for_customer'])): ?>
                <span class="woof_query_save_notes_for_customer"><?php echo do_shortcode($this->settings['query_save']['notes_for_customer']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

