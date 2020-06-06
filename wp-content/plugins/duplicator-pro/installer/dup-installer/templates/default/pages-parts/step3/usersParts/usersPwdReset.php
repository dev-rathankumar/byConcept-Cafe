<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$title         = DUPX_ArchiveConfig::getInstance()->isNetworkInstall() ? 'SUPER ADMIN' : 'ADMIN';
?>
<div class="hdr-sub3">Existing <?php echo $title; ?> users password reset</div>
<div class="dupx-opts s3-opts">
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET);
    ?>
</div>