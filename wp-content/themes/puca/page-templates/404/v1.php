<?php 
	$sidebar_configs = puca_tbay_get_page_layout_configs();
?>

<section id="main-container" class=" container inner">
	<div class="clearfix">
		<?php if ( isset($sidebar_configs['left']) ) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
			  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>
		<div id="main-content" class="main-page page-404 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">

			<section class="error-404 v1 not-found text-center clearfix">
			<div class="notfound-top">
			</div>
				<div class="page-content notfound-bottom">
					<p class="sub-title"><?php esc_html_e( 'We&sbquo;re very sorry, the page you&sbquo;re looking for cannot be found. Please try searching for something else or ', 'puca' ); ?><a class="backtohome" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Back to Homepage.', 'puca'); ?></a></p>

					<?php get_search_form(); ?>
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</div><!-- .content-area -->
		<?php if ( isset($sidebar_configs['right']) ) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
			  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>
		
	</div>
</section>