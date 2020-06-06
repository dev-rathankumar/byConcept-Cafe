<?php
$product_item = isset($product_item) ? $product_item : 'list';
$active_theme = puca_tbay_get_part_theme();
?>
<ul class="tbay-w-products-list">
	<?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
		<?php wc_get_template_part( 'item-product/'.$active_theme.'/'.$product_item ); ?>
	<?php endwhile; ?>
</ul>
<?php wp_reset_postdata(); ?>