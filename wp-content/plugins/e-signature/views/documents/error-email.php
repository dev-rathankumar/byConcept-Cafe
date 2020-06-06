<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>
<div class="esig-error-message-wrap">
<a href='http://www.approveme.com/wp-digital-e-signature' target='_blank' style='text-decoration:none;'>
	<img src='<?php echo $data['assets_dir']; ?>/images/search.svg' alt='WP E-Signature'>			<img src='<?php echo $data['assets_dir']; ?>/images/logo.png' alt='WP E-Signature'>
</a>


<p class="esig-error-text"><?php _e('Hey there!  It looks like there was an error sending an email invite. <br>Please <a href="admin.php?page=esign-email-general" target="_blank">check your mail settings</a> and try again.','esig'); ?></p>
</div>