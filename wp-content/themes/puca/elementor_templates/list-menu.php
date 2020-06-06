<?php 
/**
 * Templates Name: Elementor
 * Widget: List Nav
 */

$available_menus = $this->get_available_menus();

if (!$available_menus) {
	return;
}

$settings = $this->get_active_settings();

extract( $settings );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$menuNav = wp_get_nav_menu_items($menu);

$numItems = count($menuNav);
$i = 0;
$separator = $list_menu_separator;
?>
<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

	<div class="list-menu-wrapper">
		<?php 
			foreach ( $menuNav as $navItem ) { 
				if( ++$i === $numItems ) $separator = '';

				echo '<a href="'. esc_url($navItem->url) .'" title="'. esc_attr($navItem->title) .'">'. trim($navItem->title) .'</a>'.$separator;
			
			}
		?>
	</div>


</div>