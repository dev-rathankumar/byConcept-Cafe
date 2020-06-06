<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$wpConfig      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG);
$skipWpConfig  = ($wpConfig == 'nothing' || $wpConfig == 'original');
?>
<!-- ==========================
OPTIONS -->
<div class="hdr-sub1 toggle-hdr open" data-type="toggle" data-target="#s3-adv-opts">
    <a href="javascript:void(0)"><i class="fa fa-plus-square"></i>Options</a>
</div>
<!-- START TABS -->
<div id="s3-adv-opts" class="hdr-sub1-area tabs-area no-display">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-admin-account">Admin Account</a></li>
            <li><a href="#tabs-scan-options">Scan Options</a></li>
            <li><a href="#tabs-plugins">Plugins</a></li>
            <?php if (!$skipWpConfig) { ?>
                <li><a href="#tabs-wp-config-file">WP-Config File</a></li>
            <?php } ?>
        </ul>

        <!-- =====================
        ADMIN TAB -->
        <div id="tabs-admin-account">
            <?php dupxTplRender('pages-parts/step3/usersTab'); ?>
        </div>

        <!-- =====================
        SCAN TAB -->
        <div id="tabs-scan-options">
            <?php DUPX_View_S3::tabScanOptions(); ?>
        </div>

        <!-- =====================
        PLUGINS  TAB -->
        <div id="tabs-plugins">
            <?php DUPX_View_S3::tabPluginsContent(); ?>
        </div>
        <?php if (!$skipWpConfig) { ?>
            <!-- =====================
            WP-CONFIG TAB -->
            <div id="tabs-wp-config-file">
                <?php DUPX_View_S3::tabWpConfig(); ?>
            </div>
        <?php } ?>
    </div>
</div>
