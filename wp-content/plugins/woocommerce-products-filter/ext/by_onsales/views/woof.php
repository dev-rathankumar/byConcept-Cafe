<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$woof_ext_onsales_label=apply_filters('woof_ext_custom_title_by_onsales',__('On sale', 'woocommerce-products-filter'));
if (isset($WOOF->settings['by_onsales']) AND $WOOF->settings['by_onsales']['show'])
{
    ?>
    <div data-css-class="woof_checkbox_sales_container" class="woof_checkbox_sales_container woof_container woof_container_onsales">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
            <input type="checkbox" class="woof_checkbox_sales" id="woof_checkbox_sales" name="sales" value="0" <?php checked('salesonly', $WOOF->is_isset_in_request_data('onsales') ? 'salesonly' : '', true) ?> />&nbsp;&nbsp;<label for="woof_checkbox_sales"><?php echo $woof_ext_onsales_label ?></label><br />
        </div>
    </div>
    <?php
}


