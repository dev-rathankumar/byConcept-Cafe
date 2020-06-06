<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . esc_html( $title ) . trim($after_title);
}
$query = new WP_Query(array(
	'post_type'=>'post',
	'post__in' => $ids
));

if( isset($instance['styles']) ) {
	$styles = $instance['styles'];
}

if($query->have_posts()){
?>
	<?php if( isset($styles) && $styles == 'vertical' ) : ?>

		<div class="post-widget media-post-layout widget-content <?php echo esc_attr($styles); ?>">
			<?php while ( $query->have_posts() ): $query->the_post(); ?>
				<article class="item-post media">
					<?php
					if ( has_post_thumbnail() ) {
					  ?>
					  	<figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
							<a href="<?php the_permalink(); ?>" aria-hidden="true">
							<?php
								the_post_thumbnail( 'full', array( 'alt' => get_the_title() ) );
							?>
							</a>
					  	</figure>
				  	<?php
				 	}
					?>
					<div class="entry-header">
						<div class="meta-info">
							<span class="author"><?php echo get_avatar(puca_tbay_get_id_author_post(), 'puca_avatar_post_carousel'); ?> <?php the_author_posts_link(); ?></span>

							<span class="entry-date"><?php echo puca_time_link(); ?></span>

							<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
								<span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0', '1', esc_html__( '% comments', 'puca' ) ); ?></span>
							<?php endif; ?>

							<span class="entry-category">
					            <i class="icons icon-folder"></i>
					            <?php puca_tbay_get_random_blog_cat(); ?>
					      	</span>
							<span class="post-type"><?php puca_tbay_icon_post_formats(); ?></span>
						</div>
					</div>
				    <div class="entry-content <?php echo ( !has_post_thumbnail() ) ? 'no-thumb' : ''; ?>">
				    	<div class="entry-meta">
				            <?php
				                if (get_the_title()) {
				                ?>
				                    <h3 class="entry-title">
				                       <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				                    </h3>
				                <?php
				            	}
				            ?>
				        </div>
				    </div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>

	<?php elseif( isset($styles) && $styles == 'horizontal' ) : ?>

		<div class="post-widget media-post-layout widget-content <?php echo esc_attr($styles); ?>">
			<?php while ( $query->have_posts() ): $query->the_post(); ?>
				<article class="item-post media row">
					<?php
					if ( has_post_thumbnail() ) {
					  ?>
					<div class="col-sm-6">
					  	<figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
							<a href="<?php the_permalink(); ?>" aria-hidden="true">
							<?php
								the_post_thumbnail( 'full', array( 'alt' => get_the_title() ) );
							?>
							</a>
					  	</figure>
					</div>  	
				  	<?php
				 	}
					?>
					<div class="col-sm-6">
						<div class="entry-content">
							<div class="entry-header">
								<?php
					                if (get_the_title()) {
					                ?>
					                    <h3 class="entry-title">
					                       <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					                    </h3>
					                <?php
					            	}
					            ?>
								<div class="meta-info">
									<span class="author"><?php echo get_avatar(puca_tbay_get_id_author_post(), 'puca_avatar_post_carousel'); ?> <?php the_author_posts_link(); ?></span>

									<span class="entry-date"><?php echo puca_time_link(); ?></span>

									<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
										<span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0', '1', esc_html__( '% comments', 'puca' ) ); ?></span>
									<?php endif; ?>

									<span class="entry-category">
							            <i class="icons icon-folder"></i>
							            <?php puca_tbay_get_random_blog_cat(); ?>
							      	</span>
								</div>
							</div>
								<?php
									if ( has_excerpt()) {
										the_excerpt();
									} else {
										?>
											<div class="entry-description"><?php echo puca_tbay_substring( get_the_excerpt(), 40, '[...]' ); ?></div>
										<?php
									}
								?>
					    	</div>
					    </div>	
					</div>    
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>

	<?php endif; ?>
	
<?php } ?>
