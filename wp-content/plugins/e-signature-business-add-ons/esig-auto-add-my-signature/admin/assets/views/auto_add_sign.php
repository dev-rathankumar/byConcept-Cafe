<?php
$settings = new WP_E_Setting();
$user = new WP_E_User();
$signature = new WP_E_Signature();
$wp_user_id = get_current_user_id();

$WPuser = $user->getUserByWPID($wp_user_id);
?>

<div id="esig-auto-add-signature-popup" style="display:none;">

    <div id="auto-add-esign-popup" class="container-fluid">

        <form>

            <div class="auto-signature-agree row">
                <div class="col-sm-12">
                    <center><input type="checkbox" name="esign-add-auto-agree" id="esign-add-auto-signature-agree" value="1" checked><b> <?php _e('I Agree (Required)', 'esig'); ?></b></center>
                </div>
            </div>

            <div class="row" style="max-width: 700px;margin-bottom: 30px;">
                <div class="col-sm-12">
                    <?php echo sprintf(__('I am <b>%s</b> and I understand that by clicking "Agree & Sign" ', 'esig'), $WPuser->first_name); ?>
                    <span class="auto-signature-strong"><b><?php _e('I agree to be legally bound </b></span>by The WP E-Signature ', 'esig'); ?><a href="#" id="esig-terms" class="doc-terms"><b><?php _e('Terms of Use', 'esig'); ?></b></a>
                            <?php _e('and the contents of this agreement in its completed form.', 'esig'); ?>
                            </div>
                            </div>

            <div align="center" class="row auto-signature-pad" style="max-width: 700px;">
                                <div class="col-sm-12">
                                    <?php
                                    
                                    $signatureId = $settings->get('esig-admin-signature-id-'.$wp_user_id);
                                    if(!$signatureId){
                                       $signatureId = $settings->get('esig-admin-signature-id-'.$WPuser->user_id); 
                                    }
                                    
                                    $sig_data = $signature->signatureData($signatureId);

                                    $signature_type = esigget("signature_type",$sig_data); 

                                    $signature_full = '';

                                    if ($signature_type == 'typed') {
                                        $font_choice = $settings->get('esig-signature-type-font' . $WPuser->user_id);
                                        $signature_type = $signature->getSignature_by_type_sigid($signatureId, 'typed');

                                        $font_size = 64 - (strlen($signature_type) * 1.5 );

                                        $signature_full = '<span class="esig-signature-type-font' . $font_choice . '"><font size="' . $font_size . '">' . $signature_type . '</font></span>';
                                    } else {

                                        $image = $signature->display_owner_signature($signatureId);

                                        $signature_full = '<img src="' . $image . '">';
                                    }
                                    ?>



                                    <div class="signature-wrapper-displayonly-signed">

                                        <?php echo $signature_full; ?>

                                    </div>
                                </div>

                            </div>

                            <div class="row" style="position: relative;margin-top: 20px;" align="center">
                                <div class="col-sm-12">
                                    <a href="#" class="esig-blue-btn" id="esig-auto-add-confirm"  title="Agree and submit your signature."><?php _e('Agree & Sign', 'esig') ?></a>
                                </div>
                            </div>

                            </form>
                            </div>
                            </div><!-- esig-auto-add-signature-popup  -->

                            <!-- terms and condition start here -->
                            <div id="esig-terms-condition" style="display:none;">

                                <p id="esign-terms-goback" style="display:none;"><a href="#" id="esign-goback"><?php _e('Go Back', 'esig'); ?></a></p>

                                <div id="esign-terms-content" style="display: none;" style="max-width: 500px;" >  <h1><?php _e('Loading ........', 'esig'); ?></h1></div>

                                <p id="esign-terms-goback" style="display:none;"><a href="#" id="esign-goback"><?php _e('Go Back', 'esig'); ?></a></p>
                            </div>


