<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
?>

<section id="tabs-meta-filter">
    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>

                <a href="https://www.woocommerce-filter.com/extencion/woocommerce-filter-by-meta-fields/" target="_blank" class="button-primary"><?php echo __('About extension', 'woocommerce-products-filter') ?></a><br />
                <br />

                <h4><?php _e('Meta Fields', 'woocommerce-products-filter') ?></h4>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%;">
                            <div class="col-lg-6">
                                <h5><?php _e('Add Custom key by hands', 'woocommerce-products-filter') ?>:</h5>
                                <input type="text" value="" class="woof_meta_key_input" style="width: 75%;" />&nbsp;
                                <a href="#" id="woof_meta_add_new_btn" class="button button-primary button-large"><?php _e('Add', 'woocommerce-products-filter') ?></a> 

                            </div>

                        </td>
                        <td style="width: 50%;">
                            <div class="col-lg-6">
                                <h5><?php _e('Get keys from any product by its ID', 'woocommerce-products-filter') ?>:</h5>
                                <input type="number" min="1" class="woof_meta_keys_get_input" value="" style="width: 75%;" placeholder="<?php _e('enter product ID', 'woocommerce-products-filter') ?>" />&nbsp;
                                <a href="#" id="woof_meta_get_btn" class="button button-primary button-large"><?php _e('Get', 'woocommerce-products-filter') ?></a>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="clear"></div>

                <br />

                <div id="metaform" method="post" action="">
                    <input type="hidden" name="woof_meta_fields[]" value="" />
                    <ul id="woof_meta_list"  class="ui-sortable woof_fields">

                        <?php
                        //echo '<pre>';
                        //print_r($metas);
                        //echo '</pre>';

                        if (!empty($metas)) {
                            foreach ($metas as $m) {
                                if ($m['meta_key'] == "__META_KEY__") {
                                    continue;
                                }
                                woof_meta_print_li($m, $meta_types);
                            }
                        }
                        ?>

                    </ul>


                    <br />

                    <!--<input type="submit" class="button button-primary button-primary" value="<?php echo __('Save meta fields', 'woocommerce-products-filter') ?>" />
                    -->
                </div>

                <div style="display: none;" id="woof_meta_li_tpl">
                    <?php
                    woof_meta_print_li(array(
                        'meta_key' => '__META_KEY__',
                        'title' => '__TITLE__',
                        'search_view' => '',
                        'type' => '',
                        'options' => ''
                            ), $meta_types);
                    ?>
                </div>

                <?php

                function woof_meta_print_li($m, $meta_types) {
                    ?>
                    <li class="woof_options_li">
                        <a href="#" class="help_tip woof_drag_and_drope ui-sortable-handle" title="<?php _e('drag and drope', 'woocommerce-products-filter') ?>"><img src="<?php echo WOOF_LINK ?>img/move.png" alt="<?php echo __('move', 'woocommerce-products-filter') ?>" /></a>

                        <div class="woof_options_item">
                            <input type="text" name="woof_settings[meta_filter][<?php echo $m['meta_key'] ?>][meta_key]" value="<?php echo $m['meta_key'] ?>" readonly="" class="woof_column_li_option" />&nbsp;

                        </div>
                        <div class="woof_options_item">
                            <input type="text" style="color: green !important; font-weight: normal !important;" name="woof_settings[meta_filter][<?php echo $m['meta_key'] ?>][title]" placeholder="<?php _e('enter title', 'woocommerce-products-filter') ?>" value="<?php echo $m['title'] ?>" class="woof_column_li_option" />&nbsp;

                        </div>
                        <div class="woof_options_item">
                            <div class="select-wrap">
                                <select name="woof_settings[meta_filter][<?php echo $m['meta_key'] ?>][search_view]" class="woof_meta_view_selector" style="width: 99%;">
                                    <?php
                                    foreach ($meta_types as $key => $type):
                                        if ($m['search_view'] == $key AND $m['type'] == $type['hide_if']) {
                                            $m['search_view'] = 'textinput';
                                        }
                                        ?> 
                                        <option  <?php selected($m['search_view'], $key) ?> value="<?php echo $key ?>" data-show-options="<?php echo ($type['show_options']) ? 'yes' : 'no'; ?>" data-hideif="<?php echo $type['hide_if'] ?>" <?php echo ($m['type'] == $type['hide_if']) ? "style='display:none;'" : ""; ?>  >
                                            <?php echo $type['title'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        $show_options = false;
                        if (isset($meta_types[$m['search_view']]['show_options'])) {
                            $show_options = $meta_types[$m['search_view']]['show_options'];
                        }
                        ?>
                        <div class="woof_options_item_options" <?php if (!$show_options): ?> style="display:none;" <?php endif; ?> >
                            <div class="textarea-wrap">
                                <textarea name="woof_settings[meta_filter][<?php echo $m['meta_key'] ?>][options]" class="woof_column_li_option" ><?php echo (isset($m['options'])) ? $m['options'] : ""; ?></textarea>
                            </div>
                            <div class="woof-meta-description">
                                <p><i><?php _e('Use comma as in example: 1,2,3,4,5. If you want structure like title->value use next syntax example: France^1,Germany^2,USA^3. Countries are titles here.', 'woocommerce-products-filter') ?></i></p>
                            </div>
                        </div>
                        <div class="woof_options_item">
                            <div class="select-wrap" <?php if (in_array($m['search_view'], array('popupeditor', 'switcher'))): ?>style="display: none;"<?php endif; ?>>
                                <select name="woof_settings[meta_filter][<?php echo $m['meta_key'] ?>][type]" class="woof_meta_type_selector">
                                    <option <?php selected($m['type'], 'number') ?> value="number"><?php _e('number', 'woocommerce-products-filter') ?></option>
                                    <option <?php selected($m['type'], 'string') ?> value="string"><?php _e('string', 'woocommerce-products-filter') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="woof_options_item">
                            <a href="#" class="button button-primary woof_meta_delete" title="<?php _e('delete', 'woocommerce-products-filter') ?>"><span class="dashicons dashicons-trash"></span></a>
                        </div>

                        <div style="clear: both;"></div>
                    </li>
                    <?php
                }
                ?>
            </section>

        </div>

    </div>
</section>




