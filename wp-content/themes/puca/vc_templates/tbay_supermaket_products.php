<?php

$el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );

if ( $type == '' ) return;

 
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
	$loop            = puca_tbay_get_products( -1 , $type, 1, $number );
} else {
	$category       = get_term_by( 'id', $category, 'product_cat' );
	$cat_category   = $category->slug;
	$loop           = puca_tbay_get_products( array($cat_category), $type, 1, $number );
}

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget tb_supermarket_products widget-'. $layout_type .' widget-products products widget_products_'. $_id .' ';
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
			<div class="<?php echo esc_attr( $layout_type ); ?>-wrapper row">
				
				<?php 
					$banner_positions = (isset($banner_positions)) ? $banner_positions : '';

				?>
				<div class="pull-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?> hidden-sm hidden-xs vc_fluid col-md-2 tab-banner">


					<?php 
						$banner         = (isset($banner)) ? $banner : '';
						$banner_link    = (isset($banner_link)) ? $banner_link : ''; 
						$img            = (isset($banner)) ? wp_get_attachment_image_src($banner,'full') : ''; 

					?>

					<?php if ( !empty($img) && isset($img[0]) ): ?>
						<?php if(isset($banner_link) && !empty($banner_link)) : ?>
							<div class="img-banner tbay-image-loaded">
								<a href="<?php echo esc_url($banner_link); ?>">
						           <?php 
                                        $image_alt  = get_post_meta( $banner, '_wp_attachment_image_alt', true);
                                        puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
                                    ?>
								</a>
							</div>
						<?php else : ?>
							<div class="img-banner tbay-image-loaded">
					           <?php 
                                    $image_alt  = get_post_meta( $banner, '_wp_attachment_image_alt', true);
                                    puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
                                ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

				</div>
				
				 <div class="col-md-10">
					<?php  wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number, 'responsive_type' => $responsive_type, 'disable_mobile' => $disable_mobile ) ); ?>
				</div>

			</div>
		</div>
	<?php endif; ?>

    <?php if(isset($show_button) && $show_button == 'yes') : ?>
        <div id="more_products_<?php echo esc_attr($_id); ?>" class="more_products" data-id="<?php echo esc_attr($_id); ?>">

            <a href="javascript:void(0);" data-layout="<?php echo esc_attr($layout_type); ?>" data-type="<?php echo esc_attr($type); ?>" data-columns="<?php echo esc_attr($columns); ?>" data-loading-text="<?php esc_html_e('Loading...', 'puca'); ?>" data-loadmore="true" data-paged="1" data-category="<?php echo esc_attr($category); ?>"  data-number="<?php echo esc_attr($number); ?>" data-desktop="<?php echo esc_attr($screen_desktop); ?>" data-desktopsmall="<?php echo esc_attr($screen_desktopsmall); ?>" data-tablet="<?php echo esc_attr($screen_tablet); ?>" data-mobile="<?php echo esc_attr($screen_mobile); ?>" >
                <i class="icon-plus icons"></i>
               <span class="text"><?php echo esc_html( $button_text ); ?></span>
            </a>

        </div>
    <?php endif; ?>    

    <?php if(isset($show_view_all) && $show_view_all == 'yes') : ?>
        <div id="show-view-all<?php echo esc_attr($_id); ?>" class="show-view-all">


            <?php 
				
				if( !in_array($category, $cat_array_id) ) {
					$url            = get_permalink( wc_get_page_id( 'shop' ) );
				} else {
					$category       = get_term_by( 'id', $category, 'product_cat' );
					$url           = get_term_link( $category->term_id, 'product_cat' );
				}

            ?>

            <a href="<?php echo esc_url($url); ?>">
                <?php echo esc_html($button_text_view_all); ?>
            </a>

        </div>
    <?php endif; ?>



</div>
