<?php
defined("ABSPATH") or die("");
	$multisite_css = is_multisite() ? '' : 'display:none';
	$subsite_filter_css = is_multisite() && (DUP_PRO_License_U::getLicenseType() === DUP_PRO_License_Type::BusinessGold) ? '' : 'display:none';

	DUP_PRO_LOG::trace("subsite filter css: {$subsite_filter_css}");
	$archive_format = $global->archive_build_mode == DUP_PRO_Archive_Build_Mode::DupArchive ? 'daf' : 'zip';
?>

<style>
    /*ARCHIVE: Area*/
    form#dup-form-opts div.tabs-panel{max-height:800px; padding:10px; min-height:280px}
    form#dup-form-opts ul li.tabs{font-weight:bold}
    select#archive-format {min-width:100px; margin:1px 0px 4px 0px}
    span#dup-archive-filter-file {color:#A62426; display:none}
    span#dup-archive-filter-db {color:#A62426; display:none}
	span#dup-archive-db-only {color:#A62426; display:none}
    div#dup-file-filter-items, div#dup-db-filter-items {padding:5px 0px 0px 0px}
    /* Tab: Files */
	div#dup-exportdb-items-checked, div#dup-exportdb-items-off {min-height:275px;}
	div#dup-exportdb-items-checked {padding: 5px; max-width:650px}
    form#dup-form-opts textarea#filter-dirs {height:85px}
    form#dup-form-opts textarea#filter-exts {height:27px}
    form#dup-form-opts textarea#filter-files {height:85px}
    div.dup-quick-links {font-size:11px; float:right; display:inline-block; margin-top:2px; font-style:italic}
    div.dup-tabs-opts-help {font-style:italic; font-size:11px; margin:10px 0px 0px 10px; color:#777}
    /* Tab: Database */
    table#dup-dbtables td {padding:1px 7px 1px 4px}
	label.core-table {color:#9A1E26;font-style:italic;font-weight:bold}
    label.core-table.subcore-table-0 {color:#c12171;}
    label.core-table.subcore-table-1 {color:#c14421;}



	i.core-table-info {color:#9A1E26;font-style:italic}
	label.non-core-table {color:#000}
	label.non-core-table:hover, label.core-table:hover {text-decoration:line-through}

	
	 /* Tab: Multisite */
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
</style>

<!-- ===================
 META-BOX: ARCHIVE -->
<div class="dup-box">
<div class="dup-box-title" >
	<i class="far fa-file-archive fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Archive') ?> 
	<sup class="dup-box-title-badge"><?php echo esc_html($archive_format); ?></sup> &nbsp;
	<span style="font-size:13px">
		<span id="dup-archive-filter-file" title="<?php DUP_PRO_U::esc_attr_e('File filter enabled') ?>"><i class="fa fa-files fa-sm"></i> <i class="fa fa-filter fa-sm"></i> &nbsp;&nbsp;</span>
		<span id="dup-archive-filter-db" title="<?php DUP_PRO_U::esc_attr_e('Database filter enabled') ?>"><i class="fa fa-table fa-sm"></i> <i class="fa fa-filter fa-sm"></i></span>
		<span id="dup-archive-db-only" title="<?php DUP_PRO_U::esc_attr_e('Archive Only the Database') ?>"> <?php DUP_PRO_U::esc_html_e('Database Only') ?> </span>
	</span>

	<div class="dup-box-arrow"></div>
</div>		
<div class="dup-box-panel" id="dup-pack-archive-panel" style="<?php echo esc_attr($ui_css_archive); ?>">
	<input type="hidden" name="archive-format" value="ZIP" />

	<!-- ===================
	NESTED TABS -->
	<div data-dpro-tabs="true">
		<ul>
			<li><a href="javascript:void(0)"><?php DUP_PRO_U::esc_html_e('Files') ?></a></li>
			<li><a href="javascript:void(0)"><?php DUP_PRO_U::esc_html_e('Database') ?></a></li>
			<li style="<?php echo $multisite_css ?>"><a href="javascript:void(0)"><?php DUP_PRO_U::esc_html_e('Multisite') ?></a></li>
		</ul>

		<!-- ===================
		TAB1: FILES -->
		<div>
			<?php
			$uploads = wp_upload_dir();
			$upload_dir = DupProSnapLibIOU::safePath($uploads['basedir']);
			$content_path = defined('WP_CONTENT_DIR') ? DupProSnapLibIOU::safePath(WP_CONTENT_DIR) : '';
			?>
			
			<div style="line-height:24px">
				<b><?php DUP_PRO_U::esc_html_e('Engine') ?>:</b>
				<a href="admin.php?page=duplicator-pro-settings&tab=package" target="settings"><?php echo $global->get_archive_engine(); ?></a><br/>
				<input type="checkbox" id="export-onlydb" name="export-onlydb"  onclick="DupPro.Pack.ExportOnlyDB()" />
				<label for="export-onlydb"><?php DUP_PRO_U::esc_html_e('Archive Only the Database') ?></label>
			</div>

			<div id="dup-exportdb-items-off">
				<input type="checkbox" id="filter-on" name="filter-on" onclick="DupPro.Pack.ToggleFileFilters()" />
				<label for="filter-on"><?php DUP_PRO_U::esc_html_e("Enable File Filters") ?></label>
				<i class="fas fa-question-circle fa-sm"
				   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("File Filters:"); ?>"
				   data-tooltip="<?php DUP_PRO_U::esc_attr_e('File filters allow you to ignore directories/files and file extensions.  When creating a package only include the data you '
				   . 'want and need.  This helps to improve the overall archive build time and keep your backups simple and clean.'); ?>">
				</i>

				<div id="dup-file-filter-items">

					<!-- DIRECTORIES -->
					<label for="filter-dirs" title="<?php DUP_PRO_U::esc_attr_e("Separate all filters by semicolon"); ?>">
						<?php DUP_PRO_U::esc_html_e("Directories") ?>:
						<sup title="<?php DUP_PRO_U::esc_attr_e("Number of diectory filters") ?>" id="filter-dirs-count">(0)</sup>
					</label>
					<div class='dup-quick-links'>
						<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludePath('<?php echo esc_js(duplicator_pro_get_home_path()); ?>')">[<?php DUP_PRO_U::esc_html_e("root path") ?>]</a>
						<?php if (! empty($content_path)) :?>
							<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludePath('<?php echo DupProSnapLibIOU::safePath(WP_CONTENT_DIR); ?>')">[<?php DUP_PRO_U::esc_html_e("wp-content") ?>]</a>
						<?php endif; ?>
						<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludePath('<?php echo rtrim($upload_dir, '/'); ?>')">[<?php DUP_PRO_U::esc_html_e("wp-uploads") ?>]</a>
						<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludePath('<?php echo DupProSnapLibIOU::safePath(WP_CONTENT_DIR); ?>/cache')">[<?php DUP_PRO_U::esc_html_e("cache") ?>]</a>
						<a href="javascript:void(0)" onclick="jQuery('#filter-dirs').val(''); DupPro.Pack.CountFilters();"><?php DUP_PRO_U::esc_html_e("(clear)") ?></a>
					</div>
					<textarea name="filter-dirs" id="filter-dirs" placeholder="/full_path/exclude_path1;/full_path/exclude_path2;"></textarea><br/>

					<!-- EXTENSIONS -->
					<label class="no-select" title="<?php DUP_PRO_U::esc_attr_e("Separate all filters by semicolon"); ?>"><?php DUP_PRO_U::esc_html_e("File Extensions") ?>:</label>
					<div class='dup-quick-links'>
						<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludeExts('avi;mov;mp4;mpeg;mpg;swf;wmv;aac;m3u;mp3;mpa;wav;wma')">[<?php DUP_PRO_U::esc_html_e("media") ?>]</a>
						<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludeExts('zip;rar;tar;gz;bz2;7z')">[<?php DUP_PRO_U::esc_html_e("archive") ?>]</a>
						<a href="javascript:void(0)" onclick="jQuery('#filter-exts').val('')"><?php DUP_PRO_U::esc_html_e("(clear)") ?></a>
					</div>
					<textarea name="filter-exts" id="filter-exts" placeholder="ext1;ext2;ext3;"></textarea><br/>

					<!-- FILES -->
					<label class="no-select" title="<?php DUP_PRO_U::esc_attr_e("Separate all filters by semicolon"); ?>">
						<?php DUP_PRO_U::esc_html_e("Files") ?>:
						<sup title="<?php DUP_PRO_U::esc_attr_e("Number of file filters") ?>" id="filter-files-count">(0)</sup>
					</label>
					<div class='dup-quick-links'>
						<a href="javascript:void(0)" onclick="DupPro.Pack.AddExcludeFilePath('<?php echo esc_js(duplicator_pro_get_home_path()); ?>')"><?php DUP_PRO_U::esc_html_e("(file path)") ?></a>
						<a href="javascript:void(0)" onclick="jQuery('#filter-files').val(''); DupPro.Pack.CountFilters();"><?php DUP_PRO_U::esc_html_e("(clear)") ?></a>
					</div>
					<textarea name="filter-files" id="filter-files" placeholder="/full_path/exclude_file_1.ext;/full_path/exclude_file2.ext"></textarea>

					<div class="dup-tabs-opts-help">
						<?php DUP_PRO_U::esc_html_e("The directories, extensions and files above will be be exclude from the archive file if enable is checked."); ?> <br/>
						<?php
						DUP_PRO_U::esc_html_e("Use full path for directories or specific files.");
						echo " <b>";
						DUP_PRO_U::esc_html_e("Use filenames without paths to filter same-named files across multiple directories.");
						echo "</b>";
						?> <br/>
						<?php DUP_PRO_U::esc_html_e("Use semicolons to separate all items."); ?><br/>
                        <?php DUP_PRO_U::esc_html_e("Use # to comment a line."); ?>
					</div>
				</div>
			</div>

			<!-- DB ONLY ENABLED -->
			<div id="dup-exportdb-items-checked">
				<?php
					echo wp_kses(DUP_PRO_U::__("<b>Overview:</b><br> This advanced option excludes all files from the archive.  Only the database and a copy of the installer.php "
					. "will be included in the archive.zip file. The option can be used for backing up and moving only the database."),
						array(
							'b' => array(),
							'br' => array(),
						)	
					);

					echo '<br/><br/>';

					echo wp_kses(DUP_PRO_U::__("<b><i class='fa fa-exclamation-circle'></i> Notice:</b><br/>  Installing only the database over an existing site may have unintended consequences.  "
					 . "Be sure to know the state of your system before installing the database without the associated files."),
						array(
							'b' => array(),
							'i' => array('class'),
						)
					);

					echo '<br/><br/>';

					DUP_PRO_U::esc_html_e("For example, if you have WordPress 4.6 on this site and you copy this sites database to a host that has WordPress 4.8 files then the source code of the files "
						. " will not be in sync with the database causing possible errors.");

					echo '<br/><br/>';

					DUP_PRO_U::esc_html_e("This can also be true of plugins and themes.   When moving only the database be sure to know the database will be compatible with ALL source code files."
					. "  Please use this advanced feature with caution!");
				?>
				<br/><br/>
			</div>
		</div>

		<!-- ===================
		TAB2: DATABASE -->
		<div>
			<table>
				<tr>
					<td colspan="2" style="padding:0 0 10px 0">
						<?php DUP_PRO_U::esc_html_e("Build Mode") ?>:&nbsp; <a href="?page=duplicator-pro-settings&tab=package" target="settings"><?php echo $dbbuild_mode; ?></a>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top"><input type="checkbox" id="dbfilter-on" name="dbfilter-on" onclick="DupPro.Pack.ToggleDBFilters()" /></td>
					<td>
						<label for="dbfilter-on"><?php DUP_PRO_U::esc_html_e("Enable Table Filters") ?> &nbsp;</label>
						<i class="fas fa-question-circle fa-sm"
							data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Table Filters:"); ?>"
							data-tooltip="<?php DUP_PRO_U::esc_attr_e('Table filters allow you to ignore certain tables from a database.  When creating a package only include the data you '
							. 'want and need.  This helps to improve the overall archive build time and keep your backups simple and clean.'); ?>"> <br/>
						</i>

					</td>
				</tr>
			</table>

			<div id="dup-db-filter-items">
				<a href="javascript:void(0)" id="dball" onclick="jQuery('#dup-dbtables .checkbox').prop('checked', true).trigger('click');">[ <?php DUP_PRO_U::esc_html_e('Include All'); ?> ]</a> &nbsp;
				<a href="javascript:void(0)" id="dbnone" onclick="jQuery('#dup-dbtables .checkbox').prop('checked', false).trigger('click');">[ <?php DUP_PRO_U::esc_html_e('Exclude All'); ?> ]</a> &nbsp;
				<div class="dup-tabs-opts-help" style="margin:0; display:inline-block"><?php DUP_PRO_U::esc_html_e("Checked tables are exclude") ?></div>

				<div style="font-stretch:ultra-condensed; font-family: Calibri; white-space: nowrap">
				<?php
					$tables = $wpdb->get_results("SHOW FULL TABLES FROM `" . DB_NAME . "` WHERE Table_Type = 'BASE TABLE' ", ARRAY_N);
					$num_rows = count($tables);
					$next_row = round($num_rows / 4, 0);
					$counter = 0;

					echo '<table id="dup-dbtables"><tr><td valign="top">';
					foreach ($tables as $table) {
						if (DUP_PRO_U::isTableExists($table[0])) {
							if (DUP_PRO_U::isWPCoreTable($table[0])) {
								$tableBlogId = DUP_PRO_U::getWPBlogIdTable($table[0]);

								$core_css	 = 'core-table';
								$core_note	 = '*';

								if ($tableBlogId > 0) {
									$core_css .= ' subcore-table-'.($tableBlogId % 2);
								}
							} else {
								$core_css	 = 'non-core-table';
								$core_note	 = '';
							}

							echo "<label for='dbtables-{$table[0]}' class='{$core_css}'>"
							."<input class='checkbox dbtable' type='checkbox' name='dbtables[]' id='dbtables-{$table[0]}' value='{$table[0]}' onclick='DupPro.Pack.ExcludeTable(this)' />"
							."&nbsp;{$table[0]}{$core_note}</label><br />";
							$counter++;
							if ($next_row <= $counter) {
								echo '</td><td valign="top">';
								$counter = 0;
							}
						}
					}
					echo '</td></tr></table>';
				?>
				</div>
				<div class="dup-tabs-opts-help">
					<?php
						echo wp_kses(DUP_PRO_U::__("Checked tables will be <u>excluded</u> from the database script. "), array('u' => array()));
						DUP_PRO_U::esc_html_e("Excluding certain tables can cause your site or plugins to not work correctly after install!");
						echo '<br>';
						echo '<i class="core-table-info"> ';
						DUP_PRO_U::esc_html_e("Use caution when excluding tables! It is highly recommended to not exclude WordPress core tables*, unless you know the impact.");
						echo '</i>';
					?>
				</div>
			</div>

			<hr />
			<?php DUP_PRO_U::esc_html_e("Compatibility Mode") ?> &nbsp;
			<i class="fas fa-question-circle fa-sm"
			   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Compatibility Mode:"); ?>"
			   data-tooltip="<?php DUP_PRO_U::esc_attr_e('This is an advanced database backwards compatibility feature that should ONLY be used if having problems installing packages.'
					   . ' If the database server version is lower than the version where the package was built then these options may help generate a script that is more compliant'
					   . ' with the older database server. It is recommended to try each option separately starting with mysql40.'); ?>">
			</i> &nbsp;
			<small style="font-style:italic">
				<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-090-q" target="_blank">[<?php DUP_PRO_U::esc_html_e('full overview'); ?>]</a>
			</small>
			<br/>
			<?php
			if ($dbbuild_mode == 'MYSQLDUMP') :?>
				<?php
					$modes = isset($Package) ? explode(',', $Package->Database->Compatible) : array();
					$is_mysql40		= in_array('mysql40',	$modes);
					$is_no_table	= in_array('no_table_options',  $modes);
					$is_no_key		= in_array('no_key_options',	$modes);
					$is_no_field	= in_array('no_field_options',	$modes);
				?>
				<table class="dbmysql-compatibility">
					<tr>
						<td>
							<input type="checkbox" name="dbcompat[]" id="dbcompat-mysql40" value="mysql40" <?php echo $is_mysql40 ? 'checked="true"' :''; ?> >
							<label for="dbcompat-mysql40"><?php DUP_PRO_U::esc_html_e("mysql40") ?></label>
						</td>
						<td>
							<input type="checkbox" name="dbcompat[]" id="dbcompat-no_table_options" value="no_table_options" <?php echo $is_no_table ? 'checked="true"' :''; ?>>
							<label for="dbcompat-no_table_options"><?php DUP_PRO_U::esc_html_e("no_table_options") ?></label>
						</td>
						<td>
							<input type="checkbox" name="dbcompat[]" id="dbcompat-no_key_options" value="no_key_options" <?php echo $is_no_key ? 'checked="true"' :''; ?>>
							<label for="dbcompat-no_key_options"><?php DUP_PRO_U::esc_html_e("no_key_options") ?></label>
						</td>
						<td>
							<input type="checkbox" name="dbcompat[]" id="dbcompat-no_field_options" value="no_field_options" <?php echo $is_no_field ? 'checked="true"' :''; ?>>
							<label for="dbcompat-no_field_options"><?php DUP_PRO_U::esc_html_e("no_field_options") ?></label>
						</td>
					</tr>
				</table>
				<div class="dup-tabs-opts-help"><?php DUP_PRO_U::esc_html_e("Compatibility mode settings are not persistent.  They must be enabled with every new build!"); ?></div>
			<?php else :?>
				&nbsp; &nbsp; <i><?php DUP_PRO_U::esc_html_e("This option is only available with mysqldump mode."); ?></i>
			<?php endif; ?>

		</div>

		<!-- ===================
		TAB3: MULTI-SITE -->
		<div >
			<div style="<?php echo $multisite_css ?>; max-width:800px">
			<?php
				$license = DUP_PRO_License_U::getLicenseType();

				echo '<b>'.DUP_PRO_U::esc_html__("Overview:").'</b><br/>';
				$txt_mu_license = DUP_PRO_U::__("This Duplicator Pro <a href='admin.php?page=duplicator-pro-settings&tab=licensing' target='lic'>%s</a> has "
					. "Multisite Basic capability, ");
				$txt_mu_basic   = DUP_PRO_U::__("which backs up and migrates an entire multisite network. "
					. "Subsite to standalone conversion is not supported with Multisite Basic, only with Multisite Plus+.<br/><br/>"
					. "To gain access to Multisite Plus+ please login to your dashboard and upgrade to either a <a href='https://snapcreek.com/dashboard/' target='snap'>Business or Gold License</a>.");

				switch ($license) {
					case DUP_PRO_License_Type::Personal:
						printf(wp_kses($txt_mu_license, array('a' => array())), DUP_PRO_U::esc_html__("Personal License"));
						echo $txt_mu_basic;
						break;

					case DUP_PRO_License_Type::Freelancer:
						printf(wp_kses($txt_mu_license, array('a' => array())), DUP_PRO_U::esc_html__("Freelancer License"));
						echo $txt_mu_basic;
						break;

					case DUP_PRO_License_Type::BusinessGold:
						DUP_PRO_U::esc_html_e("When you want to move a full multisite network or convert a subsite to a standalone site just create a standard package like you would with a single site. "
							."Then browse to the installer and choose either 'Restore entire multisite network'  or 'Convert subsite into a standalone site'.  "
							."These options will be present on Step 1 of the installer when restoring a Multisite package.");

						echo '<br/><br/>';
						echo wp_kses(DUP_PRO_U::__("<u><b>Important:</b></u> Full network restoration is an installer option only if you include <b>all</b> subsites. If any subsites are filtered then you may only restore individual subsites as standalones sites at install-time."), array(
								'b' => array(),
								'u' => array(),
							));
						break;

					default:
						printf($txt_mu_license, DUP_PRO_U::__("Unlicensed"));
						echo $txt_mu_basic;
				}
				?>
			</div>

			<?php if(is_multisite() && (DUP_PRO_License_U::getLicenseType() === DUP_PRO_License_Type::BusinessGold)) :?>
			
				<table class="mu-opts">
					<tr>
						<td>
							<b><?php DUP_PRO_U::esc_html_e("Included Sub-Sites"); ?>:</b><br/>
							<select name="mu-include[]" id="mu-include" multiple="true" class="mu-selector">
								<?php
									//$sites = get_sites();
									$sites = DUP_PRO_MU::getSubsites();

									foreach($sites as $site){
										echo "<option value='".esc_attr($site->id)."'>".esc_html($site->domain.$site->path)."</option>";
									}
								?>
							</select>
						</td>
						<td>
							<button type="button" id="mu-exclude-btn" class="mu-push-btn"><i class="fa fa-chevron-right"></i></button><br/>
							<button type="button" id="mu-include-btn" class="mu-push-btn"><i class="fa fa-chevron-left"></i></button>
						</td>
						<td>
							<b><?php DUP_PRO_U::esc_html_e("Excluded Sub-Sites"); ?>:</b><br/>
							<select name="mu-exclude[]" id="mu-exclude" multiple="true" class="mu-selector"></select>
						</td>
					</tr>
				</table>

				<div class="dpro-panel-optional-txt" style="text-align: left">
					<?php DUP_PRO_U::esc_html_e("This section allows you to control which sub-sites of a multisite network you want to include within your package.  The 'Included Sub-Sites' will also be available to choose from at install time."); ?> <br/>
					<?php DUP_PRO_U::esc_html_e("By default all packages are include.  The ability to exclude sub-sites are intended to help shrink your package if needed."); ?>
				</div>
			<?php endif; ?>
			</div>

		</div>
	</div>
</div>

<div class="duplicator-error-container"></div>
<?php
    $alert1 = new DUP_PRO_UI_Dialog();
    $alert1->title		= DUP_PRO_U::__('ERROR!');
    $alert1->message	= DUP_PRO_U::__('You can\'t exclude all sites.');
    $alert1->initAlert();
?>
<script>
jQuery(function($) 
{   
	/* METHOD: Toggle Archive file filter red icon */
	DupPro.Pack.ToggleFileFilters = function () 
	{
		var $filterItems = $('#dup-file-filter-items');
		if ($("#filter-on").is(':checked')) {
			$filterItems.removeAttr('disabled').css({color: '#000'});
			$('#filter-exts, #filter-dirs, #filter-files').removeAttr('readonly').css({color: '#000'});
			$('#dup-archive-filter-file').show();
		} else {
			$filterItems.attr('disabled', 'disabled').css({color: '#999'});
			$('#filter-dirs, #filter-exts, #filter-files').attr('readonly', 'readonly').css({color: '#999'});
			$('#dup-archive-filter-file').hide();
		}
	};

	DupPro.Pack.ExportOnlyDB = function ()
	{
		$('#dup-exportdb-items-off, #dup-exportdb-items-checked').hide();
		if ($("#export-onlydb").is(':checked')) {
			$('#dup-exportdb-items-checked').show();
			$('#dup-archive-db-only').show(100);
			$('#dup-archive-filter-db').hide();
			$('#dup-archive-filter-file').hide();
		} else {
			$('#dup-exportdb-items-off').show();
			$('#dup-exportdb-items-checked').hide();
			$('#dup-archive-db-only').hide();
			DupPro.Pack.ToggleFileFilters();
		}

		DupPro.Pack.ToggleDBFilters();
	};

	/* METHOD: Toggle Database table filter red icon */
	DupPro.Pack.ToggleDBFilters = function () 
	{
		var $filterItems = $('#dup-db-filter-items');

		if ($("#dbfilter-on").is(':checked')) {
			$filterItems.removeAttr('disabled').css({color: '#000'});
			$('#dup-dbtables input').removeAttr('readonly').css({color: '#000'});
			$('#dup-archive-filter-db').show();
		} else {
			$filterItems.attr('disabled', 'disabled').css({color: '#999'});
			$('#dup-dbtables input').attr('readonly', 'readonly').css({color: '#999'});
			$('#dup-archive-filter-db').hide();
		}
	};

	/* METHOD: Formats file directory path name on seperate line of textarea */
	DupPro.Pack.AddExcludePath = function (path) 
	{
		var text = $("#filter-dirs").val() + path + ';\n';
		$("#filter-dirs").val(text);
		DupPro.Pack.CountFilters();
	};

	/*	Appends a path to the extention filter  */
	DupPro.Pack.AddExcludeExts = function (path) 
	{
		var text = $("#filter-exts").val() + path + ';';
		$("#filter-exts").val(text);
	};

	DupPro.Pack.AddExcludeFilePath = function (path) 
	{
		var text = $("#filter-files").val() + path + '/file.ext;\n';
		$("#filter-files").val(text);
		DupPro.Pack.CountFilters();
	};

	DupPro.Pack.ExcludeTable = function (check) 
	{
		var $cb = $(check);
		if ($cb.is(":checked")) {
			$cb.closest("label").css('textDecoration', 'line-through');
		} else {
			$cb.closest("label").css('textDecoration', 'none');
		}
	}

	DupPro.Pack.CountFilters = function()
    {
		 var dirCount = $("#filter-dirs").val().split(";").length - 1;
		 var fileCount = $("#filter-files").val().split(";").length - 1;
		 $("#filter-dirs-count").html(' (' + dirCount + ')');
		 $("#filter-files-count").html(' (' + fileCount + ')');
	}
 });
 
//INIT
jQuery(document).ready(function($) 
{
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

	$("#filter-dirs").keyup(function()  {DupPro.Pack.CountFilters();});
	$("#filter-files").keyup(function() {DupPro.Pack.CountFilters();});

});
</script>
