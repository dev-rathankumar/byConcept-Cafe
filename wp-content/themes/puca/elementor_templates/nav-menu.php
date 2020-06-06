<?php 
/**
 * Templates Name: Elementor
 * Widget: Menu Nav
 */
 
$available_menus = $this->get_available_menus();

if (!$available_menus) {
	return;
}

$settings = $this->get_active_settings();

extract( $settings );

$this->add_render_attribute('wrapper', 'class', ['tbay_custom_menu', 'tbay-'. $layout .'-menu' ] );

if( !empty($_css_classes) ) {  
	$this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$_id = puca_tbay_random_key();

$args = [
	'echo'        => false, 
	'menu'        => $menu,
	'container_class' => 'collapse navbar-collapse',
	'menu_id'     => 'menu-' . $this->get_nav_menu_index() . '-' . $_id,
	'walker'      => new Puca_Tbay_Custom_Nav_Menu(),
	'fallback_cb' => '__return_empty_string',
	'container'   => '',
];  

$args['menu_class']     = 'elementor-nav-menu menu';

if( $layout === 'treeview' ) {
	$args['menu_class'] .= ' treeview'; 
	$this->add_render_attribute('wrapper', 'class', 'treeview-menu' );
}

// General Menu.
$menu_html = wp_nav_menu($args);

// Dropdown Menu.
$args['menu_id'] = 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id();

if (empty($menu_html)) {
	return;
}

$this->add_render_attribute('main-menu', 'class', [
	'elementor-nav-menu--main',
	'elementor-nav-menu__container',
	'elementor-nav-menu--layout-' . $layout,
]);

$this->add_render_attribute('main-menu', 'class', 'tbay-'.$layout );

if( $layout === 'vertical' || $layout === 'treeview' ) {
	$this->add_render_attribute('main-menu', 'class', 'tbay-treevertical-lv1' );
}

$this->add_render_attribute( 
	'wrapper',
	[
		'data-wrapper' => wp_json_encode( [
			'layout' => $layout
		] ),
	]
); 

?>
<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
	<div class="widget widget_nav_menu">
		<?php $this->render_element_heading(); ?>
		
		<nav <?php echo trim($this->get_render_attribute_string('main-menu')); ?>><?php echo trim($menu_html); ?></nav>
	</div>
</div>