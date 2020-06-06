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
                <?php
                if (isset($GLOBALS['DUPX_AC']->brand) && isset($GLOBALS['DUPX_AC']->brand->logo) && !empty($GLOBALS['DUPX_AC']->brand->logo)) {
                    echo $GLOBALS['DUPX_AC']->brand->logo;
                } else {
                    ?>
                    <i class="fa fa-bolt fa-sm"></i> Duplicator Pro
                    <?php
                }
                ?>
            </div>
        </td>
        <td class="wiz-dupx-version">
            <a href="javascript:void(0)" onclick="DUPX.openServerDetails()">version:<?php echo $GLOBALS['DUPX_AC']->version_dup; ?></a>&nbsp;
            <?php DUPX_View_Funcs::helpLockLink(); ?>
            <div style="padding: 6px 0">
                <?php DUPX_View_Funcs::helpLink($paramView); ?>
            </div>
        </td>
    </tr>
</table>