<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php 
if(!empty($meta_options)){
    $meta_options=  explode(',',$meta_options);
}else{
    $meta_options=array();
}

 global  $WOOF;
 $request = $WOOF->get_request_data();
 $woof_value="";
 if (isset($request['select_'.$meta_key]))
 {
     $woof_value = $request['select_'.$meta_key];
 }
$show_title_label= (isset($meta_settings['show_title_label']))?$meta_settings['show_title_label']:1;
$css_classes = "woof_block_html_items";
$show_toggle = 0;
$shown_options_tags=0;
if (isset($meta_settings['show_toggle_button'])) {
    $show_toggle = (int) $meta_settings['show_toggle_button'];
}
//***
$block_is_closed = true;
if (!empty($woof_value)) {
    $block_is_closed = false;
}
if ($show_toggle === 1 AND empty($woof_value)) {
    $css_classes .= " woof_closed_block";
}

if ($show_toggle === 2 AND empty($woof_value)) {
    $block_is_closed = false;
}

$tooltip_text = "";
if (isset($meta_settings['tooltip_text'])) {
    $tooltip_text = $meta_settings['tooltip_text'];
}
if (in_array($show_toggle, array(1, 2))) {
    $block_is_closed = apply_filters('woof_block_toggle_state', $block_is_closed);
    if($block_is_closed){
        $css_classes .= " woof_closed_block";
    }else{
        $css_classes = str_replace('woof_closed_block', '', $css_classes);
    }
}
//***
if(isset($_REQUEST['hide_terms_count_txt_short']) AND $_REQUEST['hide_terms_count_txt_short']!=-1){
    if((int)$_REQUEST['hide_terms_count_txt_short']==1){
        $_REQUEST['hide_terms_count_txt']=1;
    }else{
        $_REQUEST['hide_terms_count_txt']=0;
    }
}
//***
?>
<div data-css-class="woof_meta_select_container" class="woof_meta_select_container woof_container woof_container_<?php echo $meta_key ?>  woof_container_<?php echo "select_".$meta_key ?>">
        <div class="woof_container_inner">
        <div class="woof_container_inner woof_container_inner_meta_select">
            <?php if ($show_title_label) {
                ?>
            <<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                <?php echo WOOF_HELPER::wpml_translate(null,$options['title']) ?>
                <?php echo WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null,$options['title']),$tooltip_text) ?>
                <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?></<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                <?php
            }?>
            <div class="<?php echo $css_classes ?>">
                <select class="woof_meta_select woof_meta_select_<?php echo $meta_key ?>" name="<?php echo "select_".$meta_key ?>">
                 <option value="0"><?php echo WOOF_HELPER::wpml_translate(null,$options['title']) ?></option>
                 
                  <?php  if (count($meta_options)<1 ): ?>
                 <option value="0"><?php _e('Notice! Add options in the plugin settings->Meta filter','woocommerce-products-filter') ?></option>
                 <?php endif; ?>
                  <?php if (!empty($meta_options)): ?>
                     <?php foreach ($meta_options as $key => $option) : ?>
                         <?php
						 if(!$option){
							 continue;
						 }
                         $option_title=$option;
                         $custom_title=explode('^',$option,2);
                         if(count($custom_title)>1){
                           $option=$custom_title[1];  
                           $option_title=$custom_title[0]; 
                         }
                         $count_string = "";
                         $count = 0;
                         $show_count = get_option('woof_show_count', 0);
                         $show_count_dynamic = get_option('woof_show_count_dynamic', 0);
                         $hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos', 0);

                         if (intval($woof_value)!=$key+1)
                         {
                             if ($show_count)
                             {
                                 $meta_field=array(
                                     'key'=>$meta_key,
                                     'value'=>$option,
                                 );
                                 if ($show_count_dynamic)
                                 {
                                     $count_data=array();
                                     $count = $WOOF->dynamic_count(array(), 'select', (isset($_REQUEST['additional_taxes']))?$_REQUEST['additional_taxes']: "",$meta_field);
                                      $count_string = '(' . $count . ')';
                                 } else
                                 {
                                     $count=1;
                                     //$count = $term['count'];
                                 }
                                
                             }
                             //+++
                             if ($hide_dynamic_empty_pos AND $count == 0)
                             {
                                 continue;
                             }
                         }

                         if (isset($_REQUEST['hide_terms_count_txt']) AND $_REQUEST['hide_terms_count_txt'])
                         {
                             $count_string = "";
                         }
                         ?>
                         <option <?php if ($show_count AND $count == 0 AND $option!=$woof_value): ?>disabled=""<?php endif; ?> value="<?php echo $key+1 ?>" <?php echo selected($key+1==intval($woof_value)) ?>>
                             <?php
                         echo WOOF_HELPER::wpml_translate(null,$option_title);
                         echo $count_string ;?>
                         </option>
                     <?php $shown_options_tags++ ; ?>
                     <?php endforeach; ?>
                         
                 <?php endif; ?>
             </select> 
             <?php
             $curr_title="";
             if(isset($meta_options[intval($woof_value)-1])){
                 $op_title=explode('^',$meta_options[intval($woof_value)-1],2);
                 if(count($op_title)>1){
                     $curr_title=$op_title[0];
                 }else{
                     $curr_title=$meta_options[intval($woof_value)-1];
                 }
             }
             ?>   
             <input type="hidden" value="<?php echo WOOF_HELPER::wpml_translate(null,$curr_title );?>" data-anchor="woof_n_<?php echo "select_".$meta_key ?>_<?php echo $woof_value ?>" />
<?php if ($shown_options_tags == 0): ?>
    <style type="text/css">
        .woof_container_<?php echo $meta_key ?>{
            display:none;
        }
    </style>
<?php endif; ?>   
            </div>    
        </div>        
    </div>
</div>
