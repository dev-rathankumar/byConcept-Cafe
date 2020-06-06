<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<h2 class="title-account"><?php esc_html_e( '#my account', 'puca' ); ?>
	<span><?php esc_html_e( 'login to system', 'puca' ); ?></span>
</h2>

<form class="woocommerce-form login" method="post">

	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<p class="form-group form-row form-row-wide">
		<label for="username"><?php esc_html_e( 'Username or email address', 'puca' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
	</p>
	<p class="form-group form-row form-row-wide">
		<label for="password"><?php esc_html_e( 'Password', 'puca' ); ?> <span class="required">*</span></label>
		<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
	</p>

	<?php do_action( 'woocommerce_login_form' ); ?>

	<p class="form-row">
		<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>


		<p for="rememberme" class="inline rememberme woocommerce-form__input woocommerce-form__input-checkbox"><input name="rememberme" type="checkbox" id="rememberme" value="forever"/><span><?php esc_html_e( 'Remember me', 'puca' ); ?></span></p>

		<p class="form-group lost_password">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'puca' ); ?></a>
		</p>

		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
			<p class="form-group creat-account">
				<?php 
					$link = get_permalink( get_option('woocommerce_myaccount_page_id'));
					$link = $link.'?action=register';
				?>
				<a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Create an account', 'puca' ); ?></a>
			</p>
		<?php endif; ?>

		<p><input type="submit" class="woocommerce-Button button" name="login" value="<?php esc_html_e( 'Login', 'puca' ); ?>" /></p>
		
		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</p>

</form>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
