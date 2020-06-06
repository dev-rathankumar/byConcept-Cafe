<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

$paramsManager = DUPX_Paramas_Manager::getInstance();
$subsite_id    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
$safe_mode     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SAFE_MODE);
$url_new       = rtrim($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW), "/");

$finalReportData = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA);
?>

<!-- =========================================
VIEW: STEP 4- INPUT -->
<form id='s4-input-form' method="post" class="content-form" autocomplete="off" >
    <table class="s4-final-step">
        <tr style="vertical-align: top">
            <td style="padding-top:10px">
                <button type="button" id="s4-final-btn" class="s4-final-btns" onclick="DUPX.getAdminLogin()"><i class="fab fa-wordpress"></i> Admin Login</button>
            </td>
            <td>
                Click the Admin Login button to login and finalize this install.<br />
                <input type="checkbox" name="auto-delete" id="auto-delete" checked="true" />
                <label for="auto-delete">Auto delete installer files after login to secure site <small>(recommended!)</small></label>
                <br /><br />


                <!-- WARN: MU MESSAGES -->
                <div class="s4-warn" style="display:<?php echo ($subsite_id > 0 ? 'block' : 'none') ?>">
                    <b>Multisite</b><br />
                    Some plugins may exhibit quirks when switching from subsite to standalone mode, so all plugins have been disabled. Re-activate each plugin one-by-one and test
                    the site after each activation. If you experience issues please see the
                    <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-mu" target="_blank">Multisite Network FAQs</a> online.
                    <br /><br />
                </div>

                <!-- WARN: SAFE MODE MESSAGES -->
                <div class="s4-warn" style="display:<?php echo ($safe_mode > 0 ? 'block' : 'none') ?>">
                    <b>Safe Mode</b><br />
                    Safe mode has <u>deactivated</u> all plugins. Please be sure to enable your plugins after logging in. <i>If you notice that problems arise when activating
                        the plugins then active them one-by-one to isolate the plugin that could be causing the issue.</i>
                </div>
            </td>
        </tr>
    </table>
    <i style="color:maroon; font-size:12px">
        <i class="fa fa-exclamation-triangle fa-sm"></i> IMPORTANT FINAL STEPS: Login into the WordPress Admin to remove all <?php
        DUPX_View_Funcs::helpLink('step4', 'installation files');
        ?> and keep this site secure. This install is not complete until the installer files are removed.
    </i>
    <br /><br /><br />

    <?php
    $nManager = DUPX_NOTICE_MANAGER::getInstance();
    if ($finalReportData['extraction']['query_errs'] > 0) {
        $linkAttr = './'.DUPX_U::esc_attr($GLOBALS["LOG_FILE_NAME"]);
        $longMsg  = 'Queries that error during the deploy step are logged to the '.DUPX_View_Funcs::installerLogLink(false);
        $longMsg  .= <<<LONGMSG
file and
and marked with an **ERROR** status.   If you experience a few errors (under 5), in many cases they can be ignored as long as your site is working correctly.
However if you see a large amount of errors or you experience an issue with your site then the error messages in the log file will need to be investigated.
<br/><br/>

<b>COMMON FIXES:</b>
<ul>
    <li>
        <b>Unknown collation:</b> See Online FAQ:
        <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-090-q" target="_blank">What is Compatibility mode & 'Unknown collation' errors?</a>
    </li>
    <li>
        <b>Query Limits:</b> Update MySQL server with the <a href="https://dev.mysql.com/doc/refman/5.5/en/packet-too-large.html" target="_blank">max_allowed_packet</a>
        setting for larger payloads.
    </li>
</ul>
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg'    => 'DB EXTRACTION - INSTALL NOTICES ('.$finalReportData['extraction']['query_errs'].')',
            'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg'     => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections'    => array('database'),
            'priority'    => 5,
            'open'        => true
        ));
    }

    if ($finalReportData['replace']['errsql_sum'] > 0) {
        $longMsg = <<<LONGMSG
Update errors that show here are queries that could not be performed because the database server being used has issues running it.
Please validate the query, if it looks to be of concern please try to run the query manually.
In many cases if your site performs well without any issues you can ignore the error.
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'STEP 3 - UPDATE NOTICES ('.$finalReportData['replace']['errsql_sum'].')',
            'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg'  => $longMsg,
            'sections' => array('database'),
            'priority' => 5,
            'open'     => true
        ));
    }

    if ($finalReportData['replace']['errkey_sum'] > 0) {
        $longMsg = <<<LONGMSG
Notices should be ignored unless issues are found after you have tested an installed site.
This notice indicates that a primary key is required to run the update engine. Below is a list of tables and the rows that were not updated.
On some databases you can remove these notices by checking the box 'Enable Full Search' under options in step3 of the installer.
<br/><br/>
<small>
    <b>Advanced Searching:</b><br/>
    Use the following query to locate the table that was not updated: <br/>
    <i>SELECT @row := @row + 1 as row, t.* FROM some_table t, (SELECT @row := 0) r</i>
</small>
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg'    => 'TABLE KEY NOTICES  ('.$finalReportData['replace']['errkey_sum'].')',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections'    => array('database'),
            'priority'    => 5,
            'open'        => true
        ));
    }

    if ($finalReportData['replace']['errser_sum'] > 0) {
        $longMsg = <<<LONGMSG
Notices should be ignored unless issues are found after you have tested an installed site.
The SQL below will show data that may have not been updated during the serialization process.
Best practices for serialization notices is to just re-save the plugin/post/page in question.
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'SERIALIZATION NOTICES  ('.$finalReportData['replace']['errser_sum'] .')',
            'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'  => $longMsg,
            'sections' => array('search_replace'),
            'priority' => 5,
            'open'     => true
        ));
    }

    $numGeneralNotices = $nManager->countFinalReportNotices('general', DUPX_NOTICE_ITEM::NOTICE, '>=');
    if ($numGeneralNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No general notices',
            'level'    => DUPX_NOTICE_ITEM::INFO,
            'sections' => array('general'),
            'priority' => 5
        ));
    } else {
        $longMsg = <<<LONGMSG
The following is a list of notices that may need to be fixed in order to finalize your setup.
These values should only be investigated if you're running into issues with your site.
For more details see the <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">WordPress Codex</a>.
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg'    => 'Info',
            'level'       => DUPX_NOTICE_ITEM::INFO,
            'longMsg'     => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections'    => array('general'),
            'priority'    => 5
        ));
    }



    $numDbNotices = $nManager->countFinalReportNotices('database', DUPX_NOTICE_ITEM::NOTICE, '>');
    if ($numDbNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No errors in database',
            'level'    => DUPX_NOTICE_ITEM::INFO,
            'longMsg'  => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    } else if ($numDbNotices <= 10) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Some errors in the database ('.$numDbNotices.')',
            'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'  => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    } else if ($numDbNotices <= 100) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Errors in the database ('.$numDbNotices.')',
            'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg'  => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    } else {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Many errors in the database ('.$numDbNotices.')',
            'level'    => DUPX_NOTICE_ITEM::CRITICAL,
            'longMsg'  => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    }


    $numSerNotices = $nManager->countFinalReportNotices('search_replace', DUPX_NOTICE_ITEM::NOTICE, '>=');
    if ($numSerNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No search and replace data errors',
            'level'    => DUPX_NOTICE_ITEM::INFO,
            'longMsg'  => '',
            'sections' => 'search_replace',
            'priority' => 5
        ));
    }


    $numFilesNotices = $nManager->countFinalReportNotices('files', DUPX_NOTICE_ITEM::NOTICE, '>=');
    if ($numFilesNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No files extraction errors',
            'level'    => DUPX_NOTICE_ITEM::INFO,
            'longMsg'  => '',
            'sections' => 'files',
            'priority' => 5
        ));
    }

    $numPluginsNotices = $nManager->countFinalReportNotices('plugins', DUPX_NOTICE_ITEM::NOTICE, '>=');


    $nManager->sortFinalReport();
    $nManager->finalReportLog(array('general', 'files', 'database', 'search_replace', 'plugins'));
    ?>

    <div class="s4-go-back">
        Additional Notes:
        <ul style="margin-top: 1px">
            <li>
                <a href="javascript:void(0)" onclick="$('#s4-install-report').toggle(400)">Review Migration Report</a><br /><br>
                <table class='s4-report-results' style="width:100%">
                    <tbody>
                        <tr>
                            <td>General Notices status</td>
                            <td>(<?php echo $numGeneralNotices; ?>)</td>
                            <td><?php $nManager->getSectionErrLevelHtml('general'); ?></td>
                        </tr>
                        <tr>
                            <td>Files status</td>
                            <td>(<?php echo $numFilesNotices; ?>)</td>
                            <td> <?php $nManager->getSectionErrLevelHtml('files'); ?></td>
                        </tr>
                        <tr>
                            <td>Database migration status</td>
                            <td>(<?php echo $numDbNotices; ?>)</td>
                            <td><?php $nManager->getSectionErrLevelHtml('database'); ?></td>
                        </tr>
                        <tr>
                            <td>Search and replace migration status</td>
                            <td>(<?php echo $numSerNotices; ?>)</td>
                            <td> <?php $nManager->getSectionErrLevelHtml('search_replace'); ?></td>
                        </tr>
                        <tr>
                            <td>Plugins</td>
                            <td>(<?php echo $numPluginsNotices; ?>)</td>
                            <td> <?php $nManager->getSectionErrLevelHtml('plugins'); ?></td>
                        </tr>
                    </tbody>
                </table><br>
            </li>
            <li>
                Review this site's <a href="<?php echo $url_new; ?>" target="_blank">front-end</a> or
                re-run the installer and <a href="<?php echo "{$url_new}/installer.php"; ?>">go back to step 1</a>.
            </li>
            <?php
            $wpconfigNotice = $nManager->getFinalReporNoticeById('wp-config-changes');
            $htaccessNorice = $nManager->getFinalReporNoticeById('htaccess-changes');
            ?>
            <li>Please validate <?php echo $wpconfigNotice->longMsg; ?> and <?php echo $htaccessNorice->longMsg; ?>.</li>
            <li>For additional help and questions visit the <a href='http://snapcreek.com/support/docs/faqs/' target='_blank'>online FAQs</a>.</li>
        </ul>
    </div>

    <!-- ========================
    INSTALL REPORT -->
    <div id="s4-install-report" style='display:none'>
        <table class='s4-report-results' style="width:100%">
            <tr>
                <th colspan="4">Database Report</th>
            </tr>
            <tr style="font-weight:bold">
                <td style="width:150px"></td>
                <td>Tables</td>
                <td>Rows</td>
                <td>Cells</td>
            </tr>
            <tr>
                <td>Created</td>
                <td><span><?php echo $finalReportData['extraction']['table_count']; ?></span></td>
                <td><span><?php echo $finalReportData['extraction']['table_rows']; ?></span></td>
                <td>n/a</td>
            </tr>
            <tr>
                <td>Scanned</td>
                <td><span><?php echo $finalReportData['replace']['scan_tables']; ?></span></td>
                <td><span><?php echo $finalReportData['replace']['scan_rows']; ?></span></td>
                <td><span><?php echo $finalReportData['replace']['scan_cells']; ?></span></td>
            </tr>
            <tr>
                <td>Updated</td>
                <td><span><?php echo $finalReportData['replace']['updt_tables']; ?></span></td>
                <td><span><?php echo $finalReportData['replace']['updt_rows']; ?></span></td>
                <td><span><?php echo $finalReportData['replace']['updt_cells']; ?></span></td>
            </tr>
        </table>
        <br />

        <div id="s4-notice-reports" class="report-sections-list">
            <?php
            $nManager->displayFinalRepostSectionHtml('general', 'General notices report');
            $nManager->displayFinalRepostSectionHtml('files', 'Files notices report');
            $nManager->displayFinalRepostSectionHtml('database', 'Database notices report');
            $nManager->displayFinalRepostSectionHtml('search_replace', 'Search and replace notices report');
            $nManager->displayFinalRepostSectionHtml('plugins', 'Plugins actions report');
            ?>
        </div>
        <div class='s4-connect' style="display:none">
            <a href='http://snapcreek.com/support/docs/faqs/' target='_blank'>FAQs</a> |
            <a href='https://snapcreek.com' target='_blank'>Support</a>
        </div>
</form>