<?php
defined("ABSPATH") or die("");
DUP_PRO_U::hasCapability('export');

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/ui/class.ui.dialog.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/snaplib/class.snaplib.u.url.php');

$packages_url = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::$PACKAGES_SUBMENU_SLUG, false);
$packages_tab_url = DupProSnapLibURLU::appendQueryValue($packages_url, 'tab', 'packages');
$edit_package_url = DupProSnapLibURLU::appendQueryValue($packages_tab_url, 'inner_page', 'new1');
$edit_package_url = wp_nonce_url($edit_package_url, 'new1-package', '_wpnonce');
$inner_page = isset($_REQUEST['inner_page']) ? sanitize_text_field($_REQUEST['inner_page']) : 'list';

// nonce checking
switch ($inner_page) {
    case 'new1':
        if (!wp_verify_nonce($_GET['_wpnonce'], 'new1-package')) {
            die('Security issue');
        }
        break;
    case 'new2':
        if (!wp_verify_nonce($_GET['_wpnonce'], 'new2-package')) {
            die('Security issue');
        }
        break;
}

?>

<style>
    /*WIZARD TABS */
    div#dup-wiz {padding:0px; margin:0;  }
    div#dup-wiz-steps {margin:10px 0px 0px 10px; padding:0px;  clear:both; font-size:13px; min-width:350px;}
    div#dup-wiz-title {padding:2px 0px 0px 0px; font-size:18px;}
    #dup-wiz a { position:relative; display:block; width:auto; min-width:55px; height:25px; margin-right:8px; padding:0px 10px 0px 10px; float:left; line-height:24px; 
	color:#000; background:#E4E4E4; border-radius:5px; letter-spacing:1px; border:1px solid #E4E4E4; text-align: center }
    #dup-wiz .active-step a {color:#fff; background:#ACACAC; font-weight: bold; border:1px solid #888}
    #dup-wiz .completed-step a {color:#E1E1E1; background:#BBBBBB; }
    /*Footer */
    div.dup-button-footer input {min-width: 105px}
    div.dup-button-footer {padding: 1px 10px 0px 0px; text-align: right}
</style>

<?php
switch ($inner_page)
{
    case 'list': 
		duplicator_pro_header(DUP_PRO_U::__("Packages") . " » " . DUP_PRO_U::__('All'));
		include('packages.php');
        break;
    case 'new1': 
		duplicator_pro_header(DUP_PRO_U::__("Packages") . " » " . DUP_PRO_U::__('New'));
		include('s1.setup0.base.php');
        break;
    case 'new2': 
		duplicator_pro_header(DUP_PRO_U::__("Packages") . " » " . DUP_PRO_U::__('New'));
		include('s2.scan1.base.php');
        break;
}

?>

