<?php

//remove heading tab single product
if(!function_exists('puca_product_description_heading')){
  add_filter('woocommerce_product_description_heading',
  'puca_product_description_heading');

  function puca_product_description_heading() {
      return '';
  }
}

// share box
if ( !function_exists('puca_tbay_woocommerce_share_box') ) {
    function puca_tbay_woocommerce_share_box() {
        if ( puca_tbay_get_config('enable_code_share',false)  && puca_tbay_get_config('show_product_social_share', false) ) {
            ?>
              <div class="tbay-woo-share">
                <p><?php esc_html_e('Share: ', 'puca'); ?></p>
                <div class="addthis_inline_share_toolbox"></div>
              </div>
            <?php
        }
    }
    add_filter( 'woocommerce_single_product_summary', 'puca_tbay_woocommerce_share_box', 100 );
}


/*Hook class single product*/

// Number of products per page
if ( !function_exists('puca_tbay_woocommerce_class_single_product') ) {
    function puca_tbay_woocommerce_class_single_product($styles) {
        global $product;
        $attachment_ids = $product->get_gallery_image_ids();
        $count = count( $attachment_ids);

        $images_layout   =  apply_filters( 'woo_images_layout_single_product', 10, 2 );

        $active_stick   = '';

        if( isset($images_layout) ) {

          if( isset($count) && $images_layout == 'stick' && ($count > 0) ) {
            $active_stick = 'active-stick';
          }

          switch ($images_layout) {
            case 'vertical-left':
              $styles = 'style-vertical';
              break;                
            case 'vertical-right':
              $styles = 'style-vertical vertical-right';
              break;          
            case 'horizontal-bottom':
              $styles = 'style-horizontal';
              break;             
            case 'horizontal-top':
              $styles = 'style-horizontal horizontal-top';
              break;          
            case 'stick':       
            case 'gallery':
            case 'slide':
            case 'carousel':
              $styles = 'style-'.$images_layout;
              break;
            default:
              $styles = 'style-vertical';
              break;
          }
        }

        $styles .= ' '.$active_stick;

        return $styles;
    }
    add_filter( 'woo_class_single_product', 'puca_tbay_woocommerce_class_single_product' );
}

/*coder swallow2603*/
if ( !function_exists('puca_tbay_woocommerce_images_layout_product') ) {
    function puca_tbay_woocommerce_images_layout_product($images_layout) {
          $sidebar_configs        = puca_tbay_get_woocommerce_layout_configs();
          $thumbnail_image        = puca_tbay_get_config('thumbnail_image', 'default');

          if ( isset($_GET['thumbnail_image']) ) {
              $images_layout = $_GET['thumbnail_image'];
          }
          elseif($thumbnail_image == 'default' && isset($sidebar_configs['thumbnail'])) {
              $images_layout = $sidebar_configs['thumbnail'];

          }else {
              $images_layout = $thumbnail_image;
          }  

          return $images_layout;
    }
    add_filter( 'woo_images_layout_single_product', 'puca_tbay_woocommerce_images_layout_product' );
}



if ( !function_exists('puca_tbay_woocommerce_tabs_position_product') ) {
    function puca_tbay_woocommerce_tabs_position_product($tabs_position) {

        if ( is_singular( 'product' ) ) {
          $sidebar_configs        = puca_tbay_get_woocommerce_layout_configs();
 
          $single_tabs_position   = puca_tbay_get_config('single_tabs_position', 'default');

          if ( isset($_GET['tabs_position']) ) {
              $tabs_position = $_GET['tabs_position'];
          }
          elseif($single_tabs_position == 'default' && isset($sidebar_configs['tabs_position'])) {
              $tabs_position = $sidebar_configs['tabs_position'];

          }else {
              $tabs_position = $single_tabs_position;
          }  
          
          return $tabs_position;
        }
    }
    add_filter( 'woo_tabs_position_layout_single_product', 'puca_tbay_woocommerce_tabs_position_product' );
}


/**
* Function For Multi Layouts Single Product 
*/
//-----------------------------------------------------
/**
 * Output the product images.
 *
 * @subpackage  Product/images
 */

function woocommerce_show_product_images() { 
    $images_layout   =  apply_filters( 'woo_images_layout_single_product', 10, 2 );

    if( isset($images_layout) ) {
      if( $images_layout == 'default' || $images_layout == '' ) {
        wc_get_template( 'single-product/product-image.php' );
      } else {
        wc_get_template( 'single-product/images/product-image-'.$images_layout.'.php' );
      }
    }

}


function woocommerce_show_product_thumbnails() {

  $images_layout   =  apply_filters( 'woo_images_layout_single_product', 10, 2 );


  if( isset($images_layout) ) {

    if( $images_layout == 'default' ||  $images_layout == 'horizontal-top' ||  $images_layout == 'horizontal-bottom' || $images_layout == 'vertical-left' || $images_layout == 'vertical-right') {
        wc_get_template( 'single-product/product-thumbnails.php' );
    } else {
        wc_get_template( 'single-product/thumbnails/product-thumbnails-'.$images_layout.'.php' );
    }
  }

}

/**
* Function For Multi Layouts Single Product 
*/
//-----------------------------------------------------
/**
 * Output the product images.
 *
 * @subpackage  Product/images
 */

if ( !function_exists('puca_remove_hook_single_product') ) {
  function puca_remove_hook_single_product() {

      $images_layout   =  apply_filters( 'woo_images_layout_single_product', 10, 2 );

      $tabs_position   =  apply_filters( 'woo_tabs_position_layout_single_product', 10, 2 );

      if( isset($tabs_position) ) {


        switch ($tabs_position) {
            case 'bottom':
                break;          

            case 'right':
                remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );              

                add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 100 );

                wp_enqueue_script( 'hc-sticky' );

                break;

            default:

        }

      }

  }
  add_action('woocommerce_before_single_product', 'puca_remove_hook_single_product',30);
}

/*product tabs right body class*/
if ( ! function_exists( 'puca_woo_product_body_class_tabs_right' ) ) {
  function puca_woo_product_body_class_tabs_right( $classes ) {

    $tabs_position   =  apply_filters( 'woo_tabs_position_layout_single_product', 10, 2 );


    if( isset($tabs_position) && $tabs_position == 'right' ) {
      $classes[] = 'tbay-body-product-tabs-right';
    }

    return $classes;

  }
  add_filter( 'body_class', 'puca_woo_product_body_class_tabs_right',99 );
}



if ( !function_exists('puca_tbay_woocommerce_tabs_style_product') ) {
    function puca_tbay_woocommerce_tabs_style_product($tabs_layout) {

        if ( is_singular( 'product' ) ) {
          $sidebar_configs  = puca_tbay_get_woocommerce_layout_configs();
          $tabs_style       = puca_tbay_get_config('style_single_tabs_style', 'default');

          if ( isset($_GET['tabs_product']) ) {
              $tabs_layout = $_GET['tabs_product'];
          }
          elseif($tabs_style == 'default' && isset($sidebar_configs['tabs'])) {
              $tabs_layout = $sidebar_configs['tabs'];

          }else { 
              $tabs_layout = $tabs_style;
          }  

          return $tabs_layout;
        }
    }
    add_filter( 'woo_tabs_style_single_product', 'puca_tbay_woocommerce_tabs_style_product' );
}



/**
* Function For Multi Layouts Single Product 
*/
//-----------------------------------------------------
/**
 * Output the product tabs.
 *
 * @subpackage  Product/Tabs
 */
if ( !function_exists('woocommerce_output_product_data_tabs') ) {
  function woocommerce_output_product_data_tabs() {
      $tabs_layout   =  apply_filters( 'woo_tabs_style_single_product', 10, 2 );

      if( isset($tabs_layout) ) {

        if( $tabs_layout == 'default' ||  $tabs_layout == 'tbhorizontal') {
          wc_get_template( 'single-product/tabs/tabs.php' );
        } else {
          wc_get_template( 'single-product/tabs/tabs-'.$tabs_layout.'.php' );
        }
      }
  }
}


/*Add video to product detail*/
if ( !function_exists('puca_tbay_woocommerce_add_video_field') ) {
  add_action( 'woocommerce_product_options_general_product_data', 'puca_tbay_woocommerce_add_video_field' );

  function puca_tbay_woocommerce_add_video_field(){

    $args = apply_filters( 'puca_tbay_woocommerce_simple_url_video_args', array(
        'id' => '_video_url',
        'label' => esc_html__('Featured Video URL', 'puca'),
        'placeholder' => esc_html__('Video URL', 'puca'),
        'desc_tip' => true,
        'description' => esc_html__('Enter the video url at https://vimeo.com/ or https://www.youtube.com/', 'puca'))
    );

    echo '<div class="options_group">';

    woocommerce_wp_text_input( $args ) ;

    echo '</div>';
  }
}

if ( !function_exists('puca_tbay_save_video_url') ) {
  add_action( 'woocommerce_process_product_meta', 'puca_tbay_save_video_url', 10, 2 );
  function puca_tbay_save_video_url( $post_id, $post ) {
      if ( isset( $_POST['_video_url'] ) ) {
          update_post_meta( $post_id, '_video_url', esc_attr( $_POST['_video_url'] ) );
      }
  }
}

if ( !function_exists('puca_tbay_VideoUrlType') ) {
  function puca_tbay_VideoUrlType($url) {


      $yt_rx = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
      $has_match_youtube = preg_match($yt_rx, $url, $yt_matches);


      $vm_rx = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';
      $has_match_vimeo = preg_match($vm_rx, $url, $vm_matches);


      //Then we want the video id which is:
      if($has_match_youtube) {
          $video_id = $yt_matches[5]; 
          $type = 'youtube';
      }
      elseif($has_match_vimeo) {
          $video_id = $vm_matches[5];
          $type = 'vimeo';
      }
      else {
          $video_id = 0;
          $type = 'none';
      }


      $data['video_id'] = $video_id;
      $data['video_type'] = $type;

      return $data;
  }
}

if ( !function_exists('puca_tbay_get_video_product') ) {
  add_action( 'tbay_product_video', 'puca_tbay_get_video_product', 10 );
  function  puca_tbay_get_video_product() {
    global $product;
 

    if( get_post_meta( $product->get_id(), '_video_url', true ) ) {
      $video = puca_tbay_VideoUrlType(get_post_meta( $product->get_id(), '_video_url', true ));

      if( $video['video_type'] == 'youtube' ) {
        $url  = 'https://www.youtube.com/embed/'.$video['video_id'].'?autoplay=1';
        $icon = '<i class="fa fa-youtube-play" aria-hidden="true"></i>'.esc_html__('View Video','puca');

      }elseif(( $video['video_type'] == 'vimeo' )) {
        $url = 'https://player.vimeo.com/video/'.$video['video_id'].'?autoplay=1';
        $icon = '<i class="fa fa-vimeo-square" aria-hidden="true"></i>'.esc_html__('View Video','puca');

      }

    }

    ?>

    <?php if( !empty($url) ) : ?>

      <div class="modal fade" id="productvideo">
        <div class="modal-dialog">
          <div class="modal-content tbay-modalContent">

            <div class="modal-body">
              
              <div class="close-button">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="embed-responsive embed-responsive-16by9">
                          <iframe class="embed-responsive-item"></iframe>
              </div>
            </div>

          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

      <button type="button" class="tbay-modalButton" data-toggle="modal" data-tbaySrc="<?php echo esc_attr($url); ?>" data-tbayWidth="640" data-tbayHeight="480" data-target="#productvideo"  data-tbayVideoFullscreen="true"><?php echo trim($icon); ?></button>

    <?php endif; ?>
  <?php
  }
}



/*product time countdown*/
if(!function_exists('puca_woo_product_single_time_countdown')){

    add_action( 'woocommerce_before_single_product', 'puca_woo_product_single_time_countdown', 20 );

    function puca_woo_product_single_time_countdown() {

        global $product;

        $style_countdown   = puca_tbay_get_config('show_product_countdown',false);

        if ( isset($_GET['countdown']) ) {
            $countdown = $_GET['countdown'];
        }else {
            $countdown = $style_countdown;
        }  

        if(!$countdown || !$product->is_on_sale() ) {
          return '';
        }


        wp_enqueue_script( 'jquery-countdowntimer' );
        $time_sale = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
        $_id = puca_tbay_random_key();
        ?>
        <?php if ( $time_sale ): ?>
            <div class="container tbay-time-wrapper">
              <div class="time tbay-time">
                  <div class="title"><?php esc_html_e('Sale Countdown','puca'); ?></div>
                  <div class="tbay-countdown" data-id="<?php echo esc_attr($_id); ?>-<?php echo esc_attr($product->get_id()); ?>" id="countdown-<?php echo esc_attr($_id); ?>-<?php echo esc_attr($product->get_id()); ?>" data-time="timmer" data-days="<?php esc_html_e('Days','puca'); ?>" data-hours="<?php esc_html_e('Hours','puca'); ?>"  data-mins="<?php esc_html_e('Mins','puca'); ?>" data-secs="<?php esc_html_e('Secs','puca'); ?>"
                     data-date="<?php echo date('m', $time_sale).'-'.date('d', $time_sale).'-'.date('Y', $time_sale).'-'. date('H', $time_sale) . '-' . date('i', $time_sale) . '-' .  date('s', $time_sale) ; ?>">
                </div>
              </div> 
            </div> 
        <?php endif; ?> 
        <?php
    }
}

/*product nav*/
if ( !function_exists('puca_render_product_nav') ) {
  function puca_render_product_nav($post, $position){
      if($post){
          $product = wc_get_product($post->ID);
          $img = '';
          if(has_post_thumbnail($post)){
              $img = get_the_post_thumbnail($post, 'woocommerce_gallery_thumbnail');
          }

          $img_left = $img_right = '';
          if( $position == 'left' ) {
            $img_left = $img;
          } elseif( $position == 'right' ) {
            $img_right = $img;
          }

          $link = get_permalink($post);
          echo "<div class='". esc_attr( $position ) ." psnav'>";
          echo "<a class='img-link' href=". esc_url($link) .">";
           echo trim($img_left);   
          echo "</a>"; 
          echo "  <div class='product_single_nav_inner single_nav'>
                      <a href=". esc_url($link) .">
                          <span class='name-pr'>". esc_html($post->post_title) ."</span>
                      </a>
                  </div>";
          echo "<a class='img-link' href=". esc_url($link) .">";        
            echo trim($img_right);
          echo "</a>"; 
          echo "</div>";
      }
  }
}

if ( !function_exists('puca_woo_product_nav') ) {
  function puca_woo_product_nav(){
        if ( puca_tbay_get_config('show_product_nav', false) ) {
            $prev = get_previous_post();
            $next = get_next_post();

            echo '<div class="product-nav pull-right">';  
            echo '<div class="link-images visible-lg">';
            puca_render_product_nav($prev, 'left');
            puca_render_product_nav($next, 'right');
            echo '</div>';

            echo '</div>';
        }
  }
  add_action( 'woocommerce_before_single_product', 'puca_woo_product_nav', 1 );
}


if(!function_exists('puca_woo_one_page_cart_button_text')){
  function puca_woo_one_page_cart_button_text() {
    return esc_html__( 'Shop Now', 'puca' );
  }
}

if ( !function_exists('puca_tbay_woocommerce_product_menu_bar') ) {
    function puca_tbay_woocommerce_product_menu_bar($menu_bar) {
          $menu_bar   = puca_tbay_get_config('show_product_menu_bar', false);

          if ( isset($_GET['product_menu_bar']) ) {
              $menu_bar = $_GET['product_menu_bar'];
          }

          return $menu_bar;
    }
    add_filter( 'woo_product_menu_bar', 'puca_tbay_woocommerce_product_menu_bar' );
}

/*product one page*/
if(!function_exists('puca_woo_product_single_one_page')){
    if(!wp_is_mobile() ) {
      add_action( 'woocommerce_before_single_product', 'puca_woo_product_single_one_page', 30 );
    }

    function puca_woo_product_single_one_page() {

        $menu_bar   =  apply_filters( 'woo_product_menu_bar', 10, 2 );

        if( isset($menu_bar) && $menu_bar ) {
          global $product;
          $id = $product->get_id();
          wp_enqueue_script( 'jquery-onepagenav' );
          ?>

          <ul id="onepage-single-product" class="nav nav-pills">
            <li class="current"><a href="#main-container"><?php esc_html_e('Product Preview','puca'); ?></a></li>
            <li class="shop-now"><a href="#shop-now"><?php esc_html_e('Shop Now','puca'); ?></a></li>

            <?php if( puca_tbay_get_config('show_product_review_tab', true) ) : ?>
              <li><a href="#woocommerce-tabs"><?php esc_html_e('Reviews','puca'); ?></a></li>
            <?php endif; ?>

            <?php if( puca_tbay_get_config('show_product_releated', true) ) : ?>
              <li><a href="#product-related"><?php esc_html_e('Related Products','puca'); ?></a></li>  
            <?php endif; ?>        
          </ul>

          <?php
          
        }
    }
}

/*product one page body class*/
if ( ! function_exists( 'puca_woo_product_body_class_single_one_page' ) ) {
  function puca_woo_product_body_class_single_one_page( $classes ) {

    $menu_bar   =  apply_filters( 'woo_product_menu_bar', 10, 2 );

    if( isset($menu_bar) && $menu_bar ) {
      $classes[] = 'tbay-body-menu-bar';
    }
    return $classes;

  }
  add_filter( 'body_class', 'puca_woo_product_body_class_single_one_page',99 );
}


if(!function_exists('puca_add_product_id_before_add_to_cart_form')){
add_action('woocommerce_before_add_to_cart_button','puca_add_product_id_before_add_to_cart_form', 99);
  function  puca_add_product_id_before_add_to_cart_form() {
      global $product;
      $id = $product->get_id();

      ?> 

      <?php if( intval( puca_tbay_get_config('enable_buy_now', false) ) && $product->get_type() !== 'external' ) : ?>
        <div id="shop-now" class="has-buy-now">
      <?php else: ?> 
        <div id="shop-now">
      <?php endif; ?>

      <?php
  }
}

if(!function_exists('puca_close_after_add_to_cart_form')){
  add_action('woocommerce_after_add_to_cart_button','puca_close_after_add_to_cart_form', 99);
  function  puca_close_after_add_to_cart_form() {
      ?>
        </div>
      <?php
  }
}

/** 
 * remove on single product panel 'Additional Information' since it already says it on tab.
 */
add_filter('woocommerce_product_additional_information_heading', 'puca_supermaket_product_additional_information_heading');
 
function puca_supermaket_product_additional_information_heading() {
    echo '';
}

if(!function_exists('puca_related_products_args')){
  add_filter( 'woocommerce_output_related_products_args', 'puca_related_products_args' );
    function puca_related_products_args( $args ) {

    $args['posts_per_page'] = puca_tbay_get_config('number_product_releated', 4); // 4 related products

    return $args;
  }
}


// define the woocommerce_before_add_to_cart_button callback 
if(!function_exists('puca_action_woocommerce_before_add_to_cart_button')){
  function puca_action_woocommerce_before_add_to_cart_button(  ) { 
      $content = puca_tbay_get_config('html_before_add_to_cart_btn');
      echo trim($content);
  }
  add_action( 'woocommerce_before_add_to_cart_form', 'puca_action_woocommerce_before_add_to_cart_button', 10, 0 ); 
}
// define the woocommerce_before_add_to_cart_button callback 
if(!function_exists('puca_action_woocommerce_after_add_to_cart_button')){
  function puca_action_woocommerce_after_add_to_cart_button(  ) { 
      $content = puca_tbay_get_config('html_after_add_to_cart_btn');
      echo trim($content);
  }
  add_action( 'woocommerce_after_add_to_cart_form', 'puca_action_woocommerce_after_add_to_cart_button', 999, 0 ); 
}


/*Add The WooCommerce Total Sales Count*/
if(!function_exists('puca_single_product_add_total_sales_count')){ 
  function puca_single_product_add_total_sales_count() { 
    global $product;

     if( !intval( puca_tbay_get_config('enable_total_sales', true) ) || $product->get_type() === 'external' || $product->get_type() === 'grouped' )  return;

    $count = get_post_meta($product->get_id(),'total_sales', true); 

    $text = sprintf( '<span class="rate-sold"><span class="count">%s</span> <span class="sold-text">%s</span></span>',
        number_format_i18n($count),
        esc_html__('sold', 'puca')
    );

    echo trim($text);
  }
  add_action( 'puca_woo_after_single_rating', 'puca_single_product_add_total_sales_count', 10 ); 
}

if(!function_exists('puca_woocommerce_buy_now')){
  function puca_woocommerce_buy_now(  ) { 
        global $product;
        if ( ! intval( puca_tbay_get_config('enable_buy_now', false) ) ) {
            return; 
        }

        if ( $product->get_type() == 'external' ) { 
            return;
        }

        $class = 'tbay-buy-now button';

        if( !empty($product) && $product->is_type( 'variable' ) ){
            $default_attributes = puca_get_default_attributes( $product );
            $variation_id = puca_find_matching_product_variation( $product, $default_attributes );

            if( empty($variation_id) ) {
                $class .= ' disabled';
            } 
        }
 
        echo sprintf( '<button class="'. $class .'">%s</button>', esc_html__('Buy Now', 'puca') );
        echo '<input type="hidden" value="0" name="puca_buy_now" />';
  } 
  add_action( 'woocommerce_after_add_to_cart_button', 'puca_woocommerce_buy_now', 8 ); 
}