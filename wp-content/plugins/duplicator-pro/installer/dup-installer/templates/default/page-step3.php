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
    <?php DUPX_U_Html::getHeaderMain('Step <span class="step">3</span> of 4: Update Data <div class="sub-header">This step will update the database and config files to match your new sites values.</div>'); ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/step3/main'); ?>
    </div>
    <?php
    dupxTplRender('parts/ajax-error');
    dupxTplRender('parts/progress-bar');
    ?>
</div>
<?php
dupxTplRender('scripts/step3-init');
dupxTplRender('pages-parts/page-footer');
