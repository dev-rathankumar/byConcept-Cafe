</form>	


<?php
if (array_key_exists('form_tail', $data)) {
    echo $data['form_tail'];
}
?>



<div class="af-inner_edit" id="standard_view_popup_edit" style="display:none;">

    <div class="container-fluid noPadding text-center invitations-container_ajax">

        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid invitations-container">

                    <div class="row">
                        <div class="col-md-12">
                            <img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="esig-popup-header"><?php _e('Who needs to sign this document?', 'esig'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12" id="esig-signer-edit-wrapper">
                    
            </div>
        </div>

        <div class="row" align="center">
            <div class="col-md-12">
            <input type="button" value="Save Changes" class="submit button button-primary button-large" id="submit_signer_save" name="signersave">
            </div>
        </div>

    </div>  

</div>		





<!--E-signature dialog content here -->	
<div id="esig-dialog-content" style="display: none;"> </div>
