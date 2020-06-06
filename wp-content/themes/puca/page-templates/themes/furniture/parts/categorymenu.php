<?php if ( has_nav_menu( 'nav-category-menu' ) ): ?>

<div class="category-inside treeview-menu">
	<h3 class="category-inside-title"><?php esc_html_e('All Department', 'puca'); ?></h3>
	<div class="category-inside-content">
		<nav class="tbay-topmenu" role="navigation">
			<?php
				$args = array(
					'theme_location' => 'nav-category-menu',
					'menu_class'      => 'tbay-menu-category list-inline treeview',
					'fallback_cb'     => '',
					'menu_id' => 'category-menu',
				);
				if( class_exists("Puca_Tbay_Custom_Nav_Menu") ){

					$args['walker'] = new Puca_Tbay_Custom_Nav_Menu();
				}
				wp_nav_menu($args);
			?>
		</nav>
	</div>
</div>
<?php endif;?>