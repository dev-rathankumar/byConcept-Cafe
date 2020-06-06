<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WOOF;
echo do_shortcode(stripcslashes($WOOF->settings['override_no_products']));
