<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

?>
<div class="hdr-sub1 toggle-hdr open" data-type="toggle" data-target="#validate-area">
    <a id="validate-area-link">
        <i class="fa fa-plus-square"></i>Validation
    </a>
    <span id="validate-global-badge-status" class="status-badge wait" ></span>
</div>
<div id="validate-area" class="hdr-sub1-area no-display" >
    <div class='info-top'>
        The system validation checks help to make sure the system is ready for install.
    </div>
    <div id="validate-result" >
        <?php dupxTplRender('parts/validation/validate-noresult'); ?>
    </div>
</div>