<?php
defined("ABSPATH") or die("");

DUP_PRO_U::hasCapability('manage_options');

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.package.template.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.global.entity.php');
$is_freelancer_plus = (DUP_PRO_License_U::getLicenseType() >= DUP_PRO_License_Type::Freelancer);

global $wp_version;
global $wpdb;

$nonce_action = 'duppro-template-edit';

$was_updated = false;
$package_template_id = isset($_REQUEST['package_template_id']) ? sanitize_text_field($_REQUEST['package_template_id']) : -1;
$package_templates = DUP_PRO_Package_Template_Entity::get_all(true);
$package_template_count = count($package_templates);

$view_state = DUP_PRO_UI_ViewState::getArray();
$ui_css_archive = (isset($view_state['dup-template-archive-panel']) && $view_state['dup-template-archive-panel']) ? 'display:block' : 'display:none';
$ui_css_install = (isset($view_state['dup-template-install-panel']) && $view_state['dup-template-install-panel']) ? 'display:block' : 'display:none';

if ($package_template_id == -1) {
	$package_template	 = new DUP_PRO_Package_Template_Entity();
} else {
	$package_template	 = DUP_PRO_Package_Template_Entity::get_by_id($package_template_id);
	DUP_PRO_LOG::traceObject("getting template $package_template_id", $package_template);
}

if (isset($_REQUEST['action'])) {
	DUP_PRO_U::verifyNonce($_POST['_wpnonce'], $nonce_action);
	if ($_REQUEST['action'] == 'save') {
		DUP_PRO_LOG::traceObject('request', $_REQUEST);

		// Checkboxes don't set post values when off so have to manually set these
		$package_template->set_post_variables($_REQUEST);
		$package_template->save();
		$was_updated		 = true;
	} else if ($_REQUEST['action'] == 'copy-template') {
		$source_template_id = sanitize_text_field($_REQUEST['duppro-source-template-id']);

		if ($source_template_id != -1) {
			$package_template->copy_from_source_id($source_template_id);
			$package_template->save();
		}
	}
}

/*
if (!empty($_GET['_wpnonce'])) {
	if (!wp_verify_nonce($_GET['_wpnonce'], 'edit-template')) {
		die('Security issue');
	}
}*/

$installer_pass = (base64_decode($package_template->installer_opts_secure_pass)) ? base64_decode($package_template->installer_opts_secure_pass) : '';
$installer_cpnldbaction = isset($package_template->installer_opts_cpnl_db_action) ? $package_template->installer_opts_cpnl_db_action : 'create';
$uploads = wp_upload_dir();
$upload_dir = DupProSnapLibIOU::safePath($uploads['basedir']);
$content_path = defined('WP_CONTENT_DIR') ? DupProSnapLibIOU::safePath(WP_CONTENT_DIR) : '';
?>

<style>
    table.dpro-edit-toolbar select {float:left}
	table.form-table td {padding:2px;}
	table.form-table th {padding:5px; font-weight: normal}
    div#dpro-notes-add {float:right; margin:-4px 2px 4px 0;}
    div.dpro-template-general {margin:8px 0 10px 0}
    div.dpro-template-general label {font-weight: bold}
    div.dpro-template-general input, textarea {width:100%}
	b.dpro-hdr {display:block; font-size:14px;  margin:3px 0 10px 0; padding:3px 0 3px 0; border-bottom: 1px solid #dfdfdf}
	form#dpro-template-form textarea, input[type="text"], input[type="password"] {width:100%}

	/*ARCHIVE*/
	div#dup-exportdb-items-checked, div#dup-exportdb-items-off {min-height:275px;}
	div#dup-exportdb-items-checked {padding:0 5px 5px 5px; max-width:800px}
    textarea#_archive_filter_dirs {width:100%; height:75px}
    textarea#_archive_filter_files {width:100%; height:75px}
    input#_archive_filter_exts {width:100%}
    div.dup-quick-links {font-size:11px; float:right; display:inline-block; margin-bottom:2px; font-style:italic}
	table#dup-dbtables td {padding:2px;vertical-align: top}
	ul#parsley-id-multiple-_database_filter_tables {display:none}

    /* MULTISITE */
    table.mu-mode td {padding: 10px}
    table.mu-opts td {padding: 10px}
    select.mu-selector {
        height:175px !important; 
        width:450px; 
        max-width: 450px
    }
    select.mu-selector option {
        padding: 2px 0;
    }
    button.mu-push-btn {padding: 5px; width:40px; font-size:14px}

	/*INSTALLER */
	table.dpro-install-setup {width:100%}
	table.dpro-install-setup tr{vertical-align: top}
    div.dpro-install-hdr-2 {font-weight:bold; border-bottom:1px solid #dfdfdf; padding-bottom:2px; width:100%; margin-bottom:8px}
	tr.dpro-install-hdr-2 td:first-child {font-weight:bold;}
	tr.dpro-install-hdr-2 td {border-bottom:1px solid #dfdfdf; padding-bottom:2px;}
	div.tabs-panel {max-height:350px !important}
	ul.add-menu-item-tabs li, ul.category-tabs li {padding:3px 30px 5px}
	div.secure-pass-area {display:none}
	
	label.secure-pass-lbl {display:inline-block; width:125px}
	div#dup-template-install-panel div.tabs-panel{min-height:150px}
	div#dpro-pass-toggle {position: relative; margin:8px 0 0 0; width:243px}
	input#_installer_opts_secure_pass {border-radius:4px 0 0 4px; width:217px; height: 23px; min-height: auto; margin:0; padding: 0 4px;}
	button.pass-toggle {height: 23px; width: 27px; position:absolute; top:0px; right:0px; border:1px solid silver; border-radius:0 4px 4px 0; cursor:pointer}
	span#dpro-install-secure-lock {color:#A62426; display:none; font-size:14px}
	span#dpro-install-secure-unlock {color:#A62426; display:none; font-size:14px}
</style>


<form id="dpro-template-form" data-parsley-validate data-parsley-ui-enabled="true" action="<?php echo esc_url($edit_template_url); ?>" method="post">
<?php wp_nonce_field($nonce_action); ?>
<input type="hidden" id="dpro-template-form-action" name="action" value="save">
<input type="hidden" name="package_template_id" value="<?php echo intval($package_template->id); ?>">

<!-- ====================
SUB-TABS -->
<?php if ($was_updated) : ?>
	<div class="notice notice-success is-dismissible dpro-wpnotice-box"><p><?php DUP_PRO_U::esc_html_e('Template Updated'); ?></p></div>
<?php endif; ?>

<!-- ====================
TOOL-BAR -->
<table class="dpro-edit-toolbar">
	<tr>
		<td>
			<?php if ($package_template_count > 0) :
				$general_templates = array();
				$existing_templates = array();
				foreach ($package_templates as $copy_package_template) {
					if ($copy_package_template->id != $package_template->id) {
						if ($copy_package_template->is_default || $copy_package_template->is_manual) {
							$general_templates[$copy_package_template->id] = $copy_package_template->is_manual ? DUP_PRO_U::__("Active Build Settings") : $copy_package_template->name;
						} else {
							$existing_templates[$copy_package_template->id] = $copy_package_template->name;
						}
					}
				}
				?>

				<select name="duppro-source-template-id">
					<option value="-1"><?php DUP_PRO_U::esc_html_e("Copy From");?></option>
					<?php
					if (!empty($general_templates)) {
						asort($general_templates);
					?>
						<optgroup label="<?php DUP_PRO_U::esc_attr_e("General Templates");?>">
							<?php
							foreach ($general_templates as $id => $val) {
							?>
								<option value="<?php echo esc_attr($id);?>"><?php echo esc_html($val);?></option>
							<?php
							}
							?>
						</optgroup>
					<?php
					}
					?>
					<?php
					if (!empty($existing_templates)) {
						asort($existing_templates);
					?>
						<optgroup label="<?php DUP_PRO_U::esc_attr_e("Existing Templates");?>">
							<?php
							foreach ($existing_templates as $id =>  $val) {
							?>
								<option value="<?php esc_attr($id);?>"><?php echo esc_html($val);?></option>
							<?php
							}
							?>
						</optgroup>
					<?php
					}
					?>
				</select>
				<input type="button" class="button action" value="<?php DUP_PRO_U::esc_attr_e("Apply") ?>" onclick="DupPro.Template.Copy()">
			<?php else : ?>
				<select disabled="disabled"><option value="-1" selected="selected"><?php _e("Copy From"); ?></option></select>
				<input type="button" class="button action" value="<?php DUP_PRO_U::esc_attr_e("Apply") ?>" onclick="DupPro.Template.Copy()"  disabled="disabled">
			<?php endif; ?>
		</td>
		<td>
			<div class="btnnav">
				<a href="<?php echo esc_url($templates_tab_url); ?>" class="button"><i class="far fa-clone"></i> <?php DUP_PRO_U::esc_html_e('Templates'); ?></a>
				<?php if ($package_template_id == -1) : ?>
				<a href="javascript:void(0)" class="button disabled"><?php DUP_PRO_U::esc_html_e('Add New') ?></a>
				<?php else : ?>	
					<a href="admin.php?page=duplicator-pro-tools&tab=templates&inner_page=edit&_wpnonce=<?php echo wp_create_nonce('edit-template');?>" class="button"><?php DUP_PRO_U::esc_html_e("Add New"); ?></a>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>
<hr class="dpro-edit-toolbar-divider"/>

<div class="dpro-template-general">
	<label><?php _e("Template Name"); ?>:</label>

	<input type="text" id="template-name" name="name" data-parsley-errors-container="#template_name_error_container" data-parsley-required="true" value="<?php echo esc_attr($package_template->name); ?>" autocomplete="off">
	<div id="template_name_error_container" class="duplicator-error-container"></div>

	<label><?php _e("Notes"); ?>:</label> <br/>
	<textarea id="template-notes" name="notes" style="height:50px"><?php echo esc_textarea($package_template->notes);?></textarea>
</div>

<!-- ===============================
ARCHIVE -->
<div class="dup-box">
<div class="dup-box-title">
	<i class="far fa-file-archive fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Archive') ?>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-template-archive-panel" style="<?php echo esc_attr($ui_css_archive); ?>">

	<!-- =================
	FILES -->
	<b class="dpro-hdr"><i class="fa fa-files fa-sm"></i> <?php DUP_PRO_U::esc_html_e('FILES'); ?></b>

	<input id="archive_export_onlydb" type="checkbox" <?php DUP_PRO_UI::echoChecked($package_template->archive_export_onlydb) ?> name="archive_export_onlydb"   onclick="DupPro.Template.ExportOnlyDB()"  />
	<label for="archive_export_onlydb"><?php _e("Archive Only the Database"); ?></label> <br/>

	<div id="dup-exportdb-items-off">

		<input id="archive_filter_on" type="checkbox" <?php DUP_PRO_UI::echoChecked($package_template->archive_filter_on) ?> name="archive_filter_on"  onclick="DupPro.Template.ToggleFileFilters()" />
		<label for="archive_filter_on"><?php _e("Enable File Filter"); ?></label>
		<br/>

		<label><?php _e("Directories"); ?>:</label>
		<div class='dup-quick-links'>
			<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludePath('<?php echo esc_js(duplicator_pro_get_home_path()); ?>')">[<?php DUP_PRO_U::esc_html_e("root path") ?>]</a>
			<?php if (! empty($content_path)) :?>
				<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludePath('<?php echo rtrim(WP_CONTENT_DIR, '/'); ?>')">[<?php DUP_PRO_U::esc_html_e("wp-content") ?>]</a>
			<?php endif; ?>
			<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludePath('<?php echo rtrim($upload_dir, '/'); ?>')">[<?php DUP_PRO_U::esc_html_e("wp-uploads") ?>]</a>
			<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludePath('<?php echo DupProSnapLibIOU::safePath(WP_CONTENT_DIR); ?>/cache')">[<?php DUP_PRO_U::esc_html_e("cache") ?>]</a>
			<a href="javascript:void(0)" onclick="jQuery('#_archive_filter_dirs').val('')"><?php DUP_PRO_U::esc_html_e("(clear)") ?></a>
		</div>
		<textarea name="_archive_filter_dirs" id="_archive_filter_dirs" placeholder="/full_path/exclude_path1;/full_path/exclude_path2;">
			<?php echo str_replace(";", ";\n", esc_textarea($package_template->archive_filter_dirs)) ?>
		</textarea>
		<br/>

		<label><?php _e("Extensions"); ?>:</label>
		<div class='dup-quick-links'>
			<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludeExts('avi;mov;mp4;mpeg;mpg;swf;wmv;aac;m3u;mp3;mpa;wav;wma')">[<?php DUP_PRO_U::esc_html_e("media") ?>]</a>
			<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludeExts('zip;rar;tar;gz;bz2;7z')">[<?php DUP_PRO_U::esc_html_e("archive") ?>]</a>
			<a href="javascript:void(0)" onclick="jQuery('#_archive_filter_exts').val('')"><?php DUP_PRO_U::esc_html_e("(clear)") ?></a>
		</div>
		<input type="text" name="_archive_filter_exts" id="_archive_filter_exts" value="<?php echo esc_attr($package_template->archive_filter_exts);?>" placeholder="ext1;ext2;ext3">
		<br/>

		<label><?php _e("Files"); ?>:</label>
		<div class='dup-quick-links'>
			<a href="javascript:void(0)" onclick="DupPro.Template.AddExcludeFilePath('<?php echo esc_js(duplicator_pro_get_home_path()); ?>')">[<?php DUP_PRO_U::esc_html_e("file path") ?>]</a>
			<a href="javascript:void(0)" onclick="jQuery('#_archive_filter_files').val('')"><?php DUP_PRO_U::esc_html_e("(clear)") ?></a>
		</div>
		<textarea name="_archive_filter_files" id="_archive_filter_files" placeholder="/full_path/exclude_file_1.ext;/full_path/exclude_file2.ext"><?php echo str_replace(";", ";\n", esc_textarea($package_template->archive_filter_files)) ?></textarea>
	</div>
	<br/>

	<!-- DB ONLY ENABLED -->
	<div id="dup-exportdb-items-checked">
		<b><?php DUP_PRO_U::esc_html_e('Overview:'); ?></b><br/> 
		<?php
			DUP_PRO_U::esc_html_e("This advanced option excludes all files from the archive.  Only the database and a copy of the installer.php "
			. "will be included in the archive.zip file. The option can be used for backing up and moving only the database.");
			?>
			<br/><br/>
			<b><i class='fa fa-exclamation-circle'></i> <?php DUP_PRO_U::esc_html_e('Notice:'); ?></b><br/>  
			<?php DUP_PRO_U::esc_html_e("Installing only the database over an existing site may have unintended consequences. "
			 . "Be sure to know the state of your system before installing the database without the associated files.");

			echo '<br/><br/>';

			DUP_PRO_U::esc_html_e("For example, if you have WordPress 4.6 on this site and you copy this sites database to a host that has WordPress 4.8 files then the source code of the files will not be in sync with the database causing possible errors.");

			echo '<br/><br/>';

			DUP_PRO_U::esc_html_e("This can also be true of plugins and themes. When moving only the database be sure to know the database will be compatible with ALL source code files. Please use this advanced feature with caution!");
		?>
		<br/><br/>
	</div>

	<!-- =================
	DATABASE -->
	<b class="dpro-hdr"><i class="fa fa-table fa-sm"></i> <?php DUP_PRO_U::esc_html_e('DATABASE'); ?></b>
	<input type="checkbox" id="_datbase_filter_on" <?php DUP_PRO_UI::echoChecked($package_template->database_filter_on) ?> name="_database_filter_on" />
	<label for="_datbase_filter_on"><?php DUP_PRO_U::esc_html_e("Enable Table Filters"); ?></label>
	<i class="fas fa-question-circle fa-sm"
		data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Database Table Filters:"); ?>"
		data-tooltip="<?php DUP_PRO_U::esc_attr_e('Checked tables will not be added to the database script.  Excluding certain tables can possibly cause your site or plugins to not work correctly after install!'); ?>">
	</i><br/><br/>

	<div id="dup-db-filter-items">
		<a href="javascript:void(0)" id="dball" onclick="jQuery('#dup-dbtables .checkbox').prop('checked', true).trigger('click');">[ <?php DUP_PRO_U::esc_html_e('Include All'); ?> ]</a> &nbsp;
		<a href="javascript:void(0)" id="dbnone" onclick="jQuery('#dup-dbtables .checkbox').prop('checked', false).trigger('click');">[ <?php DUP_PRO_U::esc_html_e('Exclude All'); ?> ]</a>
		<div style="font-family: Calibri; white-space: nowrap">
			<?php
			$tables = $wpdb->get_results("SHOW FULL TABLES FROM `" . DB_NAME . "` WHERE Table_Type = 'BASE TABLE' ", ARRAY_N);

			$num_rows = count($tables);
			echo '<table id="dup-dbtables"><tr><td>';
			$next_row = round($num_rows / 3, 0);
			$counter = 0;
			$tableList = explode(',', $package_template->database_filter_tables);
			foreach ($tables as $table) {
				if (DUP_PRO_U::isTableExists($table[0])) {
					if (in_array($table[0], $tableList)) {
						$checked = 'checked="checked"';
						$css	 = 'text-decoration:line-through';
					} else {
						$checked = '';
						$css	 = '';
					}
					echo "<label for='_database_filter_tables-".esc_attr($table[0])."' style='".esc_attr($css)."'>".
					"<input class='checkbox dbtable' $checked type='checkbox' name='_database_filter_tables[]' id='_database_filter_tables-".esc_attr($table[0])."' value='".esc_attr($table[0])."' onclick='DupPro.Template.ExcludeTable(this)' />&nbsp;".esc_html($table[0]).
					"</label><br />";
					$counter++;
					if ($next_row <= $counter) {
						echo '</td><td valign="top">';
						$counter = 0;
					}
				}
			}
			echo '</td></tr></table>';
			?>
		</div><br/>
	</div>

	<?php DUP_PRO_U::esc_html_e("Compatibility Mode"); ?>
	<i class="fas fa-question-circle fa-sm"
		data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Legacy Support:"); ?>"
		data-tooltip="<?php DUP_PRO_U::esc_attr_e('This option is not available as a template setting.  It can only be used when creating a new package.  Please see the FAQ for a full overview of using this feature.'); ?>">
	</i><br/>
	<i><?php
			$url = "<a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-090-q' target='_blank'>" . DUP_PRO_U::esc_html__('FAQ details') . "</a>";
			printf(DUP_PRO_U::esc_html__("Not enabled for template settings. Please see the full %s"), $url);
		?>
	</i>

	<!-- For now not including in filters since don't want to encourage use with schedules since filtering creates incomplete multisite -->
	<?php if(false && is_multisite() && (DUP_PRO_License_U::getLicenseType() === DUP_PRO_License_Type::BusinessGold)) : ?>
	<!-- ===================
	 MULTI-SITE:  -->
	<div style="margin-top: 30px">
		<b class="dpro-hdr"><i class="fa fa-columns"></i> <?php DUP_PRO_U::esc_html_e('MULTISITE'); ?></b>
		<table class="mu-opts">
			<tr>
				<td>
					<b><?php DUP_PRO_U::esc_html_e("Excluded Sub-Sites"); ?>:</b><br/>
					<select name="_mu_exclude[]" id="mu-exclude" multiple="true" class="mu-selector">
						<?php

						foreach($package_template->filter_sites as $site_id){
							$site_details = get_blog_details($site_id);
							echo "<option value='".intval($site_id)."'>".esc_html($site_details->blogname)."</option>";
						}
						?>
					</select>
				</td>
				<td>
					<button type="button" id="mu-include-btn" class="mu-push-btn"><i class="fa fa-chevron-right"></i></button><br/>
					<button type="button" id="mu-exclude-btn" class="mu-push-btn"><i class="fa fa-chevron-left"></i></button>
				</td>
				<td>
					<b><?php DUP_PRO_U::esc_html_e("Included Sub-Sites"); ?>:</b><br/>
					<select name="_mu_include[]" id="mu-include" multiple="true" class="mu-selector">
						<?php
						$sites = DUP_PRO_MU::getSubsites();
						foreach($sites as $site) {
							if(!in_array($site->id, $package_template->filter_sites)) {
								echo "<option value='".esc_attr($site->id)."'>".esc_html($site->domain.$site->path)."</option>";
							}
						}
						?>
					</select>
				</td>
			</tr>
		</table>

		<div class="dpro-panel-optional-txt" style="text-align: left">
			<?php DUP_PRO_U::esc_html_e("This section allows you to control which sub-sites of a multisite network you want to include within your package.  The 'Included Sub-Sites' will also be available to choose from at install time."); ?> <br/>
			<?php DUP_PRO_U::esc_html_e("By default all packages are include.  The ability to exclude sub-sites are intended to help shrink your package if needed."); ?>
		</div>
	</div>
	<?php endif; ?>
</div>
</div><br />


<!-- ===============================
INSTALLER -->
<div class="dup-box">
<div class="dup-box-title">
	<i class="fa fa-bolt fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Installer') ?>
	<span id="dpro-install-secure-lock" title="<?php DUP_PRO_U::esc_attr_e('Installer password protection is on') ?>"><i class="fa fa-lock fa-sm"></i> </span>
	<span id="dpro-install-secure-unlock" title="<?php DUP_PRO_U::esc_attr_e('Installer password protection is off') ?>"><i class="fa fa-unlock-alt"></i> </span>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-template-install-panel" style="<?php echo esc_attr($ui_css_install); ?>">

	<div class="dpro-panel-optional-txt">
		<b><?php DUP_PRO_U::esc_html_e('All values in this section are'); ?> <u><?php DUP_PRO_U::esc_html_e('optional'); ?></u></b>
		<i class="fas fa-question-circle fa-sm"
			data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Setup/Prefills"); ?>"
			data-tooltip="<?php DUP_PRO_U::esc_attr_e('All values in this section are OPTIONAL! If you know ahead of time the database input fields the installer will use, '
				. 'then you can optionally enter them here and they will be prefilled at install time.  Otherwise you can just enter them in at install time and ignore '
				. 'all these options in the Installer section.'); ?>"></i>

	</div>

	<table class="dpro-install-setup"  style="margin-top:-10px">
		<tr>
			<td colspan="2"><div class="dpro-install-hdr-2"><?php DUP_PRO_U::esc_html_e("Setup") ?></div></td>
		</tr>
		<tr>
			<td style="width:130px"><b><?php DUP_PRO_U::esc_html_e("Branding") ?></b></td>
			<td>
				<?php
					$brands = DUP_PRO_Brand_Entity::get_all();
					if($is_freelancer_plus) :
				?>
					<select name="installer_opts_brand" id="installer_opts_brand" onchange="DupPro.Template.BrandChange();">
						<?php
						$active_brand_id = 0;
						foreach ($brands as $i=>$brand) :
							if($brand->active) $active_brand_id = $brand->id;
						?>
							<option value="<?php echo esc_attr($brand->id);?>" title="<?php echo esc_attr($brand->notes); ?>"<?php echo ((isset($_REQUEST['inner_page']) && $_REQUEST['inner_page'] == 'edit') ? $package_template->installer_opts_brand : $brand->active)==$brand->id ? ' selected' : ''; ?>>
								<?php echo esc_html($brand->name); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php
					$preview_url = array(
						get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default" ),
						get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=".intval($active_brand_id) )
					);
					?>
					<a href="<?php echo esc_url($preview_url[$active_brand_id > 0 ? 1 : 0]); ?>" target="_blank" class="button" id="brand-preview"><?php DUP_PRO_U::esc_html_e("Preview"); ?></a> &nbsp;
					<i class="fas fa-question-circle fa-sm"
					   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Choose Brand:"); ?>"
					   data-tooltip="<?php DUP_PRO_U::esc_attr_e('This option changes the branding of the installer file.  Click the preview button to see the selected style.'); ?>"></i>
				<?php else : ?>
					<a href="admin.php?page=duplicator-pro-settings&tab=package&sub=brand" class="upgrade-link"><?php DUP_PRO_U::esc_html_e("Enable Branding"); ?></a>
				<?php endif; ?>
					<br/><br/>
			</td>
		</tr>
		<tr>
			<td><b><?php DUP_PRO_U::esc_html_e("Security") ?></b></td>
			<td>
				<?php
					$dup_install_secure_pass = isset($package_template->installer_opts_secure_pass) ? base64_decode($package_template->installer_opts_secure_pass) : '';

				?>
				<input type="checkbox" name="_installer_opts_secure_on" id="_installer_opts_secure_on" <?php echo ($package_template->installer_opts_secure_on) ? "checked='checked'" : ""; ?> onclick="DupPro.Template.EnableInstallerPassword()" />
				<label for="_installer_opts_secure_on"><?php DUP_PRO_U::esc_html_e("Enable Password Protection") ?></label>
				<i class="fas fa-question-circle fa-sm"
				   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Security:"); ?>"
				   data-tooltip="<?php DUP_PRO_U::esc_attr_e('Enabling this option will allow for basic password protection on the installer. Before running the installer the '
							   . 'password below must be entered before proceeding with an install.  This password is a general deterrent and should not be substituted for properly '
							   . 'keeping your files secure.  Be sure to remove all installer files when the install process is completed.'); ?>"></i>

				<div id="dpro-pass-toggle">
					<input type="password" name="_installer_opts_secure_pass" id="_installer_opts_secure_pass" required="required" value="<?php echo esc_attr($dup_install_secure_pass);?>" />
					<button type="button" id="secure-btn" class="pass-toggle" onclick="DupPro.Template.ToggleInstallerPassword()" title="<?php DUP_PRO_U::esc_attr_e('Show/Hide Password'); ?>"><i class="fas fa-eye fa-sm"></i></button>
				</div>

			</td>
		</tr>
	</table>
	<br/>

	<table style="width:100%">
		<tr>
			<td colspan="2"><div class="dpro-install-hdr-2"><?php DUP_PRO_U::esc_html_e("Prefills") ?></div></td>
		</tr>
	</table>

	<!-- ===================
	STEP1 TABS -->
	<div data-dpro-tabs="true">
		<ul>
			<li><?php DUP_PRO_U::esc_html_e('Basic') ?></li>
			<li id="dpro-cpnl-tab-lbl"><?php DUP_PRO_U::esc_html_e('cPanel') ?></li>
		</ul>

		<!-- ===================
		TAB1: Basic -->
		<div>
			 <table class="form-table">
				<tr>
					<td colspan="2">
						<b class="dpro-hdr"><?php DUP_PRO_U::esc_html_e('MySQL Server'); ?></b>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e("Host"); ?></th>
					<td><input type="text" placeholder="localhost" name="installer_opts_db_host" value="<?php echo esc_attr($package_template->installer_opts_db_host);?>"></td>
				</tr>
				<tr valign="top">
					<th><label><?php _e("Database"); ?></label></th>
					<td><input type="text" placeholder="<?php DUP_PRO_U::esc_attr_e('valid database name'); ?>" name="installer_opts_db_name" value="<?php echo esc_attr($package_template->installer_opts_db_name);?>"></td>
				</tr>
				<tr valign="top">
					<th><label><?php _e("User"); ?></label></th>
					<td><input type="text" placeholder="<?php DUP_PRO_U::esc_attr_e('valid database user'); ?>" name="installer_opts_db_user" value="<?php echo esc_attr($package_template->installer_opts_db_user);?>"></td>
				</tr>
			</table>
			<br/><br/>
		</div>

		<!-- ===================
		TAB2: cPanel -->
		<div style="height:550px !important">
			<table class="form-table">
				<tr valign="top">
					<td colspan="2"><b class="dpro-hdr"><?php DUP_PRO_U::esc_html_e('cPanel Login'); ?></b></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Automation"); ?></label></th>
					<td>
						<input type="checkbox" name="installer_opts_cpnl_enable" id="installer_opts_cpnl_enable" <?php DUP_PRO_UI::echoChecked($package_template->installer_opts_cpnl_enable); ?> >
						<label for="installer_opts_cpnl_enable">Auto Select cPanel</label>
						<i class="fas fa-question-circle fa-sm" data-tooltip-title="Auto Select cPanel:" data-tooltip="<?php DUP_PRO_U::esc_attr_e('Enabling this options will automatically select the cPanel tab when step one of the installer is shown.');?>" ></i>
							&nbsp; &nbsp;
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Host"); ?></label></th>
					<td><input type="text" name="installer_opts_cpnl_host" value="<?php echo esc_attr($package_template->installer_opts_cpnl_host); ?>"  placeholder="<?php DUP_PRO_U::esc_attr_e('valid cpanel host address'); ?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php DUP_PRO_U::esc_html_e("User"); ?></label></th>
					<td><input type="text" name="installer_opts_cpnl_user" value="<?php echo esc_attr($package_template->installer_opts_cpnl_user); ?>"  placeholder="<?php DUP_PRO_U::esc_attr_e('valid cpanel user login'); ?>"></td>
				</tr>
				<tr>
					<td colspan="2">
						<b class="dpro-hdr"><?php DUP_PRO_U::esc_html_e('MySQL Server'); ?></b>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e("Action"); ?></label></th>
					<td>
						<select name="installer_opts_cpnl_db_action" id="cpnl-dbaction">
							<option value="create" <?php echo ($installer_cpnldbaction == 'create') ? 'selected' : ''; ?>>Create A New Database</option>
							<option value="empty"  <?php echo ($installer_cpnldbaction == 'empty')  ? 'selected' : ''; ?>>Connect to Existing Database and Remove All Data</option>
							<!--option value="rename">Connect to Existing Database and Rename Existing Tables</option-->
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e("Host"); ?></label></th>
					<td><input type="text" name="installer_opts_cpnl_db_host" value="<?php echo esc_attr($package_template->installer_opts_cpnl_db_host);?>" placeholder="<?php DUP_PRO_U::esc_attr_e('localhost'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e("Database"); ?></label></th>
					<td><input type="text" name="installer_opts_cpnl_db_name" value="<?php echo esc_attr($package_template->installer_opts_cpnl_db_name);?>" placeholder="<?php DUP_PRO_U::esc_attr_e('valid database name'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e("User"); ?></label></th>
					<td><input type="text" name="installer_opts_cpnl_db_user" value="<?php echo esc_attr($package_template->installer_opts_cpnl_db_user); ?>" placeholder="<?php DUP_PRO_U::esc_attr_e('valid database user'); ?>" /></td>
				</tr>
			</table>
		</div>
	</div><br/>
	<small><?php DUP_PRO_U::esc_html_e("All other inputs can be entered at install time.") ?></small>
	<br/><br/>

</div>
</div><br/>

<button class="button button-primary" type="submit"><?php DUP_PRO_U::esc_html_e('Save Template'); ?></button>
</form>

<?php
    $alert1 = new DUP_PRO_UI_Dialog();
    $alert1->title		= DUP_PRO_U::__('Transfer Error');
    $alert1->message	= DUP_PRO_U::__('You can\'t exclude all sites!');
    $alert1->initAlert();
?>

<script>
jQuery(document).ready(function($) {

	/* When installer brand changes preview button is updated */
	DupPro.Template.BrandChange = function()
	{
		var $brand	= $("#installer_opts_brand");
		var $id		= $brand.val();
		var $url    = new Array();

		<?php if(is_multisite()) : ?>
			$url = [
				'<?php echo network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default" ); ?>',
				'<?php echo network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=" ); ?>' + $id ];
		<?php else: ?>
			$url = [
				'<?php echo get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default" ); ?>',
				'<?php echo get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=" ); ?>' + $id ];
		<?php endif; ?>

		$("#brand-preview").attr( 'href', $url[ $id > 0 ? 1 : 0 ] );
	};

	/* Enables strike through on excluded DB table */
	DupPro.Template.ExcludeTable = function (check)
	{
		var $cb = $(check);
		if ($cb.is(":checked")) {
			$cb.closest("label").css('textDecoration', 'line-through');
		} else {
			$cb.closest("label").css('textDecoration', 'none');
		}
	}

	/* Enables visual for Database Only check */
	DupPro.Template.ExportOnlyDB = function ()
	{
		$('#dup-exportdb-items-off, #dup-exportdb-items-checked').hide();
		$("#archive_export_onlydb").is(':checked')
			? $('#dup-exportdb-items-checked').show()
			: $('#dup-exportdb-items-off').show();
	};

	/* Formats file directory path name on seperate line of textarea */
	DupPro.Template.AddExcludePath = function (path)
	{
		var text = $("#_archive_filter_dirs").val() + path + ';\n';
		$("#_archive_filter_dirs").val(text);
	};

	/* Appends a path to the extention filter  */
	DupPro.Template.AddExcludeExts = function (path)
	{
		var text = $("#_archive_filter_exts").val() + path + ';';
		$("#_archive_filter_exts").val(text);
	};

	/* Formats file path name on seperate line of textarea */
	DupPro.Template.AddExcludeFilePath = function (path)
	{
		var text = $("#_archive_filter_files").val() + path + '/file.ext;\n';
		$("#_archive_filter_files").val(text);
	};

	/* Used to duplicate a template */
	DupPro.Template.Copy = function()
	{
		$("#dpro-template-form-action").val('copy-template');
		$("#dpro-template-form").parsley().destroy();
		$("#dpro-template-form").submit();
	};

	/* Shows/Hides the password information */
	DupPro.Template.ToggleInstallerPassword = function()
	{
		var $input  = $('#_installer_opts_secure_pass');
		var $button =  $('#secure-btn');
		if (($input).attr('type') == 'text') {
			$input.attr('type', 'password');
			$button.html('<i class="fas fa-eye fa-sm"></i>');
		} else {
			$input.attr('type', 'text');
			$button.html('<i class="fas fa-eye-slash fa-sm"></i>');
		}
	}

	DupPro.Template.EnableInstallerPassword = function ()
	{
		var $button =  $('#secure-btn');
		if ($('#_installer_opts_secure_on').is(':checked')) {
			$('#_installer_opts_secure_pass').attr('readonly', false);
			$('#_installer_opts_secure_pass').attr('required', 'true').focus();
			$('#dpro-install-secure-lock').show();
			$('#dpro-install-secure-unlock').hide();
			$button.removeAttr('disabled');
		} else {
			$('#_installer_opts_secure_pass').removeAttr('required');
			$('#_installer_opts_secure_pass').attr('readonly', true);
			$('#dpro-install-secure-lock').hide();
			$('#dpro-install-secure-unlock').show();
			$button.attr('disabled', 'true');
		}
	};

	DupPro.Template.ToggleFileFilters = function () 
	{
		var readonly_enabled_disabled_items = $('#_archive_filter_dirs, #_archive_filter_exts, #_archive_filter_files');
		if ($("#archive_filter_on").is(':checked')) {
			readonly_enabled_disabled_items.removeAttr('readonly').css({color: '#000'});
		} else {
			readonly_enabled_disabled_items.attr('readonly', 'readonly').css({color: '#999'});			
		}
	};

	//INIT
	$('#template-name').focus().select();
	$('#_archive_filter_dirs').val($('#_archive_filter_dirs').val().trim());
	//Default to cPanel tab if used
	$('#cpnl-enable').is(":checked") ? $('#dpro-cpnl-tab-lbl').trigger("click") : null;
	DupPro.Template.EnableInstallerPassword();
	DupPro.Template.ExportOnlyDB();
	DupPro.Template.BrandChange();
	DupPro.Template.ToggleFileFilters();

    //MU-Transfer buttons
    $('#mu-include-btn').click(function() {
        return !$('#mu-exclude option:selected').remove().appendTo('#mu-include');
    });

    $('#mu-exclude-btn').click(function() {
        var include_all_count = $('#mu-include option').length;
        var include_selected_count = $('#mu-include option:selected').length;

        if(include_all_count > include_selected_count) {
            return !$('#mu-include option:selected').remove().appendTo('#mu-exclude');
		} else {
            <?php $alert1->showAlert(); ?>
        }
    });

});
</script>
