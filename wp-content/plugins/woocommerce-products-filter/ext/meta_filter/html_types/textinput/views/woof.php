<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div data-css-class="woof_textinput_container" class="woof_textinput_container woof_container  woof_container_<?php echo "textinput_".$meta_key ?>">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <?php
        global $WOOF;
        $woof_text = '';
        $request = $WOOF->get_request_data();

        if (isset($request['textinput_'.$meta_key]))
        {
            $woof_text = $request['textinput_'.$meta_key];
        }
        //+++
        if (!isset($placeholder))
        {
            $p = __('enter a text here ...', 'woocommerce-products-filter');
        }

        if (isset($options['title']) AND ! isset($placeholder))
        {
            if (!empty($options['title']))
            {
                $p = $options['title'];
                $p = WOOF_HELPER::wpml_translate(null, $p);
                $p = __($p, 'woocommerce-products-filter');
            }

        }
        //***
        $unique_id = uniqid('woof_meta_filter_');
        ?>

        <div class="woof_show_textinput_container ">
            <img width="36" class="woof_show_text_search_loader" style="display: none;" src="<?php echo $loader_img ?>" alt="loader" />
            <a href="javascript:void(0);" data-uid="<?php echo $unique_id ?>" class="woof_textinput_go <?php echo $unique_id ?>"></a>
            <input type="search" class="woof_meta_filter_textinput <?php echo $unique_id ?>" id="<?php echo $unique_id ?>" data-uid="<?php echo $unique_id ?>" data-auto_res_count="<?php echo(isset($auto_res_count) ? $auto_res_count : 0) ?>" data-auto_search_by="<?php echo(isset($auto_search_by) ? $auto_search_by : "") ?>" placeholder="<?php echo(isset($placeholder) ? $placeholder : $p) ?>" name="textinput_<?php echo $meta_key?>" value="<?php echo $woof_text ?>" />
        </div>

    </div>
</div>
