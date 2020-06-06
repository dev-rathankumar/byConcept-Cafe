<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>
<div class="esig-error-message-wrap">
<a href='https://www.approveme.com/wp-digital-e-signature' target='_blank' style='text-decoration:none;'>
				<img src='<?php echo ESIGN_ASSETS_DIR_URI ; ?>/images/logo.png' alt='WP E-Signature'>
</a>
<h1><?php _e('Access Denied', 'esig' );?></h1>

<p><?php _e( 'Whoops! It looks like you don’t have access to this page! Only the super admin user has access to ALL WP E-Signature pages.', 'esig'); ?></p> 


<p> <?php echo sprintf( __("Your super admin is currently: %s","esig"), WP_E_Sig()->user->esig_get_administrator_displayname()) ; ?> </p>

<p><?php _e("If your super admin enables our Unlimited Sender Roles add-on, you can get back to creating documents. Woo-hoo!","esig");?></p>

<p> <a href="mailto:<?php echo WP_E_Sig()->user->esig_get_administrator_email(); ?>"> <?php _e("Send them an email here.","esig");?></a> </p>

<p><?php _e('<a href="https://www.approveme.com/wordpress-electronic-digital-signature-add-ons/"> View a list of other features here.</a>', 'esig' ); ?></p>
</div>

<?php //echo $data['esig_user_role']; ?>