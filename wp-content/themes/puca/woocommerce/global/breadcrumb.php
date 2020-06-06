<?php
/**
 * Shop breadcrumb
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $breadcrumb ) ) {

	echo trim($wrap_before);

	$end = '' ;
	foreach ( $breadcrumb as $key => $crumb ) {

		echo trim($before);

		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
		} else {
			if( !is_product() ) {
				echo '<li>'.esc_html( $crumb[0] ).'</li>';
			}
		}

		echo trim($after);

		$end = esc_html( $crumb[0] );
	}

	echo trim($wrap_after);

}
?>
