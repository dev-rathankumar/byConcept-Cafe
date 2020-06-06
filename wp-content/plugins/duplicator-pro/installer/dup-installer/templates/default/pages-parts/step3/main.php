<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/views/classes/class.view.s3.php');

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>

<!-- =========================================
VIEW: STEP 3- INPUT -->
<form id='s3-input-form' method="post" class="content-form" autocomplete="off">
    <?php if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION) == 'manual') { ?>
        <div class="dupx-notice s3-manaual-msg">
            Manual SQL execution is enabled
        </div>
        <?php
    }

    DUPX_View_S3::newSettings();
    DUPX_View_S3::mappingMode();
    DUPX_View_S3::customSearchAndReaplce();
    dupxTplRender('pages-parts/step3/options');
    ?>
    <div class="footer-buttons">
        <div class="content-left">
        </div>
        <div class="content-right" >
            <button id="s3-next" type="button"  onclick="DUPX.runSiteUpdate()" class="default-btn"> Next <i class="fa fa-caret-right"></i> </button>
        </div>
    </div>
</form>