<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . esc_html( $title ) . trim($after_title);
}
?>

<div class="comment-widget widget-content">
    <?php
    $number = $instance['number_comment'];
    $all_comments = get_comments( array('status' => 'approve', 'number'=>$number) );
    if (is_array( $all_comments)) {

        foreach($all_comments as $comment) { ?>
            <article class="clearfix">
                <div class="avatar-comment-widget">
                    <?php echo get_avatar($comment, '70'); ?>
                </div>
                <div class="content-comment-widget">
                    <h6>
                        <?php echo strip_tags($comment->comment_author); ?> <?php echo esc_html__('says', 'puca' ); ?>:
                    </h6>
                    <a class="comment-text-side" href="<?php echo esc_url( get_permalink($comment->ID) ); ?>#comment-<?php echo esc_attr( $comment->comment_ID ); ?>" title="<?php echo strip_tags($comment->comment_author); ?> on <?php echo esc_html( $comment->post_title ); ?>">
                        <?php echo puca_tbay_substring($comment->comment_content, 10, '...'); ?>
                    </a>
                </div>
            </article>
    <?php } 
    } ?>
</div>