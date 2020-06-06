<?php defined("ABSPATH") or die(""); ?>
<!-- ==============================
STORED DATA -->
<div class="dup-box">
	<div class="dup-box-title">
		<i class="fas fa-th-list fa-sm"></i>
		<?php DUP_PRO_U::esc_html_e("Stored Data"); ?>
		<div class="dup-box-arrow"></div>
	</div>
	<div class="dup-box-panel" id="dup-settings-diag-opts-panel" style="padding:0px 20px 0px 25px; <?php echo esc_attr($ui_css_opts_panel) ?>" >
		<h3 class="title" style="margin-left:-15px"><?php DUP_PRO_U::esc_html_e("Data Cleanup") ?> </h3>
		<table class="dpro-reset-opts">
			<tr valign="top">
				<td>
					<button type="button" class="dpro-store-fixed-btn button button-small" id="dpro-remove-installer-files-btn" onclick="DupPro.Tools.removeInstallerFiles()">
						<?php DUP_PRO_U::esc_html_e("Remove Installation Files"); ?>
					</button>
				</td>
				<td>
					<?php DUP_PRO_U::esc_html_e("Removes all reserved installation files."); ?>
					<a href="javascript:void(0)" onclick="jQuery('#dpro-tools-delete-moreinfo').toggle()">[<?php DUP_PRO_U::esc_html_e("more info"); ?>]</a>
					<br/>
					<div id="dpro-tools-delete-moreinfo">
						<?php
							DUP_PRO_U::esc_html_e("Clicking on the 'Remove Installation Files' button will remove the following installation files.  These files are typically from a previous Duplicator install. "
									. "If you are unsure of the source, please validate the files.  These files should never be left on production systems for security reasons.  "
									. "Below is a list of all the installation files used by Duplicator.  Please be sure these are removed from your server.");
							echo "<br/><br/>";

							$installer_files = array_keys($installer_files);
							echo '<div class="success">'.implode('</div><div class="success">', $installer_files).'</div>';
							?>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<button type="button" class="dpro-store-fixed-btn button button-small" onclick="DupPro.Tools.removeOrphans()">
						<?php DUP_PRO_U::esc_html_e("Delete Package Orphans"); ?>
					</button>
				</td>
				<td>
					<?php DUP_PRO_U::esc_html_e("Removes all package files NOT found in the packages screen."); ?>
					<a href="javascript:void(0)" onclick="jQuery('#dpro-tools-delete-orphans-moreinfo').toggle()">[<?php DUP_PRO_U::esc_html_e("more info"); ?>]</a>
					<br/>
					<div id="dpro-tools-delete-orphans-moreinfo">
						<?php
							if (count($orphaned_filepaths) > 0) {
								DUP_PRO_U::esc_html_e("Clicking on the 'Delete Package Orphans' button will remove the following files.  "
									."Orphaned files are typically generated from previous installations of Duplicator. They may also exist if they did not get properly removed "
									."when they were selected from the main packages screen.  The files below are no longer associated with active packages in the main "
									."Packages screen and should be safe to remove. <b>IMPORTANT: Don't click button if you want to retain any of the following files:</b>");
								echo "<br/><br/>";

								foreach ($orphaned_filepaths as $filepath) {
									echo "<div class='failed'><i class='fa fa-exclamation-triangle'></i> ".esc_html($filepath)." </div>";
								}
							} else {
								DUP_PRO_U::esc_html_e('No orphaned package files found.');
							}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<button type="button" class="dpro-store-fixed-btn button button-small" onclick="DupPro.Tools.ClearBuildCache()">
						<?php DUP_PRO_U::esc_html_e("Clear Build Cache"); ?>
					</button>
				</td>
				<td><?php DUP_PRO_U::esc_html_e('Removes all build data from:'); ?> [<?php echo esc_html(DUPLICATOR_PRO_SSDIR_PATH_TMP);?>].</td>
			</tr>
		</table>
		<br/>

		<h3 class="title" style="margin-left:-15px"><?php DUP_PRO_U::esc_html_e("Options Values") ?> </h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php DUP_PRO_U::esc_html_e("Key") ?> <i>duplicator_pro_</i></th>
					<th>&nbsp; <?php DUP_PRO_U::esc_html_e("Value") ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sql = "SELECT * FROM `{$wpdb->base_prefix}options` WHERE  `option_name` LIKE  '%duplicator_pro_%' ORDER BY option_name";
				/* @var $global DUP_PRO_Global_Entity */
				$global = DUP_PRO_Global_Entity::get_instance();

				foreach ($wpdb->get_results("{$sql}") as $key => $row) :
								if(($global->license_key_visible) || ($row->option_name != 'duplicator_pro_license_key'))
								{
				?>
					<tr>
						<td>
							<?php
								$key_name = str_replace('duplicator_pro_', '', $row->option_name);

							echo (in_array($row->option_name, $GLOBALS['DUPLICATOR_PRO_OPTS_DELETE']))
									? "<a href='javascript:void(0)' onclick='DupPro.Settings.DeleteOption(this)'>".esc_html($key_name)."</a>"
									: esc_html($key_name);
							?>
						</td>
						<td><textarea class="dup-opts-read" readonly="readonly"><?php echo esc_textarea($row->option_value);?></textarea></td>
					</tr>
								<?php }
				endforeach; ?>
			</tbody>
		</table>
		<br/>
	</div>
</div>
<br/>