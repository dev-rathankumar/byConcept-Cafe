



  <div id="esig-update-popup" style="display:none;">
  
		
		<div class="esig-dialog-header">
        	<div class="esig-alert">
            	<span class="icon-esig-alert"></span>
            </div>
		   <h3><?php _e('Important: Security & Critical Updates Available','esig'); ?></h3>
		   
		   <p class="esig-updater-text"><?php 
		   
		   $esig_user= new WP_E_User();
		    
		    $wpid = get_current_user_id();
		    
		    $users = $esig_user->getUserByWPID($wpid); 
		    echo $users->first_name . ","; 
		   
		   ?>
		   
		   
		  <?php _e('we have great news! We recently added some new features along with some 
		   critical security updates for <a href="https://www.approveme.com/wp-digital-e-signature?updates" target="_blank">WP E-Signature</a>. How would you like to handle these updates?','esig'); ?> </p>
		</div>
		
		<div class="updater-row"><hr/></div>
		
		<!-- updater setting option -->
		
		
		<!-- scroller section start here -->
	<div class="esig-updator-scroll-wrap">
        <div class="esig-updater-scroll">
		
		<?php 
				echo "<p>". get_option('esig-core-update') . "</p>";	
		
		?>
		
		
		</div>
	</div>
		<!-- esig updater button section -->
	<div class="esig-updater-button">
		  <?php $esig_core_update_url = get_option('esig-core-update-url'); ?>
		  <span> <a href="#" class="button esig-secondary-btn"  id="esig-core-remind-btn"> <?php _e('Remind Me Later','esig');?> </a></span>
           <span> <a href="<?php echo $esig_core_update_url; ?>" class="button esig-dgr-btn" id="esig-update-core-btn"> <?php _e('Update Now','esig');?> </a></span>

	</div>
	
  </div>