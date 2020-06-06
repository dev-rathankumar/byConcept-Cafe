<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex,nofollow">
        <title>Duplicator Professional</title>

        <link rel="apple-touch-icon" sizes="180x180" href="favicon/pro01_apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="favicon/pro01_favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="favicon/pro01_favicon-16x16.png">
        <link rel="manifest" href="favicon/site.webmanifest">
        <link rel="mask-icon" href="favicon/pro01_safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="favicon/pro01_favicon.ico">
        <meta name="msapplication-TileColor" content="#00aba9">
        <meta name="msapplication-config" content="favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        <link rel='stylesheet' href='assets/normalize.css' type='text/css' media='all' />
        <link rel='stylesheet' href='assets/font-awesome/css/all.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='assets/js/password-strength/password.css' type='text/css' media='all' />
        <?php
        require(DUPX_INIT.'/assets/inc.libs.css.php');
        require(DUPX_INIT.'/assets/inc.css.php');
        ?>
        <script src="<?php echo DUPX_INIT_URL;?>/assets/inc.libs.js?v=<?php echo $GLOBALS['DUPX_AC']->version_dup; ?>"></script>
        <?php
        require(DUPX_INIT.'/assets/inc.js.php');
        dupxTplRender('scripts/dupx-functions');
        ?>
        <script type="text/javascript" src="assets/js/password-strength/password.js"></script>
    </head>
    <body id="<?php echo $bodyId; ?>" class="<?php echo $bodyClasses; ?>" >
        <div id="content">
            <?php
            dupxTplRender('parts/top-header.php', array(
                'paramView' => $paramView
            ));
            if (!isset($skipTopMessages) || $skipTopMessages !== true) {
                dupxTplRender('parts/top-messages.php');
            }
