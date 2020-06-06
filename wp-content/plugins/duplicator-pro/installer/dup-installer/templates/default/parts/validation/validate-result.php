<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$archiveConfig = DUPX_ArchiveConfig::getInstance();
?>
<!-- REQUIREMENTS -->
<div class="s1-reqs" id="s1-reqs-all">
    <div class="header">
        <table class="s1-checks-area">
            <tr>
                <td class="title">Requirements <small>(must pass)</small></td>
                <td class="toggle"><a href="javascript:void(0)" onclick="DUPX.toggleAll('#s1-reqs-all')">[toggle]</a></td>
            </tr>
        </table>
    </div>

    <!-- REQ 10 -->
    <span class="status-badge <?php echo ($req['10'] == 'Pass') ? 'pass' : 'fail' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-reqs10"><i class="fa fa-caret-right"></i> Permissions</div>
    <div class="info" id="s1-reqs10">
        <table>
            <tr>
                <td><b>Deployment Path:</b> </td>
                <td><i><?php echo DUPX_U::esc_html($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?></i> </td>
            </tr>
            <tr>
                <td><b>Suhosin Extension:</b> </td>
                <td><?php echo extension_loaded('suhosin') ? "<i class='dupx-fail'>Enabled</i>" : "<i class='dupx-pass'>Disabled</i>"; ?> </td>
            </tr>
            <tr>
                <td><b>PHP Safe Mode:</b> </td>
                <td><?php echo (DUPX_Server::phpSafeModeOn()) ? "<i class='dupx-fail'>Enabled</i>" : "<i class='dupx-pass'>Disabled</i>"; ?> </td>
            </tr>
            <?php
            if (!empty($ret_is_dir_writable['failedObjects'])) {
                ?>
                <tr>
                    <td colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>Overwrite fails for these folders or files (change permissions or remove then restart):</b><br/>
                        <ul style="color:maroon; word-break: break-word; margin: 0 0 0 0; padding: 4px 0 0 15px; line-height: 1.7em;">
                            <?php
                            echo '<li>'.implode('</li><li>', $ret_is_dir_writable['failedObjects']).'</li>';
                            ?>
                        </ul>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>                     

        <br/>
        The deployment path must be writable by PHP in order to extract the archive file.  Incorrect permissions and extension such as
        <a href="https://suhosin.org/stories/index.html" target="_blank">suhosin</a> can interfere with PHP's ability to write/extract files.
        Please see the <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-055-q" target="_blank">FAQ permission</a> help link for details.
        PHP with <a href='http://php.net/manual/en/features.safe-mode.php' target='_blank'>safe mode</a> should be disabled.  If Safe Mode is enabled then
        contact your hosting provider or server administrator to disable PHP safe mode.
    </div>
    <!-- REQ 20 -->
    <span class="status-badge <?php echo ($req['20'] == 'Pass') ? 'pass' : 'fail' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-reqs20"><i class="fa fa-caret-right"></i> PHP Mysqli</div>
    <div class="info" id="s1-reqs20">
        Support for the PHP <a href='http://us2.php.net/manual/en/mysqli.installation.php' target='_blank'>mysqli extension</a> is required.
        Please contact your hosting provider or server administrator to enable the mysqli extension.  <i>The detection for this call uses
            the function_exists('mysqli_connect') call.</i>
    </div>

    <!-- REQ 50 -->
    <span class="status-badge <?php echo ($req['50'] == 'Pass') ? 'pass' : 'fail' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-reqs50"><i class="fa fa-caret-right"></i> Install Package</div>
    <div class="info" id="s1-reqs50">
        The package can be installed. Sometimes restore only package can be installed on different server.            
    </div>

    <!-- REQ 60 -->
    <?php if ($req['60'] === 'Fail') { ?>
        <span class="status-badge <?php echo ($req['60'] == 'Pass') ? 'pass' : 'fail' ?>"></span>
        <div class="title" data-type="toggle" data-target="#s1-reqs60"><i class="fa fa-caret-right"></i> Managed hosting not supported</div>
        <div class="info" id="s1-reqs60">
            Managed hosting <?php echo DUPX_u::esc_html($managedHosting); ?> detected.    
            This managed hosting isn't supported yet. 
        </div> 
    <?php } ?>

</div><br/>

<!-- ====================================
NOTICES  -->
<div class="s1-reqs" id="s1-notice-all">
    <div class="header">
        <table class="s1-checks-area">
            <tr>
                <td class="title">Notices <small>(optional)</small></td>
                <td class="toggle"><a href="javascript:void(0)" onclick="DUPX.toggleAll('#s1-notice-all')">[toggle]</a></td>
            </tr>
        </table>
    </div>

    <!-- NOTICE 10: OVERWRITE INSTALL -->
    <?php if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL && DUPX_Server::isWordPress()) : ?>
        <span class="status-badge warn"></span>
        <div class="title" data-type="toggle" data-target="#s1-notice10"><i class="fa fa-caret-right"></i> Overwrite Install</div>
        <div class="info" id="s1-notice10">
            <b>Deployment Path:</b> <i><?php echo DUPX_U::esc_html($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?></i>
            <br/><br/>

            Duplicator is in "Overwrite Install" mode because it has detected an existing WordPress site at the deployment path above.  This mode allows for the installer
            to be dropped directly into an existing WordPress site and overwrite its contents.   Any content inside of the archive file
            will <u>overwrite</u> the contents from the deployment path.  To continue choose one of these options:

            <ol>
                <li>Ignore this notice and continue with the install if you want to overwrite this sites files.</li>
                <li>Move this installer and archive to another empty directory path to keep this sites files.</li>
            </ol>

            <small style="color:maroon">
                <b>Notice:</b> Existing content such as plugin/themes/images will still show-up after the install is complete if they did not already exist in
                the archive file. For example if you have an SEO plugin in the current site but that same SEO plugin <u>does not exist</u> in the archive file
                then that plugin will display as a disabled plugin after the install is completed. The same concept with themes and images applies.  This will
                not impact the sites operation, and the behavior is expected.
            </small>
            <br/><br/>


            <small style="color:#025d02">
                <b>Recommendation:</b> It is recommended you only overwrite WordPress sites that have a minimal	setup (plugins/themes).  Typically a fresh install or a
                cPanel 'one click' install is the best baseline to work from when using this mode but is not required.
            </small>
        </div>

        <!-- NOTICE 20: ARCHIVE EXTRACTED -->
    <?php elseif (DUPX_Conf_Utils::isManualExtractFilePresent()) : ?>
        <span class="status-badge warn"></span>
        <div class="title" data-type="toggle" data-target="#s1-notice20"><i class="fa fa-caret-right"></i> Archive Extracted</div>
        <div class="info" id="s1-notice20">
            <b>Deployment Path:</b> <i><?php echo DUPX_U::esc_html($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?></i>
            <br/><br/>

            The installer has detected that the archive file has been extracted to the deployment path above.  To continue choose one of these options:

            <ol>
                <li>Skip the extraction process by <a href="javascript:void(0)" onclick="DUPX.getManaualArchiveOpt()">[enabling manual archive extraction]</a> </li>
                <li>Ignore this message and continue with the install process to re-extract the archive file.</li>
            </ol>

            <small>Note: This test looks for a file named <i>dup-manual-extract__[HASH]</i> in the <?php echo DUPX_U::esc_html(DUPX_INIT); ?> directory.  If the file exists then this notice is shown.
                The <i>dup-manual-extract__[HASH]</i> file is created with every archive and removed once the install is complete.  For more details on this process see the
                <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q" target="_blank">manual extraction FAQ</a>.</small>
        </div>
    <?php endif; ?>

    <!-- NOTICE 25: DATABASE ONLY -->
    <?php if ($archiveConfig->exportOnlyDB && !DUPX_Server::isWordPress()) : ?>
        <span class="status-badge warn"></span>
        <div class="title" data-type="toggle" data-target="#s1-notice25"><i class="fa fa-caret-right"></i> Database Only</div>
        <div class="info" id="s1-notice25">
            <b>Deployment Path:</b> <i><?php echo DUPX_U::esc_html($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?></i>
            <br/><br/>

            The installer has detected that a WordPress site does not exist at the deployment path above. This installer is currently in 'Database Only' mode because that is
            how the archive was created.  If core WordPress site files do not exist at the path above then they will need to be placed there in order for a WordPress site
            to properly work.  To continue choose one of these options:

            <ol>
                <li>Place this installer and archive at a path where core WordPress files already exist to hide this message. </li>
                <li>Create a new package that includes both the database and the core WordPress files.</li>
                <li>Ignore this message and install only the database (for advanced users only).</li>
            </ol>

            <small>Note: This test simply looks for the directories <?php echo DUPX_Server::$wpCoreDirsList; ?> and a wp-config.php file.  If they are not found in the
                deployment path above then this notice is shown.</small>

        </div>
    <?php endif; ?>


    <!-- NOTICE 30 -->
    <span class="status-badge <?php echo ($notice['30'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice30"><i class="fa fa-caret-right"></i> Package Age</div>
    <div class="info" id="s1-notice30">
        This package is <?php echo "{$fulldays}"; ?> day(s) old. Packages older than 180 days might be considered stale.  It is recommended to build a new
        package unless your aware of the content and its data.  This is message is simply a recommendation.
    </div>

    <!-- NOTICE 45 -->
    <span class="status-badge <?php echo ($notice['45'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice45"><i class="fa fa-caret-right"></i> PHP Version Mismatch</div>
    <div class="info" id="s1-notice45">
        <?php
        $cssStyle = $notice['45'] == 'Good' ? 'color:green' : 'color:red';
        echo "<b style='{$cssStyle}'>You are migrating site from PHP ".$archiveConfig->version_php." to PHP ".phpversion()."</b>.<br/>"
        ."If the PHP version of your website is different than the PHP version of your package 
                it MAY cause problems with the functioning of your website.<br/>";
        ?>
    </div>

    <!-- NOTICE 50 -->
    <span class="status-badge <?php echo ($notice['50'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice50"><i class="fa fa-caret-right"></i> PHP Open Base</div>
    <div class="info" id="s1-notice50">
        <b>Open BaseDir:</b> <i><?php echo $notice['50'] == 'Good' ? "<i class='dupx-pass'>Disabled</i>" : "<i class='dupx-fail'>Enabled</i>"; ?></i>
        <br/><br/>

        If <a href="http://php.net/manual/en/ini.core.php#ini.open-basedir" target="_blank">open_basedir</a> is enabled and you're
        having issues getting your site to install properly please work with your host and follow these steps to prevent issues:
        <ol style="margin:7px; line-height:19px">
            <li>Disable the open_basedir setting in the php.ini file</li>
            <li>If the host will not disable, then add the path below to the open_basedir setting in the php.ini<br/>
                <i style="color:maroon">"<?php echo str_replace('\\', '/', dirname(__FILE__)); ?>"</i>
            </li>
            <li>Save the settings and restart the web server</li>
        </ol>
        Note: This warning will still show if you choose option #2 and open_basedir is enabled, but should allow the installer to run properly.  Please work with your
        hosting provider or server administrator to set this up correctly.
    </div>

    <!-- NOTICE 60 -->
    <span class="status-badge <?php echo ($notice['60'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice60"><i class="fa fa-caret-right"></i> PHP Timeout</div>
    <div class="info" id="s1-notice60">
        <b>Archive Size:</b> <?php echo DUPX_U::readableByteSize(DUPX_Conf_Utils::archiveSize()) ?>  <small>(detection limit is set at <?php echo DUPX_U::readableByteSize($max_time_size) ?>) </small><br/>
        <b>PHP max_execution_time:</b> <?php echo "{$max_time_ini}"; ?> <small>(zero means not limit)</small> <br/>
        <b>PHP set_time_limit:</b> <?php echo ($max_time_zero) ? '<i style="color:green">Success</i>' : '<i style="color:maroon">Failed</i>' ?>
        <br/><br/>

        The PHP <a href="http://php.net/manual/en/info.configuration.php#ini.max-execution-time" target="_blank">max_execution_time</a> setting is used to
        determine how long a PHP process is allowed to run.  If the setting is too small and the archive file size is too large then PHP may not have enough
        time to finish running before the process is killed causing a timeout.
        <br/><br/>

        Duplicator Pro attempts to turn off the timeout by using the
        <a href="http://php.net/manual/en/function.set-time-limit.php" target="_blank">set_time_limit</a> setting.   If this notice shows as a warning then it is
        still safe to continue with the install.  However, if a timeout occurs then you will need to consider working with the max_execution_time setting or extracting the
        archive file using the 'Manual Archive Extraction' method.
        Please see the	<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-100-q" target="_blank">FAQ timeout</a> help link for more details.
    </div>

    <!-- NOTICE 70 -->
    <span class="status-badge <?php echo ($notice['70'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice08"><i class="fa fa-caret-right"></i> Wordfence</div>
    <div class="info" id="s1-notice08">
        <?php if ($parent_has_wordfence): ?>
            You are installing in a subdirectory of another site that has Wordfence installed.
            Temporarily deactivate Wordfence on the parent site before continuing with the install.
        <?php else: ?>
            Having Wordfence in a parent site can interfere with the install, however no such condition was detected.
        <?php endif; ?>
    </div>

    <!-- NOTICE 80 -->
    <span class="status-badge <?php echo ($notice['80'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice80"><i class="fa fa-caret-right"></i> wp-config.php File Location</div>
    <div class="info" id="s1-notice80">
        When this item shows a warning, it indicates the wp-config.php file was detected in the directory above the WordPress root folder on the source site. 
        <br/><br/>
        The Duplicator Installer will place the wp-config.php file in the root folder of the WordPress installation. This will not affect operation of the site.
    </div>

    <!-- NOTICE 90 -->
    <span class="status-badge <?php echo ($notice['90'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice90"><i class="fa fa-caret-right"></i> wp-content Directory Location</div>
    <div class="info" id="s1-notice90">
        When this item shows a warning, it indicates the wp-content directory was not in the WordPress root folder on the source site.
        <br/><br/>
        The Duplicator Installer will place the wp-content directory in the WordPress root folder of the WordPress installation. This will not affect operation of the site.
    </div>

    <!-- NOTICE 100 -->
    <span class="status-badge <?php echo ($notice['100'] == 'Good') ? 'good' : 'warn' ?>"></span>
    <div class="title" data-type="toggle" data-target="#s1-notice100"><i class="fa fa-caret-right"></i> Sufficient Disk Space</div>
    <div class="info" id="s1-notice100">
        <?php
        echo ($notice['100'] == 'Good') ? 'You have sufficient disk space in your machine to extract the archive.' : 'You don’t have sufficient disk space in your machine to extract the archive. Ask your host to increase disk space.'
        ?>
    </div>

    <?php if (($managedHosting = DUPX_Custom_Host_Manager::getInstance()->isManaged()) !== false) { ?>
        <span class="status-badge <?php echo ($notice['110'] == 'Good') ? 'good' : 'warn' ?>"></span>
        <div class="title" data-type="toggle" data-target="#s1-notice110"><i class="fa fa-caret-right"></i> Table prefix of managed hosting</div>
        <div class="info" id="s1-notice110">
            <?php if ($notice['110'] == 'Good') { ?>
                The prefix of the existing WordPress configuration table is equal of the prefix of the table of the source site where the package was created.
            <?php } else { ?>
                The prefix of the existing WordPress configuration table does not match the prefix of the table of the source site where the package was created, so the prefix will be changed to the managed hosting prefix.
            <?php } ?>
        </div>
    <?php } ?>
</div>
