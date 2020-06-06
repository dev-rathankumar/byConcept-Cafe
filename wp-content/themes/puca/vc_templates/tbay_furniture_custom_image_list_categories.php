<?php

$el_class = $css = $css_animation = $disable_mobile = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$loop_type = $auto_type = $autospeed_type = $shop_now = '';
extract( $atts );


$categoriestabs     = (array) vc_param_group_parse_atts( $categoriestabs );
$categoriestabsv2   = (array) vc_param_group_parse_atts( $categoriestabsv2 );
$categoriestabsv3   = (array) vc_param_group_parse_atts( $categoriestabsv3 );
$categoriestabsgrid   = (array) vc_param_group_parse_atts( $categoriestabsgrid );


if (isset($categoriestabsv2[0]['images']) && !empty($categoriestabsv2[0]['images'])) {
    $categoriestabs = $categoriestabsv2;
} elseif (isset($categoriestabsv3[0]['images']) && !empty($categoriestabsv3[0]['images'])) {
    $categoriestabs = $categoriestabsv3;
} elseif (isset($categoriestabsgrid[0]['images']) && !empty($categoriestabsgrid[0]['images'])) {
    $categoriestabs = $categoriestabsgrid;
}

$_id = puca_tbay_random_key();

if(isset($responsive_type) && $responsive_type == 'yes') {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =      $columns;
    $screen_desktopsmall     =      $columns;
    $screen_tablet           =      $columns; 
    $screen_mobile           =      $columns;  
}

$active_theme = puca_tbay_get_part_theme();

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'widget widget-'. $layout_type .' widget-categories categories custom ';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr($css_class); ?>">

    <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
        <h3 class="widget-title">
            <?php if ( isset($title) && $title ): ?>
                <span><?php echo esc_html( $title ); ?></span>
            <?php endif; ?>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>

	<?php if ( isset($categoriestabs) && $categoriestabs ) : ?>
		<div class="widget-content woocommerce">
			<div class="<?php echo esc_attr( $layout_type ); ?>-wrapper">
                <?php if( isset($layout_type) && (stripos($layout_type, 'carousel') === 0) ) : ?>

                    <?php  wc_get_template( 'layout-categories/'.  $active_theme .'/'.  $layout_type .'-custom.php' , array( 'categoriestabs' => $categoriestabs, 'columns' => $columns, 'rows' => $rows, 'data_loop' => $loop_type, 'data_auto' => $auto_type, 'data_autospeed' => $autospeed_type, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'shop_now' => $shop_now,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'disable_mobile' => $disable_mobile ) ); ?>

                <?php elseif( isset($layout_type) &&  $layout_type == 'single' ) : ?>

                    <?php  wc_get_template( 'layout-categories/'.  $active_theme .'/'.  $layout_type .'-custom.php' , array( 'category' => $category, 'images' => $image) ); ?>

                <?php else : ?>

                    <?php

                    $data_responsive = '';
                    
                    if( isset($responsive_type) && $responsive_type === 'yes' ) {  

                        $data_responsive .= ' data-xlgdesktop='. $columns .'';
                        $data_responsive .= ' data-desktop='. $screen_desktop .'';
                        $data_responsive .= ' data-desktopsmall='. $screen_desktopsmall .'';
                        $data_responsive .= ' data-tablet='. $screen_tablet .'';
                        $data_responsive .= ' data-mobile='. $screen_mobile .'';

                    }

                    ?>

                    <div class="row" <?php echo esc_attr($data_responsive); ?>>
                        <?php  wc_get_template( 'layout-categories/'.  $active_theme .'/'.  $layout_type .'-custom.php' , array( 'categoriestabs' => $categoriestabs, 'columns' => $columns , 'screen_desktop' => $screen_desktop, 'screen_desktopsmall' => $screen_desktopsmall, 'screen_tablet' => $screen_tablet, 'screen_mobile' => $screen_mobile ) ); ?>
                    </div>


                    <?php 


                    if( $show_view_all === 'yes' ) {

                        $aUrl = get_permalink( wc_get_page_id( 'shop' ) );

                        echo '<a class="btn-default show-all" href="'. esc_url($aUrl) .'"><i class="icon-grid icons"></i>'. esc_html($button_text_view_all) .'</a>';
                    }


                    ?>

                <?php endif; ?>



			</div>
		</div>
	<?php endif; ?>

</div>
