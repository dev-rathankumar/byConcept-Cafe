<?php

$tabs_style = $el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = '';
extract( $atts );
$_id = puca_tbay_random_key();

if (isset($categoriestabs) && !empty($categoriestabs)):
    $categoriestabs = (array) vc_param_group_parse_atts( $categoriestabs );

$i = 0;

if( isset($responsive_type) ) {
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

$active_theme = puca_tbay_get_part_theme();

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-products widget-categoriestabs widget-categoriestabs-banner '. $tabs_style .' ';

if((isset($tab_title_center) && $tab_title_center == 'yes')) {
    $class_to_filter .= 'title-center ';
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

        <div class="widget-content woocommerce">

            <div class="">




                    <ul role="tablist" class="nav nav-tabs">
                        <?php foreach ($categoriestabs as $tab) : ?>

                        <?php 


                            if( !in_array($tab['category'], $cat_array_id) ) {
                                $tab_name = esc_html__('All category','puca');
                            } else {
                                $category   = get_term_by( 'id', $tab['category'], 'product_cat' );
                                $tab_name   = $category->name;         
                            }

                        
                        ?> 

                        <?php 
                            $li_class = ($i == 0 ? ' class=active' : '');
                        ?>
                        <li <?php echo esc_attr( $li_class ); ?>>
                            <a href="#tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" data-toggle="tab">
                                <?php echo esc_html($tab_name); ?>
                            </a>
                        </li>

                        <?php $i++; endforeach; ?>
                    </ul>

                    <div class="widget-inner">
                        
                        <div class="tab-content-product">
                            <div class="tab-content">
                                <?php $i = 0; foreach ($categoriestabs as $tab) : ?>


                                    <?php 

                                        $type = isset($tab['producttabs']) ? $tab['producttabs'] : '' ;

                                        if( !in_array($tab['category'], $cat_array_id) ) {
                                            $loop            = puca_tbay_get_products( -1 , $type, 1, $number );
                                            $link            = get_permalink( wc_get_page_id( 'shop' ) );
                                        } else {
                                            $category       = get_term_by( 'id', $tab['category'], 'product_cat' );
                                            $cat_category   = $category->slug;
                                            $loop           = puca_tbay_get_products( array($cat_category), $type, 1, $number );
                                            $link           = get_term_link( $category->term_id, 'product_cat' );
                                        }

                                        $tab_class = ($i == 0 ? 'active' : '');
                                    ?>

                                    <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" class="row tab-pane animated fadeIn <?php echo esc_attr( $tab_class ); ?>">

                                        <?php 
                                            $banner_positions = (isset($tab['banner_positions'])) ? $tab['banner_positions'] : '';

                                        ?>
                                        <div class="pull-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?> hidden-sm hidden-xs vc_fluid col-md-2 tab-banner">


                                            <?php 
                                                $banner         = (isset($tab['banner'])) ? $tab['banner'] : '';
                                                $banner_link    = (isset($tab['banner_link'])) ? $tab['banner_link'] : ''; 
                                                $img            = (isset($tab['banner'])) ? wp_get_attachment_image_src($tab['banner'],'full') : ''; 

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

                                        <div class=" col-md-10">
                                            <?php wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'responsive_type' => $responsive_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number, 'disable_mobile' => $disable_mobile ) ); ?>
                                        </div>


                                    </div>

                                <?php $i++; endforeach; ?>
                            </div>
                    </div>

                </div>


            </div>

        
        </div>
    </div>

<?php endif; /*close without tabs*/ ?>