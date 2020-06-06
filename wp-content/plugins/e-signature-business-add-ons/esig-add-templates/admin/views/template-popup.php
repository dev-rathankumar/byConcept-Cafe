

<div id="template-option-step2" class="esign-form-panel" style="display:none;">


    <div class="container-fluid text-center" align="center" id="template_top">

        <div class="row">
            <div class="col-sm-12 noPadding">
                <img src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 noPadding">
                <h2 class="esig-popup-header"><?php _e('What Are You Trying To Do?', 'esig'); ?></h2>
            </div>
        </div>


        <p id="create_template" align="center">
            <?php if (class_exists('ESIG_SIF_Admin')) { ?>
                <a href="#"  id="esig_template_create" class="button-primary esig-button-large"><?php _e('+ Create template', 'esig'); ?> </a>
            <?php } else { ?>
                <a href="edit.php?post_type=esign&page=esign-add-document&esig_type=template"  class="button-primary esig-button-large"><?php _e('+ Create template', 'esig'); ?> </a>
            <?php } ?>
        </p>
        <form id="esig_create_template" name="esig-view-document" action="" method="post">
            <p id="no_of_signer" style="display:none;" align="center">
                <input type="text" name="signerno" placeholder="how many signers?" list="signer_list" />
                <datalist  id="signer_list">
                    <option value="1"><?php _e('Signer 1', 'esig'); ?></option>
                    <option value="2"><?php _e('Signer 2', 'esig'); ?> </option>
                    <option value="3"> <?php _e('Signer 3', 'esig'); ?> </option>
                    <option value="5"> <?php _e('Signer 5', 'esig'); ?> </option>
                    <option value="10"> <?php _e('Signer 10', 'esig'); ?> </option>
                </datalist> 
            </p>

            <p id="create_template_basic_next" style="display:none;" align="center">
                <?php
                $doc_id = WP_E_Sig()->document->document_max() + 1;
                WP_E_Sig()->invite->deleteDocumentInvitations($doc_id);
                ?>			
                <a href="#" id="esig_template_basic_next" data-document="<?php echo $doc_id ;?>" class="button-primary esig-button-large"><?php _e('+ Next Step', 'esig'); ?></a>	
            </p>
        </form>

        <p id="upload_template_button" align="center">
            <a href="#" id="esig_template_upload" class="button-primary esig-button-large"><?php _e('+ Use existing template', 'esig'); ?></a>	
        </p>
        <form id="esig_select_template" name="esig-view-document" action="" method="post">
            <p id="template_type" style="display:none;" align="center">
                <select data-placeholder="Choose a Option..." class="chosen-select" tabindex="2" id="esig_temp_doc_type" name="esig_temp_document_type">
                    <option value="doctype"><?php _e('Select Document Type', 'esig'); ?></option>
                    <option value="basic"> <?php _e('Basic Document', 'esig'); ?> </option>

                    <?php
                    if (class_exists('ESIG_SAD_Admin')) {
                        ?>
                        <option value="sad"><?php _e('Stand Alone Document', 'esig'); ?> </option>
                    <?php } ?>

                </select>
            </p>


            <p align="center" id="upload_template_content" style="display:none;">
                <select class="chosen-select" tabindex="2" id="template_id" name="template_id">

                </select>
            </p>
            <p id="insert_template_button" style="display:none;" align="center">
                <input type="hidden" value="Insert template" class="submit button button-primary esig-button-large" id="submit_insert"  name="insert_template">
                <input type="button" value="Next Step" class="submit button button-primary esig-button-large" id="template_insert"  name="template_button">
            </p>

        </form>

    </div>  <!----------------- first part end here ------------->



    <div class="container-fluid noPadding" id="standard_view_popup_bottom" style="display:none">

        <div class="row">
            <div class="col-sm-12">
                <form name="esig-view-document" class="form-inline" id="temp-basic-signer-form" action="" method="post">

                    <input type="hidden" id="hidden_temp_type" name="esig_temp_document_type" value="">
                    <input type="hidden" id="hidden_temp_id" name="template_id" value="">

                    <div class="container-fluid noPadding invitations-container">	

                        <div class="row af-inner">
                            <div class="col-sm-12">

                                <div class="container-fluid" id="recipient_emails_temp">

                                    <div class="row topPadding bottomPadding" id="signer_main_temp">

                                        <div class="col-sm-5 noPadding">
                                            <input class="form-control esig-input" type="text"  name="recipient_fnames[]" placeholder="Signers Name" />
                                        </div>
                                        <div class="col-sm-5 noPadding leftPadding-5">
                                            <input class="form-control esig-input" type="text" name="recipient_emails[]" placeholder="email@address.com" />
                                        </div>  
                                        <div class="col-sm-2 noPadding text-left"> 
                                            <?php echo apply_filters("esig_second_layer_verification", ""); ?>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- [data-group=recipient-emails] -->
                        <div class="row af-inner">
                            <div class="col-sm-12">
                                <div id="esig-signer-setting-box" class="esig-signer-container">
                                    <span class="esig-signer-left"> <?php echo apply_filters("esig-signer-order-filter-temp", ""); ?> </span>
                                    <span class="esig-signer-right"> <a href="#" id="addRecipient_temp"><?php _e('+ Add Signer', 'esig'); ?></a></span>
                                </div>

                            </div>
                        </div>

                        <div class="row af-inner">
                            <div class="col-sm-12">
                                <div class="container-fluid">
                                    <div class="row noPadding">
                                        <div class="col-sm-12">
                                            <?php
                                            echo apply_filters("esig_cc_users_temp", "");
                                            ?>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <div class="row af-inner">
                            <div class="col-sm-12 text-right">
                                
                                <div class="container-fluid noPadding" id="second_layer_temp">
                                       
                                </div>
                                
                            </div>    
                        </div>



                    </div>
                    <p align="center">
                        <input type="submit" value="Insert template" class="submit button button-primary button-large" id="submit_insert"  name="nextstep">
                    </p>
                </form>
            </div></div>
    </div>

</div> 
