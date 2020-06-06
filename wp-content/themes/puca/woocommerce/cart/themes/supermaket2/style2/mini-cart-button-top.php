<?php   
	global $woocommerce; 
	$_id = puca_tbay_random_key();
?>
<div class="tbay-topcart">
 <div id="cart" class="cart-dropdown dropdown version-1">
        <a class="dropdown-toggle mini-cart v1 top" data-toggle="dropdown" aria-expanded="true" role="button" aria-haspopup="true" data-delay="0" href="#" title="<?php esc_html_e('View your shopping cart', 'puca'); ?>">
            
	        <span class="text-skin cart-icon">
				<i class="icon-bag"></i>
				<span class="mini-cart-items">
				   <?php echo sprintf( '%d', $woocommerce->cart->cart_contents_count );?>
				</span>
			</span>

			<span class="sub-title"><?php echo esc_html__('My Cart', 'puca'); ?></span>
            
        </a>            
    </div>
</div>    