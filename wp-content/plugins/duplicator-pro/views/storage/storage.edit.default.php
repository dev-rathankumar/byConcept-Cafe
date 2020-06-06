<?php
defined("ABSPATH") or die("");
DUP_PRO_U::hasCapability('export');

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.global.entity.php');

global $wp_version;
global $wpdb;

$global = DUP_PRO_Global_Entity::get_instance();
$storage_obj = DUP_PRO_Storage_Entity::get_default_local_storage();

$nonce_action = 'duppro-default-storage-edit';
$was_updated = false;

if (isset($_REQUEST['action'])) {
	DUP_PRO_U::verifyNonce($_POST['_wpnonce'], $nonce_action);
	if ($_REQUEST['action'] == 'save') {
		$gdrive_error_message			 = NULL;
		$global->max_default_store_files = (int) $_REQUEST['max_default_store_files'];
		$global->purge_default_package_record = isset($_REQUEST['purge_default_package_record']);

		$global->save();

		$local_folder_created		 = false;
		$local_folder_creation_error = false;
		$was_updated		 = true;
		$edit_create_text	 = DUP_PRO_U::__('Edit Default');
	}
}
?>

<style>
    #dup-storage-form input[type="text"], input[type="password"] { width: 250px;}
	#dup-storage-form input#name {width:100%; max-width: 500px}
	#dup-storage-form input#_local_storage_folder {width:100% !important; max-width: 500px}
	td.dpro-sub-title {padding:0; margin: 0}
	td.dpro-sub-title b{padding:20px 0; margin: 0; display:block; font-size:1.25em;}
	input#max_default_store_files {width:50px !important}
</style>

<?php 
	if ($was_updated) {
	$update_message = 'Default Storage Provider Updated';
	echo "<div class='notice notice-success is-dismissible dpro-wpnotice-box'><p>{$update_message}</p></div>";
}
?>
 <!-- ====================
TOOL-BAR -->
<table class="dpro-edit-toolbar">
	<tr>
		<td></td>
		<td>
			<div class="btnnav">
				<a href="<?php echo esc_url($storage_tab_url); ?>" class="button"> <i class="fas fa-database fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Providers'); ?></a>
				<span><?php DUP_PRO_U::esc_html_e('Edit Default Storage'); ?></span>
			</div>
		</td>
	</tr>
</table>
<hr class="dpro-edit-toolbar-divider"/>
	 
<form id="dpro-default-storage-form" action="<?php echo esc_url($edit_default_storage_url); ?>" method="post" data-parsley-ui-enabled="true">
    <?php wp_nonce_field($nonce_action); ?>
    <input type="hidden" id="dup-storage-form-action" name="action" value="save">
 
    <table class="provider form-table">	
		<tr valign="top">
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Name"); ?></label></th>
            <td>
				<?php DUP_PRO_U::esc_html_e('Default'); ?>
				<i class="fas fa-question-circle fa-sm"
				   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Default Storage Type:"); ?>"
				   data-tooltip="<?php DUP_PRO_U::esc_attr_e('The "Default" storage type is a built in type that cannot be removed.  This storage type is used by default should '
					   . 'no other storage types be available.  This storage type is always stored to the local server.'); ?>">
				</i>
			</td>
        </tr>	
        <tr valign="top">
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Type"); ?></label></th>
            <td><?php DUP_PRO_U::esc_html_e('Local Server'); ?></td>
        </tr>	
        <tr valign="top">
            <th scope="row"><label><?php DUP_PRO_U::esc_html_e("Location"); ?></label></th>
            <td><?php echo esc_html($storage_obj->local_storage_folder); ?></td>
        </tr>			
        <tr>
            <th scope="row"><label for=""><?php DUP_PRO_U::esc_html_e("Max Packages"); ?></label></th>
            <td>
                <label for="max_default_store_files">
					<input data-parsley-errors-container="#max_default_store_files_error_container" id="max_default_store_files" name="max_default_store_files" type="text" data-parsley-type="number" data-parsley-min="0" data-parsley-required="true" value="<?php echo intval($global->max_default_store_files); ?>" maxlength="4">&nbsp;
					<?php DUP_PRO_U::esc_html_e("Number of packages to keep in folder. "); ?> <br/>
					<i><?php DUP_PRO_U::esc_html_e("When this limit is exceeded, the oldest package will be deleted. Set to 0 for no limit."); ?></i>
				</label>
                <div id="max_default_store_files_error_container" class="duplicator-error-container"></div>
            </td>
        </tr>
		<tr>
			<th scope="row"><label for=""></label></th>
			<td>
				<label for="purge_default_package_record">
				<input name="purge_default_package_record" <?php DUP_PRO_UI::echoChecked($global->purge_default_package_record); ?> class="checkbox" value="1" type="checkbox" id="purge_default_package_record" >
				<i><?php DUP_PRO_U::esc_html_e("Delete associated package record when Max Packages limit is exceeded."); ?></i></label>
			</td>
		</tr>
    </table>

    <br style="clear:both" />
    <button class="button button-primary" type="submit"><?php DUP_PRO_U::esc_html_e('Save Provider'); ?></button>
</form>

<script>
    jQuery(document).ready(function ($) 
	{
		$('#dpro-default-storage-form').parsley();  
    });
</script>
