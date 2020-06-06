<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

<div id='esig-settings-container' class='wrap approveme_main wpd-sign' >		
		  
    <div id='esig-headlink-col1'>
    
    	<div class="esig-masthead">
    		<a href='https://www.approveme.com/wp-digital-e-signature' target='_blank' style='text-decoration:none;'>
				<img src='<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/logo.png' alt='WP E-Signature'>
			</a>
			<br>
			<img src='<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/pen_icon_gray.svg' alt='Signing documents just got easier'>
			<span class='settings-title'><?php _e('Signing documents just got alot easier.','esig'); ?></span>
		</div>
	
	</div><!--/esig-headlink-col1-->
	
    <div id='esig-headlink-col2'>
		 <ul>
		 	<li class='esig-extension-headimg'>
				<span class='esig-extension-headtext'>
					<?php _e('To enable more features and signature functions you should visit','esig'); ?>
				</span>
				<br> 
		 <a href='admin.php?page=esign-addons' class='esig-extension-headlink'><?php _e('E-Sign Add-On Extensions.','esig'); ?></a></li>
		 </ul>
	</div><!--/esig-headlink-col2-->
</div><!--/wrap approveme_main wpd-sig-->
