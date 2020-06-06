<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

$screen_desktop          =      isset($columns) ? $columns : 4;
$screen_desktopsmall     =      isset($columns) ? $columns : 3;
$screen_tablet           =      isset($columns) ? $columns : 3;
$screen_landscape_mobile =      3;
$screen_mobile           =      2;

$data_responsive  = '  data-xlgdesktop="'. $columns .'" data-desktop="'. $screen_desktop .'" data-desktopsmall="'. $screen_desktopsmall .'" data-tablet="'. $screen_tablet .'" data-mobile="'. $screen_mobile .'"'; 
?>
<div class="instagram-widget <?php echo isset($style) ? esc_attr($style) : ''; ?>">
<?php
if ( $title ) {
    echo trim($before_title)  . trim( $title );
?>
    <a href="<?php echo trailingslashit( '//instagram.com/' . esc_attr( trim( $username ) ) ); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>"></a>
<?php
    echo  trim($after_title);
}
$bcol = 12/(int)$columns;
if ($columns == 5) {
    $bcol = 'cus-5';
}

    if ( $username != '' ) {
        $media_array = tbay_framework_scrape_instagram( $username );

        if ( is_wp_error( $media_array ) ) {
 
                $_id = puca_tbay_random_key();

                $data_infor = ' data-like="false" data-comment="false" data-time_ago="false" data-number="'. $number .'"  data-username="'. $username .'" data-image_size="'. $size .'"  data-id="#instagram-feed'. $_id .'" ';
                wp_enqueue_script( 'jquery-timeago' );
                wp_enqueue_script( 'jquery-instagramfeed' );
            ?>
            <div id="instagram-feed<?php echo esc_attr($_id); ?>" class="row grid instagram-pics instagram-feed tbay-addon-instagram" <?php echo trim($data_responsive); ?>  <?php echo trim($data_infor); ?> >

            </div>
            <?php
        } else {

            // filter for images only?
            if ( $images_only = apply_filters( 'tbay_framework_instagram_widget_images_only', FALSE ) ) {
                $media_array = array_filter( $media_array, 'tbay_framework_images_only' );
            }

            // slice list down to required number
            $media_array = array_slice( $media_array, 0, $number );
            ?>
            <div class="row grid instagram-pics">
                <?php
                foreach ( $media_array as $item ) {
                    echo '<div class="col-md-'.esc_attr($bcol).' col-xs-6">';
                    echo '<a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'"><img src="'. esc_url( $item[$size] ) .'" /></a>';
                    echo '</div>';
                }
                ?>
            </div>
            <?php
        }
    }
?>
</div>