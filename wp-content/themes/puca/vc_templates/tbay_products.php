<?php

$el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );

if ( $type == '' ) return;

if (isset($categories) && !empty($categories) && strpos($categories, ',') !== false) {
    $categories = explode(',', $categories);
    $categories = puca_tbay_get_category_by_id($categories);

    $loop = puca_tbay_get_products( $categories, $type, 1, $number ); 
} else if( isset($categories) && !empty($categories) ) {
    $categories = get_term_by( 'id', $categories, 'product_cat' )->slug;

    $loop = puca_tbay_get_products( array($categories), $type, 1, $number ); 
} else {

    $loop = puca_tbay_get_products( '', $type, 1, $number ); 
}

$data_categories = $categories;
 
$_id = puca_tbay_random_key();
$_count = 1;


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

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-'. $layout_type .' widget-products products widget_products_'. $_id .' ';


if(isset($carousel_blur) && $carousel_blur) {
    $class_to_filter .= ' carousel-blur';
    $loop_type       = 'yes';
}

if(isset($special_home5) && $special_home5) {
    $class_to_filter .= ' special-home5';
}

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

	<?php if ( $loop->have_posts() ) : ?>
		<div class="widget-content woocommerce">
			<div class="<?php echo esc_attr( $layout_type ); ?>-wrapper">

                <?php  wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number, 'responsive_type' => $responsive_type, 'disable_mobile' => $disable_mobile ) ); ?>

			</div>
		</div>
	<?php endif; ?>

    <?php if(isset($show_button) && $show_button == 'yes') : ?>
        <div id="more_products_<?php echo esc_attr($_id); ?>" class="more_products" data-id="<?php echo esc_attr($_id); ?>">

            <a href="javascript:void(0);" data-layout="<?php echo esc_attr($layout_type); ?>" data-type="<?php echo esc_attr($type); ?>" data-columns="<?php echo esc_attr($columns); ?>" data-loading-text="<?php esc_html_e('Loading...', 'puca'); ?>" data-loadmore="true" data-paged="1" data-category="<?php echo esc_attr($data_categories); ?>"  data-number="<?php echo esc_attr($number); ?>" data-desktop="<?php echo esc_attr($screen_desktop); ?>" data-desktopsmall="<?php echo esc_attr($screen_desktopsmall); ?>" data-tablet="<?php echo esc_attr($screen_tablet); ?>" data-mobile="<?php echo esc_attr($screen_mobile); ?>" >
                <i class="icon-plus icons"></i>
               <span class="text"><?php echo esc_html( $button_text ); ?></span>
            </a>

        </div>
    <?php endif; ?>    

    <?php if(isset($show_view_all) && $show_view_all == 'yes') : ?>
        <div id="show-view-all<?php echo esc_attr($_id); ?>" class="show-view-all">


            <?php 
                if( empty($data_categories) ) {
                    $url = get_permalink( wc_get_page_id( 'shop' ) );
                } else if( is_array($data_categories) ) {
                    $category   = get_term_by( 'slug', $data_categories['0'], 'product_cat' );
                    $url = get_term_link( $category->term_id, 'product_cat' );
                } else {
                    $url  = get_term_link($categories, 'product_cat');
                }

            ?>

            <a href="<?php echo esc_url($url); ?>">
                <?php echo esc_html($button_text_view_all); ?>
            </a>

        </div>
    <?php endif; ?>



</div>
