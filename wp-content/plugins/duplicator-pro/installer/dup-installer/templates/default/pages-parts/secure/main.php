<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<form method="post" id="i1-pass-form" class="content-form"  data-parsley-validate="" autocomplete="off" >
    <div id="pwd-check-fail" class="error-pane no-display">
        <p>Invalid Password! Please try again...</p>
    </div>

    <div style="text-align: center">
        This file was password protected when it was created.   If you do not remember the password	check the details of the package on	the site where it was created or visit
        the online FAQ for <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-030-q" target="_blank">more details</a>.
        <br/><br/><br/>
    </div>
    <div class="i1-pass-data">
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SECURE_PASS);
        ?>
        <div class="footer-buttons" >
            <div class="content-left">
            </div>
            <div class="content-right" >
                <button type="submit" name="secure-btn" id="secure-btn" class="default-btn" >Submit</button>
            </div>
        </div>
    </div>
</form>