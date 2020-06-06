<?php
/**
 * Group container template
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$groups_list = array();
foreach( $types_list as $single_type ) {
    $groups_list[ $single_type->group_id ][] = $single_type;
}

$collapse_feature = apply_filters( 'yith_wapo_enable_collapse_feature', get_option( 'yith_wapo_settings_enable_collapse_feature' ) == 'yes' );
$addons_collapsed = apply_filters( 'yith_wapo_show_addons_collapsed', get_option( 'yith_wapo_settings_show_addons_collapsed' ) == 'yes' );

?>

<div id="yith_wapo_groups_container" class="yith_wapo_groups_container<?php
        echo $collapse_feature ? ' enable-collapse-feature' : '';
        echo $addons_collapsed ? ' show-addons-collapsed' : '';
    ?>"
    style="<?php echo apply_filters( 'yith_wapo_hide_groups_container', false ) ? 'display: none;' : '';?>">

    <?php

        foreach( $groups_list as $key => $types_list ) {
            $group = new YITH_WAPO_Group( $key );
            if( ! empty( $group->name ) ) {
                echo '<h2 class="group-name">' . esc_html( $group->name ) . '</h2>';
            }
            echo '<div class="yith_wapo_groups_container_wrap">';
            foreach ( $types_list as $single_type ) {
                $yith_wapo_frontend->printSingleGroupType( $product, $single_type );
            }
            echo '</div>';
        }

        $product_id = yit_get_base_product_id( $product );

        $product_display_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $product ) : $product->get_display_price();

        if ( function_exists('YITH_WCTM') && ! YITH_WCTM()->check_price_hidden(false,$product_id) ) {
            $product_display_price = yit_get_display_price( $product );
        } elseif ( !function_exists('YITH_WCTM') ) {
            $product_display_price = yit_get_display_price( $product );
        }

        $product_display_price = empty( $product_display_price ) ? 0 : $product_display_price;

        $product_tax_rates = wc_tax_enabled() ? WC_Tax::get_rates( $product->get_tax_class() ) : array();
        $current_tax_rate  = !! $product_tax_rates && is_array( $product_tax_rates ) ? current( $product_tax_rates ) : array();
        $tax_rates         = is_array( $current_tax_rate ) && isset( $current_tax_rate[ 'rate' ] ) ? $current_tax_rate[ 'rate' ] : false;

    ?>

    <div class="yith_wapo_group_total
        <?php echo ( get_option( 'yith_wapo_settings_show_add_ons_price_table' , 'no' ) == 'yes' ? 'yith_wapo_keep_show' : '' ); ?>"
        data-product-price="<?php echo esc_attr( $product_display_price ); ?>"
        data-product-id="<?php echo esc_attr( $product_id ); ?>"
        <?php if ( $tax_rates > 0 && apply_filters( 'wapo_enable_tax_string', false ) ) : ?>
            data-tax-rate="<?php echo esc_attr( $tax_rates ); ?>"
            data-tax-string="<?php echo apply_filters( 'wapo_total_tax_string', __( ' including VAT ', 'pizzaro' ) ); ?>"
        <?php endif; ?>
        >
        <div class="yith_wapo_group_product_price_total"><span class="price amount"></span></div>
        <div class="yith_wapo_group_option_total"><span class="price amount"></span></div>
        <div class="yith_wapo_group_final_total"><span class="price amount"></span></div>
    </div>

    <!-- Hidden input for checking single page -->
    <input type="hidden" name="yith_wapo_is_single" id="yith_wapo_is_single" value="1">

</div>