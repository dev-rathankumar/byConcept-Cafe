<?php
defined("ABSPATH") or die("");
DUP_PRO_U::hasCapability('export');
?>
<style>
    div#dup-store-err-details {display:none}
</style>
<?php
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/views/inc.header.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/snaplib/class.snaplib.u.url.php');

$profile_url = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::$STORAGE_SUBMENU_SLUG, false);
$storage_tab_url = DupProSnapLibURLU::appendQueryValue($profile_url, 'tab', 'storage');

$edit_storage_url = DupProSnapLibURLU::appendQueryValue($storage_tab_url, 'inner_page', 'edit');
$edit_default_storage_url = DupProSnapLibURLU::appendQueryValue($storage_tab_url, 'inner_page', 'edit-default');

$inner_page = isset($_REQUEST['inner_page']) ? sanitize_text_field($_REQUEST['inner_page']) : 'storage';

/**
 * 
 * @param Exception $e
 * @return string
 */
function getDupProStorageErrorMsg($e)
{
    $storage_error_msg = '<div class="error-txt" style="margin:10px 0 20px 0; max-width:750px">';
    $storage_error_msg .= DUP_PRO_U::esc_html__('An error has occurred while trying to read a storage item!  ');
    $storage_error_msg .= DUP_PRO_U::esc_html__('To resolve this issue please delete the storage item and re-enter its information.  ');
    $storage_error_msg .= DUP_PRO_U::esc_html__('If the problem persists please contact the support team.');
    $storage_error_msg .= '</div>';
    $storage_error_msg .= '<a href="javascript:void(0)" onclick="jQuery(\'#dup-store-err-details\').toggle();">';
    $storage_error_msg .= DUP_PRO_U::esc_html__('Show Details');
    $storage_error_msg .= '</a>';
    $storage_error_msg .= '<div id="dup-store-err-details" >'.esc_html($e->getMessage()).
        "<br/><br/><small>".
        esc_html($e->getTraceAsString()) .
        "</small></div>";
    return $storage_error_msg;
}

try {
    switch ($inner_page) {
        case 'storage':
            // I left the global try catch for security but the exceptions should be managed inside the list.
            include('storage.list.php');
            break;
        case 'edit':
            include('storage.edit.php');
            break;
        case 'edit-default':
            include('storage.edit.default.php');
            break;
    }
} 
catch (Exception $e) {
    echo getDupProStorageErrorMsg($e);
}


