<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

$get_sub_title = '';
if ( isset($sub_title) && $sub_title ) {
    $get_sub_title = '<span class="subtitle">'. esc_html($sub_title) .'</span>';
}

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . trim($after_title) . trim($get_sub_title);
}


$taxonomy     = 'product_cat';
$orderby      = 'name';  
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no   
$empty        = 0;

$args = array(
     'taxonomy'     => $taxonomy,
     'orderby'      => $orderby,
     'number'       => $numbers,
     'parent' => 0,
     'pad_counts'   => $pad_counts,
     'hierarchical' => $hierarchical,
     'title_li'     => $title,
     'hide_empty'   => $empty
);
$all_categories = get_categories( $args );


$_id = puca_tbay_random_key();
$_count = 1;


$screen_desktop          =      isset($columns_desktop) ? $columns_desktop : 4;
$screen_desktopsmall     =      isset($columns_destsmall) ? $columns_destsmall : 3;
$screen_tablet           =      isset($columns_tablet) ? $columns_tablet : 3;
$screen_mobile           =      isset($columns_mobile) ? $columns_mobile : 1;

$active_theme = puca_tbay_get_part_theme();

$layout_type = 'grid';

$data_responsive = '';

$data_responsive .= ' data-xlgdesktop='. $columns;
$data_responsive .= ' data-desktop='. $screen_desktop;
$data_responsive .= ' data-desktopsmall='. $screen_desktopsmall;
$data_responsive .= ' data-tablet='. $screen_tablet;
$data_responsive .= ' data-mobile='. $screen_mobile ;

$_id = puca_tbay_random_key();

?>
<div class="widget widget-grid widget-categories categories">

    <?php if ( $all_categories ) : ?>
        <div class="widget-content woocommerce">
            <div class="<?php echo esc_attr( $layout_type ); ?>-wrapper">

                <div class="row grid" <?php echo esc_attr($data_responsive); ?>>
                    <?php  wc_get_template( 'layout-categories/'.  $active_theme .'/'. $layout_type .'.php' , array( 'all_categories' => $all_categories, 'columns' => $columns, 'number' => $numbers , 'screen_desktop' => $screen_desktop, 'screen_desktopsmall' => $screen_desktopsmall, 'screen_tablet' => $screen_tablet, 'screen_mobile' => $screen_mobile ) ); ?>
                </div>


            </div>
        </div>
    <?php endif; ?>

</div>
