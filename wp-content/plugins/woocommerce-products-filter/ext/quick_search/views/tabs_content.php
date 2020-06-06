<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
?>

<section id="tabs-quick-text">
    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>

                <a href="https://www.woocommerce-filter.com/extencion/quick-search/" target="_blank" class="button-primary"><?php echo __('About extension', 'woocommerce-products-filter') ?></a><br />
                <br />

                <div class="woof-control-section" style="display:none;"  >

                    <h4><?php _e('How often assemble data file', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $cron_systems = array(
                                0 => __('WordPress Cron', 'woocommerce-products-filter'),
                                    //1 => __('External Cron', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_quick_search']['cron_system'])) {
                                $woof_settings['woof_quick_search']['cron_system'] = 0;
                            }
                            $cron_system = $woof_settings['woof_quick_search']['cron_system'];
                            $cron_system = -1; //hide  cron sys
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_quick_search][cron_system]" class="chosen_select woof_cron_system">
                                    <?php foreach ($cron_systems as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($cron_system == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <!-- <?php _e('External cron is more predictable with time of execution, but additional knowledge how to set it correctly is required (<i style="color: orange;">External cron will be ready in the next version of the extension</i>)', 'woocommerce-products-filter') ?> -->
                                <?php _e('Products assembling period in data file for quick search.', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_external_cron_option" style="display: <?php echo($cron_system == 1 ? 'block' : 'none') ?>;">

                    <h4><?php _e('Secret key for external cron', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_quick_search']['cron_secret_key']) OR empty($woof_settings['woof_quick_search']['cron_secret_key'])) {
                        $woof_settings['woof_quick_search']['cron_secret_key'] = 'woof_stat_updating';
                    }
                    $cron_secret_key = sanitize_title($woof_settings['woof_quick_search']['cron_secret_key']);
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_quick_search][cron_secret_key]" value="<?php echo $cron_secret_key ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Enter any random text in the field and use it in the external cron with link like: http://mysite.com/?woof_stat_collection=__YOUR_SECRET_KEY_HERE__', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_wp_cron_option" style="display: <?php echo($cron_system == 0 ? 'block' : 'none') ?>;">

                    <h4><?php _e('WordPress Cron period', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $wp_cron_periods = array(
                                'daily' => __('daily', 'woocommerce-products-filter'),
                                'week' => __('weekly', 'woocommerce-products-filter'),
                                'twicemonthly' => __('twicemonthly', 'woocommerce-products-filter'),
                                'month' => __('monthly', 'woocommerce-products-filter'),
                                    //'min1' => __('min1', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_quick_search']['wp_cron_period'])) {
                                $woof_settings['woof_quick_search']['wp_cron_period'] = 'weekly';
                            }
                            $wp_cron_period = $woof_settings['woof_quick_search']['wp_cron_period'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_quick_search][wp_cron_period]" class="chosen_select">
                                    <?php foreach ($wp_cron_periods as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($wp_cron_period == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php _e('Weekly recommended', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_update_search_data_file">

                    <h4><?php _e('Reassemble data file', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">
                        <div class="woof-control">
                            <a id="woof_quick_search_update"  class="button" ><?php _e('Update now!', 'woocommerce-products-filter') ?></a><span class="woof_qt_messange"></span><span class="woof_qt_product_count"></span>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Reassemble data file for quick search manually now', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_file_data_option" >

                    <h4><?php _e('Additional Quick-search Filter options', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_quick_search']['quick_search_tax_conditionals']) OR empty($woof_settings['woof_quick_search']['cron_secret_key'])) {
                        $woof_settings['woof_quick_search']['quick_search_tax_conditionals'] = '';
                    }
                    $tax_conditionals = $woof_settings['woof_quick_search']['quick_search_tax_conditionals'];
                    ?>
                    <div class="woof-control-container">

                        <div class="woof-control" style="display: none;">
                            <h5><?php _e('Taxonomy conditionals', 'woocommerce-products-filter') ?></h5>
                            <input type="text" name="woof_settings[woof_quick_search][quick_search_tax_conditionals]" value="<?php echo $tax_conditionals ?>" />
                        </div>

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $taxonomies = $this->get_taxonomies();
                            if (!empty($taxonomies)) {
                                foreach ($taxonomies as $slug => $t) {
                                    $all_items[urldecode($slug)] = $t->labels->name;
                                }
                            }

                            asort($all_items);
//***

                            if (!isset($woof_settings['woof_quick_search']['items_for_text_search']) OR empty($woof_settings['woof_quick_search']['items_for_text_search'])) {
                                $woof_settings['woof_quick_search']['items_for_text_search'] = array();
                            }
                            $items_for_stat = (array) $woof_settings['woof_quick_search']['items_for_text_search'];
                            ?>
                            <br />
                            <h5><?php _e('Additional search data for text search', 'woocommerce-products-filter') ?></h5>
                            <div class="select-wrap">

                                <select multiple="" name="woof_settings[woof_quick_search][items_for_text_search][]" class="chosen_select">
                                    <?php foreach ($all_items as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if (in_array($key, $items_for_stat)): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select><br />

                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Additional search data: which taxonomies terms titles should be included into TEXT search data file. So, your customers will be able to find with TEXT search input also products using your taxonomies terms titles! After selecting new taxonomies here do not forget press Save and then Reassemble data file!', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

            </section>

        </div>

    </div>
</section>

<style type="text/css">
    .woof_qt_succes{
        font-style: italic;
        color: green;
        margin-left: 20px;
        display: inline-block;
    }
    .woof_qt_fail{
        font-style: italic;
        color: red;
        margin-left: 20px;
        display: inline-block;
    }

</style>

<script type="text/javascript">
    jQuery('#woof_quick_search_update').click(function () {
        var qs_nonce = "<?php echo wp_create_nonce('woof-qs-nonce') ?>";
        woof_qs_create_search_file(qs_nonce, ajaxurl);
    });
</script>

