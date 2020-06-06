<?php
/**
 * Pizzaro template functions used in content such as post, page, etc.
 *
 * @package pizzaro
 */

if ( ! function_exists( 'pizzaro_toggle_page_header' ) ) {
	/**
	 *
	 */
	function pizzaro_toggle_page_header() {

		$should_show_page_header = true;

		global $post;
		$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );

		if ( isset( $page_meta_values['hide_page_header'] ) && $page_meta_values['hide_page_header'] == '1' ) {
			$should_show_page_header = false;
		}

		if( is_woocommerce_activated() && is_account_page() && !is_user_logged_in() ) {
			$should_show_page_header = false;

			if( is_wc_endpoint_url() ) {
				$should_show_page_header = true;
			}
		}

		if ( is_woocommerce_activated() && is_cart() && WC()->cart->is_empty() ) {
			$should_show_page_header = false;
		}

		return $should_show_page_header;
	}
}

if ( ! function_exists( 'pizzaro_site_content_style' ) ) {
	/**
	 * Print the style attribute to site-content div
	 *
	 * @since 1.0.0
	 */
	function pizzaro_site_content_style() {
		$attributes = pizzaro_get_site_content_style();
		// Separates style attributes with ;
		if( count( $attributes ) > 0 ) {
			echo 'style="' . join( '; ', $attributes ) . '"';
		}
	}
}

if ( ! function_exists( 'pizzaro_get_site_content_style' ) ) {
	/**
	 * Add the style attribute array to site-content div
	 *
	 * @since 1.0.0
	 */
	function pizzaro_get_site_content_style() {
		global $post;

		$atts = array();

		if ( is_page_template( 'template-homepage-v3.php' ) || is_page_template( 'template-homepage-v4.php' ) ) {

			$image_width = apply_filters( 'pizzaro_content_background_image_width', 1170 );

			if( $post && has_post_thumbnail( $post->ID ) ) {
				$image_id = get_post_thumbnail_id( $post->ID );
				$image_attr = wp_get_attachment_image_src( $image_id, 'full' );
				if ( $image_attr[1] >= $image_width ) {
					$atts[] = 'background-size:cover';
					$atts[] = 'background-position:center center';
					$atts[] = 'background-image:url( ' . esc_url( $image_attr[0] ) . ' )';
					$atts[] = 'padding-top:480px';
				}
			}
		}

		$atts = apply_filters( 'pizzaro_site_content_style_atts', $atts );

		return array_unique( $atts );
	}
}

if ( ! function_exists( 'pizzaro_page_header' ) ) {
	/**
	 * Display the post header with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function pizzaro_page_header() {
		global $post;
		$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );

		if ( isset( $page_meta_values['page_title'] ) && ! empty( $page_meta_values['page_title'] ) ) {
			$page_title = $page_meta_values['page_title'];
		} else {
			$page_title = get_the_title();
		}

		if( apply_filters( 'pizzaro_show_page_header', true ) ) {
			?>
			<header class="entry-header">
				<h1 class="entry-title"><?php echo apply_filters( 'pizzaro_page_title', wp_kses_post( $page_title ) ); ?></h1>
				<?php pizzaro_page_subtitle(); ?>
			</header><!-- .entry-header -->
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_page_subtitle' ) ) {
	function pizzaro_page_subtitle() {
		global $post;
		$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );

		if ( isset( $page_meta_values['page_subtitle'] ) && ! empty( $page_meta_values['page_subtitle'] ) ) {
			?>
			<p class="entry-subtitle"><?php echo apply_filters( 'pizzaro_page_subtitle', wp_kses_post( $page_meta_values['page_subtitle'] ), $post ); ?></p>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_page_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function pizzaro_page_content() {
		?>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pizzaro' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'pizzaro_homepage_content' ) ) {
	/**
	 * Display homepage content
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @return  void
	 */
	function pizzaro_homepage_content() {
		while ( have_posts() ) {
			the_post();

			?>
			<div class="entry-content">
				<?php
					the_content();
					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pizzaro' ),
						'after'  => '</div>',
					) );
				?>
			</div>
			<?php

		} // end of the loop.
	}
}

if( ! function_exists( 'pizzaro_page_header_image' ) ) {
	/**
	 * Display the page header image
	 * @since 1.0.0
	 * @return void
	 */
	function pizzaro_page_header_image() {
		global $post;

		$image_url = apply_filters( 'pizzaro_default_page_header_image', '' );

		if( ! is_front_page() ) {

			$image_width = apply_filters( 'pizzaro_page_header_image_width', 1170 );

			if( $post ){
				$image_id = get_post_thumbnail_id( $post->ID );
				$image = wp_get_attachment_image_src( $image_id, array( $image_width, $image_width ) );
				if ( is_page() && has_post_thumbnail( $post->ID ) && $image[1] >= $image_width ) {
					echo '<div class="page-featured-image">' . get_the_post_thumbnail( $post->ID, 'full' ) . '</div>';
				}
			}
		}
	}
}

if ( ! function_exists( 'pizzaro_get_blog_style' ) ) {
	function pizzaro_get_blog_style() {
		return apply_filters( 'pizzaro_blog_style', 'blog-default' );
	}
}

if ( ! function_exists( 'pizzaro_get_blog_layout' ) ) {
	function pizzaro_get_blog_layout() {

		if( is_home() || ( 'post' == get_post_type() && ( is_category() || is_tag() || is_author() || is_date() || is_year() || is_month() || is_time() ) ) ) {
			$layout = apply_filters( 'pizzaro_blog_layout', 'right-sidebar' );
		} elseif ( is_single() && 'post' == get_post_type() ) {
			$layout = apply_filters( 'pizzaro_blog_single_layout', 'right-sidebar' );
		} else {
			$layout = 'full-width';
		}

		return $layout;
	}
}

if ( ! function_exists( 'pizzaro_get_post_thumbnail_size' ) ) {
	/**
	 * Returns post thumbnail size
	 *
	 * @since 1.0.0
	 */
	function pizzaro_get_post_thumbnail_size() {
		$blog_style = pizzaro_get_blog_style();
		$blog_layout = pizzaro_get_blog_layout();

		if ( is_single() ) {
			$image_size = 'full';
		} elseif( $blog_style == 'blog-grid' ) {
			$image_size = 'full';
		} elseif( $blog_style == 'blog-default' && $blog_layout == 'full-width' ) {
			$image_size = 'full';
		} else {
			$image_size = 'full';
		}

		return $image_size;
	}
}

if ( ! function_exists( 'pizzaro_post_header' ) ) {
	/**
	 * Display the post header with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function pizzaro_post_header() {
		?>
		<header class="entry-header">
		<?php
		if ( is_single() ) {
			pizzaro_post_media_attachment();
			the_title( '<h1 class="entry-title">', '</h1>' );
			pizzaro_post_meta();
		} else {
			pizzaro_post_media_attachment();
			the_title( sprintf( '<h1 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' );
			pizzaro_post_meta();
		}
		?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'pizzaro_post_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function pizzaro_post_content() {
		?>
		<div class="entry-content">
		<?php
		the_content(
			sprintf(
				__( 'Continue reading %s', 'pizzaro' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			)
		);

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pizzaro' ),
			'after'  => '</div>',
		) );
		?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'pizzaro_loop_post_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function pizzaro_loop_post_content() {
		?>
		<div class="entry-content">

			<?php the_excerpt(); ?>

			<div class="post-readmore">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="read-more-text"><?php echo esc_html__( 'Read More', 'pizzaro' ); ?></a>
			</div>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><?php comments_popup_link( esc_html__( 'Leave a comment', 'pizzaro' ), esc_html__( '1 Comment', 'pizzaro' ), esc_html__( '% Comments', 'pizzaro' ) ); ?></span>
			<?php endif; ?>

		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_post_meta' ) ) {
	/**
	 * Display the post meta
	 *
	 * @since 1.0.0
	 */
	function pizzaro_post_meta() {
		?>
		<div class="entry-meta">
			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search.

				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'pizzaro' ) );

				if ( $categories_list ) : ?>
					<div class="cat-links">
						<?php
						echo wp_kses_post( $categories_list );
						?>
					</div>
				<?php endif; // End if categories. ?>

				<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'pizzaro' ) );

				if ( $tags_list ) : ?>
					<div class="tags-links">
						<?php
						echo '<div class="label">' . esc_attr( __( 'Tagged', 'pizzaro' ) ) . '</div>';
						echo wp_kses_post( $tags_list );
						?>
					</div>
				<?php endif; // End if $tags_list. ?>

				<?php pizzaro_posted_on(); ?>

				<div class="author">
					<?php
						// echo get_avatar( get_the_author_meta( 'ID' ), 128 );
						echo '<div class="label">' . esc_attr( __( 'Posted by:', 'pizzaro' ) ) . '</div>';
						the_author_posts_link();
					?>
				</div>

			<?php else : ?>

				<?php pizzaro_posted_on(); ?>

			<?php endif; // End if 'post' == get_post_type(). ?>

		</div>
		<?php
	}
}

if( ! function_exists( 'pizzaro_author_info' ) ) {
	/**
	 * Display Author Info
	 */
	function pizzaro_author_info() {
		if( apply_filters( 'pizzaro_show_author_info', true ) && get_the_author_meta( 'description' ) !== '' ) :
			?>
			<div class="post-author-info">
				<div class="media">
					<div class="media-left media-middle">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php echo get_avatar( get_the_author_meta( 'ID' ) , 160 ); ?>
						</a>
					</div>
					<div class="media-body">
						<h4 class="media-heading"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author(); ?></a></h4>
						<p><?php echo get_the_author_meta( 'description' );?></p>
					</div>
				</div>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'pizzaro_paging_nav' ) ) {
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 */
	function pizzaro_paging_nav() {
		global $wp_query;

		$args = array(
			'type' 	    => 'list',
			'next_text' => _x( 'Next Page &nbsp;&nbsp;&nbsp;&rarr;', 'Next page', 'pizzaro' ),
			'prev_text' => _x( '&larr;&nbsp;&nbsp;&nbsp; Previous Page', 'Previous page', 'pizzaro' ),
		);

		the_posts_pagination( $args );
	}
}

if ( ! function_exists( 'pizzaro_post_nav' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 */
	function pizzaro_post_nav() {
		$args = array(
			'next_text' => '%title',
			'prev_text' => '%title',
		);
		the_post_navigation( $args );
	}
}

if ( ! function_exists( 'pizzaro_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function pizzaro_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time> <time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			_x( '%s', 'post date', 'pizzaro' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo wp_kses( apply_filters( 'pizzaro_single_post_posted_on_html', '<span class="posted-on">' . $posted_on . '</span>', $posted_on ), array(
			'span' => array(
				'class'  => array(),
			),
			'a'    => array(
				'href'  => array(),
				'title' => array(),
				'rel'   => array(),
			),
			'time' => array(
				'datetime' => array(),
				'class'    => array(),
			),
		) );
	}
}

if ( ! function_exists( 'pizzaro_post_thumbnail' ) ) {
	/**
	 * Display post thumbnail
	 *
	 * @var $size thumbnail size. thumbnail|medium|large|full|$custom
	 * @uses has_post_thumbnail()
	 * @uses get_the_post_thumbnail
	 * @param string $size the post thumbnail size.
	 * @param boolean $should_link
	 * @param boolean $should_placehold
	 * @since 1.0.0
	 */
	function pizzaro_post_thumbnail( $size = 'thumbnail', $should_link = false, $should_placehold = false ) {

		global $post;

		$post_thumbnail = '';

		if ( has_post_thumbnail() ) {

			$post_thumbnail = apply_filters( 'pizzaro_post_thumbnail', get_the_post_thumbnail( $post->ID, $size, array( 'itemprop' => 'image' ) ) );

		} else {

			if ( $should_placehold ) {
				if ( 'sticky-thumb' === $size ) {
					$default_post_thumbnail = '<img src="//placehold.it/1030x550" />';
				} else {
					$default_post_thumbnail = '<img src="//placehold.it/370x220" />';
				}

				$post_thumbnail = apply_filters( 'pizzaro_default_post_thumbnail', $default_post_thumbnail );
			}

		}

		if ( ! empty( $post_thumbnail ) ) {

			if ( $should_link ) {
				$post_thumbnail = sprintf( '<a href="%s">%s</a>', esc_url( get_permalink() ), $post_thumbnail );
			}

			echo wp_kses_post( sprintf( '<div class="post-thumbnail">%s</div>', $post_thumbnail ) );
		}
	}
}

if ( ! function_exists( 'pizzaro_sticky_post' ) ) {
	/**
	 * Displays Sticky Post
	 */
	function pizzaro_sticky_post() {

		if ( pizzaro_get_blog_layout() == 'full-width' && is_home() && ! is_paged() ) {

			$sticky_args = array(
				'posts_per_page'      => 1,
				'post__in'            => get_option( 'sticky_posts' ),
				'ignore_sticky_posts' => 1
			);

			$sticky_query = new WP_Query( $sticky_args );

			while ( $sticky_query->have_posts() ) {
				$sticky_query->the_post();
				get_template_part( 'content', get_post_format() );
			}

			wp_reset_postdata();
		}
	}
}

if ( ! function_exists( 'pizzaro_blog_menu' ) ) {
	/**
	 * Displays category menu at the top of Blog
	 */
	function pizzaro_blog_menu() {
		$categories = get_categories( apply_filters( 'pizzaro_blog_menu_categories_args', array( 'number' => 12, 'hide_empty' => true, 'parent' => 0 ) ) );
		if( pizzaro_get_blog_layout() == 'full-width' && ! empty( $categories ) ) {
			?>
			<div class="blog-menu">
				<div class="container">
					<?php foreach( $categories as $category ) : ?>
					<span class="menu-item"><a href="<?php echo esc_url( get_term_link( $category ) ); ?>" <?php if ( is_category( $category->term_id ) && in_category( $category->term_id ) ) : ?>class="active"<?php endif; ?>><?php echo esc_html( $category->name ); ?></a></span>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_wrap_posts_loop' ) ) {
	/**
	 * Wrap Posts Loop
	 */
	function pizzaro_wrap_posts_loop() {
		?><div class="posts"><?php
	}
}

if ( ! function_exists( 'pizzaro_wrap_posts_loop_end' ) ) {
	/**
	 * Wraps close Posts Loop
	 */
	function pizzaro_wrap_posts_loop_end() {
		?></div><!-- /.posts --><?php
	}
}

if( ! function_exists( 'pizzaro_custom_excerpt' ) ) {
	/**
	 * Custom Length Excerpts
	 */
	function pizzaro_custom_excerpt( $excerpt = '', $excerpt_length = 20, $tags = '', $trailing = '' ) {

		$string_check = explode(' ', $excerpt);
		if (count($string_check, COUNT_RECURSIVE) > $excerpt_length) {
			$excerpt = strip_shortcodes( $excerpt );
			$new_excerpt_words = explode(' ', $excerpt, $excerpt_length+1);
			array_pop($new_excerpt_words);
			$excerpt_text = implode(' ', $new_excerpt_words);
			$temp_content = strip_tags($excerpt_text, $tags);
			$short_content = preg_replace('`\[[^\]]*\]`','',$temp_content);
			$short_content .= $trailing;

			return $short_content;
		} else {
			// no trimming needed, excerpt is too short.
			return $excerpt;
		}
	}
}

if ( ! function_exists( 'pizzaro_display_comments' ) ) {
	/**
	 * Pizzaro display comments
	 *
	 * @since  1.0.0
	 */
	function pizzaro_display_comments() {
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || '0' != get_comments_number() ) :
			comments_template();
		endif;
	}
}

if ( ! function_exists( 'pizzaro_comment' ) ) {
	/**
	 * Pizzaro comment template
	 *
	 * @param array $comment the comment array.
	 * @param array $args the comment args.
	 * @param int   $depth the comment depth.
	 * @since 1.0.0
	 */
	function pizzaro_comment( $comment, $args, $depth ) {
		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
		?>
		<<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
		<div class="comment-body">
		<div class="comment-meta commentmetadata">
			<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 128 ); ?>
			<?php printf( wp_kses_post( '<cite class="fn">%s</cite>', 'pizzaro' ), get_comment_author_link() ); ?>
			</div>
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'pizzaro' ); ?></em>
				<br />
			<?php endif; ?>

			<a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>" class="comment-date">
				<?php echo '<time datetime="' . get_comment_date( 'c' ) . '">' . get_comment_date() . '</time>'; ?>
			</a>
		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-content">
		<?php endif; ?>
		<div class="comment-text">
		<?php comment_text(); ?>
		</div>
		<div class="reply">
		<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		<?php edit_comment_link( esc_html__( 'Edit', 'pizzaro' ), '  ', '' ); ?>
		</div>
		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
		</div>
		<?php endif; ?>
	<?php
	}
}

if( ! function_exists( 'pizzaro_post_media_attachment' ) ) {
	/**
	 * Displays the media attachment of the post
	 * @since 1.0.0
	 */
	function pizzaro_post_media_attachment() {

		$id = get_the_ID();
		$post_format = get_post_format();
		$should_media = is_single() ? true : apply_filters( 'pizzaro_loop_post_display_media', true );
		$image_size = pizzaro_get_post_thumbnail_size();
		$should_link = is_single() ? false : true ;
		$should_placehold = is_single() ? false : apply_filters( 'pizzaro_loop_post_placeholder_img', false );

		ob_start();

		if( $should_media && $post_format == 'gallery' ){
			pizzaro_gallery_slideshow( $id );
		} else if ( $should_media && $post_format == 'video' ){
			pizzaro_video_player( $id );
		} else if ( $should_media && $post_format == 'audio' ){
			pizzaro_audio_player( $id );
		} else {
			pizzaro_post_thumbnail( $image_size, $should_link, $should_placehold );
		}

		$media_attachment = ob_get_clean();

		if( ! empty( $media_attachment ) ) {
			echo '<div class="media-attachment">' . $media_attachment . '</div>';
		}

	}
}

if ( !function_exists( 'pizzaro_gallery_slideshow' ) ) {
	/**
	 * Output Gallery (slide show) for Post Format.
	 */
	function pizzaro_gallery_slideshow($post_id , $thumbnail = 'post-thumbnail') {
		global $post, $pizzaro_version;

		$post_id = esc_attr( ( $post_id ? $post_id : $post->ID ) );

		// Get the media ID's
		$ids = esc_attr( get_post_meta($post_id, 'postformat_gallery_ids', true) );

		// Query the media data
		$attachments = get_posts( array(
			'post__in' 			=> explode(",", $ids),
			'orderby' 			=> 'post__in',
			'post_type' 		=> 'attachment',
			'post_mime_type' 	=> 'image',
			'post_status' 		=> 'any',
			'numberposts' 		=> -1
		));

		// Create the media display
		if ($attachments) :
			wp_enqueue_script( 'owl-carousel-js', 	get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array( 'jquery' ), $pizzaro_version, true );
		?>
		<div class="media-attachment-gallery">
			<div id="owl-carousel-<?php echo esc_attr( $post_id ); ?>" class="owl-carousel owl-inner-pagination owl-inner-nav owl-blog-post-gallery">
			<?php foreach ($attachments as $attachment): ?>
				<div class="item">
					<figure>
						<?php echo wp_get_attachment_image($attachment->ID, $thumbnail); ?>
					</figure>
				</div><!-- /.item -->
			<?php endforeach; ?>
			</div>

		</div><!-- /.media-attachment-gallery -->
		<script type="text/javascript">

			jQuery(document).ready(function(){
				if(jQuery().owlCarousel) {
					jQuery("#owl-carousel-<?php echo esc_attr( $post_id ); ?>").owlCarousel({
						items : 1,
						nav : false,
						slideSpeed : 300,
						dots: true,
						paginationSpeed : 400,
						navText: ["", ""],
						autoHeight: true,
						responsive:{
							0:{
								items:1
							},
							600:{
								items:1
							},
							1000:{
								items:1
							}
						}
					});

					jQuery(".slider-next").on( 'click', function () {
						var owl = jQuery(jQuery(this).data('target'));
						owl.trigger('next.owl.carousel');
						return false;
					});

					jQuery(".slider-prev").on( 'click', function () {
						var owl = jQuery(jQuery(this).data('target'));
						owl.trigger('prev.owl.carousel');
						return false;
					});
				}
			});

		</script>
		<?php endif;
	}
}

if ( !function_exists( 'pizzaro_audio_player' ) ) {
	/**
	 *  Output Audio Player for Post Format
	 */
    function pizzaro_audio_player($post_id, $width = 1200) {
    	global $post;

    	$post_id = esc_attr( ( $post_id ? $post_id : $post->ID ) );

    	// Get the player media
		$mp3    = get_post_meta($post_id, 'postformat_audio_mp3', 		TRUE);
		$ogg    = get_post_meta($post_id, 'postformat_audio_ogg', 		TRUE);
		$embed  = get_post_meta($post_id, 'postformat_audio_embedded', 	TRUE);
		$height = get_post_meta($post_id, 'postformat_poster_height', 	TRUE);

		if ( isset($embed) && $embed != '' ) {
			// Embed Audio
			if( !empty($embed) ) {
				// run oEmbed for known sources to generate embed code from audio links
				echo $GLOBALS['wp_embed']->autoembed( stripslashes( htmlspecialchars_decode( $embed ) ) );

				return; // and.... Done!
			}

		} else if( ! empty( $mp3 ) || ! empty ( $ogg ) ) {

			wp_enqueue_script( 'jplayer', get_template_directory_uri() . '/assets/js/jquery.jplayer.min.js', array( 'jquery' ), '1.10.2', true );

		    // Other audio formats ?>

			<script type="text/javascript">

				jQuery(document).ready(function(){

					if(jQuery().jPlayer) {
						jQuery("#jquery_jplayer_<?php echo esc_attr( $post_id ); ?>").jPlayer({
							ready: function (event) {

								// set media
								jQuery(this).jPlayer("setMedia", {
								    <?php
								    if($mp3 != '') :
										echo 'mp3: "'. $mp3 .'",';
									endif;
									if($ogg != '') :
										echo 'oga: "'. $ogg .'",';
									endif; ?>
									end: ""
								});
							},
							<?php if( !empty($poster) ) { ?>
							size: {
	        				    width: "<?php echo esc_js( $width ); ?>px",
	        				    height: "<?php echo esc_js( $height . 'px' ); ?>"
	        				},
	        				<?php } ?>
							swfPath: "<?php echo get_template_directory_uri(); ?>/assets/js",
							cssSelectorAncestor: "#jp_interface_<?php echo esc_attr( $post_id ); ?>",
							supplied: "<?php if($ogg != '') : ?>oga,<?php endif; ?><?php if($mp3 != '') : ?>mp3, <?php endif; ?> all"
						});

					}
				});
			</script>

			<div id="jquery_jplayer_<?php echo esc_attr( $post_id ); ?>" class="jp-jplayer jp-jplayer-audio"></div>

			<div class="jp-audio-container">
				<div class="jp-audio">
					<div class="jp-type-single">
						<div id="jp_interface_<?php echo esc_attr( $post_id ); ?>" class="jp-interface">
							<ul class="jp-controls">
								<li><div class="seperator-first"></div></li>
								<li><div class="seperator-second"></div></li>
								<li><a href="#" class="jp-play" tabindex="1"><i class="fa fa-play"></i><span>play</span></a></li>
								<li><a href="#" class="jp-pause" tabindex="1"><i class="fa fa-pause"></i><span>pause</span></a></li>
								<li><a href="#" class="jp-mute" tabindex="1"><i class="fa fa-volume-up"></i><span>mute</span></a></li>
								<li><a href="#" class="jp-unmute" tabindex="1"><i class="fa fa-volume-off"></i><span>unmute</span></a></li>
							</ul>
							<div class="jp-progress-container">
								<div class="jp-progress">
									<div class="jp-seek-bar">
										<div class="jp-play-bar"></div>
									</div>
								</div>
							</div>
							<div class="jp-volume-bar-container">
								<div class="jp-volume-bar">
									<div class="jp-volume-bar-value"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		} // End if embedded/else
    }
}

if ( !function_exists( 'pizzaro_video_player' ) ) {
	/**
	 * Video Player / Embeds (self-hosted, jPlayer)
	 */
    function pizzaro_video_player($post_id, $width = 1200) {
    	global $post;

    	$post_id = esc_attr( ( $post_id ? $post_id : $post->ID ) );

    	// Get the player media options
    	$embed 		= get_post_meta($post_id, 'postformat_video_embed', 	true);
    	$height 	= get_post_meta($post_id, 'postformat_video_height', 	true);
    	$m4v 		= get_post_meta($post_id, 'postformat_video_m4v', 		true);
    	$ogv 		= get_post_meta($post_id, 'postformat_video_ogv', 		true);
    	$webm 		= get_post_meta($post_id, 'postformat_video_webm', 		true);
    	$poster 	= get_post_meta($post_id, 'postformat_video_poster', 	true);

		if( !empty($embed) ) {
			$embed = do_shortcode( $embed );
			// run oEmbed for known sources to generate embed code from video links
			echo '<div class="video-container"><div class="embed-responsive embed-responsive-16by9">'. $GLOBALS['wp_embed']->autoembed( stripslashes(htmlspecialchars_decode($embed)) ) .'</div></div>';

			return; // and.... Done!
		} else if( ! empty( $m4v ) || ! empty ( $ogv ) || ! empty ( $webm ) || ! empty ( $poster ) ) {
			wp_enqueue_script( 'jplayer', get_template_directory_uri() . '/assets/js/jquery.jplayer.min.js', array( 'jquery' ), '1.10.2', true );

			?>
		    <script type="text/javascript">
		    	jQuery(document).ready(function(){

		    		if(jQuery().jPlayer) {
		    			jQuery("#jquery_jplayer_<?php echo esc_attr( $post_id ); ?>").jPlayer({
		    				ready: function (event) {
								// mobile display helper
								// if(event.jPlayer.status.noVolume) {	$('#jp_interface_<?php echo esc_attr( $post_id ); ?>').addClass('no-volume'); }
								// set media
		    					jQuery(this).jPlayer("setMedia", {
		    						<?php if($m4v != '') : ?>
		    						m4v: "<?php echo esc_js( $m4v ); ?>",
		    						<?php endif; ?>
		    						<?php if($ogv != '') : ?>
		    						ogv: "<?php echo esc_js( $ogv ); ?>",
		    						<?php endif; ?>
		    						<?php if($webm != '') : ?>
		    						webmv: "<?php echo esc_js( $webm ); ?>",
		    						<?php endif; ?>
		    						<?php if ($poster != '') : ?>
		    						poster: "<?php echo esc_js( $poster ); ?>"
		    						<?php endif; ?>
		    					});
		    				},
		    				size: {
		    				    width: "<?php echo esc_js( $width ); ?>px",
		    				},
		    				swfPath: "<?php echo get_template_directory_uri(); ?>/assets/js",
		    				cssSelectorAncestor: "#jp_interface_<?php echo esc_attr( $post_id ); ?>",
		    				supplied: "<?php if($m4v != '') : ?>m4v, <?php endif; ?><?php if($ogv != '') : ?>ogv, <?php endif; ?> all"
		    			});
		    		}
		    	});
		    </script>

		    <div id="jquery_jplayer_<?php echo esc_attr( $post_id ); ?>" class="jp-jplayer jp-jplayer-video"></div>

		    <div class="jp-video-container">
		        <div class="jp-video">
		            <div class="jp-type-single">
		                <div id="jp_interface_<?php echo esc_attr( $post_id ); ?>" class="jp-interface">
		                    <ul class="jp-controls">
		                    	<li><div class="seperator-first"></div></li>
		                        <li><div class="seperator-second"></div></li>
		                        <li><a href="#" class="jp-play" tabindex="1"><i class="fa fa-play"></i><span>play</span></a></li>
		                        <li><a href="#" class="jp-pause" tabindex="1"><i class="fa fa-pause"></i><span>pause</span></a></li>
		                        <li><a href="#" class="jp-mute" tabindex="1"><i class="fa fa-volume-up"></i><span>mute</span></a></li>
		                        <li><a href="#" class="jp-unmute" tabindex="1"><i class="fa fa-volume-off"></i><span>unmute</span></a></li>
		                    </ul>
		                    <div class="jp-progress-container">
		                        <div class="jp-progress">
		                            <div class="jp-seek-bar">
		                                <div class="jp-play-bar"></div>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="jp-volume-bar-container">
		                        <div class="jp-volume-bar">
		                            <div class="jp-volume-bar-value"></div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		    <?php
		}
	}
}
