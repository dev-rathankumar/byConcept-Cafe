<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;


$paramsManager = DUPX_Paramas_Manager::getInstance();
$subsite_id    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
$safe_mode     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SAFE_MODE);
$url_new     = rtrim($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW), "/");

$admin_base        = basename($GLOBALS['DUPX_AC']->wplogin_url);

$site_url = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SITE_URL);
$admin_redirect = (($GLOBALS['DUPX_AC']->mu_mode > 0) && ($subsite_id == -1))
    ? "{$site_url}/wp-admin/network/admin.php?page=duplicator-pro-tools&tab=d"
    : "{$site_url}/wp-admin/admin.php?page=duplicator-pro-tools&tab=d";


$admin_redirect = "{$admin_redirect}&in=" . DUPX_Security::getInstance()->getBootloader() . "&sm=" . $safe_mode;
$admin_redirect = urlencode($admin_redirect);
$admin_url_qry  = (strpos($admin_base, '?') === false) ? '?' : '&';
$admin_login    = "{$site_url}/{$admin_base}{$admin_url_qry}redirect_to={$admin_redirect}";


?><script>
    var loginURL;
    DUPX.getAdminLogin = function() {
        if ($('input#auto-delete').is(':checked')) {
            var action = encodeURIComponent('&action=installer');
            loginURL = '<?php echo $admin_login; ?>' + action;
            window.open(loginURL, '_blank');
        } else {
            loginURL = '<?php echo $admin_login; ?>';
            window.open(loginURL, '_blank');
        }
    };

    //DOCUMENT LOAD
    $(document).ready(function() {

        //INIT Routines
        $("*[data-type='toggle']").click(DUPX.toggleClick);
        $("#tabs").tabs();

    });
</script>