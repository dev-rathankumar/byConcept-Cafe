


<div id="approval_signer_view_popup" class="esign-form-panel" style="display:none">

    <div class="container-fluid approval-invitations-container" id="esig-approval-signer-setting" align="center">	

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
                <div class="esig-popup-header"><?php _e('Who needs to approve this document?', 'esig'); ?></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 af-inner">

                <div id="recipient_approval_signer" class="container-fluid">

                    
                    <?php 
                     $count = 0;
                    $document_id = esigget('document_id');
                    $assign_approval = WP_E_Sig()->meta->get($document_id, 'esig_assign_approval_signer');
                    
                    $api = WP_E_Sig();

                    $signer_order = $api->meta->get($document_id, 'esig_signer_order_sad');
                    $signer_order_checked = (isset($signer_order) && $signer_order == 'active') ? "checked" : "";

                    if(!$assign_approval){
                        
                    ?>
                    
                    <div id="signer_main" class="row topPadding bottomPadding no-move">
                        <div class="col-sm-5 noPadding"><input type="text" class="form-control esig-input" name="approval_signer_fname[]"  readonly="" placeholder="Signer 1(from website)" /></div>
                        <div class="col-sm-5 noPadding leftPadding-5"><input type="text" class="form-control esig-input" name="approval_signer_emails[]" readonly="" placeholder="Signer 1(from website)"/></div>
                        <div class="col-sm-2"> </div>
                    </div>

                    <?php
                    }
                    else {
                   
                    if ($document_id) {


                        $signers = json_decode($api->meta->get($document_id, 'esig_assign_approval_signer_save'), true);

                       if ($signer_order_checked == "checked") {  ?>
                      
                      <div id="signer_main" class="row topPadding bottomPadding no-move">
                     <div class="col-sm-2 noPadding" style="width:5% !important;">1.</div>
                     <div class="col-sm-4 noPadding" style="width:39% !important;"><input class="form-control esig-input" type="text" name="approval_signer_fname[]" placeholder="Signer 1(from website)" readonly /> </div>
                  <div  class="col-sm-4 noPadding leftPadding-5" style="width:39% !important;"><input type="text" class="form-control esig-input" name="approval_signer_emails[]" placeholder="Signer 1(from website)" readonly /></div>
                  <div  class="col-sm-2  text-left" style="width:14% !important;"></div></div> 
                    
                        <?php } else { ?>

                      <div id="signer_main" class="row topPadding bottomPadding no-move">
                     <div class="col-sm-5 noPadding"><input class="form-control esig-input" type="text" name="approval_signer_fname[]"  placeholder="Signer 1(from website)" readonly /> </div>
                  <div  class="col-sm-5 noPadding leftPadding-5"><input type="text" class="form-control esig-input" name="approval_signer_emails[]" placeholder="Signer 1(from website)" readonly /></div>
                  <div  class="col-sm-2  text-left"></div></div> 
                         <?php 
                        }
                        for ($i = 1; $i < count($signers[1]); $i++) {
                            
                            $email_address = $signers[1][$i];
                            $signer_fname = $signers[0][$i];
                            $j = $i + 1;
                            ?>

                           
                                <?php if ($signer_order_checked == "checked") { ?>
                                   
                                    <div id="signer_main" class="row">
                  <div class="col-sm-2 noPadding" style="width:5% !important;"><span id="signer-sl" class="signer-sl"><?php echo $j; ?></span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span></div>
                  <div class="col-sm-4 noPadding" style="width:39% !important;"><input class="form-control esig-input" type="text" name="approval_signer_fname[]" value="<?php echo $signer_fname; ?>" placeholder="Signers Name" /> </div>
                  <div  class="col-sm-4 noPadding leftPadding-5" style="width:39% !important;"><input type="text" class="form-control esig-input" name="approval_signer_emails[]" value="<?php echo $email_address; ?>" placeholder="email@address.com" /></div>
                  <div  class="col-sm-2  text-left" style="width:14% !important;"><span id="esig-del-signer" class="deleteIcon"></span></div></div>   
                  
                                <?php } else { ?>

                                   <div id="signer_main" class="row topPadding bottomPadding">
                  <div class="col-sm-5 noPadding"><input class="form-control esig-input" type="text" name="approval_signer_fname[]" value="<?php echo $signer_fname; ?>" placeholder="Signers Name" /> </div>
                  <div  class="col-sm-5 noPadding leftPadding-5"><input type="text" class="form-control esig-input" name="approval_signer_emails[]" value="<?php echo $email_address; ?>" placeholder="email@address.com" /></div>
                  <div  class="col-sm-2  text-left"><span id="esig-del-signer" class="deleteIcon"></span></div></div>  
                                 <!--<input type="text" name="recipient_lnames[]" placeholder="Signers last name" /> -->
                                <?php } ?>
                           
                            <?php
                            $count++;
                        }
                    }
                    } // else end here 
                    ?>
                </div> 

            </div>
        </div>

        <?php
        $display = ($count > 1) ? 'block;' : 'none';
        ?>
        <div class="row">
            <div class="col-sm-12">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6 text-left"> <span id="esign-approval-signer-order" style="display: <?php echo $display; ?>;" >

                                <div><label> <input type="checkbox" id="esign_assign_approval_signer_order" name="esign_assign_approval_signer_order" value="1" <?php echo $signer_order_checked; ?> >
                                    <?php _e('Assign signer order', 'esig'); ?></label></div>

                            </span></div>
                        <div class="col-sm-6 text-right topMargin bottomMargin">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-8 text-left"><a href="#" id="add-approval-signer"><?php _e('+ Add Signer', 'esig'); ?></a></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>



        <p align="center">
            <input type="button" value="Save" class="submit button button-primary button-large" id="submit_approval_signer_save" name="signersave">
        </p>




    </div>

</div>



