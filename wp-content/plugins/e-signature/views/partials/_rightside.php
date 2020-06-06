<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>      


	 <?php 
            // add new addons here (array push)
        $esig_plugin_list = array(
           
               'esig-active-campaign/esig-active-campaign.php'=>'ActiveCampaign',
                'esig-assign-signer-order/esig-assign-signer-order.php'=>'Assign Signer Order',
                'esig-unlimited-sender-roles/esig-usr.php'=>'Unlimited Sender Roles',
                'esig-upload-logo-and-branding/esig-upload-logo-brand.php'=>'Upload Logo And Branding',
                'esig-attach-pdf-to-email/esig-pdf-to-email.php'=>'Attach PDF to Email',
                'esig-auto-add-my-signature/esig-aams.php'=>'Auto Add My Signature',
                'esig-document-activity-notifications/esig-dan.php'=>'Document Activity Notifications',
                'esig-add-templates/esig-at.php'=>'Document Templates',
                'esig-dropbox-sync/esig-ds.php'=>'Dropbox Sync',
                'esig-save-as-pdf/esig-pdf.php'=>'Save As PDF',
                'esig-signer-input-fields/esig-sif.php'=>'Signer Input Fields',
                'esig-signing-reminders/esig-reminders.php'=>'Signing Reminders',
                'esig-stand-alone-docs/esig-sad.php'=>'Stand Alone Documents',
                'esig-url-redirect-after-signing/esig-url.php'=>'URL Redirect After Signing',
              
            );
            
      $array_Plugins = get_plugins();
      
    
     
      // check if not install all 
      if(count(array_intersect_key($array_Plugins, $esig_plugin_list)) < count($esig_plugin_list) && is_esig_super_admin() && !Esign_licenses::is_business_license()) {
    ?>
    
    <div class="esig-sidebar-ad">
	<h3><?php _e('Documents Signed 30% Faster', 'esig' ); ?></h3>
	<p align="center"><span class="esig-ad-subline">- <?php _e('Signature Automation', 'esig' );?> -</span><br>
	<img src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/add-on-ad1.svg">
	<span class="esig-ad-text"><?php _e('Get an extra hour every week with signer reminders, stand alone docs, and E-Signature awesomeness!', 'esig' );?></span></p>
	<p align="center"><a href="?page=esign-addons&tab=get-more" class="esig-red-btn"><span><?php _e('Get Premium Add-Ons', 'esig' );?></span></a></p>
	
	</div> 
	
   
        
	<div class="postbox premium-modules" style="margin-top:14px;border-color: #14AF3F;border-width: 5px;background: #FDFCE5;">
            <h3 class="hndle"><span style="color: #13759B;"><?php _e('Get a Premium Module', 'esig' );?></span></h3>
            <div class="inside">
                <ul>
               
                <?php 
                
                foreach($esig_plugin_list as $plugin_file => $plugin_name) 
				 {
                        
                       if (!array_key_exists($plugin_file,$array_Plugins))
                       {
                          
                           echo '<li class="li_link">
                       		<a href="https://www.approveme.com/downloads/">'. $plugin_name  .'</a>
                        </li> ';
                       }
                 }
                
                ?>
                
                    
					
                </ul>
            </div>
        </div>
        
	<?php 
    // showing premium module end here 
    }
    ?>

<div id="esig-support" class="postbox">

	<h3 class="hndle esig-section-title"><span><?php _e('Found a bug? Need support?', 'esig' ); ?></span></h3>
		<div class="inside">
       
        <?php add_thickbox();  ?>
        <p><a href="https://www.youtube.com/embed/RWNKE1_qFwU?&autoplay=1&rel=0&theme=light&hd=1&autohide=1&showinfo=0&color=white&showinfo=0?TB_iframe=true&width=960&height=500" class="button-secondary thickbox">
		<?php _e('Watch Getting Started Video', 'esig' ); ?></a></p>
		
		<?php
		 if(is_esig_super_admin())
		{
			?>
        <p><a id="esig-report-bug" class="button-secondary" href="https://www.approveme.com/support/" target="_blank"><?php _e('Report a Bug', 'esig' ); ?></a></p>
	
	<p> <a target="_blank" class="button-secondary" href="https://www.approveme.com/support"><?php _e('Open a Support Ticket', 'esig' );?></a></p>	
	<?php } ?>
	<p> <a target="_blank" class="button-secondary" href="http://approveme.uservoice.com/forums/243780-general"><?php _e('Submit Feature Idea', 'esig' );?></a></p>				

	<p><b><?php _e('Getting Started', 'esig' );?></b><br>
	<a target="_blank" href="https://www.approveme.com/wp-digital-signature-plugin-docs/"><?php _e('Quick Start Guide', 'esig' );?></a><br>
	<a target="_blank" href="https://www.approveme.com/wp-digital-signature-plugin-docs/faq/"><?php _e('Frequently Asked Questions', 'esig' );?></a></p>
	
</div>


       
    
     
