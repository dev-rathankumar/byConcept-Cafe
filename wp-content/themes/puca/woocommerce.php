<?php

get_header();
$sidebar_configs = puca_tbay_get_woocommerce_layout_configs();

if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) {
	$sidebar_configs['main']['class'] .= ' pull-right';
}

$class_main = apply_filters('puca_tbay_woocommerce_content_class', 'container');

if( isset($sidebar_configs['container_full']) &&  $sidebar_configs['container_full'] ) {
    $class_main .= ' container-full';
}

$images_layout   =  apply_filters( 'woo_images_layout_single_product', 10, 2 );

if( $images_layout =='carousel' && is_singular( 'product' ) ) {
	$class_main = '';
}

$active_theme = puca_tbay_get_theme();

$content_class = '';
if ( isset($sidebar_configs['left']) && is_active_sidebar($sidebar_configs['left']['sidebar']) && !isset($sidebar_configs['right']) ) {
	$content_class  .= $sidebar_configs['main']['class'];
	$content_class  .= ' pull-right';
} else if(!isset($sidebar_configs['left'])) {
	$content_class  .= $sidebar_configs['main']['class'];
}

 if( is_shop() || is_product_category() ) {
    wp_enqueue_style('sumoselect');
    wp_enqueue_script('jquery-sumoselect'); 
 }

?>

<?php do_action( 'puca_woo_template_main_before' ); ?>

<section id="main-container" class="main-content <?php echo esc_attr($class_main); ?>">
	<div class="row">
		
		<?php if ( isset($sidebar_configs['left']) && is_active_sidebar($sidebar_configs['left']['sidebar']) && isset($sidebar_configs['right']) ) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
			  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>

		<div id="main-content" class="<?php  echo ( !is_singular( 'product' ) ) ? 'archive-shop' : 'singular-shop'; ?> col-xs-12 <?php echo esc_attr($content_class); ?>">

			<?php do_action( 'puca_woo_template_main_primary_before' ); ?>

			<div id="primary" class="content-area">
				<div id="content" class="site-content" role="main">

					<?php  
				 if ( is_singular( 'product' ) ) {

		            while ( have_posts() ) : the_post();

		                wc_get_template_part( 'single-product/themes/'.$active_theme.'/content', 'single-product' );

		            endwhile;

		        } else { ?>
		            <?php if ( is_search() || ( apply_filters( 'woocommerce_show_page_title', true ) && apply_filters('puca_woo_cat_title_des_img', false) ) ) : ?>
		            		<h1 class="page-title title-woocommerce"><?php woocommerce_page_title(); ?></h1>
		            <?php endif; ?>



		            <?php

			            if ( apply_filters('puca_woo_cat_title_des_img', false ) ) {
			            	do_action( 'woocommerce_archive_description' ); 
			            }
		            ?>


		            

		            <?php if ( have_posts() ) : ?>

		            	<?php if((is_shop() && '' !== get_option('woocommerce_shop_page_display')) || (is_product_category() && 'subcategories' == get_option('woocommerce_category_archive_display')) || (is_product_category() && 'both' == get_option('woocommerce_category_archive_display'))) : ?>
						
							<ul class="all-subcategories row">
								<?php puca_woocommerce_sub_categories(); ?>
								<li class="clearfix"></li>
							</ul>				
						
						<?php endif; ?>


						<?php do_action('woocommerce_before_shop_loop'); ?>



		                <?php woocommerce_product_loop_start(); ?>

		                   
							<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
								<?php while ( have_posts() ) : ?>
									<?php the_post(); ?>
									<?php wc_get_template_part( 'content', 'product' ); ?>
								<?php endwhile; ?>
							<?php endif; ?>

		                <?php woocommerce_product_loop_end(); ?>
		                


		               	<?php do_action('woocommerce_after_shop_loop'); ?>


					<?php else : ?> 

						<?php do_action( 'woocommerce_no_products_found' ); ?>

					<?php endif; ?>

		        <?php } ?>

				</div><!-- #content -->
			</div><!-- #primary -->

			<?php do_action( 'puca_woo_template_main_primary_after' ); ?>

		</div><!-- #main-content -->
		
		<?php if ( isset($sidebar_configs['left']) && is_active_sidebar($sidebar_configs['left']['sidebar']) && !isset($sidebar_configs['right']) ) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
				<?php do_action( 'puca_after_sidebar_mobile' ); ?>
			  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>
		
		<?php if (  isset($sidebar_configs['right']) && is_active_sidebar($sidebar_configs['right']['sidebar'])) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
				<?php do_action( 'puca_after_sidebar_mobile' ); ?>
			  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php if ( is_singular( 'product' ) ) : ?>
 <?php do_action( 'puca_woo_singular_template_main_after' ); ?>
<?php endif; ?>

<?php

get_footer();