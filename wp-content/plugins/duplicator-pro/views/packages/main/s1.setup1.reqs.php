<?php
defined("ABSPATH") or die(""); 
$global = DUP_PRO_Global_Entity::get_instance();
?>

<style>
    /* -----------------------------
    REQUIREMENTS*/
    div.dup-sys-section {margin:1px 0px 5px 0px}
    div.dup-sys-title {display:inline-block; width:250px; padding:1px; }
    div.dup-sys-title div {display:inline-block;float:right; }
    div.dup-sys-info {display:none; max-width: 98%; margin:4px 4px 12px 4px}	
    div.dup-sys-pass {display:inline-block; color:green;}
    div.dup-sys-fail {display:inline-block; color:#AF0000;}
    div.dup-sys-contact {padding:5px 0px 0px 10px; font-size:11px; font-style:italic}
    span.dup-toggle {float:left; margin:0 2px 2px 0; }
    table.dup-sys-info-results td:first-child {width:200px}
</style>

<!-- =========================================
SYSTEM REQUIREMENTS -->
<?php if (! $dup_tests['Success']) : ?>
<div class="dup-box">
    <div class="dup-box-title">
        <i class="far fa-check-circle"></i>
        <?php
			DUP_PRO_U::esc_html_e("Requirements:");
			echo ($dup_tests['Success']) ? ' <div class="dup-sys-pass">Pass</div>' : ' <div class="dup-sys-fail">Fail</div>';
        ?>
        <div class="dup-box-arrow"></div>
    </div>
    <div class="dup-box-panel">
        <div class="dup-sys-section">
            <i><?php DUP_PRO_U::esc_html_e("System requirements must pass for the Duplicator to work properly.  Click each link for details."); ?></i>
        </div>

        <!-- PHP SUPPORT -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php DUP_PRO_U::esc_html_e('PHP Support'); ?></a>
                <div><?php echo $dup_tests['PHP']['ALL']; ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <table class="dup-sys-info-results">
                    <tr>
                        <td><?php printf("%s [%s]", DUP_PRO_U::__("PHP Version"), phpversion()); ?></td>
                        <td><?php echo $dup_tests['PHP']['VERSION'] ?></td>
                    </tr>
                    <?php if($global->archive_build_mode == DUP_PRO_Archive_Build_Mode::ZipArchive) : ?>
						<tr>
							<td><?php DUP_PRO_U::esc_html_e('ZipArchive Enabled'); ?></td>
							<td>
								<?php
									echo $dup_tests['PHP']['ZIP'];
									if ($dup_tests['PHP']['ZIP']  == 'Fail') {
										echo ' &nbsp; <i style="font-size:12px"><a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-060-q" target="_blank">'
										. DUP_PRO_U::esc_html__('Overview on how to enable ZipArchive') . '</i> </a>';
									}
								?>
							</td>
						</tr>
                    <?php endif; ?>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e('Function');?> <a href="http://php.net/manual/en/function.file-get-contents.php" target="_blank">file_get_contents</a></td>
						<td><?php echo esc_html($dup_tests['PHP']['FUNC_1']); ?></td>
					</tr>					
					<tr>
						<td><?php DUP_PRO_U::esc_html_e('Function');?> <a href="http://php.net/manual/en/function.file-put-contents.php" target="_blank">file_put_contents</a></td>
						<td><?php echo esc_html($dup_tests['PHP']['FUNC_2']); ?></td>
					</tr>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e('Function');?> <a href="http://php.net/manual/en/mbstring.installation.php" target="_blank">mb_strlen</a></td>
						<td><?php echo esc_html($dup_tests['PHP']['FUNC_3']); ?></td>
					</tr>	
                </table>
                <small>
                    <?php DUP_PRO_U::esc_html_e("PHP versions 5.2.17+ or higher is required. Please note that in versioning logic a value such as 5.2.9 is less than 5.2.17. For compression to work the ZipArchive extension for PHP is required. Safe Mode should be set to 'Off' in you php.ini file and is deprecated as of PHP 5.3.0.  For any issues in this section please contact your hosting provider or server administrator.  For additional information see our online documentation."); ?>
                </small>
            </div>
        </div>		

        <!-- PERMISSIONS -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php DUP_PRO_U::esc_html_e('Permissions'); ?></a> <div><?php echo esc_html($dup_tests['IO']['ALL']); ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <b><?php DUP_PRO_U::esc_html_e("Required Paths"); ?></b>
                <div style="padding:3px 0px 0px 15px">
                    <?php
					printf("<b>%s</b> &nbsp; [%s] <br/>", esc_html($dup_tests['IO']['WPROOT']), esc_html(DUP_PRO_Archive::getArchiveListPaths('home')));
                    printf("<b>%s</b> &nbsp; [%s] <br/>", esc_html($dup_tests['IO']['SSDIR']), esc_html(DUPLICATOR_PRO_SSDIR_PATH));
                    printf("<b>%s</b> &nbsp; [%s] <br/>", esc_html($dup_tests['IO']['SSTMP']), esc_html(DUPLICATOR_PRO_SSDIR_PATH_TMP));
                    ?>
                </div>

                <small>
                    <?php DUP_PRO_U::esc_html_e("Permissions can be difficult to resolve on some systems. If the plugin can not read the above paths here are a few things to try. 1) Set the above paths to have permissions of 755 for directories and 644 for files. You can temporarily try 777 however, be sure you don’t leave them this way. 2) Check the owner/group settings for both files and directories. The PHP script owner and the process owner are different. The script owner owns the PHP script but the process owner is the user the script is running as, thus determining its capabilities/privileges in the file system. For more details contact your host or server administrator or visit the 'Help' menu under Duplicator for additional online resources."); ?>
                </small>					
            </div>
        </div>

        <!-- SERVER SUPPORT -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php DUP_PRO_U::esc_html_e('Server Support'); ?></a>
                <div><?php echo $dup_tests['SRV']['ALL']; ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <table class="dup-sys-info-results">
                    <tr>
                        <td><?php printf("%s [%s]", DUP_PRO_U::esc_html__("MySQL Version"), esc_html(DUP_PRO_DB::getVersion())); ?></td>
                        <td><?php echo esc_html($dup_tests['SRV']['MYSQL_VER']); ?></td>
                    </tr>
                </table>
                <small>
                    <?php
                    DUP_PRO_U::esc_html_e("MySQL version 5.0+ or better is required.  Contact your server administrator and request MySQL Server 5.0+ be installed.");
                    ?>										
                </small>
            </div>
        </div>

        <!-- INSTALLATION FILES -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php DUP_PRO_U::esc_html_e('Installation Files'); ?></a> <div><?php echo esc_html($dup_tests['RES']['INSTALL']); ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <?php if ($dup_tests['RES']['INSTALL'] == 'Pass') : ?>
                    <?php 
						DUP_PRO_U::esc_html_e("No reserved installation files [{$dup_intaller_files}] where found from a previous install.  You are clear to create a new package."); 
					?>
                <?php else: ?>                     
                    <form method="post" action="admin.php?page=duplicator-pro-tools&tab=diagnostics&action=installer">
                        <?php DUP_PRO_U::esc_html_e("An installer file(s) was found in the WordPress root directory. Installer file names include [{$dup_intaller_files}].  To archive your data correctly please remove any of these files and try creating your package again."); ?>
                        <br/><input type='submit' class='button action' value='<?php DUP_PRO_U::esc_attr_e('Remove Files Now') ?>' style='font-size:10px; margin-top:5px;' />
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ONLINE SUPPORT -->
        <div class="dup-sys-contact">
            <?php
            printf("<i class='fa fa-question-circle'></i> %s <a href='admin.php?page=duplicator-pro-tools&tab=support'>[%s]</a>", 
				DUP_PRO_U::__("For additional help please see the "),
				DUP_PRO_U::__("help page"));
            ?>
        </div>

    </div>
</div>
<?php endif; ?>

<script>
//INIT
jQuery(document).ready(function ($) 
{
	DupPro.Pack.ToggleSystemDetails = function(anchor) 
	{
		$(anchor).parent().siblings('.dup-sys-info').toggle();
	}

	//Init: Toogle for system requirment detial links
	$('.dup-sys-title a').each(function () {
		$(this).attr('href', 'javascript:void(0)');
		$(this).click(function() { DupPro.Pack.ToggleSystemDetails(this); });
		$(this).prepend("<span class='ui-icon ui-icon-triangle-1-e dup-toggle' />");
	});

	//Init: Color code Pass/Fail/Warn items
	$('.dup-sys-title div').each(function () {
		$(this).addClass(($(this).text() == 'Pass') ? 'dup-sys-pass' : 'dup-sys-fail');
	});       

});
</script>