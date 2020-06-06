<?php 

if ( !function_exists('puca_tbay_private_size_image_setup') ) {
	function puca_tbay_private_size_image_setup() {

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		*/

		update_option('puca_avatar_post_carousel', 60, 60, true);
		add_image_size('puca_avatar_post_carousel', 60, 60, true); //(cropped)


		update_option('puca_avatar_post_carousel2', 100, 100, true);
		add_image_size('puca_avatar_post_carousel2', 100, 100, true); //(cropped)


		// Post Thumbnails Size
		set_post_thumbnail_size(570, 320, true); // Unlimited height, soft crop
		update_option('thumbnail_size_w', 570);
		update_option('thumbnail_size_h', 320);						

		update_option('medium_size_w', 555);
		update_option('medium_size_h', 360);

	}
	add_action( 'after_setup_theme', 'puca_tbay_private_size_image_setup' );
}
/*
* Remove config default media
*
*/
if(puca_tbay_get_global_config('config_media',false)) {
	remove_action( 'after_setup_theme', 'puca_tbay_private_size_image_setup' );
}

if ( !function_exists('puca_tbay_private_menu_setup') ) {
	function puca_tbay_private_menu_setup() {

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus( array(
			'primary' 			=> esc_html__( 'Primary Menu', 'puca' ),
			'mobile-menu' 		=> esc_html__( 'Mobile Menu','puca' ),
			'topmenu'  			=> esc_html__( 'Top Menu', 'puca' ),
			'nav-account'  		=> esc_html__( 'Nav Account', 'puca' ),
			'nav-category-img'  => esc_html__( 'Image Category Menu', 'puca' ),
			'footer-menu'  		=> esc_html__( 'Footer Menu', 'puca' ),
		) );

	}
	add_action( 'after_setup_theme', 'puca_tbay_private_menu_setup' );
}

/**
 *  Include Load Google Front
 */

if ( !function_exists('puca_fonts_url') ) {
	function puca_fonts_url() {
		/**
		 * Load Google Front
		 */

	    $fonts_url = '';

	    /* Translators: If there are characters in your language that are not
	    * supported by Montserrat, translate this to 'off'. Do not translate
	    * into your own language.
	    */
	    $OpenSans 		= _x( 'on', 'Open Sans: on or off', 'puca' );
	 
	    if ( 'off' !== $OpenSans ) {
	        $font_families = array();
	 
	        if ( 'off' !== $OpenSans ) {
	            $font_families[] = 'Open+Sans:300,400,400i,600,700,800';
	        }
			
	 
	        $query_args = array(
	            'family' => ( implode( '%7C', $font_families ) ),
	            'subset' => urlencode( 'latin,latin-ext' ),
	            'display' => urlencode( 'swap' ),
	        );
	 		
	 		$protocol = is_ssl() ? 'https:' : 'http:';
	        $fonts_url = add_query_arg( $query_args, $protocol .'//fonts.googleapis.com/css' );
	    }
	 
	    return esc_url_raw( $fonts_url );
	}
}

if ( !function_exists('puca_tbay_fonts_url') ) {
	function puca_tbay_fonts_url() {  
		$protocol 		  = is_ssl() ? 'https:' : 'http:';
		$show_typography  = puca_tbay_get_config('show_typography', false);
		$font_source 	  = puca_tbay_get_config('font_source', "1");
		$font_google_code = puca_tbay_get_config('font_google_code');
		if( !$show_typography ) {
			wp_enqueue_style( 'puca-theme-fonts', puca_fonts_url(), array(), null );
		} else if ( $font_source == "2" && !empty($font_google_code) ) {
			wp_enqueue_style( 'puca-theme-fonts', $font_google_code, array(), null );
		}
	}
	add_action('wp_enqueue_scripts', 'puca_tbay_fonts_url');
}
 

// hooks
if ( !function_exists('puca_tbay_private_enqueue_styles') ) {
    function puca_tbay_private_enqueue_styles() {
    	$suffix = (puca_tbay_get_config('minified_js', false)) ? '.min' : PUCA_MIN_JS;
    	wp_register_script( 'jquery-countdowntimer', PUCA_SCRIPTS . '/jquery.time-to' . $suffix . '.js', array( 'jquery' ), '1.2.1', true );

    }
}
add_action( 'wp_enqueue_scripts', 'puca_tbay_private_enqueue_styles', 50 );

/**
 * Register Sidebar
 *
 */
if ( !function_exists('puca_tbay_widgets_init') ) {
	function puca_tbay_widgets_init() {

		register_sidebar( array(
			'name'          => esc_html__( 'Topbar welcome Header 2', 'puca' ),
			'id'            => 'topbar-welcome',
			'description'   => esc_html__( 'Add widgets here to appear in Topbar welcome.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );	

		register_sidebar( array(
			'name'          => esc_html__( 'Product Top Shop Full Width', 'puca' ),
			'id'            => 'product-top-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );				

		register_sidebar( array(
			'name'          => esc_html__( 'Product Top Shop Multi Viewed', 'puca' ),
			'id'            => 'product-top-multi-viewed-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Product Canvas Sidebar', 'puca' ),
			'id'            => 'product-canvas-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );				

		register_sidebar( array(
			'name'          => esc_html__( 'Blog Top Search Sidebar Left,Right Main v4,v5', 'puca' ),
			'id'            => 'blog-top-search',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );					

		register_sidebar( array(
			'name'          => esc_html__( 'Blog Top Sidebar Left,Right Main v4', 'puca' ),
			'id'            => 'blog-top-sidebar1',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );				

		register_sidebar( array(
			'name'          => esc_html__( 'Blog Top Sidebar Left,Right Main v5', 'puca' ),
			'id'            => 'blog-top-sidebar2',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Blog Main V4,5 left sidebar', 'puca' ),
			'id'            => 'blog-left-sidebar-45',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		
		register_sidebar( array(
			'name'          => esc_html__( 'Blog Main V4,5 right sidebar', 'puca' ),
			'id'            => 'blog-right-sidebar-45',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar Default', 'puca' ),
			'id'            => 'sidebar-default',
			'description'   => esc_html__( 'Add widgets here to appear in your Sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		
		register_sidebar( array(
			'name'          => esc_html__( 'Blog left sidebar', 'puca' ),
			'id'            => 'blog-left-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Blog right sidebar', 'puca' ),
			'id'            => 'blog-right-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Product left sidebar', 'puca' ),
			'id'            => 'product-left-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Product right sidebar', 'puca' ),
			'id'            => 'product-right-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Newsletter Popup sidebar', 'puca' ),
			'id'            => 'newsletter-popup-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Footer', 'puca' ),
			'id'            => 'footer',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'puca' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		
	}
	add_action( 'widgets_init', 'puca_tbay_widgets_init' );
}

if ( !function_exists( 'puca_tbay_autocomplete_search' ) ) { 
    function puca_tbay_autocomplete_search() {
    	wp_enqueue_script('jquery-ui-autocomplete');
        if ( puca_tbay_get_global_config('autocomplete_search') ) {
            add_action( 'wp_ajax_puca_autocomplete_search', 'puca_tbay_autocomplete_suggestions' );
            add_action( 'wp_ajax_nopriv_puca_autocomplete_search', 'puca_tbay_autocomplete_suggestions' );
        }
    }
}
add_action( 'init', 'puca_tbay_autocomplete_search' );

if ( !function_exists( 'puca_tbay_autocomplete_suggestions' ) ) {
    function puca_tbay_autocomplete_suggestions() {
		// Query for suggestions
		$search_keyword  = $_REQUEST['term'];

		$args = array(
		    's'                   => $search_keyword,
		    'post_status'         => 'publish',
		    'orderby'         	  => 'relevance',
		    'posts_per_page'      => -1,
		    'ignore_sticky_posts' => 1,
		    'suppress_filters'    => false,
		);

		if ( isset($_REQUEST['post_type']) && $_REQUEST['post_type'] != 'all') {
        	$args['post_type'] = $_REQUEST['post_type'];
        } 

		if ( isset( $_REQUEST['category'] ) && !empty($_REQUEST['category']) ) {
		    $args['tax_query'] = array(
		        'relation' => 'AND',
		        array(
		            'taxonomy' => 'product_cat',
		            'field'    => 'slug',
		            'terms'    => $_REQUEST['category']
		        ) );
		}

		if ( version_compare( WC()->version, '2.7.0', '<' ) ) {
		    $args['meta_query'] = array(
		        array(
			        'key'     => '_visibility',
			        'value'   => array( 'search', 'visible' ),
			        'compare' => 'IN'
		        ),
		    );
		}else{
		    $product_visibility_term_ids = wc_get_product_visibility_term_ids();
		    $args['tax_query'][] = array(
		        'taxonomy' => 'product_visibility',
		        'field'    => 'term_taxonomy_id',
		        'terms'    => $product_visibility_term_ids['exclude-from-search'],
		        'operator' => 'NOT IN',
		    );
		}

		$posts = get_posts( $args );

		$style = '';    
		if ( isset($_REQUEST['style']) ) {
		    $style  = $_REQUEST['style'];
		}
        $suggestions = array();
        $show_image = puca_tbay_get_config('show_search_product_image', true);
        $show_price = puca_tbay_get_config('search_type') == 'product' ? puca_tbay_get_config('show_search_product_price') : false;
        $number 	= puca_tbay_get_config('search_max_number_results', 5); 
        global $post;
        $count = count($posts);
        
        $view_all = ( ($count - $number ) > 0 ) ? true : false;
        $index = 0;
        foreach ($posts as $post): setup_postdata($post);
            
            if( $index == $number ) break;

            $suggestion = array();
            $suggestion['label'] = esc_html($post->post_title);
            $suggestion['style'] = $style;
            $suggestion['link'] = get_permalink();
            $suggestion['result'] = $count.' '. esc_html__('result found with', 'puca') .' "'.$search_keyword.'" ';

            $suggestion['view_all'] = $view_all;

            if ( $show_image && has_post_thumbnail( $post->ID ) ) {
                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'woocommerce_thumbnail' );
                $suggestion['image'] = $image[0];
            } else {
                $suggestion['image'] = '';
            }
            if ( $show_price ) {
            	$product = new WC_Product( get_the_ID() );
                $suggestion['price'] = $product->get_price_html();
            } else {
                $suggestion['price'] = '';
            }

            $suggestions[]= $suggestion;

            $index++;
        endforeach;
        $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
        echo trim($response);
     
        exit;
    }
}


if ( !function_exists('puca_tbay_get_blog_layout_configs') ) {
	function puca_tbay_get_blog_layout_configs() {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
    } else {
			$left2 = puca_tbay_get_config('blog_'.$page.'_left_sidebar45');
			$right2 = puca_tbay_get_config('blog_'.$page.'_right_sidebar45'); 	
    }

		$left = puca_tbay_get_config('blog_'.$page.'_left_sidebar');
		$right = puca_tbay_get_config('blog_'.$page.'_right_sidebar');



		if ( !is_singular( 'post' ) ) {

				$blog_archive_layout =  ( isset($_GET['blog_archive_layout']) )  ? $_GET['blog_archive_layout'] : puca_tbay_get_config('blog_archive_layout', 'main-v1');

				if( isset($blog_archive_layout) ) {

	        	switch ( $blog_archive_layout ) {
							case 'main-v1':
						 		$configs['main'] 			= array( 'class' => 'style-grid archive-full' );
						 		$configs['columns'] 		= 4;
						 		$configs['image_sizes'] 	= 'post-thumbnail';
						 		break;							 	
						 	case 'main-v2':
						 		$configs['main'] 			= array( 'class' => 'style-grid archive-full style-center' );
						 		$configs['columns'] 		= 1;
						 		$configs['image_sizes'] 	= 'full';
						 		break;							 	
						 	case 'main-v3':
						 		$configs['main'] 			= array( 'class' => 'archive-full' );
						 		$configs['columns'] 		= 3;
						 		$configs['image_sizes'] 	= 'post-thumbnail';
						 		break;							 	
						 	case 'main-v4':
						 		$configs['main'] 			= array( 'class' => 'archive-full' );
						 		$configs['container_full'] 	= true;
						 		$configs['columns'] 		= 4;
						 		$configs['image_sizes'] 	= 'post-thumbnail';
						 		break;							 	
						 	case 'left-main-v1':
						 		$configs['left'] 			= array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
						 		$configs['main'] 			= array( 'class' => 'style-grid col-xs-12 col-md-12 col-lg-9' );
						 		$configs['columns'] 		= 3;
						 		$configs['image_sizes'] 	= 'post-thumbnail';
						 		break;							 	
						 	case 'main-right-v1':
						 		$configs['right'] 			= array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
						 		$configs['main'] 			= array( 'class' => 'style-grid col-xs-12 col-md-12 col-lg-9' );
						 		$configs['columns'] 		= 3;
						 		$configs['image_sizes'] 	= 'post-thumbnail';
						 		break;			
						 	case 'left-main-v2':
						 		$configs['left'] 			= array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
						 		$configs['main'] 			= array( 'class' => 'style-grid style-center col-xs-12 col-md-12 col-lg-9' );
						 		$configs['columns'] 		= 1;
						 		$configs['image_sizes'] 	= 'full';
						 		break;		
						 	case 'main-right-v2':
						 		$configs['right'] 			= array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
						 		$configs['main'] 			= array( 'class' => 'style-grid style-center col-xs-12 col-md-12 col-lg-9' );
						 		$configs['columns'] 		= 1;
						 		$configs['image_sizes'] 	= 'full';
						 		break;		 	
						 	case 'left-main-v3':
						 		$configs['left'] 				= array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
						 		$configs['main'] 				= array( 'class' => 'style-list col-xs-12 col-md-12 col-lg-9' );
						 		$configs['columns'] 			= 1;
						 		$configs['image_sizes'] 		= 'post-thumbnail';
						 		break;		
						 	case 'main-right-v3':
						 		$configs['right'] 				= array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
						 		$configs['main'] 				= array( 'class' => 'style-list col-xs-12 col-md-12 col-lg-9' );
						 		$configs['columns'] 			= 1;
						 		$configs['image_sizes'] 		= 'post-thumbnail';		
								break;
						 	case 'left-main-v4':
						 		$configs['left'] 				= array( 'sidebar' => $left2, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
						 		$configs['main']	 			= array( 'class' => 'style-gird col-xs-12 col-md-12 col-lg-9' );
						 		$configs['blog_top_sidebar1'] 	= true;
						 		$configs['blog_top_search'] 	= true;
						 		$configs['columns'] 			= 3;
						 		$configs['image_sizes'] 		= 'post-thumbnail';	
						 		break;		
						 	case 'main-right-v4':
						 		$configs['right'] 				= array( 'sidebar' => $right2,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
						 		$configs['main'] 				= array( 'class' => 'style-gird col-xs-12 col-md-12 col-lg-9' );
						 		$configs['blog_top_sidebar1'] 	= true;
						 		$configs['blog_top_search'] 	= true;
						 		$configs['columns'] 			= 3;
						 		$configs['image_sizes'] 		= 'post-thumbnail';	
						 		break;								 	
						 	case 'left-main-v5':
						 		$configs['left'] 				= array( 'sidebar' => $left2, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
						 		$configs['main'] 				= array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
						 		$configs['blog_top_sidebar2'] 	= true;
						 		$configs['blog_top_search'] 	= true;
						 		$configs['columns'] 			= 3;
						 		$configs['image_sizes'] 		= 'post-thumbnail';	
						 		break;		
						 	case 'main-right-v5':
						 		$configs['right'] 				= array( 'sidebar' => $right2,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
						 		$configs['main'] 				= array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
						 		$configs['blog_top_sidebar2'] 	= true;
						 		$configs['blog_top_search'] 	= true;
						 		$configs['columns'] 			= 3;
						 		$configs['image_sizes'] 		= 'post-thumbnail';	
						 		break;		
						 	default:
						 		$configs['main'] = array( 'class' => 'archive-full' );
						 		break;
	        }

	      } 

		} else {
				$blog_single_layout =	( isset($_GET['blog_single_layout']) ) ? $_GET['blog_single_layout']  :  puca_tbay_get_config('blog_single_layout', 'left-main');

				if( isset($blog_single_layout) ) {

					switch ( $blog_single_layout ) {
					 	case 'left-main':
					 		$configs['left'] = array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
					 		$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
					 		break;
					 	case 'main-right':
					 		$configs['right'] = array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
					 		$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
					 		break;
				 		case 'main':
				 			$configs['main'] = array( 'class' => 'col-xs-12 col-md-12' );
				 			break;
			 			case 'left-main-right':
			 				$configs['left'] = array( 'sidebar' => $left,  'class' => 'col-xs-12 col-md-12 col-lg-3'  );
					 		$configs['right'] = array( 'sidebar' => $right, 'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
					 		$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-6' );
			 				break;
					 	default:
					 		$configs['main'] = array( 'class' => 'col-xs-12 col-md-12' );
					 		break;
					 }

				}
		}


		return $configs; 
	}
}