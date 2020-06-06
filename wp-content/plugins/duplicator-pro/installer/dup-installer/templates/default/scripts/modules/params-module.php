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

    var wpUserNameInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME)); ?>;
    var wpPwdInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD)); ?>;
    var wpMailInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL)); ?>;

    DUPX.sendParamsStep1 = function (form, setParamOkCallback) {
        DUPX.pageComponents.resetTopMessages().showProgress({
            'title': 'Parameters update',
            'bottomText':
                    '<i>Keep this window open.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });
        let setParamAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_SET_PARAMS_S1); ?>;
        let setParamToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_SET_PARAMS_S1)); ?>;

        var formData = form.serializeForm();

        DUPX.StandarJsonAjaxWrapper(
                setParamAction,
                setParamToken,
                formData,
                function (data) {
                    if (data.actionData.isValid) {
                        if (typeof setParamOkCallback === "function") {
                            setParamOkCallback();
                        }
                    } else {
                        DUPX.pageComponents.showContent();
                        DUPX.topMessages.add(data.actionData.nextStepMessagesHtml);
                    }

                    return true;
                },
                DUPX.ajaxErrorDisplayHideError
                );
    };

    DUPX.sendParamsStep2 = function (form, setParamOkCallback) {
        DUPX.pageComponents.resetTopMessages().showProgress({
            'title': 'Parameters update',
            'bottomText':
                    '<i>Keep this window open.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });
        let setParamAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_SET_PARAMS_S2); ?>;
        let setParamToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_SET_PARAMS_S2)); ?>;

        var formData = form.serializeForm();

        DUPX.StandarJsonAjaxWrapper(
                setParamAction,
                setParamToken,
                formData,
                function (data) {
                    if (data.actionData.isValid) {
                        if (typeof setParamOkCallback === "function") {
                            setParamOkCallback();
                        }
                    } else {
                        DUPX.pageComponents.showContent();
                        DUPX.topMessages.add(data.actionData.nextStepMessagesHtml);
                    }

                    return true;
                },
                DUPX.ajaxErrorDisplayHideError
                );
    };

    DUPX.sendParamsStep3 = function (form, setParamOkCallback) {
        let setParamAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_SET_PARAMS_S3); ?>;
        let setParamToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_SET_PARAMS_S3)); ?>;

        //Validation
        var wp_username = $.trim($("#" + wpUserNameInputId).val()).length || 0;
        var wp_password = $.trim($("#" + wpPwdInputId).val()).length || 0;
        var wp_mail = $.trim($("#" + wpMailInputId).val()).length || 0;

        if (wp_username >= 1) {
            if (wp_username < 4) {
                alert("The New Admin Account 'Username' must be four or more characters");
                return false;
            } else if (wp_password < 6) {
                alert("The New Admin Account 'Password' must be six or more characters");
                return false;
            } else if (wp_mail === 0) {
                alert("The New Admin Account 'mail' is required");
                return false;
            }
        }

        var nonHttp = false;
        var failureText = '';

        /* IMPORTANT - not trimming the value for good - just in the check */
        $('input[name="search[]"]').each(function () {
            var val = $(this).val();
            if (val.trim() != "") {
                if (val.length < 3) {
                    failureText = "Custom search fields must be at least three characters.";
                }
                if (val.toLowerCase().indexOf('http') != 0) {
                    nonHttp = true;
                }
            }
        });

        $('input[name="replace[]"]').each(function () {
            var val = $(this).val();
            if (val.trim() != "") {
                // Replace fields can be anything
                if (val.toLowerCase().indexOf('http') != 0) {
                    nonHttp = true;
                }
            }
        });

        if (failureText != '') {
            alert(failureText);
            return false;
        }

        if (nonHttp) {
            if (confirm('One or more custom search and replace strings are not URLs.  Are you sure you want to continue?') == false) {
                return false;
            }
        }

        if ($('input[type=radio][name=replace_mode]:checked').val() == 'mapping') {
            $("#new-url-container").remove();
        } else if ($('input[type=radio][name=replace_mode]:checked').val() == 'legacy') {
            $("#subsite-map-container").remove();
        }

        DUPX.pageComponents.resetTopMessages().showProgress({
            'title': 'Parameters update',
            'bottomText':
                    '<i>Keep this window open.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });

        var formData = form.serializeForm();

        DUPX.StandarJsonAjaxWrapper(
                setParamAction,
                setParamToken,
                formData,
                function (data) {
                    if (data.actionData.isValid) {
                        if (typeof setParamOkCallback === "function") {
                            setParamOkCallback();
                        }
                    } else {
                        DUPX.pageComponents.showContent();
                        DUPX.topMessages.add(data.actionData.nextStepMessagesHtml);
                    }

                    return true;
                },
                DUPX.ajaxErrorDisplayHideError
                );
    };
</script>