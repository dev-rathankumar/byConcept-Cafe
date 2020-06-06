<?php
/**
 * Single Project title
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

?>
<div class="entry-header">
	<h1 class="project_title entry-title"><?php the_title(); ?></h1>
	<span class="author"><?php echo get_avatar(puca_tbay_get_id_author_post(), 'puca_avatar_post_carousel'); ?> <?php the_author_posts_link(); ?></span>
	<span class="entry-date"><?php echo puca_time_link(); ?></span>
	<span class="entry-view"><i class="icon-eye icons"></i> 
 			<?php echo puca_get_post_views(get_the_ID(), esc_html__(' views','puca')); ?>
	</span>
</div>