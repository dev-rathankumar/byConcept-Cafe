<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

dupxTplRender('pages-parts/page-header', array(
    'paramView'   => 'step2',
    'bodyId'      => 'page-step2',
    'bodyClasses' => $bodyClasses
));
?>
<div id="content-inner">
    <?php DUPX_U_Html::getHeaderMain('Step <span class="step">2</span> of 4: Install Database <div class="sub-header">This step will install the database from the archive.</div>'); ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/step2/main'); ?>
    </div>
    <?php
    dupxTplRender('parts/ajax-error');
    dupxTplRender('parts/progress-bar');
    ?>
</div>
<?php
dupxTplRender('scripts/step2-init');
dupxTplRender('pages-parts/page-footer');
