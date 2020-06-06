<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta name="robots" content="noindex,nofollow">
        <meta name="robots" content="noimageindex">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>

        <meta name="description" content="">

        <meta name="author" content="">

<!--<link rel='stylesheet' id='esig-overall-style-css'  href='<?php echo plugin_dir_url(__FILE__); ?>/style.css' type='text/css' media='all' />-->

        <link rel='stylesheet' id='dashicons-css'  href='<?php echo site_url(); ?>/wp-includes/css/dashicons.min.css?ver=3.9.1' type='text/css' media='all' />



        <title><?php _e('WP E-Signature by Approve Me - Sign Documents Using WordPress - ', 'esig'); ?></title>
        <!--noptimize-->
        <?php
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        if (!defined('DONOTMINIFY')) {
            define('DONOTMINIFY', true);
        }
        WP_E_Shortcode::esig_head();
        ?>
        <!--/noptimize-->

    </head>



    <body class="esig-template-page" oncontextmenu="return true;">

        <div id="page_loader" style="display:none;">

            <div id="d1"></div>

            <div id="d2"></div>

            <div id="d3"></div>

            <div id="d4"></div>

            <div id="d5"></div>

        </div>

        <div class="signer-header" role="navigation" >

            <div class="container" >

                <div class="navbar-header">

                    <a href="<?php echo bloginfo('url'); ?>" target="_blank" class="navbar-brand" style="color:#fff;">

                        <?php
                        echo stripslashes(WP_E_Sig()->setting->get_company_name());
//echo stripslashes($api->setting->get_generic("company_logo"));
                        ?> </a>

                </div>



                <div class="nav navbar-nav <?php if(!is_rtl()) { echo 'navbar-right';}?> doclogo-right">

                    <span class="hint--bottom  hint--rounded hint--bounce" data-hint="Click here to learn more about the security and protection of the document you are signing.">

                        <a class="disabled" href="https://www.approveme.com/security-ueta-e-sign-protection/" target="_blank"><img src="<?php echo(ESIGN_ASSETS_DIR_URI) ?>/images/verified-approveme.svg" alt="" width="140px"></a>

                    </span>

                </div>

            </div>

        </div>

        <div class="container first-page doc_page">

            <?php
// Start the Loop.
            $license_status = Esign_licenses::is_license_valid();
           

            if ($license_status) {

                while (have_posts()) : the_post();

                    the_content();

                //echo do_shortcode(wpautop($post->post_content));

                endwhile;
            } else {
                ?>

                <div class="esig-506-error">
                    <p class="alert-icon"><span class="icon-esig-alert"></span></p>

                    <h3 class="error-alert"><?php _e('Put your electronic pen down!', 'esign'); ?></h3>

                    <h4 class="error-code"><?php _e('Error 506', 'esig'); ?></h4>
                    <p><?php _e("It looks like something has gone awry with this document. Don’t panic, it's an easy fix. Give the", 'esig'); ?> <a data-toggle="modal" data-target="#esigModal" href="#"><?php _e("document sender", 'esig'); ?></a> <?php _e("this error code, they’ll know what to do. :)", 'esig'); ?></p>



                </div>

                <div id="esigModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="adminEmail">To:</label>
                                    <input type="esig_admin_email" value="<?php echo WP_E_Sig()->user->getUserEmail(WP_E_Sig()->user->esig_get_super_admin_id()); ?>" disabled class="form-control" style="width:100% !important;" id="esig_admin_email">
                                </div>
                                <div class="form-group">
                                    <label for="signerName">Name:</label>
                                    <input type="esig_signer_name" class="form-control" style="width:100% !important;" id="esig_signer_name">
                                </div>
                                <div class="form-group required">
                                    <label for="signerEmail">From:</label>
                                    <input type="esig_signer_email" class="form-control" style="width:100% !important;" id="esig_signer_email">
                                </div>
                                <div class="form-group required">
                                    <label for="message">Message:</label>
                                    <textarea class="form-control" rows="5" style="width:100% !important;" id="esig_admin_message">I am attempting to sign a document on your website but I'm receiving a 506 Error.  Will you please visit your document signing company's website to resolve the error code.  Thanks! (more info: http://aprv.me/506-error)</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="esig-expired-email-send" type="button" class="btn btn-primary">Send</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </div>
                </div>
                <?php
            }
            ?>



        </div>





        <!-- -->
        <!--

        <div class="container doc_page">



        </div>

        -->
        <?php
        $display_class = (wp_is_mobile()) ? 'style="display:none;"' : '';
        ?>

        <div id="esig-footer" <?php echo $display_class; ?> class="container footer-agree">

            <div class="esig-container">


                <div class="navbar-header agree-container">

                    <span id="esig-iam"> </span><span class="agree-text"><?php _e('I agree to be legally bound by this agreement and eSignature', 'esig'); ?> <a href="#" data-toggle="modal" data-target=".esig-terms-modal-lg" id="esig-terms" class="doc-terms"><?php _e('Terms of Use.', 'esig'); ?></a></span>

                </div>



                <div class="nav navbar-nav <?php if(!is_rtl()) { echo 'navbar-right';}?> footer-btn">

                    <?php
                    $defalut_page_id = WP_E_Sig()->setting->get_default_page();
                    $page_id = get_the_ID();
                    if ($defalut_page_id == $page_id) {
                        $docId = (esigget('document_id')) ? esigget('document_id') : WP_E_Sig()->document->document_id_by_csum(esigget('csum'));
                    } else {
                        $docId = esig_sad_document::get_instance()->get_sad_id($page_id);
                    }

                    $printOption = WP_E_General::isPrintButtonDisplay($docId);

                    if ($printOption == 'display') {
                        ?>
                        <a href="javascript:window.print()" class="agree-button" id="esig-print-button" title=""><?php _e('Print Document', 'esig'); ?></a>
                        <?php
                    }
                    echo apply_filters("esig_display_pdf_button", '', $docId);
                    ?>


                    <span id="esign_click_submit">
                        <a href="#" class="agree-button disabled" id="esig-agree-button"  title="Agree and submit your signature."><span id="esig-agreed"><?php _e('Agree & Sign', 'esig'); ?></span></a>
                    </span>



                </div>

            </div>

        </div>
        <!--esigature mobile footer when signed -->

        <?php
        if (wp_is_mobile()):
            
            ?>
            <div id="esig-mobile-footer" class="footer-agree">

                <div class="navbar-header agree-container">


                    <span class="agree-text"> <a href="<?php echo home_url('/'); ?>" data-ajax="false" class="esig-sitename"><?php _e('Back to Main Site', 'esig'); ?></a></span>

                </div>


            </div>

        <?php endif; ?>

        <!--noptimize-->
        <?php WP_E_Shortcode::esig_footer(); ?>

        <!--/noptimize-->

    </body>

</html>
