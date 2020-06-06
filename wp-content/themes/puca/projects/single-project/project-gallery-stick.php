<?php
/**
 * Single Project Image
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

global $post,$project,$projects;

?>
<div id="owl-slider-one-img" class="stick-project">

	<?php

		$attachment_ids = projects_get_gallery_attachment_ids();

		if ($attachment_ids) { ?>

			<?php

				$loop = 0;
				$columns = apply_filters('projects_project_gallery_columns', 3);

				foreach ($attachment_ids as $attachment_id) {

					$classes = array('zoom');

					if ($loop == 0 || $loop % $columns == 0)
						$classes[] = 'first';

					if (($loop + 1) % $columns == 0)
						$classes[] = 'last';

					$image_link = wp_get_attachment_url($attachment_id);

					if (! $image_link)
						continue;

					$image       = 	puca_tbay_get_attachment_image_loaded($attachment_id, apply_filters('single_project_single_thumbnail_size', 'project-single'), '', false);
					$image_class = esc_attr(implode(' ', $classes));
					$image_title = esc_attr(get_the_title($attachment_id));

					if (apply_filters('projects_gallery_link_images', true)) {
						echo '<div class="item"><a class="lightbox-gallery tbay-image-loaded" href="' . esc_url($image_link) . '" title="' . esc_attr($image_title) . '">' . trim($image) . '</a></div>';
					} else {
						echo trim($image);
					}

					$loop++;

				} // endforeach ?>

		<?php } // endif ?>

</div>
