<?php
/* @var $global DUP_PRO_Global_Entity */

defined("ABSPATH") or die("");
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.storage.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.package.template.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.brand.entity.php');

$active_brand = DUP_PRO_Brand_Entity::get_active();
$is_freelancer_plus = (DUP_PRO_License_U::getLicenseType() >= DUP_PRO_License_Type::Freelancer);

global $wpdb;

//POST BACK
$action_updated = null;
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'duplicator_pro_package_active' : $action_response = DUP_PRO_U::__('Package settings have been reset.');
			break;
	}
}

DUP_PRO_U::initStorageDirectory();

$manual_template = DUP_PRO_Package_Template_Entity::get_manual_template();

$dup_tests = array();
$dup_tests = DUP_PRO_Server::getRequirments();
if ($dup_tests['Success'] != true) {
    DUP_PRO_LOG::traceObject('Requirements', $dup_tests);
}

$default_name1 = DUP_PRO_Package::get_default_name();
$default_name2 = DUP_PRO_Package::get_default_name(false);
$default_notes = $manual_template->notes;

$view_state = DUP_PRO_UI_ViewState::getArray();
$ui_css_storage = (isset($view_state['dup-pack-storage-panel']) && $view_state['dup-pack-storage-panel']) ? 'display:block' : 'display:none';
$ui_css_archive = (isset($view_state['dup-pack-archive-panel']) && $view_state['dup-pack-archive-panel']) ? 'display:block' : 'display:none';
$ui_css_installer = (isset($view_state['dpro-pack-installer-panel']) && $view_state['dpro-pack-installer-panel']) ? 'display:block' : 'display:none';

$storage_list = DUP_PRO_Storage_Entity::get_all();
$storage_list_count = count($storage_list);
$dup_intaller_files = implode(", ", array_keys(DUP_PRO_Server::getInstallerFiles()));

$global = DUP_PRO_Global_Entity::get_instance();
$dbbuild_mode = DUP_PRO_DB::getBuildMode();
?>

<style>
	/* -----------------------------
    PACKAGE OPTS*/
	form#dup-form-opts {margin-top:10px}
    form#dup-form-opts label {line-height:22px}
    form#dup-form-opts input[type=checkbox] {margin-top:3px}
    form#dup-form-opts textarea, input[type="text"], input[type="password"] {width:100%}
	input#package-name {padding:4px;  height: 2em; font-size: 1.2em;  line-height: 100%; width: 100%;   margin: 0 0 3px;}
	select#template_id {padding: 2px; height:2em; font-size: 1.2em;  line-height: 130%; width: 100%;  }
	label.lbl-larger {font-size:1.2em !important; font-weight: bold;}
    textarea#package-notes {height:75px;}
	div.dup-notes-add {float:right; margin:0;}
	div#dup-notes-area {display:none;}
	select#template_id {width:100%; max-width: none; margin-bottom:4px}
	div.dpro-general-area {line-height:27px; margin:0 0 5px 0}
	div#dpro-template-specific-area table td:first-child {width:100px; font-weight: bold}
	div.dup-box {margin:12px 0 0 0}

	div#dpro-da-notice {padding:10px; line-height:20px; font-size: 14px}
	div#dpro-da-notice-info {display:none; padding: 5px}

	/*TABS*/
	ul.add-menu-item-tabs li, ul.category-tabs li {padding:3px 30px 5px}
</style>

<!-- TODO:
	- The "Switch Now" button should ajax post to DUP_PRO_CTRL_Package::switchDupArchiveNotice() and then refresh page and hide notice
	- The "Hide this Notice" should ajax post to DUP_PRO_CTRL_Package::switchDupArchiveNotice() and then jQuery hide notice.
	- See DUP_PRO_CTRL_Package::addQuickFilters for example
	- Update wording
-->
<?php if (isset($global->notices) && ($global->notices->dupArchiveSwitch) && ($global->archive_build_mode !== DUP_PRO_Archive_Build_Mode::DupArchive)) :?>
	<div class="notice notice-info is-dismissible">
		<div id="dpro-da-notice">
			<i class="fa fa-info-circle fa-lg"></i>
			<?php DUP_PRO_U::esc_html_e('Switch to the new \'DupArchive\' archive format for improved performance!') ?>
			<a href="javascript:void(0)" onClick="jQuery('#dpro-da-notice-info').toggle(); return false;">[<?php DUP_PRO_U::esc_html_e('more details...'); ?>]</a><br/>

			<div id="dpro-da-notice-info">
				<?php DUP_PRO_U::esc_html_e("DupArchive is a custom archive engine designed to be faster, more stable and build larger packages on budget hosts.  A DupArchive file "
					. "ends in '.daf' rather than '.zip' and works the same as a zip when installing.  The 'daf' archive requires the installer.php for extraction and only the installer.php "
					. "file will work to extract it.  Should you decide to switch back to the old zip format after trying DupArchive, you can easily do so by "
					. "going to the Settings &gt; Packages options.") ?>
			</div>
			<div style="padding: 5px">
				<a href='javascript:void(0)' class='button button-small' onClick='DupPro.Pack.HandleDupArchiveNotice(true);'><?php DUP_PRO_U::esc_html_e("Switch Now!") ?></a>
				<a href='javascript:void(0)' class='button button-small' onClick='DupPro.Pack.HandleDupArchiveNotice(false);'><?php DUP_PRO_U::esc_html_e("Hide This Notice") ?></a>
			</div>
		</div>
	</div>
<?php endif; ?>


<!-- ====================
TOOL-BAR -->
<table class="dpro-edit-toolbar">
	<tr>
		<td>
			<div id="dup-wiz">
				<div id="dup-wiz-steps">
					<div class="active-step"><a>1-<?php DUP_PRO_U::esc_html_e('Setup'); ?></a></div>
					<div><a>2-<?php DUP_PRO_U::esc_html_e('Scan'); ?> </a></div>
					<div><a>3-<?php DUP_PRO_U::esc_html_e('Build'); ?> </a></div>
				</div>
				<div id="dup-wiz-title" style="white-space: nowrap">
					<?php DUP_PRO_U::esc_html_e('Step 1: Package Setup'); ?>
				</div>
			</div>
		</td>
		<td>
            <?php include('create.package.nav.buttons.php'); ?>			
		</td>
	</tr>
</table>
<hr class="dpro-edit-toolbar-divider"/>

<?php if (! empty($action_response)) : ?>
    <div id="message" class="notice notice-success"><p><?php echo $action_response; ?></p></div>
<?php endif; ?>

<?php require_once('s1.setup1.reqs.php'); ?>

<?php
$form_action_url = '?page=duplicator-pro&tab=packages&inner_page=new2&_wpnonce='.wp_create_nonce('new2-package');
?>
<form id="dup-form-opts" method="post" action="<?php echo $form_action_url;?>" data-parsley-validate data-parsley-ui-enabled="true" >
	<input type="hidden" id="dup-form-opts-action" name="action" value="">
	<div class="dpro-general-area">
		<div>
            <label class="lbl-larger">&nbsp;Apply Template:</label>
            <i class="fas fa-question-circle fa-sm"
                data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Apply Template:"); ?>"
                data-tooltip="<?php DUP_PRO_U::esc_attr_e('An optional template configuration that can be applied to this package setup. An [Unassigned] template will retain the settings from the last scan/build.'); ?>"></i>
            
            <div style="float:right">
                <a href="admin.php?page=duplicator-pro-tools&tab=templates" class="button button-small" title="<?php DUP_PRO_U::esc_attr_e("List All Templates") ?>" target="_blank">
                    <i class="far fa-clone"></i>
                </a>
                <a href="javascript:void(0)" onclick="DupPro.Pack.EditTemplate()"  class="button button-small" title="<?php DUP_PRO_U::esc_attr_e("Edit Selected Template") ?>">
                    <i class="fa fa-pen-square"></i>
                </a>
            </div>
        </div>
		
        <div>
            <select data-parsley-ui-enabled="false" onChange="DupPro.Pack.EnableTemplate();" name="template_id" id="template_id" >
                <option value="<?php echo intval($manual_template->id); ?>"><?php echo '[' . DUP_PRO_U::esc_html__('Unassigned') . ']' ?></option>
                <?php
                    $templates = DUP_PRO_Package_Template_Entity::get_all();
                    if (count($templates) == 0) {
                        $no_templates = __('No Templates');
                        echo "<option value='-1'>$no_templates</option>";
                    } else {
                        foreach ($templates as $template) {
                            echo "<option value='{$template->id}'>{$template->name}</option>";
                        }
                    }
                    ?>
            </select>
        </div>

        <div>
            <label for="package-name" class="lbl-larger">&nbsp;<?php DUP_PRO_U::esc_html_e('Name') ?>:</label>
            <a href="javascript:void(0)" onClick="DupPro.Pack.ResetName()" title="<?php DUP_PRO_U::esc_attr_e('Toggle a default name') ?>"><i class="fa fa-undo"></i></a>
            <div class="dup-notes-add">
                <a href="javascript:void(0)" onClick="jQuery('#dup-notes-area').toggle()" class="button button-small"  title="<?php DUP_PRO_U::esc_attr_e('Add Notes') ?>">
                    <i class="far fa-edit"></i>
                </a>
            </div>
        </div>
        <div>
            <input id="package-name"  name="package-name" type="text" maxlength="40"  required="true" data-regexp="^[0-9A-Za-z|_]+$" />
            <div id="dup-notes-area">
                <label class="lbl-larger">&nbsp;<?php DUP_PRO_U::esc_html_e('Notes') ?>:</label><br/>
                <textarea id="package-notes" name="package-notes" maxlength="300" /></textarea>
            </div>
        </div>
	</div>

	<?php
		require_once('s1.setup2.store.php');
		require_once('s1.setup3.archive.php');
		require_once('s1.setup4.install.php');
	?>

	<div class="dup-button-footer">
		<input type="button" value="<?php DUP_PRO_U::esc_attr_e("Reset") ?>" class="button button-large" <?php echo ($dup_tests['Success']) ? '' : 'disabled="disabled"'; ?> onClick="DupPro.Pack.ResetSettings()" />
		<input id="button-next" type="submit" onClick="DupPro.Pack.BeforeSubmit()" value="<?php DUP_PRO_U::esc_attr_e("Next") ?> &#9654;" class="button button-primary button-large" <?php echo ($dup_tests['Success']) ? '' : 'disabled="disabled"'; ?> />
	</div>
</form>

<!-- CACHE PROTECTION: If the back-button is used from the scanner page then we need to
refresh page in-case any filters where set while on the scanner page -->
<form id="cache_detection">
	<input type="hidden" id="cache_state" name="cache_state" value="" />
</form>
<?php
    $confirm1 = new DUP_PRO_UI_Dialog();
    $confirm1->title			 = DUP_PRO_U::__('Would you like to continue');
    $confirm1->message			 = DUP_PRO_U::__('This will clear all of the current package settings.');
    $confirm1->progressText      = DUP_PRO_U::__('Please Wait...');
    $confirm1->jsCallback		 = 'DupPro.Pack.ResetSettingsRun()';
    $confirm1->initConfirm();
?>
<script>
jQuery(function($)
{
    <?php
		$template_array = array();
		$templates = DUP_PRO_Package_Template_Entity::get_all(true);
		foreach ($templates as $template) {
			$template->installer_opts_secure_pass = base64_decode($template->installer_opts_secure_pass);
			$json = json_encode($template);
			$template_array[]="\t\t{$json}";
		}
		$template_array = join(",\r\n\r\n",$template_array);
		echo "var packageTemplates = [\r\n{$template_array}\r\n\t];";
    ?>


    DupPro.Pack.BeforeSubmit = function(){
        $('#mu-exclude option').each(function(){
            $(this).attr('selected',true);
        });
    };

    // Template-specific Functions
    DupPro.Pack.GetTemplateById = function (templateId)
    {
        for (var i = 0; i < packageTemplates.length; i++) {
            var currentTemplate = packageTemplates[i];
            if (currentTemplate.id == templateId) {
                    return currentTemplate;
            }
        }
        return null;
    };

    $.fn.selectOption = function(val){
        this.val(val)
            .find('option')
            .removeAttr('selected')
            .parent()
            .find('option[value="'+ val +'"]')
            .attr('selected', 'selected')
            .parent()
            .trigger('change');
        return this;
    };

    DupPro.Pack.PopulateCurrentTemplate = function ()
    {
        var selectedId = $('#template_id').val();
        var selectedTemplate = DupPro.Pack.GetTemplateById(selectedId);
        if (selectedTemplate != null)
        {
            var name = selectedTemplate.name;

            if(selectedTemplate.is_manual) {
                name = "<?php echo DUP_PRO_Package::get_default_name(); ?>";
            }

            $("#package-name").val(name);
            $("#package-notes").val(selectedTemplate.notes);

            $("#export-onlydb").prop("checked", selectedTemplate.archive_export_onlydb);
            $("#filter-on").prop("checked", selectedTemplate.archive_filter_on);

            $("#filter-dirs").val(selectedTemplate.archive_filter_dirs.split(";").join(";\n"));
            $("#filter-exts").val(selectedTemplate.archive_filter_exts);
            $("#filter-files").val(selectedTemplate.archive_filter_files.split(";").join(";\n"));
            $("#dbfilter-on").prop("checked", selectedTemplate.database_filter_on);
            DupPro.Pack.ExportOnlyDB();

            if (typeof selectedTemplate.filter_sites != 'undefined' && selectedTemplate.filter_sites.length > 0) {
                for(var i=0; i < selectedTemplate.filter_sites.length;i++){
                    var site_id = selectedTemplate.filter_sites[i];
                    var exclude_option = $('#mu-include').find("option[value="+site_id+"]").first();
                    console.log(exclude_option.html());
                    $("#mu-exclude").append(exclude_option.clone());
                    exclude_option.remove();
                }
            }

            //-- cPanel
            $("#cpnl-enable").prop("checked", selectedTemplate.installer_opts_cpnl_enable);
            $("#cpnl-host").val(selectedTemplate.installer_opts_cpnl_host);
            $("#cpnl-user").val(selectedTemplate.installer_opts_cpnl_user);

            $("#secure-on").prop("checked", selectedTemplate.installer_opts_secure_on);
            $("#skipscan").prop("checked", selectedTemplate.installer_opts_skip_scan);
			$("#secure-pass").val(selectedTemplate.installer_opts_secure_pass);
            $("#cpnl-dbaction").selectOption(selectedTemplate.installer_opts_cpnl_db_action);
            $("#cpnl-dbhost").val(selectedTemplate.installer_opts_cpnl_db_host);
            $("#cpnl-dbname").val(selectedTemplate.installer_opts_cpnl_db_name);
            $("#cpnl-dbuser").val(selectedTemplate.installer_opts_cpnl_db_user);

            //-- Brand
            let installer_opts_brand = <?php echo intval($active_brand->id); ?>,
                installer_opts_brand_id = [],
                x = 0;

            // most tricky thing - setup proper brand on emplate change
            if (typeof selectedTemplate.installer_opts_brand != 'undefined') {
                if(selectedTemplate.installer_opts_brand <= 0) {
                    installer_opts_brand = -2;
                } else if(selectedTemplate.installer_opts_brand > 0) {
                    installer_opts_brand = Number(selectedTemplate.installer_opts_brand);
                }
            }

            // find, fix deleted brands and setup default
            for (var i = 0; i < packageTemplates.length; i++) {
                if(
                    typeof packageTemplates[i].installer_opts_brand != 'undefined'
                    && null != packageTemplates[i].installer_opts_brand
                    && packageTemplates[i].installer_opts_brand != 0
                ) {
                    installer_opts_brand_id[x]=Number(packageTemplates[i].installer_opts_brand);
                    x++;
                }
            }
            if(installer_opts_brand_id.indexOf(installer_opts_brand) < 0) {
                installer_opts_brand = <?php echo intval($active_brand->id); ?>;
            }

            $("#brand").selectOption(installer_opts_brand);

            //-- Database
            var	filterTableKey;
            if ("" != selectedTemplate.database_filter_tables) {
                var databaseFilterTables = selectedTemplate.database_filter_tables.split(",");
                $("#dup-dbtables input").prop("checked", false).css('text-decoration', 'none');

                for (filterTableKey in databaseFilterTables) {
                        var filterTable = databaseFilterTables[filterTableKey];
                        var selector = "#dbtables-" + filterTable;
                        $(selector).prop("checked", true);
                        $(selector).parent().css('text-decoration', 'line-through');
                }
            }

            $("#dbhost").val(selectedTemplate.installer_opts_db_host);
            $("#dbname").val(selectedTemplate.installer_opts_db_name);
            $("#dbuser").val(selectedTemplate.installer_opts_db_user);

        } else {
            console.log("Template ID doesn't exist?? " + selectedId);
        }

        //Default to Installer cPanel tab if used
        $('#cpnl-enable').is(":checked") ? $('#dpro-cpnl-tab-lbl').trigger("click") : null;
    };


    DupPro.Pack.ResetSettings = function ()
    {
        <?php $confirm1->showConfirm(); ?>
    };

    DupPro.Pack.ResetSettingsRun = function(){
        $('#dup-form-opts')[0].reset();
        setTimeout(function(){tb_remove();}, 800);
    }

	DupPro.Pack.EditTemplate = function ()
	{
		var manualTemplateID = <?php echo empty($manual_template->id) ? 0 : $manual_template->id; ?>;
		var templateID = $('#template_id').val();
		var url;

		if (templateID <= 0 || templateID == manualTemplateID) {
			url = 'admin.php?page=duplicator-pro-tools&tab=templates';
		} else {
			url = 'admin.php?page=duplicator-pro-tools&tab=templates&inner_page=edit&package_template_id=' + templateID;
		}
		window.open(url, 'edit-template');
	};

});

//INIT
jQuery(document).ready(function ($) {
    var DPRO_NAME_LAST;
    var DPRO_NAME_DEFAULT1	= '<?php echo esc_js($default_name1); ?>';
    var DPRO_NAME_DEFAULT2	= '<?php echo esc_js($default_name2); ?>';

    DupPro.Pack.checkPageCache = function()
    {
        var $state = $('#cache_state');
        if( $state.val() == "" ) {
            $state.val("fresh-load");
        } else {
            $state.val("cached");
			<?php
                $redirect = admin_url('admin.php?page=duplicator-pro&tab=packages&inner_page=new1');
                $redirect = wp_nonce_url($redirect, 'new1-package');
				echo "window.location.href = '{$redirect}'";
			?>
        }
    }

    DupPro.Pack.EnableTemplate = function ()
    {
        $("#dup-form-opts-action").val('template-create');
        $('#dpro-template-specific-area').show(0);
        DupPro.Pack.PopulateCurrentTemplate();
        //DupPro.Pack.ToggleInstallerPassword();
		DupPro.Pack.EnableInstallerPassword();
        DupPro.Pack.ToggleFileFilters();
        DupPro.Pack.ToggleDBFilters();
    }

    DupPro.Pack.ResetName = function ()
    {
        var current = $('#package-name').val();
        switch (current) {
			case DPRO_NAME_LAST     : $('#package-name').val(DPRO_NAME_DEFAULT2); break;
			case DPRO_NAME_DEFAULT1 : $('#package-name').val(DPRO_NAME_LAST); break;
			case DPRO_NAME_DEFAULT2 : $('#package-name').val(DPRO_NAME_DEFAULT1); break;
			default:	$('#package-name').val(DPRO_NAME_LAST);
        }
    };

    DupPro.Pack.HandleDupArchiveNotice = function(switchToDupArchive)
    {
        var ajaxData = {
			action: 'DUP_PRO_CTRL_Package_switchDupArchiveNotice',
			nonce: '<?php echo wp_create_nonce('DUP_PRO_CTRL_Package_switchDupArchiveNotice'); ?>',
			enable_duparchive : switchToDupArchive
        };

        $.ajax({
			type: "POST",
			url: ajaxurl,
			data: ajaxData,
			beforeSend: function () {},
			success: function (respData, textStatus, xHr) {
                try {
                    var data = DupPro.parseJSON(respData);
                } catch(err) {
                    console.error(err);
                    console.error('JSON parse failed for response data: ' + respData);
                    console.log('switchDupArchiveNotice:AJAX error. textStatus=');
                    console.log(textStatus);
                    location.reload();
                    return false;
                }
                location.reload();
            },
			error: function (xHr, textStatus) {
				console.log('switchDupArchiveNotice:AJAX error. textStatus=');
				console.log(textStatus);
				location.reload();
			}
        });
    };

    DupPro.Pack.checkPageCache();
    DupPro.Pack.EnableTemplate();
    DupPro.Pack.ExportOnlyDB();
    DPRO_NAME_LAST  = $('#package-name').val();
	DupPro.Pack.CountFilters();
	$('input#package-name').focus().select();

});
</script>
