<?php if ( puca_tbay_get_config('show_searchform',1) ): ?>
	<?php 
		$_id = puca_tbay_random_key();
	?>
	<div class="tbay-search-form">
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" id="form-search-<?php echo esc_attr($_id); ?>">
			<div class="form-group">
				<div class="input-group">
					  	<div class="button-group input-group-addon">
							<button type="submit" class="button-search btn btn-sm"><i class="icon-magnifier"></i></button>
						</div>
				  		<input type="text" placeholder="<?php esc_html_e( 'I&rsquo;m searching for...', 'puca' ); ?>" name="s" required oninvalid="this.setCustomValidity('<?php esc_html_e('Enter at least 2 characters', 'puca'); ?>')" oninput="setCustomValidity('')" class="tbay-search form-control input-sm"/>
						<div class="tbay-preloader"></div>

						<div class="tbay-search-results"></div>
					<?php if ( puca_tbay_get_config('search_type') != 'all' ): ?>
						<input type="hidden" name="post_type" value="<?php echo esc_attr( puca_tbay_get_config('search_type') ); ?>" class="post_type" />
					<?php endif; ?>
				</div>
				
			</div>
		</form>
	</div>

<?php endif; ?>