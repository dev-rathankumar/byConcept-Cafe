<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

dupxTplRender('pages-parts/page-header', array(
    'paramView'   => 'exception',
    'bodyId'      => 'page-exception',
    'bodyClasses' => $bodyClasses
));
?>
<div id="content-inner">
    <?php DUPX_U_Html::getHeaderMain('Exception error'); ?>
    <div id="main-content-wrapper" >
        <?php
        dupxTplRender('pages-parts/exception/main', array(
            'exception' => $exception
        ));
        ?>
    </div>
</div>

<?php
dupxTplRender('pages-parts/page-footer');
