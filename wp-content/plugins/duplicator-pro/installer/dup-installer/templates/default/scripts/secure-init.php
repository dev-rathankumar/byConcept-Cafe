<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step1',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step1'),
    DUPX_Paramas_Manager::PARAM_STEP_ACTION => DUPX_CTRL::ACTION_STEP_INIZIALIZED
);
?>
<script>
    $(document).ready(function () {
        const secureAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_PWD_CHECK); ?>;
        const secureToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_PWD_CHECK)); ?>;
        const passForm = $('#i1-pass-form');
        /**
         * Submits the password for validation
         */
        DUPX.checkPassword = function ()
        {
            passForm.parsley().validate();
            if (!passForm.parsley().isValid()) {
                return;
            }
            var formData = passForm.serializeForm();

            DUPX.StandarJsonAjaxWrapper(
                    secureAction,
                    secureToken,
                    formData,
                    function (data) {
                        if (data.actionData) {
                            DUPX.redirect(DUPX.dupInstallerUrl, 'post', <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
                        } else {
                            $('#pwd-check-fail').show();
                        }
                    },
                    DUPX.ajaxErrorDisplayHideError
                    );
        };

        passForm.submit(function (event) {
            event.preventDefault();
            DUPX.checkPassword();
        });
    });
</script>