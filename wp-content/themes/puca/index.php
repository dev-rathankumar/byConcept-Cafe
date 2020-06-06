<?php
get_header();

$get_header = puca_tbay_get_config('header_type');

if( empty($get_header) ) {
	$active_theme = puca_tbay_get_part_theme();

	$colContent = (is_active_sidebar('sidebar-default')) ? 9 : 12;
	?>
		<div id="primary" class="content-area content-index">
			<div id="main" class="site-main">
				<div class="container">
				<div class="container-inner main-content">
					<div class="row"> 
		                <!-- MAIN CONTENT -->
		                <div class="col-lg-<?php echo esc_attr($colContent); ?> col-md-<?php echo esc_attr($colContent); ?> col-sm-<?php echo esc_attr($colContent); ?>">
		                        <?php  if ( have_posts() ) : 
		                        	while ( have_posts() ) : the_post();
										?>
											<div class="layout-blog">
												<?php get_template_part( 'post-formats/'.$active_theme.'/content', get_post_format() ); ?>
											</div>
										<?php
									// End the loop.
									endwhile;
									puca_tbay_paging_nav();
									?>
		                        <?php else : ?>
		                            <?php get_template_part( 'post-formats/'.$active_theme.'/content', 'none' ); ?>
		                        <?php endif; ?>
		                </div>
						<?php if(is_active_sidebar('sidebar-default')) : ?>
							<div class="col-xs-3 sidebar">
							   <?php dynamic_sidebar('sidebar-default'); ?>
							</div>
						<?php endif;?>
		            </div>
	            </div>
	            </div>
			</div><!-- .site-main -->
		</div><!-- .content-area -->
<?php
} else {
	
	echo '<div class="tbay-wrapper-border fix-post-page"></div>';

	$sidebar_configs = puca_tbay_get_blog_layout_configs();
	$active_theme = puca_tbay_get_part_theme();

	puca_tbay_render_breadcrumbs();

	if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) {
		$sidebar_configs['main']['class'] .= ' pull-right';
	}

	$class_main = apply_filters('puca_tbay_post_content_class', 'container');

	if( isset($sidebar_configs['container_full']) &&  $sidebar_configs['container_full'] ) {
	    $class_main .= ' container-full';
	}

	$blog_columns = apply_filters( 'loop_blog_columns', 1 );


	if( isset($blog_columns) ) {

		switch ($blog_columns) {
		    case '1':
		        $blog_archive_class = 'col-xs-12 col-lg-12 col-md-12 col-sm-12';
		        break;
		    case '2':
		         $blog_archive_class = 'col-xs-12 col-lg-6 col-md-6 col-sm-6';
		        break;
		    case '3':
		         $blog_archive_class = 'col-xs-12 col-lg-4 col-md-4 col-sm-6';
		        break;	    
		    case '4':
		         $blog_archive_class = 'col-xs-12 col-xlg-3 col-lg-4 col-md-4 col-sm-6';
		        break;	    
		    case '5':
		         $blog_archive_class = 'col-xs-12 col-xlg-2-4 col-lg-2-4 col-md-2-4 col-sm-6';
		        break;	    
		    case '6':
		         $blog_archive_class = 'col-xs-12 col-xlg-2-4 col-lg-2 col-md-2 col-sm-6';
		        break;
		    default:
		      $blog_archive_class = 'col-xs-12 col-lg-4 col-md-4 col-sm-6';
		}

	}

	$columns	= $blog_columns;
	if(isset($blog_columns) && $blog_columns >= 4) {
		$screen_desktop 		= 3;
		$screen_desktopsmall 	= 3;
	} else {
		$screen_desktop 			= $blog_columns;
		$screen_desktopsmall 	= $blog_columns;
	}


	$screen_tablet 				= 2;
	$screen_mobile 				= 2;

	$data_responsive = ' data-xlgdesktop='. $columns .'';

	$data_responsive .= ' data-desktop='. $screen_desktop .'';

	$data_responsive .= ' data-desktopsmall='. $screen_desktopsmall .'';

	$data_responsive .= ' data-tablet='. $screen_tablet .'';

	$data_responsive .= ' data-mobile='. $screen_mobile .'';

	?>

	<section id="main-container" class="main-content  <?php echo esc_attr($class_main); ?> inner">

		<?php do_action( 'puca_post_template_main_container_before' ); ?>

		<div class="row">
			
			<?php if ( isset($sidebar_configs['left']) && isset($sidebar_configs['right']) ) : ?>
				<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
				  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
				   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
				  	</aside>
				</div>
			<?php endif; ?>

			<div id="main-content" class="col-sm-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
				<main id="main" class="site-main layout-blog">

					<?php do_action( 'puca_post_template_main_content_before' ); ?>

					<div class="row" <?php echo esc_attr($data_responsive); ?>>
						<?php if ( have_posts() ) : ?>

							<?php
							// Start the Loop.
							while ( have_posts() ) : the_post();

								/*
								 * Include the Post-Format-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
								 */
								?>

								<div class="<?php echo isset($blog_archive_class) ? esc_attr($blog_archive_class) : '';  ?>">
							
									<?php get_template_part( 'post-formats/'.$active_theme.'/content', get_post_format() ); ?>

								</div>

								<?php
							// End the loop.
							endwhile;

							// Previous/next page navigation.
							puca_tbay_paging_nav();

						// If no content, include the "No posts found" template.
						else :
							get_template_part( 'post-formats/'.$active_theme.'/content', 'none' );

						endif;
						?>
					</div>

					<?php do_action( 'puca_post_template_main_content_after' ); ?>

				</main><!-- .site-main -->
			</div><!-- .content-area -->
			
			<?php if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) : ?>
				<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
				  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
				   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
				  	</aside>
				</div>
			<?php endif; ?>
			
			<?php if ( isset($sidebar_configs['right']) ) : ?>
				<div class="<?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
				  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
				   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
				  	</aside>
				</div>
			<?php endif; ?>
			
		</div>

		<?php do_action( 'puca_post_template_main_container_after' ); ?>
	</section> 
	<?php

}

get_footer(); ?>
