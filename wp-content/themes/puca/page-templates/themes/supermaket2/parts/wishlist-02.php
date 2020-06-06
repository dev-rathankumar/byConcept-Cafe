
<?php if( class_exists( 'YITH_WCWL' ) ) { ?>
<div class="wishlist">
	<a class="text-skin wishlist-icon" href="<?php $wishlist_url = YITH_WCWL()->get_wishlist_url(); echo esc_attr($wishlist_url); ?>">
		<i class="icons icon-heart" aria-hidden="true"></i>
		<span class="count_wishlist"><?php $wishlist_count = YITH_WCWL()->count_products(); echo esc_attr($wishlist_count); ?></span>
		<span class="sub-title"><?php echo esc_html__('My Wishlist', 'puca'); ?></span>
	</a>
</div>
<?php } ?>