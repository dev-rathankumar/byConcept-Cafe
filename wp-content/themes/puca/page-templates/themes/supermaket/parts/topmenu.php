<?php if ( has_nav_menu( 'topmenu' ) ): ?>
    <div class="top-menu">
		<nav class="tbay-topmenu">
			<?php
				$args = array(
					'theme_location'  => 'topmenu',
					'container_class' => 'collapse navbar-collapse',
					'menu_class'      => 'tbay-menu-top nav navbar-nav megamenu',
					'fallback_cb'     => '',
					'menu_id'         => 'topmenu',
					'walker' => new Puca_Tbay_Nav_Menu()
				);
				wp_nav_menu($args);
			?>
		</nav>
    </div>
<?php endif; ?>