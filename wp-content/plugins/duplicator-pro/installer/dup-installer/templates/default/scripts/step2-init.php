<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();

$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step3',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step3')
);
?><script>
    var dbViewModeInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE)); ?>;
    var dbchunkInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHUNK)); ?>;
    var dbHostInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_HOST)); ?>;
    var dbNameInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_NAME)); ?>;
    var dbUserInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_USER)); ?>;
    var dbPassInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_PASS)); ?>;

    var cpnlHostInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_HOST)); ?>;
    var cpnlUserInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_USER)); ?>;
    var cpnlPassInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_PASS)); ?>;
    var cpnlDbHostInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_DB_HOST)); ?>;
    var cpnlDbNameInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_TXT)); ?>;
    var cpnlDbUserInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_TXT)); ?>;
    var cpnlDbPassInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_CPNL_DB_PASS)); ?>;

    var CPNL_TOKEN;
    var CPNL_DBINFO = null;
    var CPNL_DBUSERS = null;
    var CPNL_CONNECTED = false;
    var CPNL_PREFIX = false;

    DUPX.sendParamsStep2AndValidate = function () {
        //Validate input data
        var formInput = $('#s2-input-form');
        formInput.parsley().validate();
        if (!formInput.parsley().isValid()) {
            return;
        }

        var $dbArea;
        var $dbResult;
        var $dbButton;
        if ($('#' + dbViewModeInputId).val() == 'basic') {
            $dbArea = $('.s2-basic-pane .s2-dbtest-area');
            $dbResult = $('#s2-dbtest-hb-basic');
            $dbButton = $('#s2-dbtest-btn-basic');
        } else {
            $dbArea = $('.s2-cpnl-pane  .s2-dbtest-area');
            $dbResult = $('#s2-dbtest-hb-cpnl');
            $dbButton = $('#s2-dbtest-btn-cpnl');
        }

        $dbArea.show(250);
        $dbResult.html("<div class='message'><i class='fa fa-circle-notch fa-spin fa-fw'></i>&nbsp;Running Database Validation. <br/>  Please wait...</div>");
        $dbButton.attr('disabled', 'true');

        DUPX.sendParamsStep2(formInput, function () {
            DUPX.testDBConnect($dbResult, function () {
                $dbButton.removeAttr('disabled');
            }, true);
        });
    };

    /**
     * Open an in-line confirm dialog*/
    DUPX.confirmDeployment = function ()
    {
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_DB_HOST)) { ?>
            var dbhost = $("#" + dbHostInputId).val();
<?php } else { ?>
            var dbhost = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST)); ?>;
<?php } ?>
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_DB_NAME)) { ?>
            var dbname = $("#" + dbNameInputId).val();
<?php } else { ?>
            var dbname = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME)); ?>;
<?php } ?>
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_DB_USER)) { ?>
            var dbuser = $("#" + dbUserInputId).val();
<?php } else { ?>
            var dbuser = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_USER)); ?>;
<?php } ?>

        if ($('#' + dbViewModeInputId).val() == 'cpnl') {
            DUPX.cpnlSetResults();

            dbhost = $("#" + cpnlDbHostInputId).val();
            dbname = $("#cpnl-dbname-result").val();
            dbuser = $("#cpnl-dbuser-result").val();
        }

        var $formInput = $('#s2-input-form');
        $formInput.parsley().validate();
        if (!$formInput.parsley().isValid()) {
            return;
        }

        $("#db-install-dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 550,
            modal: true,
            position: {my: 'top', at: 'top+150'},
            buttons: {
                "OK": function () {
                    DUPX.runDeployment();
                    $(this).dialog("close");
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
        });

        $('#dlg-dbhost').html(dbhost);
        $('#dlg-dbname').html(dbname);
        $('#dlg-dbuser').html(dbuser);
    };

    DUPX.runDeployment = function () {
        //Validate input data
        var formInput = $('#s2-input-form');
        formInput.parsley().validate();
        if (!formInput.parsley().isValid()) {
            return;
        }

        var $dbResult;
        if ($('#' + dbViewModeInputId).val() == 'basic') {
            $dbResult = $('#s2-dbtest-hb-basic');
        } else {
            $dbResult = $('#s2-dbtest-hb-cpnl');
        }

        DUPX.sendParamsStep2(formInput, function () {
            DUPX.testDBConnect($dbResult, function () {
                DUPX.startAjaxDbInstall(true, <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
            }, false);
        });
    };

    //DOCUMENT LOAD
    $(document).ready(function () {
        //Init
<?php echo ($GLOBALS['DUPX_AC']->cpnl_enable) ? 'DUPX.togglePanels("cpanel");' : 'DUPX.togglePanels("basic");'; ?>
        $("*[data-type='toggle']").click(DUPX.toggleClick);
    });

</script>
