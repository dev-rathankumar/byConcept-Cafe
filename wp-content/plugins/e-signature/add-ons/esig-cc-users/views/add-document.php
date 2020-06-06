
<?php
$document_id = ESIG_GET('document_id');
if (Cc_Settings::is_cc_enabled($document_id)) {
    $checked = "checked";
} else {
    $checked = "";
}
?>
<p>
    <a href="#" class="tooltip">
        <img src="{{asseturl}}/images/help.png" height="20px" width="20px" align="left"><span> <?php _e('Have you ever wanted to include a non-signer on a document? Maybe a manager or possibly a supervisor. You didn’t necessarily require a legal signature from the recipient but wanted to cc them in the process so they can preview and access the contract for their own records.', 'esig'); ?></span>
    </a><input type="checkbox" id="esig_carbon_copy" <?php echo $checked; ?> name="esig_carbon_copy" value="1">
    <label class="leftPadding-5"><?php _e('+CC (Carbon copy) Recipients (when this document is successfully Signed)', 'esig'); ?></label>
</p>


<div id="esig_carbon_copy_setting" style="display:none;margin-left:75px;">


    <div id="esig-sad-document-cc" class="container-fluid" align="center">

        <div class="row">
            <div class="col-sm-12">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-sm-12">
                            <img src="<?php echo(ESIGN_ASSETS_DIR_URI); ?>/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="esig-popup-header"><?php _e('Cc user information', 'esig'); ?></div>
            </div>
        </div>

        <div class="row cc_recipient_emails_container">
            
            <div id="cc_recipient_emails" class="col-sm-12 af-inner">

                <div class="container-fluid">


                <?php
                $cc_edit_users = Cc_Settings::get_cc_information($document_id, false);

                $html = '<div id="error"></div>';
                if (is_array($cc_edit_users) && count($cc_edit_users) > 0) {

                    foreach ($cc_edit_users as $user_info) {

                        $fnames = esc_html(stripslashes($user_info->first_name));
                        $emails = $user_info->email_address;
                        ?>


                    <div id="recipient_emails" class="row">
                        <div class="col-sm-5 noPadding">  <input type="text" class="form-control esig-input"  name="cc_recipient_fnames[]" placeholder="<?php _e('CC Users Name', 'esig'); ?>"  value="<?php echo $fnames; ?>"/></div>
                            <div class="col-sm-5 noPadding leftPadding-5">     <input type="text" class="form-control esig-input" name="cc_recipient_emails[]" placeholder="<?php _e('email@address.com', 'esig');?>"  value="<?php echo $emails; ?>" /> </div>
                            <div class="col-sm-2 text-left"> <span id="esig-del-signer" class="deleteIcon"></span></div>
                        </div>
                        <?php
                    }
                } else {
                    ?>


                    <div id="recipient_emails" class="row">
                        <div class="col-sm-5 noPadding"> <input type="text" class="form-control esig-input" name="cc_recipient_fnames[]" placeholder="<?php _e('CC Users Name', 'esig'); ?>"  value=""/></div>
                        <div class="col-sm-5 noPadding"> <input type="text" class="form-control esig-input" name="cc_recipient_emails[]" placeholder="<?php _e('email@address.com', 'esig'); ?>"  value="" /></div>
                        <div class="col-sm-2 noPadding leftPadding-5"> <span id="esig-del-signer" class="deleteIcon"></span></div>
                    </div>


                    <?php
                }

              
                ?>
                </div>
            </div>   
        </div>
        
        <div id="error" style="display: none;"></div>

        <div class="row topMargin bottomMargin">
            <div class="col-sm-12">
                <div class="container-fluid" style="width:80%;">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <a href="#" id="add-sad-esig-cc"><?php _e('+ CC', 'esig') ; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div align="center" class="esig_nextstep">
            <input type="submit" value="Save" class="submit button button-primary button-large" id="esig-sad-cc-save"  name="cc_saving">

        </div>

    </div>

</div>

