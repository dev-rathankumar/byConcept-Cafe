<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress
 * @subpackage Puca
 * @since Puca 1.3.6
 */

get_header();
$sidebar_configs = puca_tbay_get_blog_layout_configs();

$active_theme = puca_tbay_get_part_theme();

$class_main = apply_filters('puca_tbay_post_content_class', 'container');

if( isset($sidebar_configs['container_full']) &&  $sidebar_configs['container_full'] ) {
    $class_main .= ' container-full';
}

puca_tbay_render_breadcrumbs();
?>
<header class="page-header">
	<div class="content <?php echo esc_attr($class_main); ?>">
	<?php
	the_archive_description( '<div class="taxonomy-description">', '</div>' );
	?>
	</div>
</header><!-- .page-header -->
<section id="main-container" class="main-content  <?php echo apply_filters('puca_tbay_blog_content_class', 'container');?> inner">
	<div class="row">
		<?php if ( isset($sidebar_configs['left']) ) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
			  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>

		<div id="main-content" class="col-sm-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<main id="main" class="site-main layout-blog">

			<?php if ( have_posts() ) : ?>

				<header class="page-header hidden">
					<?php
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->

				<?php
				// Start the Loop.
				while ( have_posts() ) : the_post(); 

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					?>
							<?php get_template_part( 'post-formats/'.$active_theme.'/content', get_post_format() ); ?>
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

			</main><!-- .site-main -->
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
<?php get_footer(); ?>
