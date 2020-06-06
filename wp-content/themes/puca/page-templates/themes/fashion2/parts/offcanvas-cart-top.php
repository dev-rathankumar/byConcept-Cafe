<?php   
	$tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type', 'v1') );
?>
<div id="tbay-top-cart" class="tbay-top-cart v1 top-<?php echo esc_attr($tbay_header); ?> ">
	<div class="container container-full">
		<div class="dropdown-content">
			<div class="widget-header-cart">
				<h3 class="widget-title heading-title"><?php esc_html_e('Shopping Cart','puca'); ?></h3>
				<a href="javascript:;" class="offcanvas-close"><span>x</span></a>
			</div>
			<div class="widget_shopping_cart_content">
		    <?php woocommerce_mini_cart(); ?>
			</div>
		</div>
	</div>
</div>