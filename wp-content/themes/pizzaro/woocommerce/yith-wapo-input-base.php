<?php
/**
 * Input field template
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$class_container = 'ywapo_input_container_' . $type;
$input_classes = array( 'ywapo_input ywapo_input_' . $type, 'ywapo_price_' . esc_attr( $price_type ) );

$index = $key;

/* price position fix */

if ( $hidelabel ) {
    $before_label = $after_label = '';
}

if ( $type == 'radio' || $type == 'checkbox') {
    $after_label .= $price_hmtl . $yith_wapo_frontend->getTooltip( stripslashes( $tooltip ) );
} else {
    $before_label .= $price_hmtl . $yith_wapo_frontend->getTooltip( stripslashes( $tooltip ) );
}

/* value fix */
if ( $type == 'radio' ) {
    $value = $key;
    $key = '';
} else if ( $type == 'date' ){
    $input_classes[] = 'ywapo_datepicker';
    $type = 'text';
}

$defatult_style_list = function_exists( 'pizzaro_redux_wapo_default_radio_ids_list' ) ? pizzaro_redux_wapo_default_radio_ids_list() : array();
$class_container .= in_array( $type_id, $defatult_style_list ) ? ' pz-radio-default' : '';

echo '<div class="ywapo_input_container ' . $class_container . '">';

echo sprintf( '%s<input placeholder="%s" data-typeid="%s" data-price="%s" data-pricetype="%s" data-index="%s" type="%s" name="%s[%s]" value="%s" %s class="%s" %s %s %s %s %s/>%s',
    $before_label,
    $placeholder,
    esc_attr( $type_id ),
    esc_attr( $price_calculated ),
    esc_attr( $price_type ),
    $index,
    esc_attr( $type ),
    esc_attr( $name ),
    $key,
    esc_attr( $value ),
    ( $checked ? 'checked' : '' ),
    implode( ' ', $input_classes ),
    $min_html,
    $max_html,
    $max_length,
    $required ? 'required="required"' : '',
    $disabled,
    $after_label
);

if ( $description != '' ) {
    echo '<p class="wapo_option_description">' . $description . '</p>';
}

echo '</div>';