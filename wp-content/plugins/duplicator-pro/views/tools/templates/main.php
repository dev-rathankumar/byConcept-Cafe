<?php
defined("ABSPATH") or die("");

//COMMON HEADER DISPLAY
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/views/inc.header.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/ui/class.ui.dialog.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/snaplib/class.snaplib.u.url.php');

$profile_url = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::$TOOLS_SUBMENU_SLUG, false);
$templates_tab_url = DupProSnapLibURLU::appendQueryValue($profile_url, 'tab', 'templates');

$edit_template_url = DupProSnapLibURLU::appendQueryValue($templates_tab_url, 'inner_page', 'edit');

$inner_page = isset($_REQUEST['inner_page']) ? sanitize_text_field($_REQUEST['inner_page']) : 'templates';

switch ($inner_page)
{
    case 'templates': include('template.list.php');
        break;

    case 'edit': include('template.edit.php');
        break;
}
?>