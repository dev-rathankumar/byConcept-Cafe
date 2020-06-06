<?php

$el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );

if ( $producttabs == '' ) return; 


$_id = puca_tbay_random_key();
$_count = 1;

$list_query = $this->getListQuery( $atts );




if( isset($responsive_type) ) {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =     	$columns;
    $screen_desktopsmall     =      3;
    $screen_tablet           =      3;
    $screen_mobile           =      1;  
}

$active_theme = puca_tbay_get_part_theme();

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-products widget-product-tabs products widget-'. $layout_type .' ';

if( isset($tab_title_center) && $tab_title_center == 'yes' ) {
	$class_to_filter .= 'title-center ';
}

$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );



if ( count($list_query) > 0 ) {
?>
	<div class="<?php echo esc_attr($css_class); ?>">
		<div class="tabs-container tab-heading text-center clearfix tab-v8">
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
			<ul class="tabs-list nav nav-tabs">
				<?php 
					$__count=0; 
				?>
				<?php foreach ($list_query as $key => $li) { ?>
					<?php $li_class = ($__count==0) ?' class=active' : ''; ?>
						<li <?php echo esc_attr( $li_class ); ?>><a href="#<?php echo esc_attr($key.'-'.$_id); ?>" data-toggle="tab" data-title="<?php echo esc_attr($li['title']);?>"><?php echo esc_html($li['title']);?></a></li>
					<?php $__count++; ?>
				<?php } ?>
			</ul>
		</div>


		<?php if(  $layout_type == 'carousel' || $layout_type == 'carousel-special' ) { ?>

			<div class="widget-content tab-content woocommerce">
				<?php $__count=0; ?>
				<?php foreach ($list_query as $key => $li) { ?>
					<?php

						if (isset($categories) && !empty($categories) && strpos($categories, ',') !== false) {
						    $category = explode(',', $categories);
						    $category = puca_tbay_get_category_by_id($category);

						    $loop = puca_tbay_get_products( $category, $key, 1, $number ); 
						} else if( isset($categories) && !empty($categories) ) {
						    $category = get_term_by( 'id', $categories, 'product_cat' )->slug;

						    $loop = puca_tbay_get_products( array($category), $key, 1, $number ); 
						} else {

						    $loop = puca_tbay_get_products( '', $key, 1, $number ); 
						}
						
						$class_tab = ($__count == 0 ? ' active' : '');
					?>
					<div class="tab-pane animated fadeIn <?php echo esc_attr( $class_tab ); ?>" id="<?php echo esc_attr($key).'-'.$_id; ?>">
						<div class="grid-wrapper">
							<?php

								if ( $loop->have_posts()) {

									wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'responsive_type' => $responsive_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number, 'disable_mobile' => $disable_mobile ) );
								}
							?>
						</div>

					</div>
					<?php $__count++; ?>
				<?php } ?>
			</div>

		<?php } else { ?>

			<div class="widget-content tab-content woocommerce">
				<?php $__count=0; ?>
				<?php foreach ($list_query as $key => $li) { ?>

					<?php
						if (isset($categories) && !empty($categories) && strpos($categories, ',') !== false) {
						    $category = explode(',', $categories);
						    $category = puca_tbay_get_category_by_id($category);

						    $loop = puca_tbay_get_products( $category, $key, 1, $number ); 
						} else if( isset($categories) && !empty($categories) ) {
						    $category = get_term_by( 'id', $categories, 'product_cat' )->slug;

						    $loop = puca_tbay_get_products( array($category), $key, 1, $number ); 
						} else {

						    $loop = puca_tbay_get_products( '', $key, 1, $number ); 
						}

						$tab_class = ($__count == 0 ? ' active' : '');
					?>

					<div class="tab-pane animated fadeIn <?php echo esc_attr( $tab_class ); ?>" id="<?php echo esc_attr($key).'-'.$_id; ?>">
						<div class="grid-wrapper products-grid">
							<?php

								if ( $loop->have_posts()) {
									
									wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'columns' => $columns, 'responsive_type' => $responsive_type, 'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number ) );
								}
							?>
						</div>

					</div>
					<?php $__count++; ?>
				<?php } ?>
			</div>		

            <?php if(isset($show_view_all) && $show_view_all == 'yes') : ?>
                <div id="show-view-all<?php echo esc_attr($_id); ?>" class="show-view-all">

                    <?php 

                    	if (isset($categories) && !empty($categories) && strpos($categories, ',') !== false) {
						    $category = explode(',', $categories);
						    $category = puca_tbay_get_category_by_id($category);
 							$url  = get_term_link($category['0'], 'product_cat');
						} else if( isset($categories) && !empty($categories) ) {
							$category = get_term_by( 'id', $categories, 'product_cat' )->slug;
							$url  = get_term_link($category, 'product_cat');
						} else {
							$url = get_permalink( wc_get_page_id( 'shop' ) );
						}

                    ?>

                    <a href="<?php echo esc_url($url); ?>">
                        <?php echo esc_html($button_text_view_all); ?>
                    </a>

                </div>
            <?php endif; ?>	

		<?php } ?>

	</div>
<?php wp_reset_postdata(); ?>
<?php } ?>

