<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>
<input type="hidden" name="woof_settings[<?php echo $key ?>][show_title_label]" value="<?php echo (isset($settings[$key]['show_title_label'])? $settings[$key]['show_title_label']:1) ?>" /> 
<input type="hidden" name="woof_settings[<?php echo $key ?>][show_toggle_button]" value="<?php echo (isset($settings[$key]['show_toggle_button'])? $settings[$key]['show_toggle_button']:0) ?>" />
<input type="hidden" name="woof_settings[<?php echo $key ?>][tooltip_text]" value="<?php echo (isset($settings[$key]['tooltip_text'])? stripcslashes($settings[$key]['tooltip_text']):"") ?>" />
<input type="hidden" name="woof_settings[<?php echo $key ?>][options]" value="<?php echo (isset($settings[$key]['options'])? $settings[$key]['options']:"") ?>" /> 
<input type="hidden" name="woof_settings[<?php echo $key ?>][search_logic]" value="<?php echo (isset($settings[$key]['search_logic'])? $settings[$key]['search_logic']:"OR") ?>" /> 
<div id="woof-modal-content-<?php echo $key ?>" style="display: none;">
        <div class="woof-form-element-container">
            <div class="woof-name-description">
                <strong><?php _e('Show title label', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('Show/Hide meta block title on the front', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $show_title = array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_title_label">
                        <?php foreach ($show_title as $id => $value) : ?>
                            <option value="<?php echo $id ?>"><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div> 
        <div class="woof-form-element-container">
            <div class="woof-name-description">
                <strong><?php _e('Show toggle button', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('Show toggle button near the title on the front above the block of html-items', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $show_toogle = array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes, show as closed', 'woocommerce-products-filter'),
                    2 => __('Yes, show as opened', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_toggle_button">
                        <?php foreach ($show_toogle as $id => $value) : ?>
                            <option value="<?php echo $id ?>"><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>  
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php _e('Tooltip', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('Show tooltip', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">

                <div class="select-wrap">
                    <textarea class="woof_popup_option" data-option="tooltip_text" ></textarea>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">
            <div class="woof-name-description">
                <strong><?php _e('Search logic', 'woocommerce-products-filter') ?></strong>
                <span><?php _e('Check search logic AND/OR', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $logic = array(
                    'OR' => __('OR', 'woocommerce-products-filter'),
                    'AND' => __('AND', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="search_logic">
                        <?php foreach ($logic as $id => $value) : ?>
                            <option value="<?php echo $id ?>"><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div> 

</div>

