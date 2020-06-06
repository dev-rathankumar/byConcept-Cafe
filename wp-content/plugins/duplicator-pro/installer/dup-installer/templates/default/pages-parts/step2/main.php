<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/views/classes/class.view.s2.php');

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>

<form id='s2-input-form' method="post" class="content-form"  autocomplete="off" data-parsley-validate="true" data-parsley-excluded="input[type=hidden], [disabled], :hidden">
    <?php
    DUPX_View_S2::modeButtons();
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE);
    ?>

    <!-- BASIC TAB -->
    <div class="s2-basic-pane">
        <?php DUPX_View_S2::basicPanel(); ?>
    </div>


    <?php
    if (!DUPX_View_S2::skipDbTest()) {
        ?>
        <!-- CPANEL TAB -->
        <div class="s2-cpnl-pane">
            <?php
            DUPX_View_S2::cpanlePanel();
            DUPX_View_S2::cpanelSetup();
            ?>
        </div>

        <?php
    }
    ?>

    <!-- BASIC VALIDATION -->
    <div class="s2-basic-pane">
        <?php DUPX_View_S2::basicValitadion(); ?>
    </div>

    <?php
    if (!DUPX_View_S2::skipDbTest()) {
        ?>
        <!-- CPANEL TAB -->
        <div class="s2-cpnl-pane">
            <?php DUPX_View_S2::cpanelValidation(); ?>
        </div>
        <?php
    }

    DUPX_View_S2::basicOptions();
    ?>

</form>
<?php dupxTplRender('pages-parts/step2/database-confirm-dialog'); ?>