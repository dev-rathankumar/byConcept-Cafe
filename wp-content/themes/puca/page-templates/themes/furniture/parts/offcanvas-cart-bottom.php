<?php   
	global $woocommerce; 
?>
<div id="tbay-bottom-cart" class="tbay-bottom-cart v1">
	<div class="content">
		<div class="widget-header-cart">
			<h3 class="widget-title heading-title"><?php esc_html_e('My cart','puca'); ?><span class="mini-cart-items"><?php echo sprintf( '%d', $woocommerce->cart->cart_contents_count );?></span></h3>
			<a href="javascript:;" class="offcanvas-close"><i class="icon-close icons"></i></a>
		</div>
		<div class="widget_shopping_cart_content">
	    <?php woocommerce_mini_cart(); ?>
		</div>
	</div>
</div>