<?php if ( has_nav_menu( 'category-menu' ) ): ?>
<div class="category-inside treeview-menu">
		<h3 class="category-inside-title"><?php esc_html_e('All Categories', 'puca'); ?></h3>
		<div class="category-inside-content">
			 <nav class="tbay-topmenu" role="navigation">
				<?php
					$args = array(
						'theme_location' => 'category-menu',
						'menu_class' => 'tbay-menu-category list-inline',
						'fallback_cb' => '',
						'menu_id' => 'category-menu',
					);

					if( class_exists("Mega_Tbay_Nav_Menu") ){

						$args['walker'] = new Mega_Tbay_Nav_Menu();
					}

					wp_nav_menu($args);
				?>
			</nav>
		</div>
</div><!-- End Category Menu -->
<?php endif;?>