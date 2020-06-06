<?php

require_once('../../../../../../wp-load.php');

if ( file_exists( ABSPATH . 'wp-config.php') ) {

	/** The config file resides in ABSPATH */
	require_once( ABSPATH . 'wp-config.php' );

} elseif ( file_exists( dirname(ABSPATH) . '/wp-config.php' ) && ! file_exists( dirname(ABSPATH) . '/wp-settings.php' ) ) {

	/** The config file resides one level above ABSPATH but is not part of another install */
	require_once( dirname(ABSPATH) . '/wp-config.php' );

}
?>

<div id="esig-pdf-error-dialog">

   <div class="esig-dialog-header">
   		
   		 <h3>Connection troubles...</h3>
        	
   </div>
   
    <div class="esig-alert-icon" align="center"><img src="<?php echo ESIGN_ASSETS_DIR_URI ; ?>/images/search.svg" width="100"></div>
   
 <p><?php 
 
 $esig_user= new WP_E_User();
  $esig_settings= new WP_E_Setting();
      
      $wpid = get_current_user_id();
      
      $users = $esig_user->getUserByWPID($wpid);
      
     // $esig_dropbox = ESIGDS_Factory::get('dropbox');
      if(!class_exists('ESIG_PDF_Admin'))
      {
        echo $users->first_name . ",";?><?php _e(" I apologize, but we're having trouble connecting to your <em>Save as PDF add-on</em> (which is required to use this feature). <strong> For your site to magically create PDF's, you will definitely need our 'Save as PDF' add-on.","esig");?></strong></p>
      <?php 
      }
      elseif(!esigDsSetting::instance()->isAuthorized())
      {
        echo $users->first_name . ",";?> <?php _e("I apologize, but we're having trouble connecting to your <em>Dropbox</em> (which is required to use this feature). <strong>  For your site to magically save PDF's, into Dropbox you need to authorize with Dropbox.","esig");?></strong></p>
    <?php 
    
      }
    
    
    if($esig_settings->esig_license_expired())
    {
    	$license_key=$esig_settings->get_generic('esig_wp_esignature_license_key') ; 
     ?>  
     
     <p> <?php _e("To complicate the situation, It looks like your license is expired... you will need to have a valid license in order install this add-on.","esig");?></p>
  
   <!-- esig updater button section -->
		<div class="esig-updater-button">
           <span> <a href="https://www.approveme.com/checkout/?edd_license_key=<?php echo $license_key ;  ?>&download_id=2660" class="button" id="esig-primary-dgr-btn"> <?php _e('Please Renew My License','esig');?> </a></span>
		</div>
		
	<?php 
	 } // expired licene end here 
	 elseif(!$esig_settings->esign_super_admin())
	 {
	 	$super_admin_id =$esig_user->esig_get_super_admin_id();
	 	$s_first_name =$esig_user->getUserFullName($super_admin_id);
	 	$s_last_name =$esig_user->getUserLastName($super_admin_id);
	 ?>
	 	<p>To complicate the situation, only the super admin <?php echo $s_first_name . " " . $s_last_name ;  ?> can install/activate add-ons.  </p>
  
   <!-- esig updater button section -->
		<div class="esig-updater-button">
           <span> <a href="mailto:<?php echo $esig_user->getUserEmail(); ?>?Subject=%28%20IMPORTANT%20%29%20-%20Please%20enable/install%20Save%20as%20PDF%20for%20WP%20E-Signature&Body=<?php echo $s_first_name ; ?>%2C%0A%0AI%20apologize%20for%20the%20inconvenience%2C%20but%20I%27m%20having%20trouble%20connecting%20to%20the%20Save%20as%20PDF%20add-on%20%28which%20is%20required%20to%20magically%20create%20PDF%27s%20with%20the%20WP%20E-Signature%20plugin%29.%20%20It%20looks%20like%20you%20are%20the%20Super%20Admin%20user%20and%20only%20the%20Super%20Admin%20user%20can%20manage%20these%20settings.%0A%0AWhen%20you%20get%20a%20chance%20can%20you%20look%20into%20this%20issue%20at%3A%0A%0A<?php echo site_url(); ?>/wp-admin/admin.php%3Fpage%3Design-addons%26tab%3Dall%0A%0AThanks%20a%20million%21" class="button" id="esig-primary-dgr-btn"> Email <?php echo $s_first_name ; ?> to request their assistance</a></span>
		</div>
	<?PHP
	 } // license activation check end here
	 elseif($esig_settings->get_generic('esig_wp_esignature_license_active') !="valid")
	 {
	 ?>
	 	<p><?php _e("To complicate the situation, it also looks like you do not have a license key (which means your site cannot communicate with ApproveMe and receive critical updates, downloads etc)... you will need to have a valid license in order to install this add-on.","esig")?>;</p>
  
   <!-- esig updater button section -->
		<div class="esig-updater-button">
           <span> <a href="https://www.approveme.com/#pricingPlans" class="button" id="esig-primary-dgr-btn"> <?php _e('Get My License Key','esig');?> </a></span>
		</div>
	<?PHP
	 } // license activation check end here 
         elseif(!esigDsSetting::instance()->isAuthorized())
         {
         ?>
   
         <div class="esig-updater-button">
           <span> <a href="admin.php?page=esign-misc-general" class="button" id="esig-primary-dgr-btn"> <?php _e('Authorize Now','esig');?> </a></span>
		</div>
             
        <?php
          }
	 else 
	 {
	 ?>
	 
	 <div class="esig-updater-button">
           <span> <a href="admin.php?page=esign-addons" class="button" id="esig-primary-dgr-btn"> <?php _e('INSTALL IT & TURN IT ON FOR ME NOW','esig');?> </a></span>
		</div>
	 	
	<?php
	 } // final block end here 
	?>
 
</div>