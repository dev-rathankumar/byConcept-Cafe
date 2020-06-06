<?php if ( has_nav_menu( 'nav-category-img' ) ): ?>
<div class="category-inside">
	 <nav class="tbay-topmenu" role="navigation">
		<?php
			$args = array(
				'theme_location' => 'nav-category-img',
				'menu_class' => 'tbay-menu-category list-inline',
				'fallback_cb' => '',
				'menu_id' => 'nav-category-img',
				'walker' => new Puca_Tbay_Nav_Menu()
			);
			wp_nav_menu($args);
		?>
	</nav>
</div><!-- End Category Menu -->
<?php endif;?>