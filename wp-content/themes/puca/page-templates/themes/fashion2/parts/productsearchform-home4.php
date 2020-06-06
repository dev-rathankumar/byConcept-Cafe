<?php if ( puca_tbay_get_config('show_searchform') ): ?>
	<?php 
		$_id = puca_tbay_random_key();
	?>
	<div class="tbay-search-form tbay-search-ajax tbay-search-normal is-category">
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" id="form-search-<?php echo esc_attr($_id); ?>">
			<div class="form-group">
				<div class="input-group">
					<?php if ( puca_tbay_get_config('search_type') != 'all' ): ?>
						<?php 
							wp_enqueue_style('sumoselect');
							wp_enqueue_script('jquery-sumoselect');	
						?>
						<div class="select-category input-group-addon">
							<?php if ( puca_tbay_get_config('search_type') == 'product' ):
								$args = array(
								    'show_counts' => false,
								    'hierarchical' => true,
								    'show_uncategorized' => 0
								);
							?>
							    <?php wc_product_dropdown_categories( $args ); ?>

							<?php elseif ( puca_tbay_get_config('search_type') == 'post' ):
								$args = array(
									'show_option_all' => esc_html__( 'All categories', 'puca' ),
								    'show_counts' => false,
								    'hierarchical' => true,
								    'show_uncategorized' => 0,
								    'name' => 'category',
									'id' => 'search-category',
									'class' => 'postform dropdown_product_cat',
								);
							?>
								<?php wp_dropdown_categories( $args ); ?>
							<?php endif; ?>
					  	</div>
				  	<?php endif; ?>
				  		<input type="text" data-style="ui-search-normal" placeholder="<?php esc_html_e( 'What&sbquo;re you searching for...', 'puca' ); ?>" name="s" oninvalid="this.setCustomValidity('<?php esc_html_e('Enter at least 2 characters', 'puca'); ?>')" oninput="setCustomValidity('')" required class="tbay-search form-control input-sm"/>
						<div class="tbay-preloader"></div>
						<div class="button-group input-group-addon">
							<button type="submit" class="button-search btn btn-sm"><i class="icon-magnifier"></i></button>
						</div>
					<?php if ( puca_tbay_get_config('search_type') != 'all' ): ?>
						<input type="hidden" name="post_type" value="<?php echo esc_attr( puca_tbay_get_config('search_type') ); ?>" class="post_type" />
					<?php endif; ?>
				</div>
				
			</div>
		</form>
	</div>

<?php endif; ?>