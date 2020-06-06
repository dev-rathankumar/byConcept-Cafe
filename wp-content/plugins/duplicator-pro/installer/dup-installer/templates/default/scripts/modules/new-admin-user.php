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
    const createNewInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)); ?>;

    $(document).ready(function () {
        $('#' + createNewInputId).change(function () {
            if ($(this).prop('checked')) {
                $('.new-admin-field, .new-admin-field > input').prop('disabled', false);
            } else {
                $('.new-admin-field, .new-admin-field > input').prop('disabled', true).val('').trigger('keyup').trigger('blur');
            }
        });
    });
</script>

