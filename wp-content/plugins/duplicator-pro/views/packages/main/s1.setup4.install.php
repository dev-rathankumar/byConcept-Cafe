<?php defined("ABSPATH") or die(""); ?>

<style>
	 /*INSTALLER: Area*/
    div.dpro-install-hdr-2 {font-weight:bold; border-bottom:1px solid #dfdfdf; padding-bottom:2px; width:100%; margin-bottom:8px}
	tr.dpro-install-hdr-2 td:first-child {font-weight:bold;}
	tr.dpro-install-hdr-2 td {border-bottom:1px solid #dfdfdf; padding-bottom:2px;}
    label.chk-labels {display:inline-block; margin-top:1px}
    table.dpro-install-tbl {width:98%;}
	table.dpro-install-setup {width:100%}
	table.dpro-install-setup tr{vertical-align: top}

	div.secure-pass-area {display:none}
	input#secure-pass{width:300px; margin: 3px 0 5px 0}
	label.secure-pass-lbl {display:inline-block; width:125px}
	div#dpro-pack-installer-panel div.tabs-panel{min-height:150px}
    div.dpro-panel-optional-txt {color:maroon}
	div#dpro-pass-toggle {position: relative; margin:8px 0 0 0; width:243px}
	input#secure-pass {border-radius:4px 0 0 4px; width:217px; height: 23px; min-height: auto; margin:0; padding: 0 4px;}
	button.pass-toggle {height: 23px; width: 27px; position:absolute; top:0px; right:0px; border:1px solid silver; border-radius:0 4px 4px 0; cursor:pointer}
	span#dpro-install-secure-lock {color:#A62426; display:none; font-size:14px}
	span#dpro-install-secure-unlock {color:#A62426; display:none; font-size:14px}
</style>

<!-- ===================
INSTALLER -->
<div class="dup-box">
<div class="dup-box-title">
	<i class="fa fa-bolt fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Installer') ?>
	<span id="dpro-install-secure-lock" title="<?php DUP_PRO_U::esc_attr_e('Installer password protection is on') ?>"><i class="fa fa-lock fa-sm"></i> </span>
	<span id="dpro-install-secure-unlock" title="<?php DUP_PRO_U::esc_attr_e('Installer password protection is off') ?>"><i class="fa fa-unlock fa-sm"></i> </span>
	<div class="dup-box-arrow"></div>
</div>		
<div class="dup-box-panel" id="dpro-pack-installer-panel" style="<?php echo esc_attr($ui_css_installer); ?>">
	<div class="dpro-panel-optional-txt">
		<b><?php DUP_PRO_U::esc_html_e('All values in this section are'); ?> <u><?php DUP_PRO_U::esc_html_e('optional'); ?></u></b>
		<i class="fas fa-question-circle fa-sm"
			data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Setup/Prefills"); ?>"
			data-tooltip="<?php DUP_PRO_U::esc_attr_e('All values in this section are OPTIONAL! If you know ahead of time the database input fields the installer will use, '
				. 'then you can optionally enter them here and they will be prefilled at install time.  Otherwise you can just enter them in at install time and ignore '
				. 'all these options in the Installer section.'); ?>"></i>
	</div>

	<table class="dpro-install-setup" style="margin-top:-10px">
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
					<select name="brand" id="brand">
						<?php
						$active_brand_id = 0;
						foreach ($brands as $i=>$brand) :
							if($brand->active) $active_brand_id = $brand->id;
						?>
							<option value="<?php echo $brand->id; ?>" title="<?php echo esc_attr($brand->notes); ?>"<?php echo $brand->active ? ' selected' : ''; ?>><?php echo esc_html($brand->name); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
					if(is_multisite()) {
						$preview_url = array(
							network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default", (DUP_PRO_U::is_ssl() ? 'https' : 'http') ),
							network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=".intval($active_brand_id), (DUP_PRO_U::is_ssl() ? 'https' : 'http') )
						);
					} else {
						$preview_url = array(
							get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default" ),
							get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=".intval($active_brand_id) )
						);
					}
					?>
					<a href="<?php echo $preview_url[$active_brand_id > 0 ? 1 : 0]; ?>" target="_blank" class="button" id="brand-preview"><?php DUP_PRO_U::esc_html_e("Preview"); ?></a> &nbsp;
					<i class="fas fa-question-circle fa-sm"
					   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Choose Brand:"); ?>"
					   data-tooltip="<?php DUP_PRO_U::esc_attr_e('This option changes the branding of the installer file.  Click the preview button to see the selected style.'); ?>"></i>
				<?php else : ?>
					<a href="admin.php?page=duplicator-pro-settings&tab=package&sub=brand"><?php DUP_PRO_U::esc_html_e("Enable Branding"); ?></a>
				<?php endif; ?>
				<br/><br/>
			</td>
		</tr>
		<tr>
			<td><b><?php DUP_PRO_U::esc_html_e("Security") ?></b></td>
			<td>
				<?php
					$dup_install_secure_pass = isset($Package->Installer->OptsSecurePass) ? $Package->Installer->OptsSecurePass : '';
				?>
				<input type="checkbox" name="secure-on" id="secure-on" onclick="DupPro.Pack.EnableInstallerPassword()" />
				<label for="secure-on"><?php DUP_PRO_U::esc_html_e("Enable Password Protection") ?></label>
				<i class="fas fa-question-circle fa-sm"
				   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Security:"); ?>"
				   data-tooltip="<?php DUP_PRO_U::esc_attr_e('Enabling this option will allow for basic password protection on the installer. Before running the installer the '
							   . 'password below must be entered before proceeding with an install.  This password is a general deterrent and should not be substituted for properly '
							   . 'keeping your files secure.  Be sure to remove all installer files when the install process is completed.'); ?>"></i>
				<br/>

				<div id="dpro-pass-toggle">
					<input type="password" name="secure-pass" id="secure-pass" required="required" value="<?php echo $dup_install_secure_pass; ?>" />
					<button type="button" id="secure-btn" class="pass-toggle" onclick="DupPro.Pack.ToggleInstallerPassword()" title="<?php DUP_PRO_U::esc_attr_e('Show/Hide Password'); ?>"><i class="fas fa-eye fa-sm"></i></button>
				</div>
				<br/>
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
	BASIC/CPANEL TABS -->
	<div data-dpro-tabs="true">
		<ul>
			<li><?php DUP_PRO_U::esc_html_e('Basic') ?></li>
			<li id="dpro-cpnl-tab-lbl"><?php DUP_PRO_U::esc_html_e('cPanel') ?></li>
		</ul>

		<!-- ===================
		TAB1: Basic -->
		<div>
			<table class="dpro-install-tbl" id="s1-installer-dbbasic">
				<tr class="dpro-install-hdr-2">
					<td><?php DUP_PRO_U::esc_html_e("MySQL Server") ?></td>
					<td colspan="2" style="text-align: right">
						<a href="javascript:void(0)" onclick="DupPro.Pack.ApplyDataCurrent('s1-installer-dbbasic')">[use current]</a>
					</td>
				</tr>
				<tr>
					<td style="width:130px"><?php DUP_PRO_U::esc_html_e("Host") ?></td>
					<td><input type="text" name="dbhost" id="dbhost" maxlength="200" placeholder="<?php DUP_PRO_U::esc_html_e("example: localhost (value is optional)") ?>" data-current="<?php echo DB_HOST ?>"/></td>
				</tr>
				<tr>
					<td><?php DUP_PRO_U::esc_html_e("Database") ?></td>
					<td><input type="text" name="dbname" id="dbname" maxlength="100" placeholder="<?php DUP_PRO_U::esc_attr_e("example: DatabaseName (value is optional)") ?>" data-current="<?php echo DB_NAME ?>" /></td>
				</tr>
				<tr>
					<td><?php DUP_PRO_U::esc_html_e("User") ?></td>
					<td><input type="text" name="dbuser" id="dbuser" maxlength="100" placeholder="<?php DUP_PRO_U::esc_attr_e("example: DatabaseUser (value is optional)") ?>" data-current="<?php echo DB_USER ?>"/></td>
				</tr>
			</table>
		</div>

		<!-- ===================
		TAB2: cPanel -->
		<div>
			<table class="dpro-install-tbl">
				<tr>
					<td colspan="2"><div class="dpro-install-hdr-2"><?php DUP_PRO_U::esc_html_e("cPanel Login") ?></div></td>
				</tr>
				<tr>
					<td style="width:130px"><?php DUP_PRO_U::esc_html_e("Automation") ?></td>
					<td>
						<input type="checkbox" name="cpnl-enable" id="cpnl-enable" />
						<label for="cpnl-enable"><?php DUP_PRO_U::esc_html_e("Auto Select cPanel") ?></label>
						<i class="fas fa-question-circle fa-sm"
							data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Auto Select cPanel:"); ?>"
							data-tooltip="<?php DUP_PRO_U::esc_attr_e('Enabling this options will automatically select the cPanel tab when step one of the installer is shown.'); ?>">
						</i>
					</td>
				</tr>
				<tr>
					<td><?php DUP_PRO_U::esc_html_e("Host") ?></td>
					<td><input type="text" name="cpnl-host" id="cpnl-host"  maxlength="200" placeholder="<?php DUP_PRO_U::esc_attr_e("example: cpanelHost (value is optional)") ?>"/></td>
				</tr>
				<tr>
					<td><?php DUP_PRO_U::esc_html_e("User") ?></td>
					<td><input type="text" name="cpnl-user" id="cpnl-user" maxlength="200" placeholder="<?php DUP_PRO_U::esc_attr_e("example: cpanelUser (value is optional)") ?>"/></td>
				</tr>
			</table><br/>

			<table class="dpro-install-tbl" id="s1-installer-dbcpanel">
				<tr class="dpro-install-hdr-2">
					<td><?php DUP_PRO_U::esc_html_e("MySQL Server") ?></td>
					<td colspan="2" style="text-align: right">
						<a href="javascript:void(0)" onclick="DupPro.Pack.ApplyDataCurrent('s1-installer-dbcpanel')">[use current]</a>
					</td>
				</tr>
				<tr>
					<td style="width:130px"><?php DUP_PRO_U::esc_html_e("Action") ?></td>
					<td>
						<select name="cpnl-dbaction" id="cpnl-dbaction">
							<option value="create">Create A New Database</option>
							<option value="empty">Connect and Delete Any Existing Data</option>
							<option value="rename">Connect and Backup Any Existing Data</option>
							<option value="manual">Manual SQL Execution (Advanced)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width:130px"><?php DUP_PRO_U::esc_html_e("Host") ?></td>
					<td><input type="text" name="cpnl-dbhost" id="cpnl-dbhost" maxlength="200" placeholder="<?php DUP_PRO_U::esc_attr_e("example: localhost (value is optional)") ?>" data-current="<?php echo esc_html(DB_HOST); ?>"/></td>
				</tr>
				<tr>
					<td><?php DUP_PRO_U::esc_html_e("Database") ?></td>
					<td><input type="text" name="cpnl-dbname" id="cpnl-dbname" data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" maxlength="100" placeholder="<?php DUP_PRO_U::esc_attr_e("example: DatabaseName (value is optional)") ?>" data-current="<?php echo esc_html(DB_NAME); ?>"/></td>
				</tr>
				<tr>
					<td><?php DUP_PRO_U::esc_html_e("User") ?></td>
					<td><input type="text" name="cpnl-dbuser" id="cpnl-dbuser" data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" maxlength="100" placeholder="<?php DUP_PRO_U::esc_attr_e("example: DatabaseUserName (value is optional)") ?>" data-current="<?php echo esc_html(DB_USER); ?>" /></td>
				</tr>
			</table>

		</div>
	</div><br/>

	<small><?php DUP_PRO_U::esc_html_e("Additional inputs can be entered at install time.") ?></small>
	<br/><br/>
</div>		
</div><br/>

<script>
(function($) {
	DupPro.Pack.ApplyDataCurrent = function(id) 
	{
		$('#' + id + ' input').each(function() 
		{
			var attr = $(this).attr('data-current');
			if (typeof attr !== typeof undefined && attr !== false) {
				$(this).attr('value', $(this).attr('data-current'));
			}
		});
	};

	DupPro.Pack.EnableInstallerPassword = function ()
	{
		var $button =  $('#secure-btn');
		if ($('#secure-on').is(':checked')) {
			$('#secure-pass').attr('readonly', false);
			$('#secure-pass').attr('required', 'true').focus();
			$('#dpro-install-secure-lock').show();
			$('#dpro-install-secure-unlock').hide();
			$button.removeAttr('disabled');
		} else {
			$('#secure-pass').removeAttr('required');
			$('#secure-pass').attr('readonly', true);
			$('#dpro-install-secure-lock').hide();
			$('#dpro-install-secure-unlock').show();
			$button.attr('disabled', 'true');
		}
	};

	DupPro.Pack.ToggleInstallerPassword = function()
	{
		var $input  = $('#secure-pass');
		var $button =  $('#secure-btn');
		if (($input).attr('type') == 'text') {
			$input.attr('type', 'password');
			$button.html('<i class="fas fa-eye fa-sm"></i>');
		} else {
			$input.attr('type', 'text');
			$button.html('<i class="fas fa-eye-slash fa-sm"></i>');
		}
	}

<?php if($is_freelancer_plus) : ?>
    // brand-preview
    var $brand = $("#brand"),
        brandCheck = function(e){
            var $this = $(this) || $brand,
                $id = $this.val(),
                <?php if(is_multisite()) : ?>
                $url = [
                    '<?php echo network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default", (DUP_PRO_U::is_ssl() ? 'https' : 'http') ); ?>',
                    '<?php echo network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=", (DUP_PRO_U::is_ssl() ? 'https' : 'http') ); ?>' + $id
                ];
                <?php else: ?>
                $url = [
                    '<?php echo get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=default" ); ?>',
                    '<?php echo get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&view=edit&action=edit&id=" ); ?>' + $id
                ];
                <?php endif; ?>
            $("#brand-preview").attr( 'href', $url[ $id > 0 ? 1 : 0 ] );

            $this.find('option[value="'+ $id +'"]')
                .attr('selected', 'selected')
                .parent();
        };
    $brand.on('select change', brandCheck);
<?php endif; ?>


}(window.jQuery));

//INIT
jQuery(document).ready(function ()
{
	//DupPro.Pack.ToggleInstallerPassword();
	//DupPro.Pack.EnableInstallerPassword();
});

</script>
