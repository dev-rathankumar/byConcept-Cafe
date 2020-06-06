<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$license_info = Esign_licenses::check_license();
$invalid_status = array( 'revoked', 'expired' );
?>

<div id="esig-settings-container">

    <div id="esig-settings-col_head">
        <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/logo.png" width="243px" height="55px" alt="Sign Documents using WP E-Signature" width="84%" style="float:right;">
    </div>

    <div id="esig-settings-col_head">
        <a href="https://www.approveme.com"><img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/approveme-badge.svg" alt="Powered by Approve Me" width="125px" style="margin-left:90px;"></a>
    </div>

    <?php if ( in_array( $license_info->license, $invalid_status ) ) : ?>
    <div id="esig-settings-col4" class="esig-settings-title"></div>
    <div id="esig_view-main" class="esig-text-left" >
        <div id="esig-view-page" class="expired-warning esig-text-left">
       
            <div class="esign-signing-options-col1 esign-signing-options expired-message-admin">
            
                <div id="esig-settings-col3" class="esig-text-left">
                <p class="center"><img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/verified-approveme-black.svg" width="243px" height="55px" alt="ApproveMe Verified" width="84%" style="float:right;"></p>
                    <p><?php _e( 'Hey', 'esig' ); ?> <?php echo WP_E_Sig()->user->getUserFullName(WP_E_Sig()->user->esig_get_super_admin_id());?><?php _e( '!', 'esig' ); ?></p>
                    <p><?php _e( 'It looks like you’re trying to create a new document with an expired license (whoops)! We’d love to help get you back to creating documents, but you’ll need to renew your license first.' , 'esig' ); ?></p>
                    <p><?php _e( 'The three main reasons for this are', 'esig' ); ?>:</p>
                    <p>
                        <ol>
                            <li>
                                <strong><?php _e( 'Legalities.', 'esig' ); ?></strong> <?php _e( 'In order to remain legally sound and UETA/ESIGN/GDPR compliant, we have to maintain strict adherence to the constantly-changing electronic document signing space. This requires us to adapt the plugin to new laws and regulations as they are created, which means regular updating of your plugin is crucial.', 'esig' ); ?>
                            </li>
                            <li>
                                <strong><?php _e( 'Security.', 'esig' ); ?></strong> <?php _e( 'Similar to legalities, the security protocols in today’s tech world are hard to keep up with! Things that were once secure are now being replaced with new technology, so using a plugin that is out of date may mean the security measures that were once top tier may now be outdated. We work hard to implement the highest security measures at all times for you and your clients to be confident in your document signing platform.', 'esig' ); ?>
                            </li>
                            <li>
                                <strong><?php _e( 'Support.', 'esig' ); ?></strong> <?php _e( 'Support is essential when working with a WordPress plugin, because there are so many variables living in the same environment, being forced to work together. Our team is here and super happy to help, but support is only available to active license holders. By renewing your license, you are allowing us to work with you to make sure things are looking good on your site.', 'esig' ); ?>
                            </li>
                        </ol>
                    </p>
                    <p><?php _e( 'We want to make sure we provide a legally binding, UETA/ESIGN compliant, secure way for you to get your documents signed online, and having an active license plays a HUGE part in this.', 'esig' ); ?></p>
                    <p>
                        <?php _e( 'The ApproveMe Team', 'esig' ); ?>
                    </p>
                    <p class="center">
                        <?php printf( __( '<a href="%s">Click here</a> to renew your current license!', 'esig' ), Esign_licenses::get_renewal_link() ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div id="esig-settings-col4" class="esig-settings-title"><h2><?php _e('What kind of document are you creating?', 'esig'); ?></h2></div>

    <div id="esig_view-main" align="center">
        <div id="esig-view-page" align="center">

            <div id="esig-settings-col3">


                <div class="esign-signing-options-col1 esign-signing-options">
                    <a href="#" id="basic_view">
                        <div id="esig-add-basic" class="esig-doc-options esig-add-document-hover">
                            <div class="icon"></div>
                            <div class="text"><?php _e('+ Basic', 'esig'); ?></div>
                        </div>
                    </a>
                    <!-- basic document benefits start -->
                    <div class="benefits">
                        <p><?php _e('Basic Benefits', 'esig'); ?></p>
                        <div class="plus-li"><?php _e('1 or more signers', 'esig'); ?></div>
                        <div class="plus-li"><?php _e('Customizable for each recipient', 'esig'); ?></div>
                        <div class="plus-li"><?php _e('Send signer invites email with WordPress', 'esig'); ?></div>
                        <div class="plus-li"><?php _e('Perfect for sales contracts, estimates, etc.', 'esig'); ?></div>
                    </div>
                </div>

            </div>

            <?php echo $data['more_option_page']; ?>

        </div>

    </div> <!-- esig page center end here  -->

    <div id="esig-settings-col4" style="text-align: center;">
        <p align="center"><img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/mini-boss.svg" alt="eSign Boss" width="75px"> <span><?php _e('Quit paying monthly fees and start signing with WP E-Signature -', 'esig'); ?> <a href="https://www.approveme.com/wordpress-electronic-digital-signature-add-ons/" target="_blank" class="esig-extension-headlink"><?php _e('Browse add-ons', 'esig'); ?></a></span></p>
    </div>
    <?php endif; ?>

</div>




<div id="standard_view_popup" class="esign-form-panel" style="display:none">


    <form name="esig-view-document" class="form-inline" id="esig-view-form" action="" method="post">
        <input type="hidden" name="document_action" value="save">

        <div class="container-fluid"  align="center">

            <div class="row">
                <div class="col-md-12">
                    <div class="container-fluid invitations-container">

                        <div class="row">
                            <div class="col-md-12">
                                <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="esig-popup-header"><?php _e('Who needs to sign this document?', 'esig'); ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 esig-signer-view">
                                <div id="recipient_emails" class="container-fluid noPadding">

                                    <div id="signer_main" class="row" >

                                        <div class="col-sm-5 noPadding" >
                                            <input class="form-control esig-input" type="text" name="recipient_fnames[]" placeholder="Signers Name" />
                                        </div>
                                        <div class="col-sm-5 noPadding leftPadding-5">
                                            <input type="text" class="form-control esig-input" name="recipient_emails[]" placeholder="email@address.com" />
                                        </div>
                                        <div class="col-sm-2 noPadding text-left">
                                            <?php $esig_second_layer_verification = apply_filters("esig_second_layer_verification", ""); ?>
                                            <?php echo $esig_second_layer_verification; ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div id="esig-view-signer-add" class="row">
                            <div class="col-sm-6 text-left">

                                <?php echo apply_filters('esig-signer-order-filter', '', ''); ?>

                            </div>
                            <div class="col-sm-6">

                                <span style="padding:10px;" >  <a href="#" id="addRecipient_view"><?php _e('+ Add Signer', 'esig'); ?> </span></a>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <div class="container-fluid" >
                        <div class="row">
                            <div class="col-md-12 noPadding">

                                <?php echo apply_filters("esig_cc_users", ""); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        <p align="center" class="esig_nextstep">
            <input type="submit" value="Next Step" class="submit button button-primary button-large" id="submit_send"  name="nextstep">

        </p>

    </form>
    <span class="settings-title"></span>
</div>




<?php
$tail = apply_filters('esig-document-footer-content', '', array());
echo $tail;
?>
