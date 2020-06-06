<?php
/* @var $global DUP_PRO_Global_Entity */
defined("ABSPATH") or die("");

DUP_PRO_U::hasCapability('manage_options');

$nonce_action		= 'duppro-settings-storage-edit';
$action_updated		= null;
$action_response	= DUP_PRO_U::__("Storage Settings Saved");

$global = DUP_PRO_Global_Entity::get_instance();
$global->configure_dropbox_transfer_mode();

//SAVE RESULTS
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
	DUP_PRO_U::verifyNonce($_POST['_wpnonce'], $nonce_action);
	$global->storage_htaccess_off           = isset($_REQUEST['_storage_htaccess_off']) ? 1 : 0;
	
	$global->ssl_useservercerts = isset($_REQUEST['ssl_useservercerts']) ? 1 : 0;
	$global->ssl_disableverify = isset($_REQUEST['ssl_disableverify']) ? 1 : 0;
	$global->ipv4_only = isset($_REQUEST['ipv4_only']) ? 1 : 0;

	$global->gdrive_upload_chunksize_in_kb  = (int) $_REQUEST['gdrive_upload_chunksize_in_kb'];
    $global->dropbox_upload_chunksize_in_kb = (int) $_REQUEST['dropbox_upload_chunksize_in_kb'];
    $global->dropbox_transfer_mode          = $_REQUEST['dropbox_transfer_mode'];
    $global->max_storage_retries            = (int) $_REQUEST['max_storage_retries'];
    $global->s3_upload_part_size_in_kb      = (int) $_REQUEST['s3_upload_part_size_in_kb'];

    $action_updated = $global->save();
}
?>

<form id="dup-settings-form" action="<?php echo self_admin_url('admin.php?page=' . DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG); ?>" method="post" data-parsley-validate>
<?php wp_nonce_field($nonce_action); ?>
<input type="hidden" name="action" value="save">
<input type="hidden" name="page"   value="<?php echo DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ?>">
<input type="hidden" name="tab"   value="storage">

<?php if ($action_updated) : ?>
	<div class="notice notice-success is-dismissible dpro-wpnotice-box"><p><?php echo $action_response; ?></p></div>
<?php endif; ?>	

<!-- ===============================
GENERAL SETTINGS -->
<h3 class="title"><?php DUP_PRO_U::esc_html_e("General") ?> </h3>
<hr size="1" />
<table class="form-table">            
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Storage"); ?></label></th>
		<td>
			<?php DUP_PRO_U::esc_html_e("Full Path"); ?>:
			<?php echo DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH); ?><br/><br/>
			<input type="checkbox" name="_storage_htaccess_off" id="_storage_htaccess_off" <?php DUP_PRO_UI::echoChecked($global->storage_htaccess_off); ?> />
			<label for="_storage_htaccess_off"><?php DUP_PRO_U::esc_html_e("Disable .htaccess File In Storage Directory") ?> </label>
			<p class="description">
				<?php DUP_PRO_U::esc_html_e("Disable if issues occur when downloading installer/archive files."); ?>
			</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Max Retries"); ?></label></th>
		<td>
			<input class="narrow-input"  type="text" name="max_storage_retries" id="max_storage_retries" data-parsley-required data-parsley-min="0" data-parsley-type="number" data-parsley-errors-container="#max_storage_retries_error_container" value="<?php echo $global->max_storage_retries; ?>" />
			<div id="max_storage_retries_error_container" class="duplicator-error-container"></div>
			<p class="description">
				<?php DUP_PRO_U::esc_html_e('Max upload/copy retries to attempt after failure encountered.'); ?>
			</p>
		</td>
	</tr>
</table>

<!-- ===============================
SSL SETTINGS -->
<h3 class="title"><?php DUP_PRO_U::esc_html_e("SSL") ?> </h3>
<hr size="1" />
<p class="description" style="color:maroon">
	<?php DUP_PRO_U::esc_html_e("Do not modify SSL settings unless you know the expected result or have talked to support."); ?>
</p>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Use server's SSL certificates"); ?></label></th>
		<td>
			<input type="checkbox" name="ssl_useservercerts" id="ssl_useservercerts" <?php echo DUP_PRO_UI::echoChecked($global->ssl_useservercerts); ?> />
			<p class="description">
				<?php
				DUP_PRO_U::esc_html_e("To use server's SSL certificates please enble it. By default Duplicator Pro uses By default uses its own store of SSL certificates to verify the identity of remote storage sites.");
				?>
			</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Disable verification of SSL certificates"); ?></label></th>
		<td>
			<input type="checkbox" name="ssl_disableverify" id="ssl_disableverify" <?php echo DUP_PRO_UI::echoChecked($global->ssl_disableverify); ?> />
			<p class="description">
				<?php
				DUP_PRO_U::esc_html_e("To disable verification of a host and the peer's SSL certificate.");
				?>
			</p>
		</td>
	</tr>

	
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Use IPv4 only"); ?></label></th>
		<td>
			<input type="checkbox" name="ipv4_only" id="ipv4_only" <?php echo DUP_PRO_UI::echoChecked($global->ipv4_only); ?> />
			<p class="description">
				<?php
				DUP_PRO_U::esc_html_e("To use IPv4 only, which can help if your host has a broken IPv6 setup (currently only supported by Google Drive)");
				?>
			</p>
		</td>
	</tr>
</table>

<!-- ===============================
GDRIVE SETTINGS -->
<h3 class="title"><?php DUP_PRO_U::esc_html_e("Google Drive") ?></h3>
<hr size="1" />
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Upload Size (KB)"); ?></label></th>
		<td>
			<input class="narrow-input" 
                   type="number"
                   min="256"
                   name="gdrive_upload_chunksize_in_kb"
                   id="gdrive_upload_chunksize_in_kb"
                   data-parsley-required
                   data-parsley-type="number"
                   data-parsley-errors-container="#gdrive_upload_chunksize_in_kb_error_container"
                   value="<?php echo esc_attr($global->gdrive_upload_chunksize_in_kb); ?>" />
			<div id="gdrive_upload_chunksize_in_kb_error_container" class="duplicator-error-container"></div>
			<p class="description">
				<?php DUP_PRO_U::esc_html_e('How much should be uploaded to Google Drive per attempt. Higher=faster but less reliable. It should be multiple of 256.'); ?>
			</p>
		</td>
	</tr>
</table>

<!-- ===============================
DROPBOX SETTINGS -->
<h3 class="title"><?php DUP_PRO_U::esc_html_e("Dropbox") ?> </h3>
<hr size="1" />
<table class="form-table">        
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Transfer Mode"); ?></label></th>
		<td>
			<input type="radio" value="<?php echo DUP_PRO_Dropbox_Transfer_Mode::Disabled ?>" name="dropbox_transfer_mode" value="mysql" id="dropbox_transfer_mode" <?php echo DUP_PRO_UI::echoChecked($global->dropbox_transfer_mode == DUP_PRO_Dropbox_Transfer_Mode::Disabled); ?> >
			<label for="dropbox_transfer_mode"><?php DUP_PRO_U::esc_html_e("Disabled"); ?></label> &nbsp;

			<input type="radio" <?php DUP_PRO_UI::echoDisabled(!DUP_PRO_Server::isCurlEnabled()) ?> value="<?php echo DUP_PRO_Dropbox_Transfer_Mode::cURL ?>" name="dropbox_transfer_mode" value="mysql" id="dropbox_transfer_mode" <?php echo DUP_PRO_UI::echoChecked($global->dropbox_transfer_mode == DUP_PRO_Dropbox_Transfer_Mode::cURL); ?>/>
			<label for="dropbox_transfer_mode">cURL</label> &nbsp;

			<input type="radio" <?php DUP_PRO_UI::echoDisabled(!DUP_PRO_Server::isURLFopenEnabled()) ?> value="<?php echo DUP_PRO_Dropbox_Transfer_Mode::FOpen_URL ?>" name="dropbox_transfer_mode" value="mysql" id="dropbox_transfer_mode" <?php echo DUP_PRO_UI::echoChecked($global->dropbox_transfer_mode == DUP_PRO_Dropbox_Transfer_Mode::FOpen_URL); ?>/>
			<label for="dropbox_transfer_mode">FOpen URL</label> &nbsp;
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Upload Size (KB)"); ?></label></th>
		<td>
			<input class="narrow-input" 
                   type="number"
                   min="100"
                   name="dropbox_upload_chunksize_in_kb"
                   id="dropbox_upload_chunksize_in_kb"
                   data-parsley-required
                   data-parsley-type="number"
                   data-parsley-errors-container="#dropbox_upload_chunksize_in_kb_error_container"
                   value="<?php echo esc_attr($global->dropbox_upload_chunksize_in_kb); ?>" />
			<div id="dropbox_upload_chunksize_in_kb_error_container" class="duplicator-error-container"></div>
			<p class="description">
				<?php DUP_PRO_U::esc_html_e('How much should be uploaded to Dropbox per attempt. Higher=faster but less reliable.'); ?>
			</p>
		</td>
	</tr>
</table>

<!-- ===============================
S3 SETTINGS -->
<h3 class="title"><?php DUP_PRO_U::esc_html_e("Amazon S3") ?></h3>
<hr size="1" />
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Upload Size (KB)"); ?></label></th>
		<td>
			<input class="narrow-input" 
                   type="number"
                   min="<?php echo DUP_PRO_S3_Client_UploadInfo::UPLOAD_PART_MIN_SIZE_IN_K; ?>"
                   max="5243000"
                   name="s3_upload_part_size_in_kb"
                   id="s3_upload_part_size_in_kb"
                   data-parsley-required
                   data-parsley-type="number"
                   data-parsley-errors-container="#s3_upload_chunksize_in_kb_error_container"
                   value="<?php echo esc_attr($global->s3_upload_part_size_in_kb); ?>" />
			<div id="s3_upload_chunksize_in_kb_error_container" class="duplicator-error-container"></div>
			<p class="description">
				<?php DUP_PRO_U::esc_html_e('How much should be uploaded to Amazon S3 per attempt. Higher=faster but less reliable.'); ?>
                <?php echo esc_html(sprintf(DUP_PRO_U::__('Min size %skb.') , DUP_PRO_S3_Client_UploadInfo::UPLOAD_PART_MIN_SIZE_IN_K)); ?>
			</p>
		</td>
	</tr>
</table>

<p class="submit dpro-save-submit">
	<input type="submit" name="submit" id="submit" class="button-primary" value="<?php DUP_PRO_U::esc_attr_e('Save Storage Settings') ?>" style="display: inline-block;" />
</p>
</form>
