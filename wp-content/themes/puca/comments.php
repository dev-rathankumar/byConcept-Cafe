<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
        <h3 class="comments-title"><?php comments_number( esc_html__('0 Comments', 'puca'), esc_html__('1 Comment', 'puca'), esc_html__('% Comments', 'puca') ); ?></h3>
		<?php puca_tbay_comment_nav(); ?>
		<ol class="comment-list">
			<?php wp_list_comments('callback=puca_tbay_list_comment'); ?>
		</ol><!-- .comment-list -->

		<?php puca_tbay_comment_nav(); ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'puca' ); ?></p>
	<?php endif; ?>

	<?php
        $aria_req = ( $req ) ? ' aria-required=true' : '';
        $comment_args = array(
                        'title_reply'=> '<span class="title">'.esc_html__('Leave a Comment','puca').'</span>',
                        'comment_field' => '<div class="form-group">
                                                <textarea rows="8" placeholder="'.esc_html__('Your comment...', 'puca').'" id="comment" class="form-control"  name="comment"'. esc_attr($aria_req).'></textarea>
                                            </div>',
                        'fields' => apply_filters(
                        	'comment_form_default_fields',
	                    		array(
	                                'author' => '<div class="form-group ">
	                                            <input type="text" placeholder="'.esc_html__('Your name', 'puca').'"   name="author" class="form-control" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' />
	                                            </div>',
	                                'email' => ' <div class="form-group ">
	                                            <input id="email" placeholder="'.esc_html__('Your email', 'puca').'"  name="email" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" ' . $aria_req . ' />
	                                            </div>',
	                                'url' => '<div class="form-group ">
	                                            <input id="url" placeholder="'.esc_html__('Website', 'puca').'" name="url" class="form-control" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '"  />
	                                            </div>',
	                            )
							),
	                        'label_submit' => esc_html__('Submit', 'puca'),
							'comment_notes_before' => '<div class="form-group h-info">'.esc_html__('Your email address will not be published.','puca').'</div>',
							'comment_notes_after' => '',
                        );
    ?>

	<?php puca_tbay_comment_form($comment_args); ?>
</div><!-- .comments-area -->
