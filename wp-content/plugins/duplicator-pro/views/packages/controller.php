<?php
defined("ABSPATH") or die("");

DUP_PRO_Handler::init_error_handler();
DUP_PRO_U::hasCapability('export');

global $wpdb;

//COMMON HEADER DISPLAY
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/views/inc.header.php');

$_REQUEST['action'] =  isset($_REQUEST['action']) ? $_REQUEST['action'] : 'main';

switch ($_REQUEST['action']) {
	case 'detail': $current_view = 'detail';
		break;
	default:
		$current_view = 'main';
		break;
}

$nonce = wp_create_nonce('DUP_PRO_CTRL_Package_getPackageFile');
?>

<script>
    jQuery(document).ready(function($)
	{
        // which: 0=installer, 1=archive, 2=sql file, 3=log
        DupPro.Pack.DownloadPackageFile = function (which, packageID)
		{
            var actionLocation = ajaxurl + '?action=DUP_PRO_CTRL_Package_getPackageFile&which=' + which + '&package_id=' + packageID + '&nonce=' + '<?php echo $nonce; ?>';
    
            if(which == 3) {
                var win = window.open(actionLocation, '_blank');
                win.focus();    
            }
            else {
                location.href = actionLocation;            
            }
			return false;
        }
    });
</script>

<div class="wrap">
    <?php
		switch ($current_view) {
			case 'main'		: include('main/controller.php'); break;
			case 'detail'	: include('details/controller.php'); break;
		}
    ?>
</div>