<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion-rangeSlider/ion.rangeSlider.min.js', array('jquery'));
wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css');
$ion_slider_skin = 'skinNice';
if (isset($this->settings['ion_slider_skin']))
{
    $ion_slider_skin = $this->settings['ion_slider_skin'];
}
wp_enqueue_style('ion.range-slider-skin', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.' . $ion_slider_skin . '.css');
//***
$request = $this->get_request_data();
$uniqid = uniqid();
if(!isset($additional_taxes)){
	$additional_taxes="";
}
$preset_min =WOOF_HELPER::get_min_price($additional_taxes);
$preset_max = WOOF_HELPER::get_max_price($additional_taxes);
if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
    $tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
    $class_max   = $preset_max;
    foreach ( $tax_classes as $tax_class ) {
	if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
	    $class_max = ceil($preset_max + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $preset_max, $tax_rates ) ));
	}
    }

    $preset_max = $class_max;
}

$min_price =$this->is_isset_in_request_data('min_price') ? esc_attr($request['min_price']) : $preset_min;
$max_price = $this->is_isset_in_request_data('max_price') ? esc_attr($request['max_price']) : $preset_max;
//***
if (class_exists('WOOCS'))
{
    $preset_min = apply_filters('woocs_exchange_value', $preset_min);
    $preset_max = apply_filters('woocs_exchange_value', $preset_max);
    $min_price = apply_filters('woocs_exchange_value', $min_price);
    $max_price = apply_filters('woocs_exchange_value', $max_price);
}
//***
$slider_step = 1;
if (isset($this->settings['by_price']['ion_slider_step']))
{
    $slider_step = $this->settings['by_price']['ion_slider_step'];
    if (!$slider_step)
    {
        $slider_step = 1;
    }
}
//***
//esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) )
$slider_prefix = '';
$slider_postfix = '';
if (class_exists('WOOCS'))
{
    global $WOOCS;
    $currencies = $WOOCS->get_currencies();
    $currency_pos = 'left';
    if (isset($currencies[$WOOCS->current_currency]))
    {
        $currency_pos = $currencies[$WOOCS->current_currency]['position'];
    }
} else
{
    $currency_pos = get_option('woocommerce_currency_pos');
}
switch ($currency_pos)
{
    case 'left':
        $slider_prefix = get_woocommerce_currency_symbol();
        break;
    case 'left_space':
        $slider_prefix = get_woocommerce_currency_symbol() . ' ';
        break;
    case 'right':
        $slider_postfix = get_woocommerce_currency_symbol();
        break;
    case 'right_space':
        $slider_postfix = ' ' . get_woocommerce_currency_symbol();
        break;

    default:
        break;
}

//***
//https://wordpress.org/support/topic/results-found/
if ($preset_max < $max_price)
{
    $max = $max_price;
} else
{
    $max = $preset_max;
}
if ($preset_min > $min_price)
{
    $min = $min_price;
} else
{
    $min = $preset_min;
}
$tax=1.0;
if(isset($this->settings['by_price']['price_tax']) AND $this->settings['by_price']['price_tax']!=0){
   $tax= $tax+floatval($this->settings['by_price']['price_tax'])/100.00;
   $min_tax=floor($min*$tax);
   $max_tax=ceil($max*$tax);
   
   if($min!=$min_price){
      $min_price=($min_price*$tax);
   }else{
      $min_price=$min_tax; 
   }
   if($max!=$max_price){
      $max_price=($max_price*$tax);
   }else{
      $max_price=$max_tax;
   }
   $min=$min_tax;
   $max=$max_tax;
}
?>
<input class="woof_range_slider" id="<?php echo $uniqid ?>" data-taxes="<?php echo $tax ?>" data-min="<?php echo $min ?>" data-max="<?php echo $max ?>" data-min-now="<?php echo $min_price ?>" data-max-now="<?php echo $max_price ?>" data-step="<?php echo $slider_step ?>" data-slider-prefix="<?php echo $slider_prefix ?>" data-slider-postfix="<?php echo $slider_postfix ?>" value="" />
