<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
</div>
<?php dupxTplRender('pages-parts/step3/usersParts/usersPwdReset'); ?>
<br><br>
<?php dupxTplRender('pages-parts/step3/usersParts/newAdminUser'); ?>
