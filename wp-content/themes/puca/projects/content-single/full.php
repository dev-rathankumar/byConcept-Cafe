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

$prcates = get_the_terms($post->ID, 'project-category');
$datagroup = array();
foreach ($prcates as $category) {
	$datagroup[] = '"'.$category->slug.'"';
}
$datagroup = implode(", ", $datagroup);

?>
<?php puca_tbay_render_breadcrumbs(); ?>
<div class="main-container page-portfolio full-width text-center">
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
				
				<div class="summary entry-summary">
							<?php
								/**
								 * projects_single_project_summary hook
								 *
								 * @hooked projects_template_single_description - 10
								 * @hooked projects_template_single_meta - 20
								 */
								add_action('projects_single_project_summary', 'projects_template_single_title', 5);			
								remove_action('projects_single_project_summary', 'projects_template_single_meta', 20);
								do_action('projects_single_project_summary');

								remove_action('projects_single_project_summary', 'projects_template_single_title', 5);			
								add_action('projects_single_project_summary', 'projects_template_single_meta', 20);

							?>
				</div><!-- .summary -->

				<div class="single-portfolio-img">
					<?php $attachment_ids = projects_get_gallery_attachment_ids(); ?>
					<?php if ($attachment_ids) { ?>

							<?php
								/**
								 * projects_single_gallery hook
								 * @hooked projects_single_gallery - 5
								 */
								 do_action('projects_single_gallery');
							?>

						<?php } ?>
				</div>

				<div class="meta-wrapper">
						<?php
							/**
							 * projects_single_project_summary hook
								 * @hooked projects_template_single_description - 10
								 * @hooked projects_template_single_meta - 20
							 */
							remove_action('projects_single_project_summary', 'projects_template_single_description', 10);
							do_action('projects_single_project_summary');
							add_action('projects_single_project_summary', 'projects_template_single_description', 10);			
						?>
				</div>

			</div><!-- #project-<?php the_ID(); ?> -->

		</div>
	</div>
</div>