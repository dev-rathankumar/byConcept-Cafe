<?php

if ( ! function_exists( 'puca_tbay_body_classes' ) ) {
	function puca_tbay_body_classes( $classes ) {
		global $post;
		if ( is_page() && is_object($post) ) {
			$class = get_post_meta( $post->ID, 'tbay_page_extra_class', true );
			if ( !empty($class) ) {
				$classes[] = trim($class);
			}
		}
		if ( puca_tbay_get_config('preload') ) {
			$classes[] = 'tbay-body-loader';
		}		

		if ( puca_tbay_is_home_page() ) {
			$classes[] = 'tbay-homepage-demo';
		}

		$get_header = puca_tbay_get_config('header_type');
	  	if( empty($get_header) ) {
	  	 	$classes[] = 'tbay-body-default';
	  	}

	  	$classes[] = 'skin-'.puca_tbay_get_theme();

		return $classes;
	}
	add_filter( 'body_class', 'puca_tbay_body_classes' );
}


if ( ! function_exists( 'puca_tbay_body_home_classes' ) ) {
	function puca_tbay_body_home_classes( $classes ) {
		global $post;
		if ( is_page() && is_object($post) ) {
			$slug = get_queried_object()->post_name;
			if ( !empty($slug) ) {
				$classes[] = trim($slug);
			}
		} 

		if( is_front_page() ) {
			$class = 'tbay-home';
			if ( !empty($class) ) {
				$classes[] = trim($class);
			}
		}

		return $classes;
	}
	add_filter( 'body_class', 'puca_tbay_body_home_classes' );
}

if ( ! function_exists( 'puca_tbay_get_shortcode_regex' ) ) {
	function puca_tbay_get_shortcode_regex( $tagregexp = '' ) {
		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return
			'\\['                                // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)"                     // 2: Shortcode name
			. '(?![\\w-])'                       // Not followed by word character or hyphen
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			. '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			. '(?:'
			. '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			. '[^\\]\\/]*'               // Not a closing bracket or forward slash
			. ')*?'
			. ')'
			. '(?:'
			. '(\\/)'                        // 4: Self closing tag ...
			. '\\]'                          // ... and closing bracket
			. '|'
			. '\\]'                          // Closing bracket
			. '(?:'
			. '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			. '[^\\[]*+'             // Not an opening bracket
			. '(?:'
			. '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			. '[^\\[]*+'         // Not an opening bracket
			. ')*+'
			. ')'
			. '\\[\\/\\2\\]'             // Closing shortcode tag
			. ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
}

if ( ! function_exists( 'puca_tbay_tagregexp' ) ) {
	function puca_tbay_tagregexp() {
		return apply_filters( 'puca_tbay_custom_tagregexp', 'video|audio|playlist|video-playlist|embed|puca_tbay_media' );
	}
}


if( ! function_exists( 'puca_tbay_text_line')) {
	function puca_tbay_text_line( $str ) {
		return trim(preg_replace("/('|\"|\r?\n)/", '', $str)); 
	}
}

if ( !function_exists('puca_tbay_get_themes') ) {
	function puca_tbay_get_themes() {
		$themes = array();
		$path   = get_template_directory() . '/css/skins/';
		
		if ( is_dir($path) ) {
			$folders = scandir($path);
			$excludes = array('.', '..', '.svn');
			foreach ($folders as $folder) {
				if ( !in_array( $folder, $excludes ) && is_dir($path . $folder) ) {
					$theme = array(
				        $folder => array( 
	                        'title' => $folder,
	                        'alt'   => $folder,
	                        'img'   => get_template_directory_uri() . '/inc/assets/images/active_theme/'.$folder.'.jpg'
	                    ),
	                );  
	                $themes = array_merge($themes,$theme);
				}
			}
		}
		return $themes;

	}
}

if ( !function_exists('puca_tbay_get_theme') ) {
	function puca_tbay_get_theme() {
		return puca_tbay_get_global_config('active_theme','fashion');

	}
}

if ( !function_exists('puca_tbay_get_part_theme') ) {
	function puca_tbay_get_part_theme() {
		$active_theme  = puca_tbay_get_global_config('active_theme','fashion');
		$active_theme  = 'themes/'.$active_theme;

		return $active_theme;

	}
}

if ( !function_exists('puca_tbay_get_header_layouts') ) {
	function puca_tbay_get_header_layouts() {
		$headers = array();
		$current_theme = puca_tbay_get_theme();

		$files = glob( get_template_directory() . '/headers/themes/'.$current_theme.'/*.php' );

		usort($files, function ($a, $b) {
		    $aIsDir = is_dir($a);
		    $bIsDir = is_dir($b);
		    if ($aIsDir === $bIsDir)
		        return strnatcasecmp($a, $b);
		    elseif ($aIsDir && !$bIsDir)
		        return -1;
		    elseif (!$aIsDir && $bIsDir)
		        return 1;
		});

	    if ( !empty( $files ) ) { 
	        foreach ( $files as $file ) {
	        	$header = str_replace( '.php', '', basename($file) );
	            $headers[$header] = $current_theme.'-'.$header;
	        }
	    }

		return $headers;
	}
}

if ( !function_exists('puca_tbay_get_header_layout') ) {
	function puca_tbay_get_header_layout() {
		global $post;
		
		if ( defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED && is_shop() ) {
			return puca_tbay_page_header_layout();
		}

		if ( is_page() && is_object($post) && isset($post->ID) ) {
			return puca_tbay_page_header_layout();
		}
		return puca_tbay_get_config('header_type', 'v1');
	}
	add_filter( 'puca_tbay_get_header_layout', 'puca_tbay_get_header_layout' );
}

if ( !function_exists('puca_tbay_get_footer_layouts') ) {
	function puca_tbay_get_footer_layouts() {
		$footers = array( '' => esc_html__('Default', 'puca'));
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'tbay_footer',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$footers[$post->post_name] = $post->post_title;
		}
		return $footers;
	}
}

if ( !function_exists('puca_tbay_get_footer_layout') ) {
	function puca_tbay_get_footer_layout() {
		if ( is_page() ) {
			global $post;
			$footer = '';
			if ( is_object($post) && isset($post->ID) ) {
				$footer = get_post_meta( $post->ID, 'tbay_page_footer_type', true );
				if ( $footer == 'global' ||  $footer == '') {
					return puca_tbay_get_config('footer_type', '');
				}
			}
			return $footer;
		} else if ( defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED && is_shop() ) {

			$post_id = wc_get_page_id('shop');
			if ( isset($post_id) ) {
				$footer = get_post_meta( $post_id, 'tbay_page_footer_type', true );
				if ( $footer == 'global' ||  $footer == '') {
					return puca_tbay_get_config('footer_type', '');
				}
			}
			return $footer;
		}

		return puca_tbay_get_config('footer_type', '');
	}
	add_filter('puca_tbay_get_footer_layout', 'puca_tbay_get_footer_layout');
}

if ( !function_exists('puca_tbay_blog_content_class') ) {
	function puca_tbay_blog_content_class( $class ) {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		if ( puca_tbay_get_config('blog_'.$page.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'puca_tbay_blog_content_class', 'puca_tbay_blog_content_class', 1 , 1  );




// layout class for woo page
if ( !function_exists('puca_tbay_post_content_class') ) {
    function puca_tbay_post_content_class( $class ) {
        $page = 'archive';
        if ( is_singular( 'post' ) ) {
            $page = 'single';

            if( !isset($_GET['blog_'.$page.'_layout']) ) {
                $class .= ' '.puca_tbay_get_config('blog_'.$page.'_layout');
            }  else {
                $class .= ' '.$_GET['blog_'.$page.'_layout'];
            }

        } else {

            if( !isset($_GET['blog_'.$page.'_layout']) ) {
                $class .= ' '.puca_tbay_get_config('blog_'.$page.'_layout');
            }  else {
                $class .= ' '.$_GET['blog_'.$page.'_layout'];
            }

        }
        return $class;
    }
}
add_filter( 'puca_tbay_post_content_class', 'puca_tbay_post_content_class' );

if ( !function_exists('puca_tbay_page_content_class') ) {
	function puca_tbay_page_content_class( $class ) {
		global $post;
		$fullwidth = get_post_meta( $post->ID, 'tbay_page_fullwidth', true );
		if ( !$fullwidth || $fullwidth == 'no' ) {
			return $class;
		}
		return 'container-fluid';
	}
}
add_filter( 'puca_tbay_page_content_class', 'puca_tbay_page_content_class', 1 , 1  );

if ( !function_exists('puca_tbay_get_page_layout_configs') ) {
	function puca_tbay_get_page_layout_configs() {
		global $post;
		if( isset($post->ID) ) {
			$left = get_post_meta( $post->ID, 'tbay_page_left_sidebar', true );
			$right = get_post_meta( $post->ID, 'tbay_page_right_sidebar', true );

			switch ( get_post_meta( $post->ID, 'tbay_page_layout', true ) ) {
				case 'left-main':
					$configs['left'] = array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
					$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
					break;
				case 'main-right':
					$configs['right'] = array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
					$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
					break;
				case 'main':
					$configs['main'] = array( 'class' => 'clearfix' );
					break;
				case 'left-main-right':
					$configs['left'] = array( 'sidebar' => $left,  'class' => 'col-xs-12 col-md-12 col-lg-3'  );
					$configs['right'] = array( 'sidebar' => $right, 'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
					$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-6' );
					break;
				default:
					$configs['main'] = array( 'class' => 'clearfix' );
					break;
			}

			return $configs; 
		}
	}
}

if ( !function_exists('puca_tbay_page_header_layout') ) {
	function puca_tbay_page_header_layout() {
		global $post;

		if ( is_object($post) && isset($post->ID) ) $post_id = $post->ID;
		
		if ( defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED  && is_shop() ) {
			$post_id = wc_get_page_id('shop');
		}

		$header = get_post_meta( $post_id, 'tbay_page_header_type', true );
		if ( $header == 'global' || $header == '' ) {
			return puca_tbay_get_config('header_type', 'v1');
		}
		return $header;
	}
}

if ( ! function_exists( 'puca_tbay_get_first_url_from_string' ) ) {
	function puca_tbay_get_first_url_from_string( $string ) {
		$pattern = "/^\b(?:(?:https?|ftp):\/\/)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		preg_match( $pattern, $string, $link );

		return ( ! empty( $link[0] ) ) ? $link[0] : false;
	}
}

/*Check in home page*/
if ( !function_exists('puca_tbay_is_home_page') ) {
	function puca_tbay_is_home_page() {
		$is_home = false;

		if( is_home() || is_front_page() || is_page( 'home-1' ) || is_page( 'home-2' ) || is_page( 'home-3' ) || is_page( 'home-4' ) || is_page( 'home-5' ) || is_page( 'home-6' ) || is_page( 'home-7' ) || is_page( 'home-8' )|| is_page( 'home-9' )|| is_page( 'home-10' )|| is_page( 'home-11' )|| is_page( 'home-12' )|| is_page( 'home-13' )|| is_page( 'home-14' )|| is_page( 'home-15' )|| is_page( 'home-16' )|| is_page( 'home-17' )|| is_page( 'home-18' )|| is_page( 'home-19' )|| is_page( 'home-20' )|| is_page( 'home-21' )|| is_page( 'home-22' )|| is_page( 'home-23' )|| is_page( 'home-24' )|| is_page( 'home-25' )|| is_page( 'home-26' ) ) {
			$is_home = true;
		}

		return $is_home;
	}
}

if ( !function_exists( 'puca_tbay_get_link_attributes' ) ) {
	function puca_tbay_get_link_attributes( $string ) {
		preg_match( '/<a href="(.*?)">/i', $string, $atts );

		return ( ! empty( $atts[1] ) ) ? $atts[1] : '';
	}
}

if ( !function_exists( 'puca_tbay_post_media' ) ) {
	function puca_tbay_post_media( $content ) {
		$is_video = ( get_post_format() == 'video' ) ? true : false;
		$media = puca_tbay_get_first_url_from_string( $content );
		if ( ! empty( $media ) ) {
			global $wp_embed;
			$content = do_shortcode( $wp_embed->run_shortcode( '[embed]' . $media . '[/embed]' ) );
		} else {
			$pattern = puca_tbay_get_shortcode_regex( puca_tbay_tagregexp() );
			preg_match( '/' . $pattern . '/s', $content, $media );
			if ( ! empty( $media[2] ) ) {
				if ( $media[2] == 'embed' ) {
					global $wp_embed;
					$content = do_shortcode( $wp_embed->run_shortcode( $media[0] ) );
				} else {
					$content = do_shortcode( $media[0] );
				}
			}
		}
		if ( ! empty( $media ) ) {
			$output = '<div class="entry-media">';
			$output .= ( $is_video ) ? '<div class="pro-fluid"><div class="pro-fluid-inner">' : '';
			$output .= $content;
			$output .= ( $is_video ) ? '</div></div>' : '';
			$output .= '</div>';

			return $output;
		}

		return false;
	}
}

if ( !function_exists( 'puca_tbay_post_gallery' ) ) {
	function puca_tbay_post_gallery( $content ) {
		$pattern = puca_tbay_get_shortcode_regex( 'gallery' );
		preg_match( '/' . $pattern . '/s', $content, $media );
		if ( ! empty( $media[2] )  ) {
			return '<div class="entry-gallery">' . do_shortcode( $media[0] ) . '<hr class="pro-clear" /></div>';
		}

		return false;
	}
}

if ( !function_exists( 'puca_tbay_random_key' ) ) {
    function puca_tbay_random_key($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $return;
    }
}

if ( !function_exists('puca_tbay_substring') ) {
    function puca_tbay_substring($string, $limit, $afterlimit = '[...]') {
        if ( empty($string) ) {
        	return $string;
        }
       	$string = explode(' ', strip_tags( $string ), $limit);

        if (count($string) >= $limit) {
            array_pop($string);
            $string = implode(" ", $string) .' '. $afterlimit;
        } else {
            $string = implode(" ", $string);
        }
        $string = preg_replace('`[[^]]*]`','',$string);
        return strip_shortcodes( $string );
    }
}

if ( !function_exists('puca_tbay_subschars') ) {
    function puca_tbay_subschars($string, $limit, $afterlimit='...'){

	    if(strlen($string) > $limit){
	        $string = substr($string, 0, $limit);
	    }else{
	        $afterlimit = '';
	    }
	    return $string . $afterlimit;
	}
}


/*testimonials*/
if ( !function_exists('puca_tbay_get_page_templates_parts') ) {
	function puca_tbay_get_page_templates_parts($slug = 'logo', $name = null) {
		$active_theme = puca_tbay_get_theme();

		return get_template_part( 'page-templates/themes/'.$active_theme.'/parts/'.$slug.'',$name);
	}
}

/*testimonials*/
if ( !function_exists('puca_tbay_get_testimonials_layouts') ) {
	function puca_tbay_get_testimonials_layouts() {
		$testimonials = array();
		$active_theme = puca_tbay_get_part_theme();
		$files = glob( get_template_directory() . '/vc_templates/testimonial/'.$active_theme.'/testimonial-*.php' );
	    if ( !empty( $files ) ) {
	        foreach ( $files as $file ) {
	        	$testi = str_replace( "testimonial-", '', str_replace( '.php', '', basename($file) ) );
	            $testimonials[$testi] = $testi;
	        }
	    }

		return $testimonials;
	}
}

/*Blog*/
if ( !function_exists('puca_tbay_get_blog_layouts') ) {
	function puca_tbay_get_blog_layouts() {
		$blogs = array(
			esc_html__('Grid', 'puca') => 'grid',
			esc_html__('List', 'puca') => 'list',
		);
		$active_theme = puca_tbay_get_part_theme();
		$files = glob( get_template_directory() . '/vc_templates/post/'.$active_theme.'/carousel/_single_*.php' );
	    if ( !empty( $files ) ) {
	        foreach ( $files as $file ) {
	        	$str = str_replace( "_single_", '', str_replace( '.php', '', basename($file) ) );
	            $blogs[$str] = $str;
	        }
	    }

		return $blogs;
	}
}

// Number of blog per row
if ( !function_exists('puca_tbay_blog_loop_columns') ) {
    function puca_tbay_blog_loop_columns($number) {

    		$sidebar_configs = puca_tbay_get_blog_layout_configs();

    		$columns 	= puca_tbay_get_config('blog_columns');

        if( isset($_GET['blog_columns']) && is_numeric($_GET['blog_columns']) ) {
            $value = $_GET['blog_columns']; 
        } elseif( empty($columns) && isset($sidebar_configs['columns']) ) {
    			$value = 	$sidebar_configs['columns']; 
    		} else {
          $value = $columns;          
        }

        if ( in_array( $value, array(1, 2, 3, 4, 5, 6) ) ) {
            $number = $value;
        }
        return $number;
    }
}
add_filter( 'loop_blog_columns', 'puca_tbay_blog_loop_columns' );

// Size Images
if ( !function_exists('puca_tbay_blog_image_size') ) {
    function puca_tbay_blog_image_size($size) {

    		$sidebar_configs = puca_tbay_get_blog_layout_configs();

    		$image_size 	= puca_tbay_get_config('image_sizes');

        if( isset($_GET['image_sizes']) ) {
            $size = $_GET['image_sizes']; 
        } elseif( empty($image_size) && isset($sidebar_configs['image_sizes']) ) {
    			$size = 	$sidebar_configs['image_sizes']; 
    		} elseif( isset($image_size) ) {
          $size = $image_size;          
        }

        return $size;
    }
}
add_filter( 'loop_blog_size_image', 'puca_tbay_blog_image_size' );



/*Add Blog Top Sidebar 1 to hook main content in page Archive*/
if ( !function_exists( 'puca_tbay_blog_top_sidebar1' ) ) {
    function puca_tbay_blog_top_sidebar1() {

    		$sidebar_configs = puca_tbay_get_blog_layout_configs();

       	if ( !is_singular( 'post' ) ) {
       		?>

       		  <?php if( isset($sidebar_configs['blog_top_sidebar1']) && $sidebar_configs['blog_top_sidebar1'] && is_active_sidebar('blog-top-sidebar1')) : ?>
                <div class="blog-top-sidebar1">

                    <div class="content">
                        <?php dynamic_sidebar('blog-top-sidebar1'); ?>
                    </div>

                </div>

            <?php endif;?>

       		<?php
        }
    }
}
add_action( 'puca_post_template_main_content_before', 'puca_tbay_blog_top_sidebar1', 10 );

/*Add Blog Top Sidebar 2 to hook main container in page Archive*/
if ( !function_exists( 'puca_tbay_blog_top_sidebar2' ) ) {
    function puca_tbay_blog_top_sidebar2() {

    		$sidebar_configs = puca_tbay_get_blog_layout_configs();

       	if ( !is_singular( 'post' ) ) {
       		?>

       		  <?php if( isset($sidebar_configs['blog_top_sidebar2']) && $sidebar_configs['blog_top_sidebar2'] && is_active_sidebar('blog-top-sidebar2')) : ?>
                <div class="blog-top-sidebar2">

                    <div class="content">
                        <?php dynamic_sidebar('blog-top-sidebar2'); ?>
                    </div>

                </div>

            <?php endif;?>

       		<?php
        }
    }
}
add_action( 'puca_post_template_main_container_before', 'puca_tbay_blog_top_sidebar2', 20 );


/*Add Blog Top Search Sidebar to hook main container in page Archive*/
if ( !function_exists( 'puca_tbay_blog_top_search' ) ) {
    function puca_tbay_blog_top_search() {

    	$sidebar_configs = puca_tbay_get_blog_layout_configs();

       	if ( !is_singular( 'post' ) ) {
       		?>

       		  <?php if( isset($sidebar_configs['blog_top_search']) && $sidebar_configs['blog_top_search'] && is_active_sidebar('blog-top-search')) : ?>
                <div class="blog-top-search">

                    <div class="content">
                        <?php dynamic_sidebar('blog-top-search'); ?>
                    </div>

                </div>

            <?php endif;?>

       		<?php
        }
    }
}
add_action( 'puca_post_template_main_container_before', 'puca_tbay_blog_top_search', 10 );

/*Check style blog image full*/
if ( !function_exists( 'puca_tbay_blog_image_sizes_full' ) ) {
    function puca_tbay_blog_image_sizes_full() {
    	$style = false;
    	$sidebar_configs = puca_tbay_get_blog_layout_configs();

       	if ( !is_singular( 'post' ) ) {
       		if( isset($sidebar_configs['image_sizes']) && $sidebar_configs['image_sizes'] == 'full') :
       			$style = true;
       		endif;
        }

        return  $style;

    }
}


// Number of post per page
if ( !function_exists('puca_tbay_loop_post_per_page') ) {
    function puca_tbay_loop_post_per_page($number) {

        if( isset($_GET['posts_per_page']) && is_numeric($_GET['posts_per_page']) ) {
            $value = $_GET['posts_per_page']; 
        } else {
            $value = get_option( 'posts_per_page' );       
        }

        if ( is_numeric( $value ) && $value ) {
            $number = absint( $value );
        }
        
        return $number;
    }
  add_filter( 'loop_post_per_page', 'puca_tbay_loop_post_per_page' );
}

if ( !function_exists('puca_tbay_posts_per_page') ) {
	function puca_tbay_posts_per_page( $wp_query ){

			if ( is_admin() || ! $wp_query->is_main_query() )
	        return;

			$value = apply_filters( 'loop_post_per_page', 6 );

		 	if( isset($value) && is_category() )
		    $wp_query->query_vars['posts_per_page'] = $value;
		 	return $wp_query;
	}
	add_action( 'pre_get_posts', 'puca_tbay_posts_per_page' );
}

if ( !function_exists('puca_tbay_share_js') ) {
	function puca_tbay_share_js() {
		 if ( puca_tbay_get_config('enable_code_share',false) && is_single() ) {
		 	echo puca_tbay_get_config('code_share');
		 }
	}
	add_action('wp_head', 'puca_tbay_share_js');
}


/*Post Views*/
if ( !function_exists('puca_set_post_views') ) {
	function puca_set_post_views($postID) {
	    $count_key = 'puca_post_views_count';
	    $count 		 = get_post_meta($postID, $count_key, true);
	    if( $count == '' ){
	        $count = 1;
	        delete_post_meta($postID, $count_key);
	        add_post_meta($postID, $count_key, '1');
	    }else{
	        $count++;
	        update_post_meta($postID, $count_key, $count);
	    }
	}
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

if ( !function_exists('puca_track_post_views') ) {
	function puca_track_post_views ($post_id) {
	    if ( !is_single() ) return;
	    if ( empty ( $post_id) ) {
	        global $post;
	        $post_id = $post->ID;    
	    }
	    puca_set_post_views($post_id);
	}
	add_action( 'wp_head', 'puca_track_post_views');
}

if ( !function_exists('puca_get_post_views') ) {
	function puca_get_post_views($postID, $text = ''){
	    $count_key = 'puca_post_views_count';
	    $count = get_post_meta($postID, $count_key, true);

	    if( $count == '' ){
	        delete_post_meta($postID, $count_key);
	        add_post_meta($postID, $count_key, '0');
	        return "0";
	    }
	    return $count.$text;
	}
}

/*Layou Page 404*/
if ( ! function_exists( 'puca_page_404_layout' ) ) {
    function puca_page_404_layout( $layout ) {

        if( isset($_GET['layout_404'])) {
            $layout = $_GET['layout_404'];
        } else {
            $layout = puca_tbay_get_config('page_404_layout', 'v1');
        }

        return $layout;
    }
    add_filter( 'puca_404_layout', 'puca_page_404_layout' );
}

/*Get Preloader*/
if ( ! function_exists( 'puca_get_select_preloader' ) ) {
    function puca_get_select_preloader( ) {

    	$enable_preload = puca_tbay_get_config('preload',false);

    	if( !$enable_preload ) return;

    	$preloader 	= puca_tbay_get_global_config('select_preloader', 'loader1');

    	$media 		= puca_tbay_get_global_config('media-preloader');

    	if( isset($preloader) ) {
	    	switch ($preloader) {
	    		case 'loader1': 
	    			?>
	                <div class="tbay-page-loader">
					  	<div id="loader"></div>
					  	<div class="loader-section section-left"></div>
					  	<div class="loader-section section-right"></div>
					</div>
	    			<?php
	    			break;    		

	    		case 'loader2':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-two">
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader3':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-three">
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader4':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-four"> <span class="spinner-cube spinner-cube1"></span> <span class="spinner-cube spinner-cube2"></span> <span class="spinner-cube spinner-cube3"></span> <span class="spinner-cube spinner-cube4"></span> <span class="spinner-cube spinner-cube5"></span> <span class="spinner-cube spinner-cube6"></span> <span class="spinner-cube spinner-cube7"></span> <span class="spinner-cube spinner-cube8"></span> <span class="spinner-cube spinner-cube9"></span> </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader5':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-five"> <span class="spinner-cube-1 spinner-cube"></span> <span class="spinner-cube-2 spinner-cube"></span> <span class="spinner-cube-4 spinner-cube"></span> <span class="spinner-cube-3 spinner-cube"></span> </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader6':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-six"> <span class=" spinner-cube-1 spinner-cube"></span> <span class=" spinner-cube-2 spinner-cube"></span> </div>
					</div>
	    			<?php
	    			break;	    		

	    		case 'custom_image':
	    			?>
					<div class="tbay-page-loader loader-img">
						<?php if( isset($media['url']) && !empty($media['url']) ): ?>
					   		<img alt="<?php echo esc_attr( $media['alt'] ); ?>" src="<?php echo esc_url($media['url']); ?>">
						<?php endif; ?>
					</div>
	    			<?php
	    			break;
	    			
	    		default:
	    			?>
	    			<div class="tbay-page-loader">
					  	<div id="loader"></div>
					  	<div class="loader-section section-left"></div>
					  	<div class="loader-section section-right"></div>
					</div>
	    			<?php
	    			break;
	    	}
	    }
     	
    }

    add_action( 'wp_body_open', 'puca_get_select_preloader', 10 );
}

if ( !function_exists('puca_gallery_atts') ) {

	add_filter( 'shortcode_atts_gallery', 'puca_gallery_atts', 10, 3 );
	
	/* Change attributes of wp gallery to modify image sizes for your needs */
	function puca_gallery_atts( $output, $pairs, $atts ) {

			
		if ( isset($atts['columns']) && $atts['columns'] == 1 ) {
			//if gallery has one column, use large size
			$output['size'] = 'full';
		} else if ( isset($atts['columns']) && $atts['columns'] >= 2 && $atts['columns'] <= 4 ) {
			//if gallery has between two and four columns, use medium size
			$output['size'] = 'full';
		} else {
			//if gallery has more than four columns, use thumbnail size
			$output['size'] = 'full';
		}
	
		return $output;
	
	}
}

if ( !function_exists('puca_get_custom_menu') ) {

	
	/* Change attributes of wp gallery to modify image sizes for your needs */
	function puca_get_custom_menu( $menu_id ) {

		$_id = puca_tbay_random_key();

        $args = array(
            'menu'              => $menu_id,
            'container_class'   => 'nav',
            'menu_class'        => 'menu',
            'fallback_cb'       => '',
            'before'            => '',
            'after'             => '',
            'echo'              => true,
            'menu_id'           => 'menu-'.$menu_id.'-'.$_id
        );

        $output = wp_nav_menu($args);

	
		return $output;
	
	}
}

/*Set excerpt show enable default*/
if ( ! function_exists( 'puca_tbay_edit_post_show_excerpt' ) ) {
	function puca_tbay_edit_post_show_excerpt() {
	  $user = wp_get_current_user();
	  $unchecked = get_user_meta( $user->ID, 'metaboxhidden_post', true );
	  if( is_array($unchecked) ) {
		$key = array_search( 'postexcerpt', $unchecked );
		if ( FALSE !== $key ) {
		   array_splice( $unchecked, $key, 1 );
		   update_user_meta( $user->ID, 'metaboxhidden_post', $unchecked );
		}
	  }
	}
	add_action( 'admin_init', 'puca_tbay_edit_post_show_excerpt', 10 );
}


if ( !function_exists('puca_tbay_menu_mobile_type') ) {
    function puca_tbay_menu_mobile_type() {
    	
        $option = puca_tbay_get_config('menu_mobile_type', 'smart_menu');
        $option = (isset($_GET['menu_mobile_type'])) ? $_GET['menu_mobile_type'] : $option;

        return $option;
    }
}
add_filter( 'puca_menu_mobile_option', 'puca_tbay_menu_mobile_type', 10, 3 );


if ( !function_exists('puca_tbay_get_attachment_image_loaded') ) {
	function puca_tbay_get_attachment_image_loaded($attachment_id, $size = 'thumbnail', $attr = '', $echo = true)  {

		$src_blank = 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D&#039;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#039; viewBox%3D&#039;0 0 600 400&#039;%2F%3E';


		$html = '';
		$image = wp_get_attachment_image_src($attachment_id, $size);
		if ( $image ) {
			list($src, $width, $height) = $image;
			$hwstring = image_hwstring($width, $height);
			$size_class = $size;
			if ( is_array( $size_class ) ) {
				$size_class = join( 'x', $size_class );
			}


			$attachment = get_post($attachment_id);
			$default_attr = array(
				'src'	=> $src_blank,
				'data-src'	=> $src,
				'class'	=> "attachment-$size_class size-$size_class",
				'alt'	=> trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
			);

			if( ! (bool) puca_tbay_get_global_config('enable_lazyloadimage',false) ) {
				$default_attr['src'] = $src;
				unset($default_attr['data-src']);
			}

			$attr = wp_parse_args( $attr, $default_attr );


			$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $size );

			if( (bool) puca_tbay_get_global_config('enable_lazyloadimage',false) ) {
				$attr['class'] = $attr['class']. ' unveil-image';
			}

			
			$attr = array_map( 'esc_attr', $attr );
			$html = rtrim("<img $hwstring");
			foreach ( $attr as $name => $value ) {
				$html .= " $name=" . '"' . $value . '"';
			}
			$html .= ' />';
		}

		if( $echo ) {
			echo trim($html);
		} else {
			return $html;
		}

	}
}


if ( !function_exists('puca_tbay_src_image_loaded') ) {
	function puca_tbay_src_image_loaded($src, $attr = '', $hwstring ='' , $echo = true)  {

		$src_blank = 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D&#039;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#039; viewBox%3D&#039;0 0 600 400&#039;%2F%3E';



		$default_attr = array(
			'src'	=> $src_blank,
			'data-src'	=> $src,
			'class'	=> '',
		);

		if( ! (bool) puca_tbay_get_global_config('enable_lazyloadimage',false) ) {
			$default_attr['src'] = $src;
			unset($default_attr['data-src']);
		}


		$attr = wp_parse_args( $attr, $default_attr );

		if( (bool) puca_tbay_get_global_config('enable_lazyloadimage',false) ) {
			$attr['class'] = $attr['class']. ' unveil-image';
		}

		$attr = array_map( 'esc_attr', $attr );
		$html = rtrim("<img $hwstring");
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';

		if( $echo ) {
			echo trim($html);
		} else {
			return $html;
		}
		
	}
}

if (!function_exists('puca_is_elementor_activated')) {
    function puca_is_elementor_activated() {
        return function_exists('elementor_load_plugin_textdomain');
    }
}

if (!function_exists('puca_is_Woocommerce_activated')) {
    function puca_is_Woocommerce_activated() {
        return class_exists('WooCommerce') ? true : false;
    }
}

if(!function_exists('puca_switcher_to_boolean')) {
	 function puca_switcher_to_boolean($var) {
		if( $var === 'yes' ) {
			return true;
		} else {
			return false;
		}
	}
}

if(!function_exists('puca_elements_ready_slick')) {
	function puca_elements_ready_slick() {
		$array = [
			'brands', 
			'products', 
			'posts-grid',
			'our-team', 
			'instagram', 
			'product-category', 
			'product-tabs', 
			'testimonials',
			'product-categories-tabs',
			'list-categories-product',
			'custom-image-list-categories',
			'custom-image-list-tags',
			'furniture-custom-image-list-categories',
			'product-flash-sales',
			'product-list-tags',
			'product-count-down',
			'supermaket2-categoriestabs',
			'supermaket2-categoriestabs-2',
			'supermaket2-categoriestabs-3',
			'supermaket-products',
			'supermaket-categories-tabs',
		];
	
		return $array; 
	}
}	

if(!function_exists('puca_elements_ready_countdown_timer')) {
	function puca_elements_ready_countdown_timer() {
		$array = [
			'product-count-down'
		];
	
		return $array; 
	}
}	
 
if(!function_exists('puca_elements_ready_layzyload_image')) {
	function puca_elements_ready_layzyload_image() {
		$array = [
			'product-flash-sales', 
			'product-count-down',
			'brands', 
			'products',   
			'posts-grid',
			'our-team', 
			'instagram', 
			'product-category', 
			'product-tabs', 
			'testimonials',
			'product-categories-tabs',
			'list-categories-product',
			'custom-image-list-categories',
			'custom-image-list-tags',
			'product-list-tags',
			'product-count-down'
		];
	
		return $array; 
	}
}	

if(!function_exists('puca_elements_ready_instagram')) {
	function puca_elements_ready_instagram() {
		$array = [
			'instagram', 
		];
	
		return $array; 
	}
}
if(!function_exists('puca_elements_ready_counter_up')) {
	function puca_elements_ready_counter_up() {
		$array = [
			'supermaket2-counter', 
		];
	
		return $array; 
	}
}	