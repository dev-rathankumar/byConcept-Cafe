<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step2',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step2')
);
?>
<script>
    const urlNewInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_URL_NEW)); ?>;
    const pathNewInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?>;
    const exeSafeModeInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_SAFE_MODE)); ?>;
    const htConfigInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)); ?>;
    const htConfigWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)); ?>;
    const otConfigInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)); ?>;
    const otConfigWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)); ?>;
    const archiveEngineInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE)); ?>;

    $(document).ready(function () {
        let validateArea = $('#validate-area');
        let validateNoResult = validateArea.find('#validate-no-result');
        let stepActions = $('.bottom-step-action');
        let step1Form = $('#s1-input-form');

        DUPX.getManaualArchiveOpt = function ()
        {
            $("html, body").animate({scrollTop: $(document).height()}, 1500);
            $("div[data-target='#s1-area-adv-opts']").find('i.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
            $('#s1-area-adv-opts').show(1000);
            $('#' + archiveEngineInputId).val('manual').focus();
        };

        DUPX.onSafeModeSwitch = function ()
        {
            var safeObj = $('#' + exeSafeModeInputId)
            var mode = safeObj ? parseInt(safeObj.val()) : 0;
            var htWr = $('#' + htConfigWrapperId);
            var otWr = $('#' + otConfigWrapperId);

            switch (mode) {
                case 1:
                case 2:
                    htWr.find('#' + htConfigInputId + '_0').prop("checked", true);
                    htWr.find('input').prop("disabled", true);
                    otWr.find('#' + otConfigInputId + '_0').prop("checked", true);
                    otWr.find('input').prop("disabled", true);
                    break;
                case 0:
                default:
                    htWr.find('input').prop("disabled", false);
                    otWr.find('input').prop("disabled", false);
                    break;
            }
            console.log("mode set to" + mode);
        };

        DUPX.validationNotValidCallback = function (validateData) {
            stepActions.addClass('no-display')
                    .filter("#error_action").removeClass('no-display')
                    .find('.s1-accept-check').each(function () {
                if (validateData.is_only_permission_issue) {
                    DUPX.setValidationBadge('#validate-global-badge-status', 'warn');
                    $(this).removeClass('no-display');
                } else {
                    DUPX.setValidationBadge('#validate-global-badge-status', 'fail');
                    $(this).addClass('no-display');
                }
            });
        };

        DUPX.toggleSetupType = function ()
        {
            var val = $("input:radio[name='setup_type']:checked").val();
            $('div.s1-setup-type-sub').hide();
            $('#s1-setup-type-sub-' + val).show(200);
        };

        /**
         * Accetps Usage Warning */
        DUPX.acceptWarning = function (agreeMsg)
        {
            if ($("#<?php echo $paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND); ?>").is(':checked')) {
                $("#s1-deploy-btn").removeAttr("disabled");
                $("#s1-deploy-btn").removeAttr("title");
            } else {
                $("#s1-deploy-btn").attr("disabled", "true");
                $("#s1-deploy-btn").attr("title", agreeMsg);
            }
        };

        DUPX.activateNextButton = function () {
            stepActions.addClass('no-display').filter("#next_action").removeClass('no-display');
        };

        DUPX.deployStep1 = function () {
            DUPX.sendParamsStep1(step1Form, function () {
                DUPX.initialValidateAction(validateArea, validateNoResult,
                        function (validateData) {
                            DUPX.startAjaxExtraction(true, <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
                        },
                        function (validateData) {
                            if (validateData.is_only_permission_issue) {
                                DUPX.startAjaxExtraction(true, <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
                            } else {
                                DUPX.setValidationBadge('#validate-global-badge-status', 'fail');
                                DUPX.pageComponents.showContent();
                            }
                        },
                        false, false);
            });
        };

        DUPX.autoUpdateOnMainChanges = function () {
            var originalUrlMainVal = $('#' + urlNewInputId).closest('.param-wrapper').data('original-default-value');
            var urlRegex = new RegExp('^' + originalUrlMainVal, '');

            $('#' + urlNewInputId).bind("keyup change", function () {
                var newUrlVal = $(this).val().replace(/\/$/, '');
                $('.auto-updatable.autoupdate-enabled[data-auto-update-from-input="' + urlNewInputId + '"]').each(function () {
                    let originalVal = $(this).data('original-default-value');
                    $(this).find('input').val(originalVal.replace(urlRegex, newUrlVal));
                });
            });

            var orginalPathMainVal = $('#' + pathNewInputId).closest('.param-wrapper').data('original-default-value');
            var pathRegex = new RegExp('^' + orginalPathMainVal, '');

            $('#' + pathNewInputId).bind("keyup change", function () {
                var newPathlVal = $(this).val().replace(/\/$/, '');
                $('.auto-updatable.autoupdate-enabled[data-auto-update-from-input="' + pathNewInputId + '"]').each(function () {
                    let originalVal = $(this).data('original-default-value');
                    $(this).find('input').val(originalVal.replace(pathRegex, newPathlVal));
                });
            });
        };

        DUPX.revalidateOnNewPathUrlChanged = function () {
            $('input.revalidate').each(function () {
                var oldValue = $(this).val();
                $(this).bind("keyup change", function () {
                    if ($(this).val() !== oldValue) {
                        oldValue = $(this).val();
                        $('#accept-perm-error').prop("checked", false);
                        stepActions.addClass('no-display').filter('#reload_action').removeClass('no-display');
                    }
                });
            });
        };

        //INIT Routines
        $("*[data-type='toggle']").click(DUPX.toggleClick);
        $(".tabs").tabs();

        DUPX.acceptWarning();
        DUPX.toggleSetupType();

        DUPX.autoUpdateOnMainChanges();
        DUPX.revalidateOnNewPathUrlChanged();

        $('#accept-perm-error').click(function () {
            if ($(this).is(':checked')) {
                stepActions.filter('#next_action').removeClass('no-display');
            } else {
                stepActions.filter('#next_action').addClass('no-display');
            }
        });

        $('#s1-deploy-btn').click(function () {
            DUPX.deployStep1();
        });

        $('#reload-btn').click(function () {
            DUPX.sendParamsStep1(step1Form, function () {
                DUPX.initialValidateAction(validateArea, validateNoResult,
                        function (validateData) {
                            DUPX.setValidationBadge('#validate-global-badge-status', 'pass');
                            DUPX.activateNextButton();
                        },
                        DUPX.validationNotValidCallback,
                        true, true);
            });
        });
        // start initial validation
        DUPX.initialValidateAction(validateArea, validateNoResult,
                function (validateData) {
                    DUPX.setValidationBadge('#validate-global-badge-status', 'pass');
                    DUPX.activateNextButton();
                },
                DUPX.validationNotValidCallback,
                true, false);
    });
</script>