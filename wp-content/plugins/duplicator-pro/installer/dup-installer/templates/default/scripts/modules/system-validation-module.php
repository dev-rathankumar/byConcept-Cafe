<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<script>
    const validateAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_VALIDATE); ?>;
    const validateToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_VALIDATE)); ?>;

    DUPX.initialValidateAction = function (validateArea, validateNoResult, isValidCallback, isInvalidCallback, showContentOnResult, resetTopMessage) {
        if (resetTopMessage) {
            DUPX.pageComponents.resetTopMessages();
        }
        DUPX.pageComponents.showProgress({
            'title': 'System validation',
            'bottomText':
                    '<i>Keep this window open during the validation process.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });

        DUPX.StandarJsonAjaxWrapper(
                validateAction,
                validateToken,
                {},
                function (data) {
                    if (showContentOnResult) {
                        DUPX.pageComponents.showContent();
                    }
                    validateNoResult.detach();
                    validateArea.empty().append(data.actionData.html);
                    validateArea.find("*[data-type='toggle']").click(DUPX.toggleClick);
                    DUPX.setValidationBadge('#validate-global-badge-status', false);

                    if (data.actionData.validateData.arcCheck === 'Fail') {
                        $('#s1-area-archive-file-link').trigger('click');
                    }

                    if (data.actionData.validateData.all_success !== true) {
                        $('#validate-area-link').trigger('click');
                    }

                    if (data.actionData.validateData.req_success !== true) {
                        if (typeof isInvalidCallback === "function") {
                            isInvalidCallback(data.actionData.validateData);
                        }
                    } else {
                        if (typeof isValidCallback === "function") {
                            isValidCallback(data.actionData.validateData);
                        }
                    }
                },
                DUPX.ajaxErrorDisplayRestart
                );
    };

    DUPX.setValidationBadge = function (selector, newClass) {
        let item = $(selector);
        if (!item.length) {
            return;
        }
        item.removeClass('wait fail warn good pass')
        if (newClass) {
            item.addClass(newClass);
        }
    };
</script>