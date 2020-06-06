<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

dupxTplRender('pages-parts/page-header', array(
    'paramView'   => 'step3',
    'bodyId'      => 'page-step3',
    'bodyClasses' => $bodyClasses
));
?>
<div id="content-inner">
    <?php DUPX_U_Html::getHeaderMain('Step <span class="step">4</span> of 4: Test Site'); ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/step4/main'); ?>
    </div>
    <?php
    dupxTplRender('parts/ajax-error');
    dupxTplRender('parts/progress-bar');
    ?>
</div>
<?php
dupxTplRender('scripts/step4-init');
dupxTplRender('pages-parts/page-footer');
