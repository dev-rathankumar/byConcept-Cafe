



   <div style="padding:0 10px;"><div class="esig-settings-wrap">
           
           <p> <?php  _e('Please authorize your Dropbox account', 'esig') ; ?></p>
           
           <p id="esig-ds-access-coode-container" style="display:none;"> 
               
               <label><?php _e("Access code","esig");?></label><input type="text" name="esig_dropbox_access_code" id="esig-dropbox-access-code" />  </p>
           
               <?php
               
               if (!dsPhpChecking()){
                   $php_version = PHP_VERSION;
                   ?>
           <div id="esig-php-required-msg" style="display:none;"><p>
               <div class="esig-dialog-header"><h3>Connection troubles...</h3></div>
               <div class="esig-alert-icon" align="center"><img src="<?php echo ESIGN_ASSETS_DIR_URI ; ?>/images/search.svg" width="100"></div>
            	
   
               <strong>Hi <?php echo WP_E_Sig()->user->getUserFullName();?>,</strong> Dropbox Sync for WP E-Signature requires a php version of 5.6.4 or greater.  It looks like your site/server is currently running <?php echo $php_version; ?>.  Please contact your webhost and request they upgrade your servers php version so you can setup this awesome feature!
               <div style="margin-top:25px;"></div>
           </div>
           </p>
           <a id="esig-dropbox-authorize-required" href="#" class="button-primary"><?php _e('Authorize', 'esig'); ?></a>
               <?php } else { ?>
           <?php  $authUrl = esigDsSetting::instance()->authUrl();  ?>
		<a id="esig-dropbox-authorize-link" href="<?php echo $authUrl;?>" class="button-primary"><?php _e('Authorize', 'esig'); ?></a>
                <?php }  ?>
               <p id="esig-ds-description" style="display:none;">  Thank you for your access code please save your settings now.</p>   
    </div>
   </div>