<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$woof_ext_instock_label=apply_filters('woof_ext_custom_title_by_instock',__('In stock', 'woocommerce-products-filter'));
if (isset($WOOF->settings['by_instock']) AND $WOOF->settings['by_instock']['show'])
{
    ?>
    <div data-css-class="woof_checkbox_instock_container" class="woof_checkbox_instock_container woof_container woof_container_stock">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
            <input type="checkbox" class="woof_checkbox_instock" id="woof_checkbox_instock" name="stock" value="0" <?php checked('instock', $WOOF->is_isset_in_request_data('stock') ? 'instock' : '', true) ?> />&nbsp;&nbsp;<label for="woof_checkbox_instock"><?php echo $woof_ext_instock_label ?></label><br />
        </div>
    </div>
    <?php
}


