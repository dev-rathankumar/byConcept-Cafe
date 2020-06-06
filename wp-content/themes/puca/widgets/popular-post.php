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
$current_theme = puca_tbay_get_theme();
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
			                      
									<span class="entry-view"><i class="icon-eye icons"></i> 
			                      		<?php echo puca_get_post_views(get_the_ID()); ?>
			                      	</span>
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

	<?php elseif( isset($styles) && $styles == 'grid2' ) : ?>

		<div class="post-widget media-post-layout widget-content <?php echo esc_attr($styles); ?>">
		<ul class="posts-list clearfix">
		<?php
			while($query->have_posts()):$query->the_post();
		?>
			<li class="col-sm-6">
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
				                      	<span class="entry-date"><?php the_time( 'd, M Y' ); ?></span>
				                     	
										<span class="entry-view"><i class="icon-eye icons"></i> 
					                      	<?php echo puca_get_post_views(get_the_ID()); ?>
					                    </span>
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

	<?php elseif( isset($styles) && $styles == 'grid4' ) : ?>

		<div class="post-widget media-post-layout widget-content <?php echo esc_attr($styles); ?>">
		<ul class="posts-list clearfix">
		<?php
			while($query->have_posts()):$query->the_post();
		?>
			<li class="col-sm-6 col-md-3">
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
				        </div>
				    </div>
				</article>
			</li>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		</ul>
		</div>

	<?php elseif( isset($styles) && $styles == 'feature' ) : ?>

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
			                <figure class="entry-thumb">
			                    <a href="<?php the_permalink(); ?>" class="entry-image">
			                        <?php the_post_thumbnail( 'widget' ); ?>
			                    </a>  
			                    <span class="post-type"><?php puca_tbay_icon_post_formats(); ?></span>
			                </figure>
				            <?php
				        }
				        ?>
				        <div class="entry-content">
							<div class="meta-info">
								<span class="author"><?php echo get_avatar(puca_tbay_get_id_author_post(), 'puca_avatar_post_carousel'); ?> <?php the_author_posts_link(); ?></span>
								<span class="entry-date"><i class="icon-clock icons"></i><?php echo puca_time_link(); ?></span>
								<span class="entry-view"><i class="icon-eye icons"></i> 
			                      	<?php echo puca_get_post_views(get_the_ID()); ?>
			                    </span>
								
							</div>
							<div class="entry-meta">
					            <?php
					                if (get_the_title()) {
					                ?>
					                    <h4 class="entry-title">
					                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					                    </h4>
					                <?php
					            }
					            ?>
					        </div>
							<?php
								if ( has_excerpt()) {
									the_excerpt();
								} else {
									?>
										<div class="entry-description"><?php echo puca_tbay_substring( get_the_excerpt(), 25, '[...]' ); ?> <a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read More', 'puca' ); ?>"><i class="icon-arrow-right-circle icons"></i></a></div>
									<?php
								}
							?>
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
