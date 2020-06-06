<?php
/**
 * The template for displaying project content within loops.
 *
 * Override this template by copying it to yourtheme/projects/content-project.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

global $projects_loop,$post,$puca_projectrows;

// Store loop count we're currently on
if (empty($projects_loop['loop']))
	$projects_loop['loop'] = 0;
// Store column count for displaying the grid
if (empty($projects_loop['columns'])) {
	$projects_loop['columns'] = apply_filters('projects_loop_columns', 4);
}

$portfolio_per_page 	= 	puca_tbay_get_config('portfolio_per_page',15);

if (isset($_GET['columns'])) {
	$projects_loop['columns'] = (int)$_GET['columns'];
}

// Increase loop count
$projects_loop['loop']++;
// Extra post classes
$classes = array();
if (0 == ($projects_loop['loop'] - 1) % $projects_loop['columns'] && $projects_loop['loop'] > 1)
	$classes[] = 'first';
if (0 == $projects_loop['loop'] % $projects_loop['columns'])
	$classes[] = 'last';


if($projects_loop['columns'] == 5) {
	$colwidth = '2-4';
} else {
	$colwidth = 12/$projects_loop['columns'];
}

$classes[] = 'item-col col-xs-12 col-sm-'.$colwidth;

$prcates = get_the_terms($post->ID, 'project-category');
$datagroup = array();
foreach ($prcates as $category) {
	$datagroup[] = '"'.$category->slug.'"';
}
$datagroup = implode(", ", $datagroup);

$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
?>
<?php
if ((0 == ($projects_loop['loop'] - 1) % 2) && ($projects_loop['columns'] == 2)) {
	if($puca_projectrows !=1 ) {
		echo '<div class="group">';
	}
}

$puca_projectsfound = 2;

?>
<div <?php post_class($classes); ?> data-groups='[<?php echo esc_attr($datagroup); ?>]'>
	<div class="content-project">

	<?php do_action('projects_before_loop_item'); ?>
	<?php
		/**
		 * projects_loop_item hook
		 *
		 * @hooked projects_template_loop_project_thumbnail - 10
		 * @hooked projects_template_loop_project_title - 20
		 */
		do_action('projects_loop_item');
	?>
	<div class="work-overlay">
		<div class="project-icons">
			<div class="project-icon"><a class="lightbox-gallery" href="<?php echo esc_url($large_image_url[0]); ?>" title="<?php esc_html_e('Zoom Image', 'puca'); ?>"><i class="fa fa-search"></i></a></div>
			<div class="project-icon"><a class="link-project" href="<?php the_permalink(); ?>" title="<?php esc_html_e('Portfolio Single', 'puca'); ?>"><i class="fa fa-link"></i></a></div>
		</div>
	</div>

	<?php
		/**
		 * projects_after_loop_item hook
		 *
		 * @hooked projects_template_short_description - 10
		 */
		//do_action('projects_after_loop_item');
	?>
	</div>
</div>
<?php if ((0 == $projects_loop['loop'] % 2 || $puca_projectsfound == $projects_loop['loop']) && ($projects_loop['columns'] == 2)) { 
	if($puca_projectrows!=1) {
		echo '</div>';
	}
} ?>