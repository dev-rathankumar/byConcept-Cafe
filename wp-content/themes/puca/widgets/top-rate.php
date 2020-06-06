<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . trim($after_title);
}

$number = min(max((int)$instance['number'], 1), 20);
$date_limit = min(max((int)$instance['date_limit'], 0), 999);
$post_type  = post_type_exists($instance['post_type']) ? $instance['post_type'] : 'post';
$sort       = isset($instance['sort']) ? esc_attr($instance['sort']) : 'bayesian_rating';

$posts = PostRatings()->getTopRated(array(
    'post_type'  => $post_type,
    'number'     => $number,
    'sortby'     => $sort,
    'order'      => in_array($instance['order'], array('ASC', 'DESC'), true) ? $instance['order'] : 'DESC',
    'date_limit' => $date_limit,
));

if ($posts) {

	$rt_option = PostRatings()->getOptions();
	$max_rating =  $rt_option['max_rating'];
	?>
	<div class="post-widget widget-rate widget-content">
		<?php foreach ($posts as $key => $post) { ?>
			<?php
				$rating = (float)get_post_meta($post->ID, 'rating', true);
				$current_rating = apply_filters('post_ratings_current_rating', sprintf('%.2F / %d', $rating, $max_rating), $rating, $max_rating);
			?>
			<article>
				<?php
					if(has_post_thumbnail()){
				?>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'widget' ); ?>
				</a>
				<?php } ?>
				<h6>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h6>
				<div class="post-rate ratings">
					<ul class="rated" style="width:<?php echo esc_attr($max_rating * 16); ?>px" title="<?php echo esc_attr($current_rating); ?>">
						<li class="rating" style="width:<?php echo esc_attr($rating * 16); ?>px">
							<span class="average">
								<?php echo trim($current_rating); ?>
							</span>
							<span class="best">
								<?php echo trim($max_rating); ?>
							</span>
						</li>
					</ul>
				</div>
				<p class="post-date">
					<?php the_time( 'd M Y' ); ?>
				</p>
			</article>
		<?php } ?>
	</div>
<?php } ?>