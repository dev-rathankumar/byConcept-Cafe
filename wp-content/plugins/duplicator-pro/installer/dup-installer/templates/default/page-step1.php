<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

dupxTplRender('pages-parts/page-header', array(
    'paramView'   => 'step1',
    'bodyId'      => 'page-step1',
    'bodyClasses' => $bodyClasses
));
?>
<div id="content-inner">
    <?php DUPX_U_Html::getHeaderMain('Step <span class="step">1</span> of 4: Deployment <div class="sub-header">This step will extract the archive file contents.</div>'); ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/step1/main'); ?>
    </div>
    <?php
    dupxTplRender('parts/ajax-error');
    dupxTplRender('parts/progress-bar');
    ?>
</div>
<?php
dupxTplRender('scripts/step1-init');
dupxTplRender('pages-parts/page-footer');
