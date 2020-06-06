<div id="esig-access-code-verification" style="display:none">
    
        <input type="hidden" id="esig-slv-email-address">
        
        <h2><span id="heading"> <?php _e('Security Verification', 'esig') ?></span><h2>
               
                <div id="access_cssmenu">
                    <ul>
                        <li><a href="#none" id="esig-access-none" style="color: #DFDFDF;"><?php _e('NONE ', 'esig'); ?><br /></a></li>
                        <li><a href="#access_code" id="esig-access-code" style="color: #DFDFDF;"><?php _e('ACCESS CODE', 'esig'); ?></a></li>
                    </ul>
                </div>
                <div class="tab">
                    
                    <div id="none" class="tab-content">
                        <br><div class="slv-instructions">Hey <?php echo sprintf(__(' %s! You\'re gonna need to select your desired security verification to go to next step.', 'esig'), WP_E_Sig()->user->getUserFullName()); ?></div>
                        <input type="submit" value="Next Step" class="btn btn-info" id="verification_submit_send" name="nextstep"  disabled>
                    </div>
                    
                    <div id="access_code" class="tab-content" style="display: none">
                        <span id="heading2" class="slv-instructions"><?php _e('You are required to (manually) give this access code to your recipient', 'esig'); ?></span><br>
                       
                        <div class="slv-access-inputs"><label class="slv-label"><?php _e('Access Code:', 'esig'); ?></label><br>
                            <input type="text" name="esig_access_code" id="esig_access_code" class="enter_access" value="" placeholder="<?php _e("Enter your access code","esig");?>"></div>
                        <div align="left" id="esig_verification" >
                            <input type="submit" value="Next Step" id="submit_send_layer" name="Submit"><input align="center" type="submit" value="Cancel" class="esig-access-close" id="submit_send_cancel">
                            
                        </div>

                    </div>
                </div>
  
                
</div>