



  <div id="esig-update-popup" style="display:none;">
  
		
		<div class="esig-dialog-header">
        	<div class="esig-alert">
            	<span class="icon-esig-alert"></span>
            </div>
		   <h3><?php _e('Important: Your update has been failed','esig'); ?></h3>
		   
		   <p class="esig-updater-text"><?php 
		   
		   $esig_user= new WP_E_User();
		    
		    $wpid = get_current_user_id();
		    
		    $users = $esig_user->getUserByWPID($wpid); 
		    echo $users->first_name . ","; 
		   
		   ?>
		   
		   
		  <?php _e('I\'m really embarrassed but there appears to have been a slight issue installing your add-ons from the ApproveMe server.  We\'ll remind you again in 24 hours but if the problem persists please email email support by clicking the link below.','esig'); ?> </p>
		</div>
		
		
		<!-- esig updater button section -->
		<div class="esig-updater-button">
           <span> <a href="https://www.approveme.com/support/" class="button" id="esig-primary-dgr-btn"> <?php _e('EMAIL SUPPORT NOW','esig');?> </a></span>
		</div>
	
  </div>
