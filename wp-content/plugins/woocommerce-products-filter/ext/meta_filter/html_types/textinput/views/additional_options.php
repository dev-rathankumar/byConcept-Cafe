<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<input type="hidden" name="woof_settings[<?php echo $key ?>][text_conditional]" value="<?php echo (isset($settings[$key]['text_conditional'])? $settings[$key]['text_conditional']:'LIKE') ?>" /> 
<input type="hidden" name="woof_settings[<?php echo $key ?>][text_autocomplate]" value="<?php echo (isset($settings[$key]['text_autocomplate'])? $settings[$key]['text_autocomplate']:0) ?>" /> 

<div id="woof-modal-content-<?php echo $key ?>" style="display: none;">

    <div class="woof-form-element-container">

        <div class="woof-name-description">
            <strong><?php _e('Text search conditional', 'woocommerce-products-filter') ?></strong>
            <span><?php _e('TEXT', 'woocommerce-products-filter') ?></span>
        </div>

        <div class="woof-form-element">
            <?php
            $text_conditional = array(
                'LIKE' => __('LIKE', 'woocommerce-products-filter'),
                '=' => __('EXACT', 'woocommerce-products-filter')
            );
            ?>

            <div class="select-wrap">
                <select class="woof_popup_option" data-option="text_conditional">
                    <?php foreach ($text_conditional  as $id => $value) : ?>
                        <option value="<?php echo $id; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

    </div>        
    <!--<div class="woof-form-element-container">

        <div class="woof-name-description">
            <strong><?php _e('Show subscription button', 'woocommerce-products-filter') ?></strong>
            <span><?php _e('Show subscription button without search query. For example, user will be able to subscribe for new products in any products category.', 'woocommerce-products-filter') ?></span>
        </div>

        <div class="woof-form-element">
            <?php
            $autocomplete = array(
                0 => __('No', 'woocommerce-products-filter'),
                1 => __('Yes', 'woocommerce-products-filter')
            );
            ?>

            <div class="select-wrap">
                <select class="woof_popup_option" data-option="text_autocomplate">
                    <?php foreach ($autocomplete as $id => $value) : ?>
                        <option value="<?php echo $id ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

    </div> -->
</div>

