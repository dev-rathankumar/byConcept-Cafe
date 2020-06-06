<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<?php
// To default a var, add it to an array
$vars = array(
    'form_head', // will default $data['form_head']
    'action',
    'more_options',
    'pdf_options',
    'form_tail'
);
$this->default_vals($data, $vars);
?>
<div id="page-loader-admin" class="cf" style="display:none;">
    <div class="span">
        <h2 class="cf"> <?php
            $user = new WP_E_User();
            $admin_user = $user->getUserByWPID(get_current_user_id());
            echo $admin_user->first_name;
            ?> , <?php _e('we\'re preparing your document... sip some coffee (or tea) while you wait', 'esig'); ?> </h2>
        <div class="coffee_cup"></div>
    </div>
</div>

<?php 

   echo WP_E_Notice::instance()->esig_print_notice();

?>

<h2 class="esign-form-header"><?php _e('1. Enter your documents name', 'esig'); ?></h2>

<form method="post" class="form-horizontal document-form " id="document_form" enctype="multipart/form-data">

    <?php echo $data['form_head']; ?>

    <input type="hidden" name="document_id" id="document_id" value="<?php echo esc_html($data['document_id']); ?>"/>

    <input type="hidden" name="document_action" id="document_action" value="<?php echo $data['action']; ?>" />


    <div id="titlediv">
        <div id="titlewrap">
            <input type="text" name="document_title" id="document-title" size="30" placeholder="Enter the document's name here" class="text-input span4" value="<?php echo esc_html($data['document_title']); ?>" />
        </div>
    </div>


   <div class="add_form_matt">				
        <h2 class="esign-form-header"><?php _e('2. What needs to be signed?', 'esig'); ?> <span class="settings-title"><?php _e('(copy and paste the content below)', 'esig'); ?></span></h2>

        <?php echo $data['document_editor']; ?>
    </div>
    
    <?php
    $docType = esigget("document_type",$data);
    if($docType =="normal"){
    ?>

    <div class="esign-form-panel basic_esign" >
        <div class="invitations-container">

            <div class="invitations-container-title container-fluid noPadding">
                <div class="row">
                    <div class="col-md-2 need_to_sign">
                        <a href="#" class="tooltip">
                            <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />
                            <span>
                                <?php _e('Each signer is sent a signer specific URL and email invite to sign your document.', 'esig'); ?>
                            </span>
                        </a>
                    </div>
                    <div class="col-sm-10 noPadding need_to_sign_right">
                        <h2 class="esign-form-header"><?php _e('3. Who needs to sign this document?', 'esig'); ?></h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid af-inner-doc" style="margin-top: 10px;" id="document-invitation">
                <div class="row"><div class="col-sm-12">
                <?php
                $inviteObj = new WP_E_Invite();
                echo $inviteObj->reciepent_list($data['document_id']);
                ?>
                </div></div>        
            </div>

        </div>

    </div>
    <?php } ?>
    <!-- Start of Meta Box -->
    <div id="postimagediv" class="postbox esign-form-panel">
        <h3 class="hndle esig-section-title"> <span><?php _e('Document Options', 'esig'); ?></span></h3>
        <div class="esig-inside">

            <div class="container-fluid noPadding">

                <?php echo apply_filters('esig_owner_change_option', '', $data['document_id']); ?>


                <p>

                    <a href="#" class="tooltip">
                        <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />
                        <span>
                            <?php _e('Receive an email notification everytime a signature is added to this document.', 'esig'); ?>
                        </span>
                    </a>
                    <input type="checkbox" <?php echo $data['notify_check']; ?> id="notify" name="notify"><label class="leftPadding-5"><?php _e('Notify me when a signature is added', 'esig'); ?></label>
                    <?php echo apply_filters('esig-document-notification-content', '', array("document_id" => $data['document_id'])); ?>

                </p>

                <p>
                    <a href="#" class="tooltip">
                        <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />
                        <span>
                            <?php _e('Selecting this option will automatically attach your signature from the WP E-Signature admin panel to this document.', 'esig'); ?>
                        </span>
                    </a>
                    <?php $autoAddAllow = apply_filters("esig_is_document_owner",false, esigget('document_owner',$data));
                          
                          if(!$autoAddAllow){
                            echo apply_filters("esig_non_document_owner_content",'',esigget('document_owner',$data));   
                          }
                          $autoAddDisabled= (!$autoAddAllow)?'class="not-owner"': '';
                         
                    ?>
                    <input  type="checkbox" <?php echo $data['add_signature_check'] ." ".  $autoAddDisabled ;?> id="add_signature" <?php echo $data['add_signature_select']; ?> name="add_signature"><label id="esig-auto-add-signature-label" class="leftPadding-5"><?php echo $data['document_add_signature_txt']; ?></label>
                </p>


                <?php echo $data['more_options']; ?>

                <?php echo $data['more_contents']; ?>

                <p id="esignadvanced"><a href="#"><?php _e('(+) Advanced eSign Settings', 'esig'); ?></a></p>
                <p id="esignadvanced-hide" style="display:none"><a href="#"><?php _e('(-) Hide Advanced eSign Settings', 'esig'); ?></a></p>

                <span id="advanced-settings">
                    <p>

                    <h4><?php _e('Print Document button advanced options:', 'esig'); ?></h4>

                    <select name="esig_print_option" data-placeholder="Choose a Option..." class="esig-select2" style="width:500px;" tabindex="9">
                        <?php $print_gl_option = WP_E_General::get_document_print_button(esigget('document_id', $data)); ?>
                        <option value="1" <?= ($print_gl_option == 1) ? 'selected' : null; ?>><?php _e('Only display \'Print Document\' button when document is signed by everyone', 'esig'); ?></option>
                        <option value="2" <?= ($print_gl_option == 2) ? 'selected' : null; ?>><?php _e('Hide \'Print Document\' button always, no matter what.', 'esig'); ?></option>
                        <option value="3" <?= ($print_gl_option == 3) ? 'selected' : null; ?>><?php _e('Display \'Print Document\' button always, no matter what.'); ?></option>
                        <option value="4" <?= ($print_gl_option == 4) ? 'selected' : null; ?>><?php _e('Only Display \'Print Document\' while document waiting for signature.'); ?></option>
                    </select>
                    </p>

                    <?php echo $data['pdf_options']; ?>

                    <?php echo $data['advanced_more_options']; ?>

                </span>
            </div <!-- container end here -->
        </div>
    </div>
    <!-- End of meta box -->
    <?php
    echo apply_filters('esig-add-document-form-meta-box', '');
    ?>

    <div id="esig_submit_section" class="esig-colum-conainer">
        <span class="esig-left">
            <input type="submit" value="Save as draft" class="submit button button-secondary button-large" id="submit_save" name="save">
            <input type="submit" value="Send Document" class="submit button button-primary button-large" id="submit_send"  name="send">
        </span>

        <?php if (WP_E_General::is_auto_save_enabled()): ?>

            <span class="esig-right" style="display:none;" id="esig-preview-document">
                <a id="esig-preview-link" href="<?php echo site_url(); ?>?page_id=<?php echo WP_E_Sig()->setting->get_default_page(); ?>&esigpreview=1&document_id=<?php echo $data['document_id']; ?>&mode=1" target="_blank" class="submit button esig-preview button-large"><?php _e(' Preview Document', 'esign'); ?> </a>
            </span>

        <?php endif; ?>

    </div>
