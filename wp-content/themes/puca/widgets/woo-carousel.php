<?php

$navigations = $paginations = $loop_type = $auto_type = $autospeed_type = $disable_mobile = '';

extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

$navigations 	= ($navigations) ? "yes" : "";
$paginations 	= ($paginations) ? "yes" : "";
$loop_type 		= ($loop_type) ? "yes" : "";
$auto_type 		= ($auto_type) ? "yes" : ""; 
$disable_mobile = ($disable_mobile) ? "yes" : "";

$get_sub_title = '';
if ( isset($sub_title) && $sub_title ) {
    $get_sub_title = '<span class="subtitle">'. esc_html($sub_title) .'</span>';
}

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . trim($after_title) . trim($get_sub_title);
}

if ( $types == '' ) return;

if (isset($categories) && !empty($categories)) {
    $categories = explode(',', $categories);
}

$_id = puca_tbay_random_key();
$_count = 1;

$rand = '';
if($types == 'rand') {
	$types = 'product';
	$rand  = 'rand';
}

$loop = puca_tbay_get_products( $categories, $types, 1, $numbers, $rand );      


$screen_desktop          =      isset($columns) ? $columns : 4;
$screen_desktopsmall     =      isset($columns_destsmall) ? $columns_destsmall : 3;
$screen_tablet           =      isset($columns_tablet) ? $columns_tablet : 3;
$screen_mobile           =      isset($columns_mobile) ? $columns_mobile : 1;

$pagi_type 	= 	$paginations;
$nav_type 	= 	$navigations;

$layout_type = 'carousel';

$active_theme = puca_tbay_get_part_theme();

?>
<div class="widget widget-<?php echo esc_attr($layout_type); ?> widget-products products">

	<?php if ( $loop->have_posts() ) : ?>
		<div class="widget-content woocommerce">
			<div class="<?php echo esc_attr( $layout_type ); ?>-wrapper">

                <?php  wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $numbers, 'loop_type' => $loop_type, 'auto_type' => $auto_type, 'disable_mobile' => $disable_mobile  ) ); ?>

			</div>
		</div>
	<?php endif; ?>

</div>
