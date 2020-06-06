<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . trim($after_title);
}

if( isset($instance['styles']) ) {
	$styles = $instance['styles'];
}

$args = array(
	'post_type' => 'post',
	'meta_key' => 'puca_post_views_count',
	'orderby' => 'meta_value_num', 
	'order' => 'DESC',
	'posts_per_page' => $number_post
);

$query = new WP_Query($args);
if($query->have_posts()):

	if( isset($styles) && $styles == 'list' ) :

	?>
	<div class="post-widget media-post-layout widget-content <?php echo esc_attr($styles); ?>">
	<ul class="posts-list">
	<?php
		while($query->have_posts()):$query->the_post();
	?>
		<li>
			<article class="post post-list">

			    <div class="entry-content media">
			    	<div class="entry-date"><?php echo puca_time_link(); ?></div>
			        <div class="media-body">
				        <?php
				            if (get_the_title()) {
				            ?>
				            <h4 class="entry-title">
				                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				            </h4>
				        <?php } ?>
			          	<div class="entry-content-inner">
							<span class="entry-view"><i class="icon-user icons"></i> 
	                      		<?php echo puca_get_post_views(get_the_ID()), esc_html__(' views', 'puca'); ?>
	                      	</span>
	                      	<span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0 comment', '1 comment', esc_html__( '% comments', 'puca' ) ); ?>
	                      	</span>
			          	</div>
			        </div>
			    </div>
			</article>
		</li>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
	</ul>
	</div>

	<?php endif; ?>
<?php endif; ?>
