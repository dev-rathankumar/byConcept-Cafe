<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$store_user   = get_userdata( get_query_var( 'author' ) );
$store_info   = dokan_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';

get_header();
$sidebar_configs = puca_tbay_get_woocommerce_layout_configs();

if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) {
    $sidebar_configs['main']['class'] .= ' pull-right';
}
if ( isset($sidebar_configs['left_descreption']) && !isset($sidebar_configs['right_descreption']) ) {
    $sidebar_configs['main']['class'] .= ' pull-right';
}

$class_main = apply_filters('puca_tbay_woocommerce_content_class', 'container');

if( isset($sidebar_configs['container_full']) &&  $sidebar_configs['container_full'] ) {
    $class_main .= ' container-full';
}


$content_class = '';
if ( isset($sidebar_configs['left']) && is_active_sidebar($sidebar_configs['left']['sidebar']) && !isset($sidebar_configs['right']) ) {
    $content_class  .= $sidebar_configs['main']['class'];
    $content_class  .= ' pull-right';
} else if(!isset($sidebar_configs['left'])) {
    $content_class  .= $sidebar_configs['main']['class'];
}

if ( isset($sidebar_configs['left_descreption']) && is_active_sidebar($sidebar_configs['left_descreption']['sidebar']) && !isset($sidebar_configs['right_descreption']) ) {
    $content_class  .= $sidebar_configs['main_descreption']['class'];
    $content_class  .= ' pull-right';
} else if(!isset($sidebar_configs['left_descreption'])) {
    $content_class  .= $sidebar_configs['main_descreption']['class'];
}

 if( is_shop() || is_product_category() ) {
    wp_enqueue_style('sumoselect');
    wp_enqueue_script('jquery-sumoselect'); 
 }

?>

<?php do_action( 'puca_woo_template_main_before' ); ?>
<section id="main-container" class="main-content <?php echo esc_attr($class_main); ?>">
    
    <div class="row">

        <div id="main-content" class="archive-shop col-xs-12 <?php echo esc_attr($content_class); ?>">
            <div id="dokan-primary" class="dokan-single-store" >
                <div id="dokan-content" class="store-page-wrap woocommerce site-content" role="main">
            
                    <div id="dokan-content" class="store-page-wrap woocommerce" role="main">
                        <?php dokan_get_template_part( 'store-header' ); ?>

                        <?php do_action( 'dokan_store_profile_frame_after', $store_user, $store_info ); ?>

                        <?php if ( have_posts() ) { ?>

                            <div id="tbay-shop-products-wrapper" class="tbay-shop-products-wrapper">

                                <?php woocommerce_product_loop_start(); ?>
                                    <?php while ( have_posts() ) : the_post(); ?>

                                        <?php wc_get_template_part( 'content', 'product' ); ?>

                                    <?php endwhile; // end of the loop. ?>
                                <?php woocommerce_product_loop_end(); ?>

                            </div>

                            <?php dokan_content_nav( 'nav-below' ); ?>

                        <?php } else { ?>

                            <p class="dokan-info"><?php esc_html_e( 'No products were found of this vendor!', 'puca' ); ?></p>

                        <?php } ?>
                    </div>
                </div><!-- #content -->
            </div><!-- #primary -->
        </div><!-- #main-content -->

        <?php if ( is_active_sidebar('sidebar-store') ) : ?>
            <div id="dokan-secondary" class="col-xs-12 col-md-3">
                <aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
                    <?php dynamic_sidebar( 'sidebar-store'); ?>
                </aside>
            </div>
        <?php endif; ?>
        
    </div>
</section>
<?php

get_footer();