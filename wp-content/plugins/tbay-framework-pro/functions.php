<?php
/**
 * functions for tbay framework
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Framework Pro
 */


if ( !function_exists('tbay_framework_widgets_puca_new_version') ) {
    function tbay_framework_widgets_puca_new_version() {

        if( ( defined('PUCA_THEME_VERSION') && (float)PUCA_THEME_VERSION >= 1.5 )  ) {
           
            if(class_exists('puca_Tbay_Top_Rate_Widget')) {
                register_widget( 'puca_Tbay_Top_Rate_Widget' );
            }

            if(class_exists('Puca_Tbay_Woo_Carousel')) {
                register_widget( 'Puca_Tbay_Woo_Carousel' );
            }

            if(class_exists('puca_Tbay_Custom_Menu')) {
                register_widget( 'puca_Tbay_Custom_Menu' );
            }            

            if(class_exists('Puca_Tbay_List_Categories')) {
                register_widget( 'Puca_Tbay_List_Categories' );
            }            

            if(class_exists('puca_Tbay_Popular_Post')) {
                register_widget( 'puca_Tbay_Popular_Post' );
            }            

            if(class_exists('puca_Tbay_Popular_Post2')) {
                register_widget( 'puca_Tbay_Popular_Post2' );
            }            

            if(class_exists('puca_Tbay_Posts')) {
                register_widget( 'puca_Tbay_Posts' );
            }

            if(class_exists('puca_Tbay_Recent_Comment')) {
                register_widget( 'puca_Tbay_Recent_Comment' );
            }           

            if(class_exists('puca_Tbay_Recent_Post')) {
                register_widget( 'puca_Tbay_Recent_Post' );
            }            

            if(class_exists('puca_Tbay_Single_Image')) {
                register_widget( 'puca_Tbay_Single_Image' );
            }            

            if(class_exists('puca_Tbay_Socials_Widget')) {
                register_widget( 'puca_Tbay_Socials_Widget' );
            }

            if(class_exists('puca_Tbay_Featured_Video_Widget')) {
                register_widget( 'puca_Tbay_Featured_Video_Widget' );
            }            

            if(class_exists('Tbaybase_Tbay_Popup_Newsletter')) {
                register_widget( 'Tbaybase_Tbay_Popup_Newsletter' );
            }            

            if(class_exists('TbayFramework_Widget_Instagram')) {
                register_widget( 'TbayFramework_Widget_Instagram' );
            }

        }
    }

    add_action( 'widgets_init', 'tbay_framework_widgets_puca_new_version', 30 );
}

if ( !function_exists('tbay_framework_puca_new_version') ) {
    function tbay_framework_puca_new_version() {

        if( ( defined('PUCA_THEME_VERSION') && (float)PUCA_THEME_VERSION >= 1.5 )  ) {
           
            if ( class_exists( 'WooCommerce' ) ) {
                remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
            }    
        }
    }

    add_action( 'init', 'tbay_framework_puca_new_version', 30 );
}

if( ! function_exists( 'tbay_framework_register_post_types' ) ) {
    function tbay_framework_register_post_types() {

        $types = array('footer', 'brand', 'testimonial', 'megamenu');

        if( defined('TBAY_WOOCOMMERCE_CUSTOM_TAB_ACTIVED') && TBAY_WOOCOMMERCE_CUSTOM_TAB_ACTIVED ) {
            array_push($types,'customtab');
        }
        

        $post_types = apply_filters( 'tbay_framework_register_post_types', $types);
        if ( !empty($post_types) ) {
            foreach ($post_types as $post_type) {
                if ( file_exists( TBAY_FRAMEWORK_DIR . 'classes/post-types/'.$post_type.'.php' ) ) {
                    require TBAY_FRAMEWORK_DIR . 'classes/post-types/'.$post_type.'.php';
                }
            }
        }
    }
}

if( ! function_exists( 'tbay_framework_widget_init' ) ) {
    function tbay_framework_widget_init() {
    	$widgets = apply_filters( 'tbay_framework_register_widgets', array() );
    	if ( !empty($widgets) ) {
    		foreach ($widgets as $widget) {
    			if ( file_exists( TBAY_FRAMEWORK_DIR . 'classes/widgets/'.$widget.'.php' ) ) {
    				require TBAY_FRAMEWORK_DIR . 'classes/widgets/'.$widget.'.php';
    			}
    		}
    	}
    }
}


if( ! function_exists( 'tbay_framework_get_widget_locate' ) ) {
    function tbay_framework_get_widget_locate( $name, $plugin_dir = TBAY_FRAMEWORK_DIR ) {
    	$template = '';
    	
    	// Child theme
    	if ( ! $template && ! empty( $name ) && file_exists( get_stylesheet_directory() . "/widgets/{$name}" ) ) {
    		$template = get_stylesheet_directory() . "/widgets/{$name}";
    	}

    	// Original theme
    	if ( ! $template && ! empty( $name ) && file_exists( get_template_directory() . "/widgets/{$name}" ) ) {
    		$template = get_template_directory() . "/widgets/{$name}";
    	}

    	// Plugin
    	if ( ! $template && ! empty( $name ) && file_exists( $plugin_dir . "/templates/widgets/{$name}" ) ) {
    		$template = $plugin_dir . "/templates/widgets/{$name}";
    	}

    	// Nothing found
    	if ( empty( $template ) ) {
    		throw new Exception( "Template /templates/widgets/{$name} in plugin dir {$plugin_dir} not found." );
    	}

    	return $template;
    }
}


if( ! function_exists( 'tbay_framework_display_svg_image' ) ) {
    function tbay_framework_display_svg_image( $url, $class = '', $wrap_as_img = true, $attachment_id = null ) {
        if ( ! empty( $url ) && is_string( $url ) ) {

            // we try to inline svgs
            if ( substr( $url, - 4 ) === '.svg' ) {

                //first let's see if we have an attachment and inline it in the safest way - with readfile
                //include is a little dangerous because if one has short_open_tags active, the svg header that starts with <? will be seen as PHP code
                if ( ! empty( $attachment_id ) && false !== @readfile( get_attached_file( $attachment_id ) ) ) {
                    //all good
                } elseif ( false !== ( $svg_code = get_transient( md5( $url ) ) ) ) {
                    //now try to get the svg code from cache
                    echo $svg_code;
                } else {

                    //if not let's get the file contents using WP_Filesystem
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );

                    WP_Filesystem();

                    global $wp_filesystem;
                    
                    $svg_code = $wp_filesystem->get_contents( $url );

                    if ( ! empty( $svg_code ) ) {
                        set_transient( md5( $url ), $svg_code, 12 * HOUR_IN_SECONDS );

                        echo $svg_code;
                    }
                }

            } elseif ( $wrap_as_img ) {

                if ( ! empty( $class ) ) {
                    $class = ' class="' . $class . '"';
                }

                echo '<img src="' . $url . '"' . $class . ' alt="" />';

            } else {
                echo $url;
            }
        }
    }
}

if( ! function_exists( 'tbay_framework_get_file_contents' ) ) {
    function tbay_framework_get_file_contents($url, $use_include_path, $context) {
    	return @file_get_contents($url, false, $context);
    }
}

if( ! function_exists( 'tbay_framework_scrape_instagram' ) ) {
    function tbay_framework_scrape_instagram( $username ) {

      $username = trim( strtolower( $username ) );
        switch ( substr( $username, 0, 1 ) ) {
            case '#':
                $url              = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $username );
                $transient_prefix = 'h';
                break;
            default:
                $url              = 'https://instagram.com/' . str_replace( '@', '', $username );
                $transient_prefix = 'u';
                break;
        }
        if ( false === ( $instagram = get_transient( 'insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ) ) ) ) {
            $remote = wp_remote_get( $url );
            if ( is_wp_error( $remote ) ) {
                return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'tbay-framework' ) );
            }
            if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
                return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'tbay-framework' ) );
            }
            $shards      = explode( 'window._sharedData = ', $remote['body'] );
            $insta_json  = explode( ';</script>', $shards[1] );
            $insta_array = json_decode( $insta_json[0], true );
            if ( ! $insta_array ) {
                return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'tbay-framework' ) );
            }
            if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
                $images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
            } elseif ( isset( $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
                $images = $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
            } else {
                return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'tbay-framework' ) );
            }
            if ( ! is_array( $images ) ) {
                return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'tbay-framework' ) );
            }
            $instagram = array();
            foreach ( $images as $image ) {
                if ( true === $image['node']['is_video'] ) {
                    $type = 'video';
                } else {
                    $type = 'image';
                }
                $caption = __( 'Instagram Image', 'tbay-framework' );
                if ( ! empty( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
                    $caption = wp_kses( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'], array() );
                }
                $instagram[] = array(
                    'description' => $caption,
                    'link'        => trailingslashit( '//instagram.com/p/' . $image['node']['shortcode'] ),
                    'time'        => $image['node']['taken_at_timestamp'],
                    'comments'    => $image['node']['edge_media_to_comment']['count'],
                    'likes'       => $image['node']['edge_liked_by']['count'],
                    'thumbnail'   => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][0]['src'] ),
                    'small'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][2]['src'] ),
                    'large'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][3]['src'] ),
                    'original'    => preg_replace( '/^https?\:/i', '', $image['node']['display_url'] ),
                    'type'        => $type,
                );
            } // End foreach().
            // do not set an empty transient - should help catch private or empty accounts.
            if ( ! empty( $instagram ) ) {
                $instagram = base64_encode( serialize( $instagram ) );
                set_transient( 'insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', MINUTE_IN_SECONDS * 15 ) );
            }
        }
        if ( ! empty( $instagram ) ) {
            return unserialize( base64_decode( $instagram ) );
        } else {
            return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'tbay-framework' ) );
        }

    }
}

if( ! function_exists( 'tbay_framework_time_ago' ) ) {
    function tbay_framework_time_ago($distant_timestamp, $max_units = 3) {
        $i = 0;
        
        $time = time() - $distant_timestamp; // to get the time since that moment
        $tokens = array(
            31536000    => esc_html__('year', 'tbay-framework'),
            2592000     => esc_html__('month', 'tbay-framework'),
            604800      => esc_html__('week', 'tbay-framework'),
            86400       => esc_html__('day', 'tbay-framework'),
            3600        => esc_html__('hour', 'tbay-framework'),
            60          => esc_html__('minute', 'tbay-framework'),
            1           => esc_html__('second', 'tbay-framework')
        );

        $responses = array();
        while ($i < $max_units) {
            foreach ($tokens as $unit => $text) {
                if ($time < $unit) {
                    continue;
                }
                $i++;
                $numberOfUnits = floor($time / $unit);

                array_push($responses, $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? esc_html__('s', 'tbay-framework') : ''));
                $time -= ($unit * $numberOfUnits);
                break;
            }
        }

        if (!empty($responses)) {
            return implode(', ', $responses) . esc_html__(' ago', 'tbay-framework');
        }

        return esc_html__('Just now', 'tbay-framework');
    }
}

if( ! function_exists( 'tbay_framework_images_only' ) ) {
    function tbay_framework_images_only( $media_item ) {
        if ( $media_item['type'] == 'image' )
            return true;
        return false;
    }
}

if( ! function_exists( 'tbay_framework_remove_image_srcset' ) ) {
    function tbay_framework_remove_image_srcset( $media_item ) {
        add_filter( 'wp_calculate_image_srcset', '__return_false' );
    }
    add_action( 'init', 'tbay_framework_remove_image_srcset', 10 );
}


if( ! function_exists( 'tbay_framework_product_add_metaboxes' ) ) {
    add_action( 'add_meta_boxes', 'tbay_framework_product_add_metaboxes', 50 );
    function tbay_framework_product_add_metaboxes() {

        if( function_exists( 'urna_size_guide_metabox_output' ) ) {
            //Add metaboxes size guide to product
            add_meta_box( 'woocommerce-product-size-guide-images', esc_html__( 'Product Size Guide (Only Variable product)', 'tbay-framework' ), 'urna_size_guide_metabox_output', 'product', 'side', 'low' );
        }       

        if( function_exists( 'urna_swatch_attribute_template' ) ) {
            add_meta_box( 'woocommerce-product-swatch-attribute', esc_html__( 'Swatch attribute to display', 'tbay-framework' ), 'urna_swatch_attribute_template', 'product', 'side' );    
        }    

        if( function_exists( 'urna_single_select_single_layout_template' ) ) {
            add_meta_box( 'woocommerce-product-single-layout', esc_html__( 'Select Single Product Layout', 'tbay-framework' ), 'urna_single_select_single_layout_template', 'product', 'side' );  
        }

    }
}