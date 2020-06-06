<?php
/**
 * The template for displaying project content in the single-project.php template
 *
 * Override this template by copying it to yourtheme/projects/content-single-project.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

global $wpdb,$post;

wp_enqueue_script( 'hc-sticky' );

$prcates = get_the_terms($post->ID, 'project-category');
$datagroup = array();
foreach ($prcates as $category) {
	$datagroup[] = '"'.$category->slug.'"';
}
$datagroup = implode(", ", $datagroup);

?>
<?php puca_tbay_render_breadcrumbs(); ?>
<div class="main-container page-portfolio full-width stick">
	<div class="full-wrapper portfolio-content">
		<div class="container">
			<?php
				/**
				 * projects_before_single_project hook
				 *
				 */
				 do_action('projects_before_single_project');
			?>

			<div id="project-<?php the_ID(); ?>" <?php post_class(); ?>>
				
				<div class="row wrap-project-sticky">
					<?php $attachment_ids = projects_get_gallery_attachment_ids(); ?>
					<?php if ($attachment_ids) { ?>
					<div class="col-xs-12 col-sm-12 col-md-6 wrap-left-single-project">
						<?php
							/**
							 * projects_before_single_project_summary hook
							 * @hooked projects_template_single_title - 10
							 * @hooked projects_template_single_short_description - 20
							 * @hooked projects_template_single_feature - 30
							 * @hooked projects_template_single_gallery - 40
							 */
							 do_action('projects_single_project_gallery');
						?>
					</div>
					<?php } ?>
					
					<?php 
						$class_wrap_right = ($attachment_ids) ? '6' : '12';
					?>
					<div class="wrap-right-single-project col-xs-12 col-sm-12 col-md-<?php echo esc_attr( $class_wrap_right ); ?>">
						<div class="summary entry-summary">
							<?php
								/**
								 * projects_single_project_summary hook
								 *
								 * @hooked projects_template_single_description - 10
								 * @hooked projects_template_single_meta - 20
								 */
								do_action('projects_single_project_summary');
							?>
						</div><!-- .summary -->
						
					</div>
				</div>
				<?php
					/**
					 * projects_after_single_project_summary hook
					 *
					 */
					do_action('projects_after_single_project_summary');
				?>
			</div><!-- #project-<?php the_ID(); ?> -->

			<?php
				/**
				 * projects_after_single_project hook
				 *
				 * @hooked projects_single_pagination - 10
				 */
				//do_action('projects_after_single_project');
			?>
		</div>
	</div>
</div>