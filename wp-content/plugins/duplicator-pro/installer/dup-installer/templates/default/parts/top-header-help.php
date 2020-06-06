<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<table cellspacing="0" class="header-wizard">
    <tr>
        <td style="width:100%;">
            <div class="dupx-branding-header">
                <?php if (isset($GLOBALS['DUPX_AC']->brand) && isset($GLOBALS['DUPX_AC']->brand->logo) && !empty($GLOBALS['DUPX_AC']->brand->logo)) : ?>
                    Help
                <?php else: ?>
                    <i class="fa fa-bolt fa-sm"></i> Duplicator Pro help
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>