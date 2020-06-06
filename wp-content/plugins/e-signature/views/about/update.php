



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
		<div class="esig-updater-option">
		   <p id="esig-auto-update-check"><input type="radio" id="esig-auto-check" name="esig-auto-update" value="1" checked><?php _e('Next time update my add-on\'s automatically, I love automation','esig'); ?></p>
		   <p id="esig-remind-me-check"><input type="radio" id="esig-remind-check" name="esig-auto-update" value="2"><?php _e('Remind me everytime an update is available, I\'d rather click a button','esig'); ?></p>
		
		</div>
		
		<!-- scroller section start here -->
	<div class="esig-updator-scroll-wrap">
        <div class="esig-updater-scroll">
		
		<?php 
				
				
				$plugin_list=json_decode(get_transient('esign-update-list'));
				$auto_downloads =get_transient('esign-auto-downloads');	
				if($auto_downloads)
				{
					foreach($plugin_list as $plugin)
					{
						 if(array_key_exists($plugin->addon_id,$auto_downloads))
						   {
						        $link= 'https://www.approveme.com/downloads/' . str_replace( ' ', '-', strtolower( $plugin->item_name ) );
						        echo '<p> ' . $plugin->item_name .' Add On <sub><a href="'.$link.'" target="_blank">(Version ' . $plugin->new_version . ' available)</a><sub></p>';

						   }
					}
				}
		
		?>
		
		
		</div>
	</div>
		<!-- esig updater button section -->
		<div class="esig-updater-button">
		  
		  <span> <a href="#" class="button"  id="esig-secondary-btn"> <?php _e('I\'M TOO BUSY TODAY','esig');?> </a></span>
           <span> <a href="#" class="button" id="esig-primary-dgr-btn"> <?php _e('INSTALL MY UPDATES NOW','esig');?> </a></span>
		</div>
	
  </div>