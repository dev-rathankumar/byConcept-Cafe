<?php defined("ABSPATH") or die(""); ?>
<form id="dup-settings-form" action="<?php echo self_admin_url('admin.php?page=duplicator-pro-tools&tab=diagnostics'); ?>" method="post">
    <?php wp_nonce_field('duplicator_pro_settings_page'); ?>
    <input type="hidden" id="dup-settings-form-action" name="action" value="">
    <?php if (!empty($diagnosticAction)) : ?>
        <div id="message" class="notice notice-success is-dismissible dpro-diagnostic-action-<?php echo $diagnosticAction; ?>"><p><?php echo $action_response; ?></p>
            <?php if ($_REQUEST['action'] != 'display') : ?>
                <?php
                if ($_REQUEST['action'] == 'installer') :

                    $remove_error = false;

                    delete_option("duplicator_pro_exe_safe_mode");
                    // Move installer log before cleanup
                    DUP_PRO_U::initStorageDirectory();
                    $installer_log_path = DUPLICATOR_PRO_ENHANCED_INSTALLER_DIRECTORY.'/dup-installer-log__'.DUPLICATOR_PRO_INSTALLER_HASH_PATTERN.'.txt';
                    $log_files_moved = array();
                    $glob_files         = glob($installer_log_path);
                    if (!empty($glob_files)) {
                        foreach ($glob_files as $glob_file) {
                            $installer_log_file_path = $glob_file;
                            DUP_PRO_IO::copyFile($installer_log_file_path, DUPLICATOR_PRO_SSDIR_PATH_INSTALLER);
                            $log_files_moved[] = basename($installer_log_file_path);
                        }
                    }
                    $installer_files = DUP_PRO_Server::getInstallerFiles();

                    $html               = "";
                    $removed_files      = false;
                    $originalFileFolder = false;
                    foreach ($installer_files as $filename => $path) {
                        $file_path = '';
                        if (false !== stripos($filename, '[hash]')) {
                            $glob_files = glob($path);
                            if (!empty($glob_files)) {
                                foreach ($glob_files as $glob_file) {
                                    $file_path     = $glob_file;
                                    is_dir($file_path) ? DUP_PRO_IO::deleteTree($file_path) : DUP_PRO_IO::deleteFile($file_path);
                                    $removed_files = true;
                                }
                            }
                        } else if (is_file($path)) {
                            $file_path     = $path;
                            DUP_PRO_IO::deleteFile($path);
                            $removed_files = true;
                        } else if (is_dir($path)) {
                            $file_path = $path;
                            // Extra protection to ensure we only are deleting the installer directory
                            if (DUP_PRO_STR::contains($path, 'dup-installer')) {
                                //Moving original files folder before deleting dup-installer folder
                                $orig_files__folder_paths = glob($path.'/'.DupProSnapLibOrigFileManager::ORIG_FOLDER_PREFIX.DUPLICATOR_PRO_INSTALLER_HASH_PATTERN, GLOB_ONLYDIR);

                                if (!empty($orig_files__folder_paths)) {
                                    $folderName         = basename($orig_files__folder_paths[0]);
                                    $newPath            = DUPLICATOR_PRO_SSDIR_PATH_INSTALLER.'/'.$folderName;
                                    DupProSnapLibIOU::rename($orig_files__folder_paths[0], $newPath, true);
                                    $originalFileFolder = basename($newPath);
                                }

                                //Delete dup-installer folder
                                DUP_PRO_IO::deleteTree($path, false);
                                $removed_files = true;
                            } else {
                                DUP_PRO_LOG::trace("Attempted to delete $path but it isn't the dup-installer directory!");
                            }
                        }

                        if (!empty($file_path)) {
                            if (file_exists($file_path)) {
                                echo "<div class='failed'><i class='fa fa-exclamation-triangle'></i> {$txt_found} - ".esc_html($file_path)."  </div>";
                                $remove_error = true;
                            } else {
                                echo "<div class='success'> <i class='fa fa-check'></i> {$txt_not_found} - ".esc_html($file_path)."	</div>";
                            }
                        }
                    }
                    
                    $html .= '<br>';

                    if ($originalFileFolder != false) {
                        $html .= "<div class='success'> <i class='fa fa-check'></i> ".
                            DUP_PRO_U::__('Original files folder moved in installer backup directory')." - ".esc_html($originalFileFolder).
                            "</div>";
                    }
                    
                    if (!empty($log_files_moved)) {
                        foreach ($log_files_moved as $log_moved) {
                            $html .= "<div class='success'> <i class='fa fa-check'></i> ".
                                DUP_PRO_U::__('Installer log moved in installer backup directory').
                                " - ".esc_html($log_moved)."</div>";
                        }
                    }

                    //Long Installer Check
                    if (!empty($long_installer_path) && $long_installer_path != $installer_files['installer.php']) {
                        $path_parts = pathinfo($long_installer_path);
                        $path_parts = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';
                        if ($path_parts == "php" && !is_dir($long_installer_path) && file_exists($long_installer_path)) {
                            $removed_files = true;
                            @unlink($long_installer_path);
                            $html          .= (file_exists($long_installer_path)) ? "<div class='failed'><i class='fa fa-exclamation-triangle'></i> {$txt_found} - {$long_installer_path}  </div>" : "<div class='success'> <i class='fa fa-check'></i> {$txt_not_found} - {$long_installer_path}	</div>";
                        }
                    }

                    if (!$removed_files) {
                        echo '<div><strong>'.DUP_PRO_U::__('No Duplicator files were found on this WordPress Site.').'</strong></div>';
                    }

                    echo $html;
                    ?>

                    <div style="font-style: italic; max-width:900px; padding:10px 0 25px 0;">
                        <?php
                        echo '<b><i class="fa fa-shield-alt"></i> '.DUP_PRO_U::esc_html__('Security Notes').':</b>';
                        echo DUP_PRO_U::__(' If the installer files do not successfully get removed with this action, then they WILL need to be removed manually through your hosts control panel  '
                            .'or FTP.  Please remove all installer files to avoid any security issues on this site.').'<br>';
                        echo DUP_PRO_U::__('For more details please visit '
                            .'the FAQ link <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-295-q" target="_blank">Which files need to be removed after an install?</a>');
                        echo '<br/><br/>';

                        if ($remove_error) {
                            echo DUP_PRO_U::__('Some of the installer files did not get removed, ').
                            '<a href="#" onclick="DupPro.Tools.removeInstallerFiles(); return false;" >'.
                            DUP_PRO_U::__('please retry the installer cleanup process').
                            '</a>.'.
                            DUP_PRO_U::__(' If this process continues please see the previous FAQ link.').
                            '<br><br>';
                        }
                        ?>
                        <!--<b><?php // DUP_PRO_U::esc_html_e('Archive File')       ?>:</b> -->
                        <?php
                        //DUP_PRO_U::esc_html_e("The archive file has a unique hashed name when downloaded.  Leaving the archive file on your server does not impose a security"
                        //	. " risk if the file was not renamed.  It is still highly recommended to remove the archive file after install, especially if it was renamed.");
                        //echo '<br/><br/>';

                        echo '<b><i class="fa fa-thumbs-up"></i> '.DUP_PRO_U::esc_html__('Help Support Duplicator').':</b>&nbsp;';
                        echo DUP_PRO_U::__('The Duplicator team has worked many years to make moving a WordPress site a much easier process.  Show your support with a '
                            .'<a href="https://wordpress.org/support/plugin/duplicator/reviews/?filter=5" target="_blank">5 star review</a>!  We would be thrilled if you could!');
                        ?>
                    </div>

                <?php elseif ($_REQUEST['action'] == 'purge-orphans') : ?>
                    <?php
                    $html = "";

                    foreach ($orphaned_filepaths as $filepath) {
                        @unlink($filepath);
                        echo (file_exists($filepath)) ? "<div class='failed'><i class='fa fa-exclamation-triangle'></i> {$filepath}  </div>" : "<div class='success'> <i class='fa fa-check'></i> {$filepath} </div>";
                    }

                    echo $html;
                    $orphaned_filepaths = DUP_PRO_Server::getOrphanedPackageFiles();
                    ?>
                    <br/>

                    <i><?php DUP_PRO_U::esc_html_e('If any orphaned files didn\'t get removed then delete them manually') ?>. <br/><br/></i>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    <?php endif; ?>



    <?php
    if (isset($_GET['sm'])) {

        $safe_title = DUP_PRO_U::__('This site has been successfully migrated!');
        $safe_msg   = DUP_PRO_U::__('Please test the entire site to validate the migration process!');

        switch ($_GET['sm']) {

            //safe_mode basic
            case 1:
                $safe_msg = DUP_PRO_U::__('NOTICE: Safe mode (Basic) was enabled during install, be sure to re-enable all your plugins.');
                break;

            //safe_mode advance
            case 2:
                $safe_msg = DUP_PRO_U::__('NOTICE: Safe mode (Advanced) was enabled during install, be sure to re-enable all your plugins.');

                $temp_theme       = null;
                $active_theme     = wp_get_theme();
                $available_themes = wp_get_themes();
                foreach ($available_themes as $theme) {
                    if ($temp_theme == null && $theme->stylesheet != $active_theme->stylesheet) {
                        $temp_theme = array('stylesheet' => $theme->stylesheet, 'template' => $theme->template);
                        break;
                    }
                }

                if ($temp_theme != null) {
                    //switch to another theme then backto default
                    switch_theme($temp_theme['template'], $temp_theme['stylesheet']);
                    switch_theme($active_theme->template, $active_theme->stylesheet);
                }

                break;
        }


        if (!DUP_PRO_Server::hasInstallFiles()) {
            echo "<div class='notice notice-success is-dismissible cleanup-notice'><p><b class='title'><i class='fa fa-check-circle'></i> {$safe_title}</b> "
            ."<div class='notice-safemode'>{$safe_msg}</p></div></div>";
        }
    }

    include_once 'inc.data.php';
    include_once 'inc.settings.php';
    include_once 'inc.validator.php';
    include_once 'inc.phpinfo.php';
    ?>

</form>
<?php
$confirm1               = new DUP_PRO_UI_Dialog();
$confirm1->title        = DUP_PRO_U::__('Are you sure you want to delete?');
$confirm1->message      = DUP_PRO_U::__('Delete this option value.');
$confirm1->progressText = DUP_PRO_U::__('Removing, Please Wait...');
$confirm1->jsCallback   = 'DupPro.Settings.DeleteThisOption(this)';
$confirm1->initConfirm();

$confirm2               = new DUP_PRO_UI_Dialog();
$confirm2->title        = DUP_PRO_U::__('Do you want to Continue?');
$confirm2->message      = DUP_PRO_U::__('This will run the scan validation check. This may take several minutes.');
$confirm2->progressText = DUP_PRO_U::__('Please Wait...');
$confirm2->jsCallback   = 'DupPro.Tools.RecursionRun()';
$confirm2->initConfirm();


$confirm3               = new DUP_PRO_UI_Dialog();
$confirm3->title        = DUP_PRO_U::__('This process will remove all build cache files.');
$confirm3->message      = DUP_PRO_U::__('Be sure no packages are currently building or else they will be cancelled.');
$confirm3->progressText = $confirm1->progressText;
$confirm3->jsCallback   = 'DupPro.Tools.ClearBuildCacheRun()';
$confirm3->initConfirm();
?>
<script>
    jQuery(document).ready(function ($) {

        DupPro.Settings.DeleteOption = function (anchor) {
            var key = $(anchor).text(),
                    text = '<?php DUP_PRO_U::esc_html_e("Delete this option value"); ?> [' + key + '] ?';
<?php $confirm1->showConfirm(); ?>
            $("#<?php echo esc_js($confirm1->getID()); ?>-confirm").attr('data-key', key);
            $("#<?php echo esc_js($confirm1->getID()); ?>_message").html(text);

        };

        DupPro.Settings.DeleteThisOption = function (e) {
            var key = $(e).attr('data-key');
            jQuery('#dup-settings-form-action').val(key);
            jQuery('#dup-settings-form').submit();
        }

        DupPro.Tools.removeOrphans = function () {
            window.location = '?page=duplicator-pro-tools&tab=diagnostics&action=purge-orphans';
        };

        DupPro.Tools.removeInstallerFiles = function () {
            window.location = '<?php echo "?page=duplicator-pro-tools&tab=diagnostics&action=installer&installer_name={$long_installer_path}"; ?>';
        };


        DupPro.Tools.ClearBuildCache = function () {
<?php $confirm3->showConfirm(); ?>
        };

        DupPro.Tools.ClearBuildCacheRun = function () {
            window.location = '?page=duplicator-pro-tools&tab=diagnostics&action=tmp-cache';
        }


        DupPro.Tools.Recursion = function ()
        {
<?php $confirm2->showConfirm(); ?>
        }

        DupPro.Tools.RecursionRun = function () {
            jQuery('#dup-settings-form-action').val('duplicator_recursion');
            jQuery('#dup-settings-form').submit();
        }

<?php
if ($scan_run) {
    echo "$('#duplicator-scan-results-1').html($('#duplicator-scan-results-2').html())";
}
?>

    });
</script>
