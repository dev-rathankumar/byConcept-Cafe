<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . trim($after_title);
}
$embed_code = wp_oembed_get( $instance['video_link'], array( 'width'=> $instance['video_width'] ) );
?>

<div class="widget-video-content widget-content">
    <div class="widget-video-inner embed-responsive embed-responsive-16by9">
        <?php if ( $embed_code ) { ?>
            <?php echo trim($embed_code); ?>
        <?php } else { ?>
            <span class="visual-video-error text-error">
                <?php esc_html_e( 'Video error!', 'puca' ); ?>
            </span>
        <?php } ?>
    </div>
    <?php if ( $video_name ) { ?>
        <h6 class="widget-video-name">
            <?php echo esc_html($instance['video_name']); ?>
        </h6>
    <?php } ?>
</div>