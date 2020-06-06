<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<li data-key="<?php echo $key ?>" class="woof_options_li">

    <?php
    $show = 0;
    if (isset($woof_settings[$key]['show'])) {
        $show = (int) $woof_settings[$key]['show'];
    }
    ?>

    <a href="#" class="help_tip woof_drag_and_drope" data-tip="<?php _e("drag and drope", 'woocommerce-products-filter'); ?>"><img src="<?php echo WOOF_LINK ?>img/move.png" alt="<?php _e("move", 'woocommerce-products-filter'); ?>" /></a>

    <strong style="display: inline-block; width: 176px;"><?php _e("Save search query", 'woocommerce-products-filter'); ?>:</strong>

    <img class="help_tip" data-tip="<?php _e('User can save the search query', 'woocommerce-products-filter') ?>" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16" width="16" />

    <div class="select-wrap">
        <select name="woof_settings[<?php echo $key ?>][show]" class="woof_setting_select">
            <option value="0" <?php echo selected($show, 0) ?>><?php _e('No', 'woocommerce-products-filter') ?></option>
            <option value="1" <?php echo selected($show, 1) ?>><?php _e('Yes', 'woocommerce-products-filter') ?></option>
        </select>
    </div>


    <input type="button" value="<?php _e('additional options', 'woocommerce-products-filter') ?>" data-key="<?php echo $key ?>" data-name="<?php _e("Products Messenger", 'woocommerce-products-filter'); ?>" class="woof-button js_woof_options js_woof_options_<?php echo $key ?>" />



    <?php
    $cron_key = "";
    if (!isset($woof_settings[$key]['show_label'])) {
        $woof_settings[$key]['show_label'] = 1;
    }
    if (!isset($woof_settings[$key]['label'])) {
        $woof_settings[$key]['label'] = __('Save current search query', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['placeholder'])) {
        $woof_settings[$key]['placeholder'] = __('Title of the Query*', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['btn_label'])) {
        $woof_settings[$key]['btn_label'] = __('Add this query', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['search_count'])) {
        $woof_settings[$key]['search_count'] = 2;
    }
    if (!isset($woof_settings[$key]['notes_for_customer'])) {
        $woof_settings[$key]['notes_for_customer'] = '';
    }
    ?>
    <input type="hidden" name="woof_settings[<?php echo $key ?>][show_label]" value="<?php echo $woof_settings[$key]['show_label'] ?>" /> 
    <input type="hidden" name="woof_settings[<?php echo $key ?>][label]" value="<?php echo $woof_settings[$key]['label'] ?>" />
    <input type="hidden" name="woof_settings[<?php echo $key ?>][placeholder]" value="<?php echo $woof_settings[$key]['placeholder'] ?>" />
    <input type="hidden" name="woof_settings[<?php echo $key ?>][btn_label]" value="<?php echo $woof_settings[$key]['btn_label'] ?>" />
    <input type="hidden" name="woof_settings[<?php echo $key ?>][notes_for_customer]" value="<?php echo $woof_settings[$key]['notes_for_customer'] ?>" />
    <input type="hidden" name="woof_settings[<?php echo $key ?>][search_count]" value="<?php echo $woof_settings[$key]['search_count'] ?>" />

    <div id="woof-modal-content-<?php echo $key ?>" style="display: none;">

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php _e('Label', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('The text before the block of subscription block', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="label" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php _e('Placeholder', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('The placeholder  in title field', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="placeholder" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php _e('Button label', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('The text in the button', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="btn_label" placeholder="" value="" />
            </div>

        </div>        

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php _e('Max saved queries per user', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('Maximum number of subscriptions for a single registered user.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="search_count" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php _e('Notes for customer', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('Any text notes for customer under subscription form.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="notes_for_customer"></textarea>
            </div>

        </div>

    </div>


</li>
