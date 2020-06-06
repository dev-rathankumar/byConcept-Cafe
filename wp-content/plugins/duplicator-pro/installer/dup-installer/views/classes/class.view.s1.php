<?php
/**
 * 
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * View s3 functions
 */
class DUPX_View_S1
{

    public static function getReq($ret_is_dir_writable)
    {
        $req       = array();
        $req['10'] = $ret_is_dir_writable['ret'] ? 'Pass' : 'Fail';
        $req['20'] = function_exists('mysqli_connect') ? 'Pass' : 'Fail';

        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $replaceEngine = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE);
        if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE) || $replaceEngine !== DUPX_S3_Funcs::MODE_SKIP) {
            $req['50'] = 'Pass';
        } else {
            if (DUPX_InstallerState::getInstance()->isInstallerCreatedInThisLocation()) {
                $req['50'] = 'Pass';
            } else {
                $req['50'] = 'Fail';
            }
        }

        /**
         * not supporte yet
         */
        $managed = DUPX_Custom_Host_Manager::getInstance()->isManaged();
        if ($managed === DUPX_Custom_Host_Manager::HOST_WORDPRESSCOM || $managed === DUPX_Custom_Host_Manager::HOST_PANTHEON) {
            $req['60'] = 'Fail';
        } else {
            $req['60'] = 'Pass';
        }
        return $req;
    }

    public static function getNotices()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $openbase             = ini_get("open_basedir");
        $datetime1            = $GLOBALS['DUPX_AC']->created;
        $datetime2            = date("Y-m-d H:i:s");
        $fulldays             = round(abs(strtotime($datetime1) - strtotime($datetime2)) / 86400);
        $max_time_size        = 314572800;  //300MB
        $max_time_ini         = ini_get('max_execution_time');
        $max_time_warn        = (is_numeric($max_time_ini) && $max_time_ini < 31 && $max_time_ini > 0) && DUPX_Conf_Utils::archiveSize() > $max_time_size;
        $parent_has_wordfence = file_exists($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW).'/../wp-content/plugins/wordfence/wordfence.php');

        $notice       = array();
        $notice['10'] = DUPX_InstallerState::getInstance()->getMode() !== DUPX_InstallerState::MODE_OVR_INSTALL ? 'Good' : 'Warn';
        $notice['20'] = !DUPX_Conf_Utils::isConfArkPresent() ? 'Good' : 'Warn';
        if ($archiveConfig->exportOnlyDB) {
            $notice['25'] = DUPX_Server::isWordPress() ? 'Good' : 'Warn';
        }
        $notice['30'] = $fulldays <= 180 ? 'Good' : 'Warn';

        $packagePHP      = $archiveConfig->version_php;
        $packagePHPMajor = intval($packagePHP);
        $currentPHPMajor = intval(phpversion());
        $notice['45']    = ($packagePHPMajor === $currentPHPMajor || $GLOBALS['DUPX_AC']->exportOnlyDB) ? 'Good' : 'Warn';

        $notice['50'] = empty($openbase) ? 'Good' : 'Warn';
        $notice['60'] = !$max_time_warn ? 'Good' : 'Warn';
        $notice['70'] = !$parent_has_wordfence ? 'Good' : 'Warn';
        $notice['80'] = !$GLOBALS['DUPX_AC']->is_outer_root_wp_config_file ? 'Good' : 'Warn';
        if ($archiveConfig->exportOnlyDB) {
            $notice['90'] = 'Good';
        } else {
            $notice['90'] = (!$GLOBALS['DUPX_AC']->is_outer_root_wp_content_dir) ? 'Good' : 'Warn';
        }

        $space_free    = @disk_free_space($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        $archive_size  = DUPX_Conf_Utils::archiveSize();
        $notice['100'] = ($space_free && $archive_size > 0 && $archive_size > $space_free) ? 'Warn' : 'Good';

        $notice['110'] = (DUPX_Custom_Host_Manager::getInstance()->isManaged() && $GLOBALS['DUPX_AC']->wp_tableprefix != DUPX_WPConfig::getValueFromLocalWpConfig('table_prefix', 'variable')) ? 'Warn' : 'Good';

        return $notice;
    }

    public static function infoTabs()
    {
        //ARCHIVE FILE
        if (DUPX_Conf_Utils::archiveExists()) {
            $arcCheck = 'pass';
        } else {
            if (DUPX_Conf_Utils::isConfArkPresent()) {
                $arcCheck = 'warn';
            } else {
                $arcCheck = 'fail';
            }
        }

        $hostManager     = DUPX_Custom_Host_Manager::getInstance();
        $isRestoreBackup = DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_BK_RESTORE;
        $opened          = $hostManager->isManaged() || $isRestoreBackup;
        ?>
        <div class="hdr-sub1 toggle-hdr <?php echo $opened ? 'close' : 'open'; ?>" data-type="toggle" data-target="#s1-area-archive-file">
            <a id="s1-area-archive-file-link"><i class="fa fa-plus-square"></i>Setup</a>
            <span class="status-badge <?php echo $arcCheck; ?>"></span>
        </div>
        <div id="s1-area-archive-file" class="hdr-sub1-area tabs-area <?php echo $opened ? '' : 'no-display'; ?>" >
            <div class="tabs">
                <ul>
                    <?php if ($isRestoreBackup) { ?>
                        <li><a href="#tabs-3" >Restore backup</a></li>
                        <?php
                    }

                    if ($hostManager->isManaged()) {
                        ?>
                        <li><a href="#tabs-2" >Managed Hosting</a></li>
                    <?php } ?>
                    <li><a href="#tabs-1">Archive</a></li>

                </ul>
                <?php
                self::managedInfoTab();
                self::restorBackupInfoTab();
                self::archiveInfoTab();
                ?>
            </div>
        </div>
        <?php
    }

    public static function options()
    {
        $opened = !DUPX_Ctrl_Params::isParamsValidated();
        ?>
        <!-- ==========================
        OPTIONS -->
        <div id="step1-options-toggle-btn" class="hdr-sub1 toggle-hdr <?php echo $opened ? 'close' : 'open'; ?>" data-type="toggle" data-target="#step1-options-wrapper">
            <a href="javascript:void(0)"><i class="fa fa-plus-square"></i>Options</a>
        </div>
        <!-- START TABS -->
        <div id="step1-options-wrapper" class="hdr-sub1-area tabs-area <?php echo $opened ? '' : 'no-display'; ?>">
            <div class="tabs">
                <ul>
                    <li><a href="#tabs-settings">Settings</a></li>
                    <li><a href="#tabs-advanced">Advanced</a></li>
                    <li><a href="#tabs-other">Other Config</a></li>
                </ul>
                <div id="tabs-settings">
                    <?php
                    DUPX_View_S1::newSettings();
                    DUPX_View_S1::multisiteOptions();
                    ?>
                </div>
                <div id="tabs-advanced">
                    <div class="help-target">
                        <?php DUPX_View_Funcs::helpIconLink('step1'); ?>
                    </div>
                    <?php DUPX_View_S1::generalOptions(); ?>
                    <br/><br/>
                    <?php DUPX_View_S1::configurationFilesOptions(); ?>
                </div>
                <div id="tabs-other">
                    <?php DUPX_View_S1::otherUrlsAndPaths(); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function newSettings()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        ?>
        <div class="dupx-opts s3-opts">
            <?php
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_NEW);
            ?>
        </div>
        <?php
    }

    public static function otherUrlsAndPaths()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        ?>
        <div id="other-path-url-options" class="dupx-opts dupx-advopts">
            <small><i>The recommended setting for these values is "Auto".<br>
                    The "Auto" setting derives its values from the "New Site URL" and "New Path" inputs found on the settings tab.<br>  
                    Please use caution if manually updating these values and be sure the paths are correct.</i></small>
            <?php
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SITE_URL_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SITE_URL);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW);

            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW);
            ?>
        </div>   
        <?php
    }

    protected static function archiveInfoTab()
    {
        //ARCHIVE FILE
        if (DUPX_Conf_Utils::archiveExists()) {
            $arcCheck = 'Pass';
        } else {
            if (DUPX_Conf_Utils::isConfArkPresent()) {
                $arcCheck = 'Warn';
            } else {
                $arcCheck = 'Fail';
            }
        }
        ?>
        <div id="tabs-1">
            <table class="s1-archive-local">
                <tr>
                    <td colspan="2"><div class="hdr-sub3">Site Details</div></td>
                </tr>
                <tr>
                    <td>Site:</td>
                    <td><?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->blogname); ?> </td>
                </tr>
                <tr>
                    <td>Notes:</td>
                    <td><?php echo strlen($GLOBALS['DUPX_AC']->package_notes) ? "{$GLOBALS['DUPX_AC']->package_notes}" : " - no notes - "; ?></td>
                </tr>
                <?php if ($GLOBALS['DUPX_AC']->exportOnlyDB) : ?>
                    <tr>
                        <td>Mode:</td>
                        <td>Archive only database was enabled during package package creation.</td>
                    </tr>
                <?php endif; ?>
            </table>

            <table class="s1-archive-local">
                <tr>
                    <td colspan="2"><div class="hdr-sub3">File Details</div></td>
                </tr>
                <tr>
                    <td>Size:</td>
                    <td><?php echo DUPX_U::readableByteSize(DUPX_Conf_Utils::archiveSize()); ?> </td>
                </tr>
                <tr>
                    <td>Path:</td>
                    <td><?php echo DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW); ?> </td>
                </tr>
                <tr>
                    <td>Archive:</td>
                    <td><?php echo DUPX_ArchiveConfig::getInstance()->package_name; ?> </td>
                </tr>
                <tr>
                    <td style="vertical-align:top">Status:</td>
                    <td>
                        <?php if ($arcCheck == 'Fail' || $arcCheck == 'Warn') : ?>
                            <span class="dupx-fail" style="font-style:italic">
                                <?php
                                if ($arcCheck == 'Warn') {
                                    ?>
                                    The archive file named above must be the <u>exact</u> name of the archive file placed in the root path (character for character). But you can proceed with choosing Manual Archive Extraction.
                                    <?php
                                } else {
                                    ?>
                                    The archive file named above must be the <u>exact</u> name of the archive file placed in the root path (character for character).
                                    When downloading the package files make sure both files are from the same package line.  <br/><br/>

                                    If the contents of the archive were manually transferred to this location without the archive file then simply create a temp file named with
                                    the exact name shown above and place the file in the same directory as the installer.php file.  The temp file will not need to contain any data.
                                    Afterward, refresh this page and continue with the install process.
                                    <?php
                                }
                                ?>
                            </span>
                        <?php else : ?>
                            <span class="dupx-pass">Archive file successfully detected.</span>                                
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    protected static function managedInfoTab()
    {
        $hostManager = DUPX_Custom_Host_Manager::getInstance();
        if (($identifier  = $hostManager->isManaged()) === false) {
            return;
        }
        $hostObj = $hostManager->getHosting($identifier);
        ?>
        <div id="tabs-2">
            <h3><b><?php echo $hostObj->getLabel(); ?></b> managed hosting detected</h3>
            <p>
                The installation is occurring on a WordPress managed host. Managed hosts are more restrictive than standard shared hosts so some installer settings cannot be changed. 
                These settings include new path, new URL, database connection data, and wp-config settings.
            </p>
        </div>
        <?php
    }

    protected static function restorBackupInfoTab()
    {
        if (DUPX_InstallerState::getInstance()->getMode() !== DUPX_InstallerState::MODE_BK_RESTORE) {
            return;
        }
        ?>
        <div id="tabs-3">
            <h3>Restore backup installation</h3>
            <p>
                By running this installation all the site data will be lost and the current backup restored.
                If you do not wish to continue it is still possible to close this window to interrupt the restore.
                <br><br>
                <b>Continuing, it will no longer be possible to go back.</b>
            </p>
        </div>
        <?php
    }

    public static function generalOptions()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $paramsManager  = DUPX_Paramas_Manager::getInstance();
        ?>
        <div class="hdr-sub3">Processing</div>  

        <div class="dupx-opts dupx-advopts">
            <?php
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SAFE_MODE);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_FILE_TIME);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_LOGGING);
            if (!DupProSnapLibOSU::isWindows()) {
                ?>
                <div class="param-wrapper" >
                    <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS); ?>
                    &nbsp;
                    <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE); ?>
                </div>
                <div class="param-wrapper" >
                    <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS); ?>
                    &nbsp;
                    <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE); ?>
                </div>
                <?php
            }
            if (!$archive_config->isZipArchive()) {
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CLIENT_KICKOFF);
            }
            if (!$GLOBALS['DUPX_AC']->exportOnlyDB) {
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT);
            }
            ?>
        </div>
        <?php
    }

    public static function configurationFilesOptions()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        ?>
        <div class="hdr-sub3">Configuration files</div>  

        <div class="dupx-opts dupx-advopts">
            <?php
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONFIG);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG);
            ?>
        </div>
        <?php
    }

    public static function multisiteOptions()
    {
        if (!DUPX_Conf_Utils::showMultisite()) {
            return;
        }
        ?>
        <br/><br/>
        <div class="hdr-sub3">Multisite options</div>
        <div class="dupx-opts dupx-advopts">
            <?php
            $paramsManager = DUPX_Paramas_Manager::getInstance();
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE);
            $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
            ?>
        </div>
        <?php
    }

    public static function acceptAndContinue()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        include ('view.s1.terms.php');
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND);
    }
}