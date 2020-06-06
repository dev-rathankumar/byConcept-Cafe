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

$class_to_filter = 'widget widget-products widget-categoriestabs widget-categoriestabs-3 '. $tabs_style .' ';
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

            <div class="row">

                <?php 

                    if( isset($banner_positions) ) {
                        switch ($banner_positions) {
                            case 'left':
                                $padding = 'right';
                                break;       

                            case 'right':
                                $padding = 'left';
                                break;
                            
                            default:
                                $padding = 'right';
                                break;
                        }
                    }

                ?>

                <div class="no-padding-<?php echo (isset($padding)) ? esc_attr($padding) : ''; ?> pull-<?php echo (isset($banner_positions)) ? esc_attr($banner_positions) : ''; ?> vc_fluid col-md-6 column-banner hidden-sm hidden-xs col-md-6">


                    <?php 

                        $img = wp_get_attachment_image_src($banner,'full'); 
                    ?>

                    <?php if ( !empty($img) && isset($img[0]) ): ?>

                        <?php 

                            if( isset($img[0]) ){
                                $style = 'style="background-image:url(\''.esc_url($img[0]).'\')"';
                            }

                        ?>
                        <div class="tab-banner" <?php echo trim($style); ?>>

                            <div class="wpb_wrapper">
                                <div class="banner-content">

                                    <?php if( isset($banner_title) ) : ?>
                                        <p>
                                            <?php echo esc_html($banner_title); ?>
                                        </p>

                                    <?php endif; ?>

                                    <?php if( isset($banner_des) ) : ?>
                                        <span>
                                            <?php echo trim($banner_des); ?>
                                        </span>
                                    <?php endif; ?>                                

                                    <?php if( isset($banner_link) ) : ?>
                                        <a href="<?php echo esc_url($banner_link); ?>"><?php esc_html_e('Shop now','puca'); ?></a>
                                    <?php endif; ?>

                                </div>
                                <div class="overlay"></div>
                            </div>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="tab-content-menu vc_fluid col-md-6 col-xs-12">

                    <ul role="tablist" class="nav nav-tabs">
                        <?php foreach ($categoriestabs as $tab) : ?>

                        <?php 

                            if( isset($show_catname_tabs) && $show_catname_tabs == 'yes' ) {

                                if( !in_array($tab['category'], $cat_array_id) ) {
                                    $tab_name = esc_html__('All category','puca');
                                } else {
                                    $category   = get_term_by( 'id', $tab['category'], 'product_cat' );
                                    $tab_name   = $category->name;         
                                }


                            } else {
                                $tab_slug = (isset($tab['producttabs'])) ? $tab['producttabs'] : '';
                                switch ($tab_slug) {
                                    case 'recent_product':
                                        $tab_name = esc_html__('New Arrivals', 'puca');
                                        break;                            
                                    case 'featured_product':
                                        $tab_name = esc_html__('Featured Products', 'puca');
                                        break;                           
                                    case 'best_selling':
                                        $tab_name = esc_html__('Best Sellers', 'puca');
                                        break;                            
                                    case 'top_rate':
                                        $tab_name = esc_html__('Top Rated', 'puca');
                                        break;                            
                                    case 'on_sale':
                                        $tab_name = esc_html__('On Sale', 'puca');
                                        break;
                                    
                                    default:
                                        $tab_name = esc_html__('New Arrivals', 'puca');
                                        break;
                                }
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

                                        $type = $tab['producttabs'];

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

                                    <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" class="tab-pane animated fadeIn <?php echo esc_attr( $tab_class ); ?>">

                                        <div class="hidden-xs tab-menu">
                                            <div class="tab-menu-wrapper">
                                                <?php 
                                                    $menu_id = $tab['nav_menu'];
                                                    puca_get_custom_menu($menu_id);
                                                ?>
                                            </div>
                                        </div>                        


                                        <?php wc_get_template( 'layout-products/'.$active_theme.'/'.$layout_type.'.php' , array( 'loop' => $loop, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'responsive_type' => $responsive_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number, 'disable_mobile' => $disable_mobile ) ); ?>


                                        <?php if( isset($tabs_view_more) && $tabs_view_more == 'yes') { ?>
                                            <a href="<?php echo esc_url( $link ); ?>" class="btn btn-view-all"><?php echo esc_html__('All Products', 'puca'); ?></a>
                                        <?php } ?>

                                    </div>

                                <?php $i++; endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>


            </div>

        
        </div>
    </div>

<?php endif; /*close without tabs*/ ?>