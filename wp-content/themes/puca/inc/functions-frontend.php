<?php

if ( ! function_exists( 'puca_tbay_category' ) ) {
	function puca_tbay_category( $post ) {
		// format
		$post_format = get_post_format();
		$header_class = $post_format ? '' : 'border-left';
		echo '<span class="category "> ';
		$cat = wp_get_post_categories( $post->ID );
		$k   = count( $cat );
		foreach ( $cat as $c ) {
			$categories = get_category( $c );
			$k -= 1;
			if ( $k == 0 ) {
				echo '<a href="' . esc_url( get_category_link( $categories->term_id ) ) . '" class="categories-name"><i class="fa fa-bar-chart"></i>' . esc_html($categories->name) . '</a>';
			} else {
				echo '<a href="' . esc_url( get_category_link( $categories->term_id ) ) . '" class="categories-name"><i class="fa fa-bar-chart"></i>' . esc_html($categories->name) . ', </a>';
			}
		}
		echo '</span>';
	}
}

if ( ! function_exists( 'puca_tbay_center_meta' ) ) {
	function puca_tbay_center_meta( $post ) { 
		// format
		$post_format = get_post_format();
		$id = get_the_author_meta( 'ID' );
		echo '<div class="entry-meta">';
			the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
		
			echo "<div class='entry-create'>";
			echo "<span class='entry-date'>". get_the_date( 'M d, Y' ).'</span>';
			"<span class='author'>". esc_html_e('/ By ', 'puca'); the_author_posts_link() .'</span>';
			echo '</div>';
		echo '</div>';
	}
}



if ( ! function_exists( 'puca_tbay_full_top_meta' ) ) {
	function puca_tbay_full_top_meta( $post ) {
		// format
		$post_format = get_post_format();
		$header_class = $post_format ? '' : 'border-left';
		echo '<header class="entry-header-top ' . esc_attr($header_class) . '">';
		if(!is_single()){
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		// details
		$id = get_the_author_meta( 'ID' );
		echo '<span class="entry-profile"><span class="col"><span class="entry-author-link"><strong>' . esc_html__( 'By:', 'puca' ) . '</strong><span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url( $id )) . '" rel="author">' . get_the_author() . '</a></span></span><span class="entry-date"><strong>'. esc_html__('Posted: ', 'puca') .'</strong>' . esc_html( get_the_date( 'M jS, Y' ) ) . '</span></span></span>';
		// comments
		echo '<span class="entry-categories"><strong>'. esc_html__('In:', 'puca') .'</strong> ';
		$cat = wp_get_post_categories( $post->ID );
		$k   = count( $cat );
		foreach ( $cat as $c ) {
			$categories = get_category( $c );
			$k -= 1;
			if ( $k == 0 ) {
				echo '<a href="' . esc_url( get_category_link( $categories->term_id ) ) . '" class="categories-name">' . esc_html($categories->name) . '</a>';
			} else {
				echo '<a href="' . esc_url( get_category_link( $categories->term_id ) ) . '" class="categories-name">' . esc_html($categories->name) . ', </a>';
			}
		}
		echo '</span>';
		if ( ! is_search() ) {
			if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
				echo '<span class="entry-comments-link">';
				comments_popup_link( '0', '1', '%' );
				echo '</span>';
			}
		}
		echo '</header>';
	}
}

if ( ! function_exists( 'puca_tbay_post_tags' ) ) {
	function puca_tbay_post_tags() {
		$posttags = get_the_tags();
		if ( $posttags ) {
			echo '<div class="entry-tags-list"><span class="meta-title">'.esc_html__('Tags: ', 'puca').'</span>';
			
			$size = count( $posttags );
			foreach ( $posttags as $tag ) {
				echo '<a href="' . get_tag_link( $tag->term_id ) . '">';
				echo esc_attr($tag->name);
				echo '</a>';
			}
			echo '</div>';
		}

	}
}

if ( ! function_exists( 'puca_tbay_post_share_box' ) ) {
  function puca_tbay_post_share_box() {
      if ( puca_tbay_get_config('enable_code_share',false) && puca_tbay_get_config('show_blog_social_share') ) {
          ?>
            <div class="tbay-post-share">
            	<span class="meta-title"><?php esc_html_e('Share: ', 'puca'); ?></span>
              	<div class="addthis_inline_share_toolbox"></div>
            </div>
          <?php
      }
  }
}

if ( ! function_exists( 'puca_tbay_post_format_link_helper' ) ) {
	function puca_tbay_post_format_link_helper( $content = null, $title = null, $post = null ) {
		if ( ! $content ) {
			$post = get_post( $post );
			$title = $post->post_title;
			$content = $post->post_content;
		}
		$link = puca_tbay_get_first_url_from_string( $content );
		if ( ! empty( $link ) ) {
			$title = '<a href="' . esc_url( $link ) . '" rel="bookmark">' . $title . '</a>';
			$content = str_replace( $link, '', $content );
		} else {
			$pattern = '/^\<a[^>](.*?)>(.*?)<\/a>/i';
			preg_match( $pattern, $content, $link );
			if ( ! empty( $link[0] ) && ! empty( $link[2] ) ) {
				$title = $link[0];
				$content = str_replace( $link[0], '', $content );
			} elseif ( ! empty( $link[0] ) && ! empty( $link[1] ) ) {
				$atts = shortcode_parse_atts( $link[1] );
				$target = ( ! empty( $atts['target'] ) ) ? $atts['target'] : '_self';
				$title = ( ! empty( $atts['title'] ) ) ? $atts['title'] : $title;
				$title = '<a href="' . esc_url( $atts['href'] ) . '" rel="bookmark" target="' . $target . '">' . $title . '</a>';
				$content = str_replace( $link[0], '', $content );
			} else {
				$title = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $title . '</a>';
			}
		}
		$out['title'] = '<h2 class="entry-title">' . $title . '</h2>';
		$out['content'] = $content;

		return $out;
	}
}


if ( ! function_exists( 'puca_tbay_breadcrumbs' ) ) {
	function puca_tbay_breadcrumbs() {

		$delimiter = ' / ';
		$home = esc_html__('Home', 'puca');
		$before = '<li class="active">';
		$after = '</li>';
		$title = '';
		if (!is_home() && !is_front_page() || is_paged()) {

			echo '<ol class="breadcrumb">';

			global $post;
			$homeLink = esc_url( home_url() );
			echo '<li><a href="' . esc_url($homeLink) . '" class="active">' . esc_html($home) . '</a> ' . esc_html($delimiter) . '</li> ';

			if (is_category()) {
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$thisCat = $cat_obj->term_id;
				$thisCat = get_category($thisCat);
				$parentCat = get_category($thisCat->parent);
				if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
				echo trim($before) . single_cat_title('', false) . $after;
				$title = single_cat_title('', false);
			} elseif (is_day()) {
				echo '<li><a href="' . esc_url( get_year_link(get_the_time('Y')) ) . '">' . get_the_time('Y') . '</a></li> ' . esc_html($delimiter) . ' ';
				echo '<li><a href="' . esc_url( get_month_link(get_the_time('Y'),get_the_time('m')) ) . '">' . get_the_time('F') . '</a></li> ' . esc_html($delimiter) . ' ';
				echo trim($before) . get_the_time('d') . $after;
				$title = get_the_time('d');
			} elseif (is_month()) {
				echo '<li><a href="' . esc_url( get_year_link(get_the_time('Y')) ) . '">' . get_the_time('Y') . '</a></li> ' . esc_html($delimiter) . ' ';
				echo trim($before) . get_the_time('F') . $after;
				$title = get_the_time('F');
			} elseif (is_year()) {
				echo trim($before) . get_the_time('Y') . $after;
				$title = get_the_time('Y');
			} elseif ( is_single()  && !is_attachment()) {
				if ( get_post_type() != 'post' ) {
					$delimiter = '';
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					echo '<li><a href="' . esc_url($homeLink) . '/' . $slug['slug'] . '/">' . esc_html($post_type->labels->singular_name) . '</a></li> ' . esc_html($delimiter) . ' ';
				} else {
					$delimiter = '';
					$cat = get_the_category(); $cat = $cat[0];
					echo '<li>'.get_category_parents($cat, TRUE, ' ' . $delimiter . ' ').'</li>';
				}
				$title = get_the_title();
			} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
				$post_type = get_post_type_object(get_post_type());
				if (is_object($post_type)) {
					echo trim($before) . esc_html($post_type->labels->singular_name) . $after;
					$title = $post_type->labels->singular_name;
				}
			} elseif (is_attachment()) {
			    $parent = get_post($post->post_parent);
			    $cat = get_the_category($parent->ID); 
			    if( isset($cat) && !empty($cat) ) {
			     $cat = $cat[0];
			     echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			    }
			    echo '<a href="' . esc_url( get_permalink($parent->ID) ) . '">' . esc_html($parent->post_title) . '</a></li> ' . esc_html($delimiter) . ' ';
			    echo trim($before) . get_the_title() . $after;
			    $title = get_the_title();
			} elseif ( is_page() && !$post->post_parent ) {
				echo trim($before) . get_the_title() . $after;
				$title = get_the_title();

			}  elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<a href="' . esc_url( get_permalink($page->ID) ) . '">' . get_the_title($page->ID) . '</a></li>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) echo trim($crumb) . ' ' . trim($delimiter) . ' ';
				echo trim($before) . get_the_title() . $after;
				$title = get_the_title();
			} elseif ( is_search() ) {
				echo trim($before) . esc_html__('Search results for ','puca')  . get_search_query() . '"' . $after;
				$title = esc_html__('Search results for ','puca')  . get_search_query();
			} elseif ( is_tag() ) {
				echo trim($before) . esc_html__('Posts tagged ', 'puca'). single_tag_title('', false) . '"' . $after;
				$title = esc_html__('Posts tagged "', 'puca'). single_tag_title('', false) . '"';
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo trim($before) . esc_html__('Articles posted by ', 'puca') .esc_html($userdata->display_name) . $after;
				$title = esc_html__('Articles posted by ', 'puca') . esc_html($userdata->display_name);
			} elseif ( is_404() ) {
				echo trim($before) . esc_html__('Error 404', 'puca') . $after;
				$title = esc_html__('Error 404', 'puca');
			}

			echo '</ol>';
		}
	}
}

if ( ! function_exists( 'puca_tbay_render_breadcrumbs' ) ) {
	function puca_tbay_render_breadcrumbs() {
		global $post;
		$show = true;
		$img = '';
		$style = array();


    $sidebar_configs = puca_tbay_get_blog_layout_configs();


    $breadcrumbs_layout = puca_tbay_get_config('blog_breadcrumb_layout', 'color');

    if( isset($post->post_type) && $post->post_type == 'project' ) {
			$breadcrumbs_layout = puca_tbay_get_config('portfolio_breadcrumb_layout', 'color');
    }

    if(isset($post->ID) && !empty(get_post_meta( $post->ID, 'tbay_page_breadcrumbs_layout', 'color' )) ) {
    	$breadcrumbs_layout = get_post_meta( $post->ID, 'tbay_page_breadcrumbs_layout', 'color' );
    }

    if( isset($_GET['breadcrumbs_layout']) ) {
         $breadcrumbs_layout = $_GET['breadcrumbs_layout'];
    }

    $class_container = '';
    if( isset($sidebar_configs['container_full']) &&  $sidebar_configs['container_full'] ) {
        $class_container = 'container-full';
    }

    switch ($breadcrumbs_layout) {
        case 'image':
            $breadcrumbs_class = ' breadcrumbs-image';
            break;
        case 'color':
            $breadcrumbs_class = ' breadcrumbs-color';
            break;
        case 'text':
            $breadcrumbs_class = ' breadcrumbs-text';
            break;
        default:
            $breadcrumbs_class  = ' breadcrumbs-image';
    }

    if(isset($sidebar_configs['breadscrumb_class'])) {
        $breadcrumbs_class .= ' '.$sidebar_configs['breadscrumb_class'];
    }
		if ( is_page() && is_object($post) ) { 

			$show = get_post_meta( $post->ID, 'tbay_page_show_breadcrumb', 'no' );
			
			if ( isset($show) && $show != 'yes' ) {
				echo '<div class="tbay-wrapper-border"></div>';
				return ''; 
			}

			$bgimage = get_post_meta( $post->ID, 'tbay_page_breadcrumb_image', true );
			$bgcolor = get_post_meta( $post->ID, 'tbay_page_breadcrumb_color', true );
			$style = array();
			if( $bgcolor && $breadcrumbs_layout !=='image' && $breadcrumbs_layout !=='text' ){
				$style[] = 'background-color:'.$bgcolor;
			}
			if( $bgimage  && $breadcrumbs_layout !=='color' && $breadcrumbs_layout !=='text'  ){ 
				$img = ' <img src="'.esc_url($bgimage).'">  ';
			}

		} elseif ( is_singular('post') || is_category() || is_home() || is_tag() || is_author() || is_day() || is_month() || is_year()  || is_search() || (isset($post->post_type) && $post->post_type == 'project') ) {
			$show = puca_tbay_get_config('show_blog_breadcrumb', false);

			if( isset($post->post_type) && $post->post_type == 'project' ) {
				$show = puca_tbay_get_config('show_portfolio_breadcrumb', false);
			}

			if ( !$show  ) {
				echo '<div class="tbay-wrapper-border"></div>';
				return '';  
			}
			$breadcrumb_img = puca_tbay_get_config('blog_breadcrumb_image');

			if( isset($post->post_type) && $post->post_type == 'project' ) {
				$breadcrumb_img = puca_tbay_get_config('portfolio_breadcrumb_image');
			}

	    $breadcrumb_color = puca_tbay_get_config('blog_breadcrumb_color');

	    if( isset($post->post_type) && $post->post_type == 'project' ) {
				$breadcrumb_color = puca_tbay_get_config('portfolio_breadcrumb_color');
			}

		     $style = array();
		     if( $breadcrumb_color && $breadcrumbs_layout !=='image' && $breadcrumbs_layout !=='text'   ){
		        $style[] = 'background-color:'.$breadcrumb_color;
		     }
    		if ( isset($breadcrumb_img['url']) && !empty($breadcrumb_img['url']) && $breadcrumbs_layout !=='color' && $breadcrumbs_layout !=='text' ) {
	          $img = ' <img src="'.$breadcrumb_img['url'].'">  ';
	      	}
		}

		$title = '';

		if(isset($post->ID) && !empty(get_the_title($post->ID) && is_page() ) ) {
			$title = '<h1 class="page-title">'. get_the_title($post->ID) .'</h1>';
			$breadcrumbs_class .= ' show-title';
		}

		$posttype = get_post_type($post );

		$current_theme = puca_tbay_get_theme();
		if($current_theme == 'fashion2') {

	        if ( is_category() ) {
	            $titlePost = single_cat_title('', false);
	        } else {
	            $titlePost = '';
	        }

		} else {
			$titlePost = '';
		}



		if( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post') || (is_post_type_archive('project')) || ( is_singular('project')) ) {
				$title = '<h1 class="page-title">'. esc_html($titlePost) .'</h1>';	
				$breadcrumbs_class .= ' show-title';
		}

		if( is_singular('post') ) {
			$title = '';
		}

		$estyle = !empty($style)? ' style="'.implode(";", $style).'"':"";



		echo '<section id="tbay-breadscrumb" '. trim($estyle).' class="tbay-breadscrumb '.esc_attr($breadcrumbs_class).'">'. trim($img) .'<div class="container"><div class="breadscrumb-inner" >';
			puca_tbay_breadcrumbs();
		echo ''.$title.'</div></div></section>';
		
	}
}

if ( ! function_exists( 'puca_tbay_render_title' ) ) {
	function puca_tbay_render_title() {
		global $post;
		
		if ( is_page() && is_object($post) ) { 

			$show = get_post_meta( $post->ID, 'tbay_page_show_breadcrumb', 'no' );

			if ( !$show  ) {
				echo '<header class="entry-header"><h1 class="tbay-entry-title">'. get_the_title($post->ID) .'</h1></header>';
			}
		}
		
	}
}

if ( !function_exists( 'puca_tbay_print_style_footer' ) ) {
	function puca_tbay_print_style_footer() {
    	$footer = puca_tbay_get_footer_layout();
    	if ( $footer ) {
    		$args = array(
				'name'        => $footer,
				'post_type'   => 'tbay_footer',
				'post_status' => 'publish',
				'numberposts' => 1
			);
			$posts = get_posts($args);
			foreach ( $posts as $post ) {
	    		return get_post_meta( $post->ID, '_wpb_shortcodes_custom_css', true );
	 	 	}
    	}
	}
}

if ( !function_exists( 'puca_tbay_print_style_megamenu' ) ) {
	function puca_tbay_print_style_megamenu() {

    		$args = array(
                'post_type'   => 'tbay_megamenu',
                'post_status' => 'publish',
                'posts_per_page'      => -1,
			);
			$posts = get_posts($args);
 
			$custom_cs = '';
			foreach ( $posts as $post ) {
	    		$custom_cs .= get_post_meta( $post->ID, '_wpb_shortcodes_custom_css', true );
	 	 	}

	 	 	return $custom_cs;

	} 
}

if ( !function_exists( 'puca_tbay_print_vc_style' ) ) {
	function puca_tbay_print_vc_style() {

		$vc_style = '';
		$footer_style = puca_tbay_print_style_footer();
		if ( !empty($footer_style) ) {
			$vc_style .= $footer_style;
		}	
		
		$megamenu_style = puca_tbay_print_style_megamenu();
		if ( !empty($megamenu_style) ) {
			$vc_style .= $megamenu_style;
		}	

		$custom_style = puca_tbay_custom_styles();
		if ( !empty($custom_style) ) {
			$vc_style .= $custom_style;
		}
	

		return $vc_style;
	}
}

if ( ! function_exists( 'puca_tbay_paging_nav' ) ) {
	function puca_tbay_paging_nav() {
		global $wp_query, $wp_rewrite;

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $wp_query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 1,
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_text' => esc_html__( '&larr; Previous', 'puca' ),
			'next_text' => esc_html__( 'Next &rarr;', 'puca' ),
		) );

		if ( $links ) :

		?>
		<nav class="navigation paging-navigation">
			<h1 class="screen-reader-text hidden"><?php esc_html_e( 'Posts navigation', 'puca' ); ?></h1>
			<div class="tbay-pagination">
				<?php echo trim($links); ?>
			</div><!-- .pagination -->
		</nav><!-- .navigation -->
		<?php
		endif;

	}
}


if ( ! function_exists( 'puca_tbay_post_nav' ) ) {
	function puca_tbay_post_nav() {
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}

		?>
		<nav class="navigation post-navigation">
			<h3 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'puca' ); ?></h3>
			<div class="nav-links clearfix">
				<?php
				if ( is_attachment() ) :
					previous_post_link( '%link','<div class="col-lg-6"><span class="meta-nav">'. esc_html__('Published In', 'puca').'</span></div>');
				else :
					previous_post_link( '%link','<div class="pull-left"><span class="meta-nav">'. esc_html__('Previous Post', 'puca').'</span></div>' );
					next_post_link( '%link', '<div class="pull-right"><span class="meta-nav">' . esc_html__('Next Post', 'puca').'</span><span></span></div>');
				endif;
				?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
}

if ( !function_exists('puca_tbay_pagination') ) {
    function puca_tbay_pagination($per_page, $total, $max_num_pages = '') {
    	global $wp_query, $wp_rewrite;
        ?>
        <div class="tbay-pagination">
        	<?php
        	$prev = esc_html__('Previous','puca');
        	$next = esc_html__('Next','puca');
        	$pages = $max_num_pages;
        	$args = array('class'=>'pull-left');

        	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	        if ( empty($pages) ) {
	            global $wp_query;
	            $pages = $wp_query->max_num_pages;
	            if ( !$pages ) {
	                $pages = 1;
	            }
	        }
	        $pagination = array(
	            'base' => @add_query_arg('paged','%#%'),
	            'format' => '',
	            'total' => $pages,
	            'current' => $current,
	            'prev_text' => $prev,
	            'next_text' => $next,
	            'type' => 'array'
	        );

	        if( $wp_rewrite->using_permalinks() ) {
	            $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
	        }
	        
	        if ( isset($_GET['s']) ) {
	            $cq = $_GET['s'];
	            $sq = str_replace(" ", "+", $cq);
	        }
	        
	        if ( !empty($wp_query->query_vars['s']) ) {
	            $pagination['add_args'] = array( 's' => $sq);
	        }
	        $paginations = paginate_links( $pagination );
	        if ( !empty($paginations) ) {
	            echo '<ul class="pagination '.esc_attr( $args["class"] ).'">';
	                foreach ($paginations as $key => $pg) {
	                    echo '<li>'. esc_html($pg) .'</li>';
	                }
	            echo '</ul>';
	        }
        	?>
            
        </div>
    <?php
    }
}

if ( !function_exists('puca_tbay_get_post_galleries') ) {
	function puca_tbay_get_post_galleries( $size='full' ){
	    
	    $ids = get_post_meta( get_the_ID(),'tbay_post_gallery_files' );

	    $output = array();

	    if( !empty($ids) ) {
		    $id = $ids[0];

		    if( empty($id) ) return;

		    foreach( $id as $id_img => $link_img ){
		    	$image = wp_get_attachment_image_src($id_img, $size);
		        $output[] = $image[0];
		    }
	    }
	  	
	  	return $output; 

	}
}

if ( !function_exists('puca_tbay_comment_form') ) {
	function puca_tbay_comment_form($arg, $class = 'btn-primary btn-outline ') {
		global $post;
		if ('open' == $post->comment_status) {
			ob_start();
	      	comment_form($arg);
	      	$form = ob_get_clean();
	      	?>
	      	<div class="commentform row reset-button-default">
		    	<div class="col-sm-12">
			    	<?php
			      	echo str_replace('id="submit"','id="submit"', $form);
			      	?>
		      	</div>
	      	</div>
	      	<?php
	      }
	}
}

if (!function_exists('puca_tbay_list_comment') ) {
	function puca_tbay_list_comment($comment, $args, $depth) {
		if ( is_file(get_template_directory().'/list_comments.php') ) {
	        require get_template_directory().'/list_comments.php';
      	}
	}
}

if (!function_exists('puca_tbay_display_footer_builder') ) {
	function puca_tbay_display_footer_builder($footer) {
		global $footer_builder;
		$footer_builder = true;
		$args = array(
			'name'        => $footer,
			'post_type'   => 'tbay_footer',
			'post_status' => 'publish',
			'numberposts' => 1
		);
		$posts = get_posts($args);
		foreach ( $posts as $post ) {
			if( puca_is_elementor_activated() && Elementor\Plugin::$instance->db->is_built_with_elementor( $post->ID ) ) {
				echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post->ID );
			} else {
				echo '<div class="footer"><div class="container">';
					echo do_shortcode( $post->post_content );
				echo '</div></div>';
			} 
		}
		$footer_builder = false;
	}
}

if (!function_exists('puca_tbay_header_bodyclasses') ) {
	function puca_tbay_header_bodyclasses( $classes ) {

		$tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type', 'v1') );

		$classes[] = $tbay_header;

	    return $classes;
	}
	add_filter( 'body_class','puca_tbay_header_bodyclasses' );
}

if (!function_exists('puca_tbay_get_random_blog_cat') ) {
	function puca_tbay_get_random_blog_cat() {
		$post_category = "";
		$categories = get_the_category();

		$number = rand(0, count($categories) - 1);

		if($categories){

			$post_category .= '<a href="'.esc_url( get_category_link( $categories[$number]->term_id ) ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s", 'puca' ), $categories[$number]->name ) ) . '">'.$categories[$number]->cat_name.'</a>';
		}  

		echo trim($post_category);
	}
}

if (!function_exists('puca_tbay_get_id_author_post') ) {
	function puca_tbay_get_id_author_post() {
		global $post;

		$author_id = $post->post_author;

		if( isset($author_id) ) {
			return $author_id;
		}
	}
}


if ( ! function_exists( 'puca_body_class_mobile_footer' ) ) {
	function puca_body_class_mobile_footer( $classes ) {
  
  		$mobile_footer = puca_tbay_get_config('mobile_footer',true);

  		$footer_icon = puca_tbay_get_config('mobile_footer_icon',true);

		if( isset($mobile_footer) && !$mobile_footer ) {
			$classes[] = 'mobile-hidden-footer';
		}

		if( isset($footer_icon) && !$footer_icon ) {
			$classes[] = 'mobile-hidden-footer-icon';
		}


		return $classes;

	}
	add_filter( 'body_class', 'puca_body_class_mobile_footer',99 );
}

if ( ! function_exists( 'puca_body_class_header_mobile' ) ) {
	function puca_body_class_header_mobile( $classes ) {
  
  		$layout = puca_tbay_get_config('header_mobile', 'center');

		if( isset($layout) ) {
			$classes[] = 'header-mobile-'.$layout;
		}
		return $classes;

	}
	add_filter( 'body_class', 'puca_body_class_header_mobile',99 );
}

if ( ! function_exists( 'puca_tbay_get_menu_mobile_icon' ) ) {
	function puca_tbay_get_menu_mobile_icon( $ouput) {

		$menu_option            = apply_filters( 'puca_menu_mobile_option', 10 );

		$ouput = '';
		if( $menu_option == 'smart_menu' ) {

			$ouput 	.= '<a href="#tbay-mobile-menu-navbar" class="btn btn-sm btn-danger">';
			$ouput  .= '<i class="icon-menu icons"></i>';
			$ouput  .= '</a>';			

			$ouput 	.= '<a href="#page" class="btn btn-sm btn-danger">';
			$ouput  .= '<i class="icon-close icons"></i>';
			$ouput  .= '</a>';

		}
		else {
			$ouput 	.= '<button data-toggle="offcanvas" class="btn btn-sm btn-danger btn-offcanvas btn-toggle-canvas offcanvas" type="button"><i class="icon-menu icons"></i></button>';
			
		}

		return $ouput;

	}

	add_filter( 'puca_get_menu_mobile_icon', 'puca_tbay_get_menu_mobile_icon',99 );
}