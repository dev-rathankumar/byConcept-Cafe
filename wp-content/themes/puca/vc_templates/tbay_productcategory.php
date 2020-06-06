<?php

$el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );


$cat_array = array();
$args = array(
    'type' => 'post',
    'child_of' => 0,
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => false,
    'hierarchical' => 1,
    'taxonomy' => 'product_cat'
);
$categories = get_categories( $args );
puca_tbay_get_category_childs( $categories, 0, 0, $cat_array );

$cat_array_id   = array();
foreach ($cat_array as $key => $value) {
    $cat_array_id[]   = $value;
}

if( !in_array($category, $cat_array_id) ) {
	$category = -1;
    $loop            = puca_tbay_get_products( $category , '', 1, $number );
} else {
	$cat_category = get_term_by( 'id', $category, 'product_cat' );
	$slug 		  = $cat_category->slug;
	$loop 		  = puca_tbay_get_products( array($slug), '', 1, $number);
}


if( isset($responsive_type) && $responsive_type == 'yes') {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =      $columns;
    $screen_desktopsmall     =      3;
    $screen_tablet           =      3;
    $screen_mobile           =      1;  
}

$active_theme = puca_tbay_get_part_theme();

$_id = puca_tbay_random_key();

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-products '. $layout_type .' widget_products_'. $_id .' ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr($css_class); ?>">
	 <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
        <h3 class="widget-title">
            <?php if ( isset($title) && $title ): ?>
                <span><?php echo esc_html( $title ); ?></span>
            <?php endif; ?>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>



	<?php if(  $layout_type == 'carousel' || $layout_type == 'carousel-special' ) { ?>

		<div class="widget-content">
			<?php if ( $loop->have_posts() ): ?>
				<div class="products grid-wrapper woocommerce">
					<?php if ($image_cat): ?>
						<div class="widget-banner">
							<?php echo wp_get_attachment_image( $image_cat , 'full'); ?>
						</div>
					<?php endif ?>

					<?php wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'responsive_type' => $responsive_type, 'loop' => $loop, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number, 'disable_mobile' => $disable_mobile ) ); ?>
                 
				</div> 
			<?php endif; ?>
		</div>

	<?php } else { ?>

		<div class="widget-content">
			<?php if ( $loop->have_posts() ): ?>
				<div class="products grid-wrapper woocommerce">
					<?php if ($image_cat): ?>
						<div class="widget-banner tbay-image-loaded">
							<?php echo puca_tbay_get_attachment_image_loaded($image_cat, 'full'); ?>
						</div>
					<?php endif ?>
					
					<?php wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'responsive_type' => $responsive_type, 'loop' => $loop,'loop' => $loop, 'columns' => $columns, 'number' => $number,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile ) ); ?>
				</div>
			<?php endif; ?>

			<?php if($show_button == 'yes') : ?>
		        <div id="more_products_<?php echo esc_attr($_id); ?>" class="more_products" data-id="<?php echo esc_attr($_id); ?>">

		            <a href="javascript:void(0);" data-columns="<?php echo esc_attr($columns); ?>" data-loading-text="<?php esc_html_e('Loading...', 'puca'); ?>" data-loadmore="true" data-paged="1" " data-category="<?php echo esc_attr($category); ?>"  data-number="<?php echo esc_attr($number); ?>" data-desktop="<?php echo esc_attr($screen_desktop); ?>" data-desktopsmall="<?php echo esc_attr($screen_desktopsmall); ?>" data-tablet="<?php echo esc_attr($screen_tablet); ?>" data-mobile="<?php echo esc_attr($screen_mobile); ?>" >
		               <i class="icon-plus icons"></i>
		               <span class="text"><?php echo esc_html( $button_text ); ?></span>
		            </a>

		        </div>
		    <?php endif; ?>

		</div>

	<?php } ?>

</div>