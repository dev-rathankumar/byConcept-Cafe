<?php
/**
 * The Template for displaying project archives, including the main showcase page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/projects/archive-project.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

wp_enqueue_script( 'jquery-shuffle' );


global $projects_loop,$post,$puca_projectrows;


// Store loop count we're currently on
if (empty($projects_loop['loop']))
	$projects_loop['loop'] = 0;
// Store column count for displaying the grid
if (empty($projects_loop['columns'])) {
	$projects_loop['columns'] = apply_filters('projects_loop_columns', 4);
}

$portfolio_per_page 				= apply_filters('projects_per_page', 12);

$container 				= apply_filters('projects_container_class', 'container');

function puca_fix_title_page_project($title) {

	$projects_page_id 	= projects_get_page_id( 'projects' );
	$title   			= get_the_title( $projects_page_id );

    return $title;
}
add_filter('pre_get_document_title', 'puca_fix_title_page_project');

?>

<?php

get_header(); ?>
<?php puca_tbay_render_breadcrumbs(); ?>
<div class="main-container page-portfolio archive-portfolio full-width">
	<div class="full-wrapper portfolio-content">
		<div class="page-content">
				<?php
					/**
					 * projects_before_main_content hook
					 *
					 * @hooked projects_output_content_wrapper - 10 (outputs opening divs for the content)
					 */
					do_action('projects_before_main_content');
				?>
 			<div class="container">
				<?php do_action('projects_archive_description'); ?>
			</div>

			<div class="<?php echo esc_attr($container); ?>">
				<?php if (have_posts()) : ?>

					<?php
						/**
						 * projects_before_loop hook
						 *
						 */
						do_action('projects_before_loop');
					?>
					<div class="filter-options btn-group">
						<button data-group="all" class="btn active btn--warning"><?php esc_html_e('All', 'puca');?></button>
						<?php 
						$datagroups = array();
						if( isset( $portfolio_per_page  ) ) {
							query_posts('posts_per_page='. $portfolio_per_page .'&post_type=project');
						}
						while (have_posts()) : the_post();
						
							$prcates = get_the_terms($post->ID, 'project-category');
							
							foreach ($prcates as $category) {
								$datagroups[$category->slug] = $category->name;
							}
							?>
						<?php endwhile; // end of the loop. ?>
						<?php
						foreach($datagroups as $key=>$value) { ?>
							<button data-group="<?php echo esc_attr($key);?>" class="btn btn--warning"><?php echo esc_html($value);?></button>
						<?php }
						?>
					</div>
					<div class="list_projects entry-content">
						<div class="row">
						<?php projects_project_loop_start(); ?>
							<?php $puca_projectrows = 1; ?>
							<?php while (have_posts()) : the_post(); ?>

								<?php projects_get_template_part('content', 'project'); ?>

							<?php endwhile; // end of the loop. ?>

						<?php projects_project_loop_end(); ?>
						</div>
					</div><!-- .projects -->

					<?php
						/**
						 * projects_after_loop hook
						 *
						 * @hooked projects_pagination - 10
						 */
						do_action('projects_after_loop');
					?>

				<?php else : ?>

					<?php projects_get_template('loop/no-projects-found.php'); ?>

				<?php endif; ?>

				<?php
					/**
					 * projects_after_main_content hook
					 *
					 * @hooked projects_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action('projects_after_main_content');
				?>

				<?php
					/**
					 * projects_sidebar hook
					 *
					 * @hooked projects_get_sidebar - 10
					 */
					//do_action('projects_sidebar');
				?>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>