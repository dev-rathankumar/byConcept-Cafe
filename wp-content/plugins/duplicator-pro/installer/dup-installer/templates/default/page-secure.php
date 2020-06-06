<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

dupxTplRender('pages-parts/page-header', array(
    'paramView'       => 'secure',
    'bodyId'          => 'page-secure',
    'bodyClasses'     => $bodyClasses,
    'skipTopMessages' => true
));
?>
<div id="content-inner">
    <?php DUPX_U_Html::getHeaderMain('Installer Password'); ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/secure/main'); ?>
    </div>
</div>
<?php
dupxTplRender('scripts/secure-init');
dupxTplRender('pages-parts/page-footer');
