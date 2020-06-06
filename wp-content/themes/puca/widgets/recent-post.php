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

			        <?php
			        if ( has_post_thumbnail() ) {
			            ?>
			              <div class="media-left">
			                <figure class="entry-thumb">
			                    <a href="<?php the_permalink(); ?>" class="entry-image">
			                        <?php the_post_thumbnail( 'widget' ); ?>
			                    </a>  
			                </figure>
			              </div>
			            <?php
			        }
			        ?>
			        <div class="media-body">
			          <?php
			              if (get_the_title()) {
			              ?>
			                  <h4 class="entry-title">
			                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			                  </h4>
			              <?php
			          }
			          ?>
			          <div class="entry-content-inner clearfix">
			              <div class="entry-meta">
			                   <div class="meta-info">
			                      <span class="entry-date"><?php echo puca_time_link(); ?></span>
			                     <span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0', '1', '%' ); ?></span>
			                  </div>
			              </div>
			          </div>
			        </div>
			    </div>
			</article>
		</li>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
	</ul>
	</div>

	<?php elseif( isset($styles) && $styles == 'grid' ) : ?>

		<div class="post-widget media-post-layout widget-content <?php echo esc_attr($styles); ?>">
		<ul class="posts-list">
		<?php
			while($query->have_posts()):$query->the_post();
		?>
			<li>
				<article class="post post-list">

				    <div class="entry-content media">

				        <?php
				        if ( has_post_thumbnail() ) {
				            ?>
				              <div class="media-left">
				                <figure class="entry-thumb">
				                    <a href="<?php the_permalink(); ?>" class="entry-image">
				                        <?php the_post_thumbnail( 'widget' ); ?>
				                    </a>  
				                </figure>
				              </div>
				            <?php
				        }
				        ?>
				        <div class="media-body">
				          <?php
				              if (get_the_title()) {
				              ?>
				                  <h4 class="entry-title">
				                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				                  </h4>
				              <?php
				          }
				          ?>
				          <div class="entry-content-inner clearfix">
				              <div class="entry-meta">
				                   <div class="meta-info">
				                      	<span class="entry-date"><?php echo puca_time_link(); ?></span>
				                     	<span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0', '1', '%' ); ?></span>
				                  </div>
				              </div>
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
