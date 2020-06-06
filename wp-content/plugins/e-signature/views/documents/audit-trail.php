<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$images_url = ESIGN_ASSETS_DIR_URI . ESIG_DS . "/images";
global $audit_trail_data, $esig_pdf_export;
?>

<link rel='stylesheet' id='esig-theme-style-audit-css'  href='<?php echo ESIGN_ASSETS_DIR_URI; ?>/css/audit-trail.css' type='text/css' media='all' />
<?php if($esig_pdf_export): ?>
    <link rel='stylesheet' id='esig-theme-style-audit-pdf-css'  href='<?php echo ESIGN_ASSETS_DIR_URI; ?>/css/audit-trail-pdf.css' type='text/css' media='all' />
<?php endif; ?>



<section id="audit-trail-wrapper" class="column">

	
    <div class="header" >
        <div class="row document-info">
            <div class="col left rtl-signature-certificate">
                <div class="caption"><?php _e("Signature Certificate","esig"); ?></div>
                <div class="document-name"><?php _e("Document name:","esig"); ?> <span><?php echo esig_unslash($audit_trail_data->document_name) ;?></span></div>
                <div class="subcaption">
                    <span class="image-wrapper"><img style="width: 11px; height: 14px;" src="<?php echo $images_url ; ?>/lock.png" alt=""></span><?php _e("Unique Document ID:","esig"); ?> <span><?php echo $audit_trail_data->unique_document_id; ?></span>
                </div>
            </div>
            <div class="col right logo rtl-sign-contracts">
                <a href="//aprv.me/audit-trail" target="_blank"><img style="width: 157px; height: 38px;" src="<?php echo ESIGN_ASSETS_DIR_URI . '/images/legally-signed.svg'; ?>" alt=""></a><br>
               <?php _e("Build.	Track.	Sign Contracts.","esig"); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <section class="main">
        <?php foreach($audit_trail_data->users as $user): 
        ?>
            <div class="signer-view">
                <div class="row info clearfix">
                    <div class="col user left rtl-user-image">
                        <?php if($user->image): ?>
                        <img src="<?php echo $user->image; ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="col bio left rtl-signer-id">
                        <div><?php echo $user->name; ?></div>
                        <div><?php _e("Party ID:","esig");?> <?php echo $user->party_id;?></div>
                        <?php if($user->signer_ip): ?>
                            <div><?php _e("IP Address:","esig");?> <?php echo $user->signer_ip; ?></div>
                        <?php endif; ?>
                        <div class="security-levels"><?php if($user->security_levels != "sad"){ echo  __("Security Level: ","esig") . $user->security_levels ; }  ?></div>
                    </div>
                    <div class="col sign right rtl-awating-signature">
                        <?php if(isset($user->signature_view->image_url) || isset($user->signature_view->signature_by_type)): ?>
                            <div class="digital-signature-caption"><?php _e("Digital Signature:","esig");?></div>
                            <div class="digital-signature-image">
                                <?php
                                if(isset($user->signature_view->image_url)){
                                    echo "<img src='".$user->signature_view->image_url."' alt=''>";
                                } else {
                                    echo $user->signature_view->signature_by_type;
                                }
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-signature "><?php _e("Awaiting signature","esig"); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if(isset($user->signature_view->image_url) || isset($user->signature_view->signature_by_type)): ?>
                <div class="row checksum clearfix rtl-clearfix">
                    <div class="col left checksum-caption">
                       <?php _e("Multi-Factor","esig"); ?><br>
                        <b><?php _e("Digital Fingerprint Checksum","esig"); ?></b>
                    </div>
                    <div class="col left code">
                        <?php echo $user->dfc;?>
                    </div>
                    <div class="col right dfc-image">
                        <img src="<?php echo $user->dfc_qr_code_image_data; ?>" alt="">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="row history clearfix rtl-clearfix-history">
            <table class="layout display responsive-table">
                <tbody>
                    <tr>
                        <td><b><?php _e("Timestamp","esig"); ?></b></td>
                        <td><b><?php _e("Audit","esig") ; ?></b></td>
                    </tr>
                    <?php echo $audit_trail_data->timeline; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="clearfix"></div>
	<?php if(!$esig_pdf_export): ?>
	<div class="footer">
        <div class="row clearfix">
            <div class="col left page-url-qr rtl-weapper">
                <div class="wrapper">
                    <img style="width: 90px; height: 90px;" src="<?php echo $audit_trail_data->current_url_qr; ?>" alt="">
                </div>
            </div>
            <div class="col text left pdf-right rtl-weapper-text">
               <?php _e("This audit trail report provides a detailed record of the online activity and events recorded for this document.","esig"); ?>
            </div>
            <div class="col right">
            </div>
            <div class="clearfix"></div>
        </div>
	</div>

	<section class="bottom-footer">
            <div class="blog-url pdf-blog-url"><a href="<?php echo $audit_trail_data->site_url; ?>"><?php echo $audit_trail_data->site_url; ?></a></div>
        <?php if($audit_trail_data->audit_signature_id):?>
        <div class="audit-signature pull-right"><img style=" height: auto;width: 12px;margin-top: -5px;margin-right: 5px;" src="<?php echo $images_url;?>/lock.png" alt=""><?php _e('Audit Trail Serial#','esig'); ?> <?php echo $audit_trail_data->audit_signature_id; ?></div>
        <?php endif; ?>
	</section>
	
	<?php endif; ?>
</section>


<?php if($esig_pdf_export): ?>
    <div class="audit-pdf-border"></div>
    <htmlpagefooter name="Audit_Trail_Footer" >
        <div class="footer audit-pdf-footer">
            <table>
                <tr>
                    <td class="pdf-qr-code">
                        <img style="width: 80px; height: 80px;" src="<?php echo $audit_trail_data->current_url_qr; ?>" alt="">
                    </td>
                    <td class="pdf-footer-description">
                       <?php _e("This audit trail report provides a detailed record of the online activity and events recorded for this contract.","esig"); ?>
                    </td>
                    <td class="pdf-footer-pages">
                       <?php _e("Page {PAGENO} of {nb} " , "esig" ) ; ?>
                    </td>
                </tr>
            </table>
        </div>

        <section class="bottom-footer audit-pdf-bottom-footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 40%"><a class="audit-link" href="<?php echo $audit_trail_data->site_url; ?>"><?php echo $audit_trail_data->site_url; ?></a></td>
                    <td style="width: 60%; text-align: right; ">
                        <?php if($audit_trail_data->audit_signature_id):?>
                            <div class="pdf-audit-signature audit-signature pull-right"><img style=" height: auto;width: 12px;margin-top: -5px;margin-right: 5px;" src="<?php echo $images_url; ?>/lock.png" alt=""> <?php _e('Audit Trail Serial#','esig'); ?> <?php echo $audit_trail_data->audit_signature_id; ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </section>
    </htmlpagefooter>

<?php endif; ?>
