<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/views/classes/class.view.s1.php');

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<form id="s1-input-form" method="post" class="content-form" autocomplete="off" >
    <input type="hidden" id="s1-input-dawn-status" name="dawn_status" />
    <?php
    DUPX_View_S1::infoTabs();

    dupxTplRender('parts/validation/validate-area');

    DUPX_View_S1::options();
    ?>

    <div id="validating_action" class='bottom-step-action'>
        validating
    </div>

    <div id="error_action" class="bottom-step-action no-display" >   
        <div class="s1-err-msg" >
            <i>
                This installation will not be able to proceed until the archive and validation sections both pass. Please adjust your servers settings or contact your
                server administrator, hosting provider or visit the resources below for additional help.
            </i>
            <div style="padding:10px">
                &raquo; <a href="https://snapcreek.com/duplicator/docs/faqs-tech/" target="_blank">Technical FAQs</a> <br/>
                &raquo; <a href="https://snapcreek.com/support/docs/" target="_blank">Online Documentation</a> <br/>
            </div>
        </div>

        <div class="s1-accept-check no-display" style="padding-top: 20px;">
            <input id="accept-perm-error" name="accept-perm-error" type="checkbox" />
            <label for="accept-perm-error" style="color: #AF0000;">I would like to proceed with my own risk despite the permission error</label><br/>
        </div>
    </div>

    <div id="reload_action" class="bottom-step-action no-display" >
        <div class="footer-buttons" >
            <div class="content-left">
            </div>
            <div class="content-right" >
                <button id="reload-btn" type="button" class="default-btn">
                    Revalidate <i class="fa fa-redo"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="next_action" class="bottom-step-action no-display" >           
        <div class="footer-buttons">
            <div class="content-left">
                <?php DUPX_View_S1::acceptAndContinue(); ?>
            </div>
            <div class="content-right" >
                <button 
                    id="s1-deploy-btn" 
                    type="button" 
                    title="<?php echo DUPX_U::esc_attr('To enable this button the checkbox above under the "Terms & Notices" must be checked.'); ?>" 
                    class="default-btn">
                    Next <i class="fa fa-caret-right"></i>
                </button>
            </div>
        </div>
    </div>
</form>