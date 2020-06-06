<?php
/**
 * Single Project Meta
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
?>
<div class="project-meta">
	<?php
		// Categories
		$terms_as_text 	= get_the_term_list( $post->ID, 'project-category', '<li>', ' </li><li>, ', '</li>' );

		// Meta
		$client 		= esc_attr( get_post_meta( $post->ID, '_client', true ) );
		$url 			= esc_url( get_post_meta( $post->ID, '_url', true ) );

		do_action( 'projects_before_meta' );

		/**
		 * Display categories if they're set
		 */
		if ( $terms_as_text ) {
			echo '<div class="categories">';
			echo '<span class="title">' . esc_html__( 'Categories: ', 'puca' ) . '</span>';
			echo '<ul class="single-project-categories">';
			echo trim($terms_as_text);
			echo '</ul>';
			echo '</div>';
		}

		/**
		 * Display client if set
		 */
		if ( $client ) {
			echo '<div class="client">';
			echo '<span class="title">' . esc_html__( 'Client: ', 'puca' ) . '</span>';
			echo '<span class="client-name">' . esc_html($client) . '</span>';
			echo '</div>';
		}

		if ( puca_tbay_get_config('enable_code_share',false) && puca_tbay_get_config('show_portfolio_social_share',false) ) {
			?>
				<div class="social-sharing"><?php puca_tbay_post_share_box(); ?></div>
			<?php
		}

		/**
		 * Display link if set
		 */
		if ( $url ) {
			echo '<div class="url">';
			echo '<a class="tbay-button" target="_blank" href="' . esc_url($url) . '"><i class="icon-info icons"></i>' . apply_filters( 'projects_visit_project_link',  esc_html__( 'Watch project', 'puca' ) ) . '</a>';
			echo '</div>';
		}

		do_action( 'projects_after_meta' );
	?>
</div>