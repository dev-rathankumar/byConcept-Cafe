<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php'); ?>

<h3><?php // _e('Licenses', 'esig' );?></h3>

 <?php

 echo $data['message'];

 echo WP_E_Sig()->notice->esig_print_notice();

 ?>
<form name="settings_form" class="settings-form license-form" method="post" action="<?php echo $data['post_action']; ?>">
<table class="form-table">
	<tbody>


       <!-- Display Only When No License is Entered -->

       <?php

        $result = Esign_licenses::check_license() ;

        WP_E_Sig()->common->force_to_check_update();


       if($result->license !='valid' && $result->license !== 'inactive') { ?>

       <p><h3 class="esig-activate-license esign-feedback-alert"><span class="icon-esig-alert"></span> <?php
        Esign_licenses::esig_super_admin(true); ?>, <?php _e('You will need to activate your license to get started!','esig');?></h3></p>

       	<?php  } // invalid end here

           // display form
           Esign_licenses::get_license_form($result);

           if($result->license == 'valid' &&  $result->license_type != 'business-license')
           {

                $license_type_data = array_values( (array) $result->license_price );
                $license_type_keys = wp_list_pluck( $license_type_data, 'name' );
                $business_key      = array_values( array_keys( $license_type_keys, 'Business License' ) );
                $business_key      = $business_key[0];
                $current_price_id  = false;
                foreach ( $license_type_data as $key => $license_type ) {
                    if ( $result->license_type === sanitize_title( $license_type->name ) ) {
                        $current_price_id = $key;
                        break;
                    }
                }
                $amount = ( $license_type_data[ $business_key ]->amount ) - ( $license_type_data[ $current_price_id ]->amount );
                if ( $amount > 0 ) :
                ?>
                <div class="esig-add-on-block esig-pro-pack open">
                 <?php echo sprintf(__('<h3>Get the E-Signature Business Pack</h3>
                    <p style="display:block;">The Business Pack gets you access to WP E-Signature add-ons that unlock so much more functionality and features that WP E-Signature can do... like Dropbox Sync, Signing Reminders, Save as PDF, Stand Alone Documents, URL Redirect After Signing, Custom Fields and more. With the Business Pack, you get access to all our ApproveMe built WP E-Signature Add-ons plus any more we build in the next year (which will be a ton).</p>
                    <a class="esig-btn-pro" href="https://www.approveme.com/e-signature-upgrade-license/" target="_blank">Get all our add-ons for $%d</a> ','esig'),$amount); ?>

                </div>
                <?php
                endif;

           }


           // if license invalid then show license content.
           if($result->license !='valid') {

           ?>


        <tr><td colspan="3"><br><br>
        <div id="esig-settings-col1">
        <span class="license-login"></span><img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/licensing/login.jpg" class="onboarding-screenshots" />
        <br /><strong><?php _e('1. Log into your account','esig') ; ?> </strong><br />
        <p><?php _e('Login to your account at','esig'); ?> <a href="https://www.approveme.com/profile/" target="_blank"><?php _e('ApproveMe','esig'); ?></a><?php _e('. An email was sent with your username and password at the time of purchase.','esig'); ?></p>
        </div>

        <div class="steps-chevron">
        <span class="icon-chevron-right"></span>
        </div>

        <div id="esig-settings-col1">
        <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/licensing/purchase-history.jpg" class="onboarding-screenshots" />
        <br /><strong><?php _e('2. Click on Purchase History','esig'); ?> </strong><br />
        <p><?php _e('Once you\'ve logged in.  Click on "My Downloads" and then click "Purchase History".','esig') ?></p>
        </div>

        <div class="steps-chevron">
        <span class="icon-chevron-right"></span>
        </div>

        <div id="esig-settings-col1">
        <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/licensing/license-key.jpg" class="onboarding-screenshots" />
        <br /><strong><?php _e('3. Click the Key icon','esig'); ?> </strong><br />
        <p><?php _e('Clicking on the magical "Key Icon" will reveal your top secret license key.  An <em>active</em> license key is required for updates and support.','esig'); ?></p>
        </div>
        <!-- End of Display When No License is Entered -->
        </tr>
        <?php
          }// license content logic end here
        ?>
	</tbody>
</table>



</form>

 <!-- Addons progress bar -->
 <div class="esig-addon-devbox" style="display:none;">
  <div class="esig-addons-wrap">
    <div class="progress-wrap">
      <div class="progress">
        <span class="countup"></span>
      </div>
    </div>
  </div>
</div>
