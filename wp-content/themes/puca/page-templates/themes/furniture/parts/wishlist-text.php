
<?php if( class_exists( 'YITH_WCWL' ) ) { ?>
	<a class="text-skin wishlist-icon" href="<?php $wishlist_url = YITH_WCWL()->get_wishlist_url(); echo esc_url($wishlist_url); ?>">
		<?php esc_html_e('My Wishlist','puca'); ?>
		<span class="count_wishlist">(<?php $wishlist_count = YITH_WCWL()->count_products(); echo esc_html($wishlist_count); ?>)</span>
	</a>
<?php } ?>