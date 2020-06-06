<?php
defined("ABSPATH") or die("");
DUP_PRO_U::hasCapability('export');

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/snaplib/class.snaplib.u.url.php');

$inner_page = isset($_REQUEST['inner_page']) ? sanitize_text_field($_REQUEST['inner_page']) : 'schedules';
/*
switch ($inner_page)
{
    case 'edit':
        if (!wp_verify_nonce($_GET['_wpnonce'], 'edit-schedule')) {
            die('Security issue');
        }
        break;
}*/

$profile_url = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::$SCHEDULES_SUBMENU_SLUG, false);
$schedules_tab_url = DupProSnapLibURLU::appendQueryValue($profile_url, 'tab', 'schedules');
$edit_schedule_url = DupProSnapLibURLU::appendQueryValue($schedules_tab_url, 'inner_page', 'edit');
// Not used wp_nonce_url because Edit existing schedule js not working after encoding
$edit_schedule_url .= '&_wpnonce='.wp_create_nonce('edit-schedule');

new DUP_PRO_CTRL_Schedule();
switch ($inner_page)
{
    case 'schedules': include('schedule.list.php');
        break;
    case 'edit': include('schedule.edit.php');
        break;
}
?>