<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

<?php 

  echo $data['message']; 
  
   $esig_notice = new WP_E_Notice();
   
   echo $esig_notice->esig_print_notice();

?>


    <?php
    
     $logo_alignment = apply_filters('esig-logo-alignment','',esigget('wpUserId', $data));
    
    ?>


 <div <?php echo $logo_alignment; ?>  class="esig_header_top"> <?php echo $data['document_logo']; ?></div>

 
<div class="document-sign-page">
    
     <?php $title_alignment = apply_filters('esig-document_title-alignment','',esigget('wpUserId', $data)); ?>
	<p class="doc_title" <?php echo $title_alignment; ?> id="doc_title"><?php if(array_key_exists('document_title', $data)) { echo $data['document_title'];} ?></p>
        
    <br />

	<form name="sign-form" id="sign-form" class="sign-form" method="post" enctype="multipart/form-data" action="<?php echo $data['action_url']; ?>">
		
		<?php if (array_key_exists('document_content', $data)) { echo $data['document_content']; } ?>
		
		
		<div class="signatures row">
		
			<input type="hidden" id="invite_hash" name="invite_hash" value="<?php if (array_key_exists('invite_hash', $data)) { echo $data['invite_hash'];} ?>" />
			<input type="hidden" name="checksum" value="<?php if (array_key_exists('checksum', $data)) { echo $data['checksum']; } ?>" />
                        <input type="hidden" name="esig_nonce" autocomplete="off"  value="<?php echo  esigget('nonce',$data); ?>" />
                        <input type="hidden" id="esig-screen-width" name="esig_screen_width" value="" />
                        
                        <p class="esig-anti-spam">Leave this empty: <input type="hidden" autocomplete="off" name="esig_sp_url" /></p>
			
			<p >
					<?php if (array_key_exists('recipient_first_name', $data)) { 
                                            
                                            $fname = $data['recipient_first_name']; 
                                            
                                        }else {
                                            $fname = apply_filters("esig_sad_legal_fname","");
                                        }
                                        $legalName = (WP_E_Sig()->document->isFormIntegration(esigget('invite_hash', $data)))? "text" : "esiglegalname" ; 
                                        $classFormIntregation =(!WP_E_Sig()->document->isFormIntegration(esigget('invite_hash', $data)))? "esig-no-form-integration" : null ; 
                                       
                                        ?>
						<input  type="<?php echo $legalName; ?>" required class="form-control <?php echo $classFormIntregation;?>" id="recipient_first_name" name="recipient_first_name" value="<?php echo $fname; ?>"  <?php echo $data['extra_attr']; ?>   placeholder="<?php _e('Your legal name','esig') ; ?>"/>
					</p>
				
					<?php if (array_key_exists('signer_sign_pad_before', $data)) { echo $data['signer_sign_pad_before']; } ?>

			<div class="col-sm-6" id="signature-wrapper" >
                            <div class="signature-wrapper-displayonly recipient" data-rel="popup">
                                <span id="esig-signature-added rtl-sign-arrow" >
                                    <img src="<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/sign-arrow.svg" class="sign-arrow rtl-sign-arrow" width="80px" height="70px"/>
                                    <canvas id="signatureCanvas2" class="sign-here pad <?php if(array_key_exists('signature_classes', $data)) echo $data['signature_classes']; ?>" height="100"></canvas>
                                    <input type="hidden" name="recipient_signature" class="output" value=''>
                                </span>
                            </div>
                                <?php  if (!wp_is_mobile()): ?>
                            <div id="signer-signature" style="display:none">
                                <div id="tabs">
                                    <div class="signature-tab">
                                        <article id="adopt">
                                            <header class="ds-title p">
                                                <label for="full-name"><?php _e('Please Confirm full name and signature.','esig'); ?></label>
                                            </header>
                                            <div class="full-name">
                                                <div class="wrapper">
                                                    <div class="text-input-wrapper">
                                                        <input id="esignature-in-text" value="<?php if (array_key_exists('recipient_first_name', $data)) { echo $data['recipient_first_name']; } ?>" name="esignature_in_text" class="esignature-in-text" maxlength="64" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="#" id="esig-type-in-change-fonts" style="display:none;"><?php _e('Change fonts','esig'); ?></a>
                                            <div class="clear-float"></div>
                                        </article>
                                        <ul>
                                            <li>
                                                <a href="#tabs-1" id="esig-tab-draw" class="selected"><?php _e('Draw Signature','esig') ; ?> <br /></a>
                                            </li>
                                            <li>
                                                <a href="#tabs-2" id="esig-tab-type"><?php _e('Type In Signature','esig'); ?><br /></a>
                                            </li>
                                        </ul>
                                    </div> <!-- type signature end here -->
  			<div id="tabs-1">
                            <div class="signature-wrapper">
                                <span class="instructions">
                                    <?php _e('Draw your signature with <strong>your mouse, tablet or smartphone</strong>', 'esig' );?>
                                </span>
                                <a href="#clear" class="clearButton" style="margin-bottom:25px;"><?php _e('Clear', 'esig' );?></a>
                                <canvas  id="signatureCanvas" class="sign-here pad <?php if(array_key_exists('signature_classes', $data)) echo $data['signature_classes']; ?>" width="500" height="100" ></canvas>
                                <input type="hidden" name="output" class="output" value='<?php if (array_key_exists('output', $data)) { echo $data['output']; } ?>'/>
                                <div class="description">
                                    <?php _e('I agree that I am <span id="esig-iam-draw"></span> and I agree this is a legal representation of my signature for all purposes 
						just the same as a pen-and-paper signature','esig'); ?>
                                </div>
                                <button class="button saveButton" data-nonce="<?php if(array_key_exists('nonce', $data)) echo $data['nonce']; ?>"><?php _e('Insert Signature','esig');?></button>
                            </div>
                        </div>
  			<div id="tabs-2">
                            <div>
                                <!-- type esignature start here -->
                                <div id="type-in-signature">
                                    <div id="esig-type-in-preview" class="pad" width="450px" height="100px">
						
							<?php 
							
									if (array_key_exists('output_type', $data)) 
										{ 
											$wp_user_id = get_current_user_id();
										
											$font_choice = $this->model->get('esig-signature-type-font'.$wp_user_id);
										
											 echo '<input type="hidden" name="font_type" id="font-type" value="'. $font_choice .'">';
										
										} 
									
							?>
                                    </div>
                                    <div id="esig-type-in-controls">
                                        <div>
                                            <div  id="type-in-text-accept-signature-statement">
                                                <label for="type-in-text-accept-signature">
                                                    <?php _e('I agree that I am <span id="esig-iam-type"></span> and','esig'); ?>
                                                    <span class="signature">
                                                        <?php _e(' I understand this is a legal representation of my signature','esig'); ?>
                                                    </span>
                                                </label>
                                            </div>
                                            <div>
                                                <a id="esig-type-in-text-accept-signature" class="blue-sub alt button-appme button" href="#">
                                                    <span class="esig-signature-type-add"><?php _e('Adopt & Sign','esig'); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix error"></div>
                                        
                                </div>
                                    
                            </div>
                                
                        </div>
                            
                            </div>
                                <!-- -->
                        </div>
                                
                     </div> 
					
			<?php endif; ?>
			
			<?php if (array_key_exists('signer_sign_pad_after', $data)) { echo $data['signer_sign_pad_after']; } ?>
	
			<?php if (array_key_exists('recipient_signatures', $data)) { echo $data['recipient_signatures']; } ?>

		<?php if (array_key_exists('owner_signature', $data)) { echo $data['owner_signature']; } ?>
		
		
		
		
		<div id="esig-mob-input"></div>	
			<span style="display:none;">
				<input type="submit" name="submit-signature" value="Submit signature" />
			</span>
		
		</div>
		
	

	<div class="audit-wrapper">
		
		<div class="row page-break-before">
			<div class="esig-logo col-sm-8 eisg-rtl-legally-signed">
				<a href="//aprv.me/audit-trail" target="_blank"><img src="<?php echo ESIGN_ASSETS_DIR_URI ; ?>/images/legally-signed.svg" alt="WP E-Signature"/></a>
			</div>
			<div class="col-sm-4 rtl-site-name">
				<span><?php echo $data['blog_name']; ?></span>
				<a href="<?php echo $data['blog_url']; ?>" class="esig-sitename" target="_blank"><?php echo $data['blog_url']; ?></a>
			</div>
		</div>
		
		<?php if (array_key_exists('audit_report', $data)) { echo $data['audit_report']; } ?>
	
	</div>
</div>

<div id="agree-button-tip" style="display:none;">
	<div class="header">
		<span class="header-title"><?php _e('Agree &amp; Sign Below', 'esig'); ?></span>
	</div>
	<p>
		<?php _e('Click on "Agree &amp; Sign" to legally sign this document and agree to the WP E-Signature', 'esig'); ?> <a href="#" data-toggle="modal" data-target=".esig-terms-modal-lg" id="esig-terms" class="doc-terms"><?php _e('Terms of Use', 'esig'); ?></a>. 
		<?php _e('If you have questions about the contents of this document, you can email the', 'esig');  ?><span class="hint--bottom  hint--rounded hint--bounce" data-hint="<?php echo $data['owner_email']; ?>"> <a href="mailto:<?php echo $data['owner_email']; ?>"><?php _e('document owner.', 'esig'); ?></a></a>
	</p>
</div>
<style type="text/css">
	.mobile-overlay-bg{position: fixed;top: 0;}
	.mobile-overlay-bg-black{background: black!important; margin: 0 !important; padding:0 !important;}
</style>
<div class="mobile-overlay-bg" style="display:none;">
	
<div class="overlay-content">
		<p class="overlay_logo"><img src="<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/approveme-whitelogo.svg" width="120px" height="80px"/></p>
		<a class="closeButton"></a>
		
		<div class="overlay-content">
		<p align="center" class="doc_title" id="doc_title"> <?php _e('Document Name:', 'esig'); ?> <?php echo $data['document_title']; ?></p>
		<p>
		<?php _e('Click on "Agree &amp; Sign" to legally sign this document and agree to the WP E-Signature', 'esig'); ?> <a href="https://www.approveme.com/terms-of-use/" target="_blank" class="doc-terms"><?php _e('Terms of Use','esig'); ?></a>. 
		</p>
		<p>&nbsp;</p>
		<p align="center" id="esign_click_mobile_submit rtl-submit">
		<a href="#" class="agree-button" title="Agree and submit your signature."><?php _e('Agree & Sign', 'esig'); ?></a>
		</p>
		</div>
	</div>		
</div>

<!-- terms and condition start here -->

<div class="modal fade esig-terms-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" <?php if(wp_is_mobile()){ echo 'data-backdrop="false"' ; } ?>>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
     <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php _e('Terms of Use', 'esig'); ?></h4>
      </div>
      <div class="modal-body">
       <h1><?php _e('Loading ........', 'esig'); ?></h1>
       
      </div>
    </div>
  </div>
</div>


<!-- mobile signature modal -->

<?php  if (wp_is_mobile()) : ?>
	
	<div id="esig-mobile-dialog" class="modal fade modal-transparent" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	
	<div class="modal-dialog modal-lg">
    <div class="modal-content">
        
        <div class="container" height="5px">&nbsp;</div>
       
        <div class="container sig-header" style="width: 100% !important;">
            
            <div class="esig-left">
                 <span class="esig-sig-type-display">
                                        <a href="#" id="mobile-sig" title="Show navigation" class="signature-type"><?php _e('Signature Type','esig'); ?></a>
                            </span>
                            <ul id="esig_mobile_drop_down" class="clearfix">
                                    <li>
                                        <span id="esig-draw-style">
                                            <a href="#" id="mobile-draw-sig" aria-haspopup="true"><?php _e('Draw Signature','esig');?></a>
                                        </span>
                                    </li>
                                    <li>
                                        <span id="esig-type-style" class="esig-type-style-inactive">
                                            <a href="#" id="mobile-type-sig"><?php _e('Type Signature','esig');?></a>
                                        </span>
                                    </li>
                            </ul>
            </div>
            <div class="esig-right">
                <a href="#" id="mobile-next-step" class="esig-mobile-button disabled"><?php _e('Next Step','esig'); ?></a>
            </div>
            
        </div> 
        
        <div class="container sig-header-next-page" style="display:none;">
            
            <div style="float:left;width:40%;"><a href="#" id="mobile-go-back" title="Show navigation"><?php _e('Go Back','esig');?></a></div>
            <div style="float:left;width:60%;text-align: right"><a href="#" id="mobile-adopt-sign" class="esig-mobile-button"><?php _e('Adopt & Sign','esig');?></a></div>
        
        </div>
           
        
            <div class="container signature-description-nextpage" style="display:none;">
                    <?php _e('By clicking Adopt & Sign, I agree','esig'); ?> 
                    <a href="#" data-toggle="modal" data-target=".esig-terms-modal-lg" id="esig-terms" class="doc-terms"><?php _e('Terms of Use', 'esig'); ?></a> <?php _e('that I am <span id="esig-auto-fill-name">  </span> and this signature will be the electronic representation of my signature for all purposes when I (or my agent) use them on documents, including legally binding contracts - just the same as a pen-and-paper signature.','esig');?>
                </div>
               
        <div class="row">&nbsp;</div>
            <div class="container">
    
                            <!-- middle content start here -->
              <div class="row">
                  <div class="col-sm-12">
                     <div class="sig-middle">
                
                <div id="mobile-type-signature" class="sig-draw-section" style="display:none;">
                    <div class="signature-type-input">
                        <input type="esiglegalname" id="esignature-in-text" value="<?php if (array_key_exists('recipient_first_name', $data)) { echo $data['recipient_first_name']; } ?>" name="esignature_in_text" class="esignature-in-text" maxlength="64" type="text">
                    </div>
                    <div id="esig-mobile-type-selection" class="signature-description-adopt" style="display:none;">
                        <!-- When only type selected display then -->
                    </div>
                    <div id="esig-type-in-preview" class="pad" height="100px">
                        <?php 
                        if (array_key_exists('output_type', $data))
                        {
                            $wp_user_id = get_current_user_id();
                            $font_choice = $this->model->get('esig-signature-type-font'.$wp_user_id);
                            echo '<input type="hidden" name="font_type" id="font-type" value="'. $font_choice .'">';
                            
                        }
                        ?>
                    </div>
                </div>
                         
                <div id="mobile-draw-signature" class="sig-draw-section">
                    <div id="mobile-sigpad" class="signature-wrapper">
                        <a href="#clear" class="clearButton" style="margin-bottom:25px;">X</a>
                        <canvas  id="signatureCanvas" class="pad <?php echo esigget('signature_classes',$data); ?>"  height="100" >
                                
                        </canvas>
                        <input type="hidden" name="output" class="output" value='<?php if (array_key_exists('output', $data)) { echo $data['output']; } ?>'/>
                        </div>
                    <div class="signature-description">
                        <span class="esig-big-desc"><?php _e('Draw Your Signature','esig'); ?></span>
                        <span class="esig-small-desc"><?php _e('With a tablet, mouse or smartphone','esig');?></span>
                    </div>
                </div>
            </div><!-- sig middle end here -->
                            <!-- middle content end here -->
                        </div>
                    </div>
                            </div>
        
        
        <div class="container sig-footer">
            <div class="row">
                <div class="col-sm-12">
                       <a href="javascript:;" id="esig-mobile-sig-close"><?php _e('Close','esig'); ?></a>
                </div>
            </div>
        </div>
        
        
  
        <!-- bootstarp modal end here -->
      
</div>
</div>
</div>

<?php endif; ?>
</form>

