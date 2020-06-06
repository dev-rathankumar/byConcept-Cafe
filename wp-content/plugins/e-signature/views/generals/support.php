<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

<?php include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php'); ?>
	

<div id="esig-settings-container">
    <div id="esig-settings-col1"><p align="left" style="margin: 0 0 0 11px;"><span class="esig-settingstitle"><?php _e('Premium Tech Support That <br>Goes Above and Beyond.', 'esig' ); ?></span></p>
	
	<ul>
		<li class="esig-support"><?php _e('Plugin Installation & troubleshooting', 'esig' );?></li>
		<li class="esig-support"><?php _e('Access to Support Forums', 'esig' );?></li>
		<li class="esig-support"><?php _e('Advanced Technical Support', 'esig' );?></li>
		<li class="esig-support"><?php _e('Logging into your Site', 'esig' );?></li>
		<li class="esig-support"><?php _e('SSL Installation & Support', 'esig' );?></li>
	</ul>
	<p>
	<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/boss-point.svg" alt="Sign up for WP E-Signature Premium Support" width="94%">
	</p>
	</div>
	
    <div id="esig-settings-col2">
		<div id="w3tc_services" class="postbox" style="position: relative; width: 400px; top: 0px; left: 0px;">
		<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle" style="padding: 14px;font-weight: 100;"><span><div class="w3_widget_logo"></div><?php _e('Select your support plan:', 'esig' );?></span></h3>
		<div class="inside">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="M29HVHE98HPH6">
<input type="hidden" name="on0" value="">
<input type="hidden" name="cpp_header_image" value="https://d2n0h3y1azxcuk.cloudfront.net/Hosted+Images/paypal-header.png">
		<ul>
		<li><input type="radio" name="os0" value="Annual Support" checked="checked"><span class="esig-supportprice"><?php _e('$129 / yr - Sign up for annual support', 'esig' ); ?></li>
		<li><input type="radio" name="os0" value="Monthly Support"><?php _e('$39 / mo - Sign up for monthly support', 'esig' );?></li>
		</ul>
		<div id="buy-w3-service-area"></div>
		<p>
		<input type="hidden" name="currency_code" value="USD">
		<input type="submit" class="button button-primary button-large"  border="0" name="submit" value="Sign Up for Support" alt="Sign up for Support">
		</p>
</form></div>
		</div>
	</div>
</div>
