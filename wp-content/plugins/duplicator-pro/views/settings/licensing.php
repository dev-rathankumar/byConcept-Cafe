<?php
/* @var $global DUP_PRO_Global_Entity */
defined("ABSPATH") or die("");

DUP_PRO_U::hasCapability('manage_options');

$global	 = DUP_PRO_Global_Entity::get_instance();
$sglobal = DUP_PRO_Secure_Global_Entity::getInstance();

$force_refresh	 = true;
$nonce_action	 = 'duppro-settings-licensing-edit';
$error_response	 = null;
$action_response = null;
$license_activation_response = null;
$is_localhost = strstr($_SERVER['HTTP_HOST'], 'localhost');

//SAVE RESULTS
if (isset($_POST['action'])) {
	$action = sanitize_text_field($_POST['action']);
	switch ($action) {
		case 'activate':
			
			DUP_PRO_U::verifyNonce($_POST['_wpnonce'], 'duplicator-pro-licence');

            /**
                 * If license isn't visible input is always disabled
                 */
            if ($global->license_key_visible)  {
                $submitted_license_key = trim($_REQUEST['_license_key']);
            } else {
                $submitted_license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME);
            }

			if (DUP_PRO_License_U::isValidOvrKey($submitted_license_key)) {
				DUP_PRO_License_U::setOvrKey($submitted_license_key);
			} else {
				if (preg_match('/^[a-f0-9]{32}$/i', $submitted_license_key)) {

					update_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, $submitted_license_key);
					$license_activation_response = DUP_PRO_License_U::changeLicenseActivation(true);

					switch ($license_activation_response) {
						case DUP_PRO_License_Activation_Response::OK:
							$action_response = DUP_PRO_U::__("License Activated");
							break;

						case DUP_PRO_License_Activation_Response::POST_ERROR:
							$error_response = sprintf(DUP_PRO_U::__("Cannot communicate with snapcreek.com. Please see <a target='_blank' href='%s'>this FAQ entry</a> for possible causes and resolutions."),
								'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-005-q');
							break;

						case DUP_PRO_License_Activation_Response::INVALID_RESPONSE:
						default:
							$error_response = DUP_PRO_U::__('Error activating license.');
							break;
					}
				} else {
					$error_response = DUP_PRO_U::__('Please enter a valid key. Key should be 32 characters long.');
				}
			}
			break;

		case 'deactivate':
		case 'clear_key' :
			DUP_PRO_U::verifyNonce($_POST['_wpnonce'], 'duplicator-pro-licence');

			$license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME);

			if (DUP_PRO_License_U::isValidOvrKey($license_key)) {
				// Reset license key otherwise will be artificially stuck on as valid
				update_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');
			} else {
				$license_activation_response = DUP_PRO_License_U::changeLicenseActivation(false);

				switch ($license_activation_response) {
					case DUP_PRO_License_Activation_Response::OK:
						$action_response = DUP_PRO_U::__("License Deactivated");
						break;

					case DUP_PRO_License_Activation_Response::POST_ERROR:
						$error_response = sprintf(DUP_PRO_U::__("Cannot communicate with snapcreek.com. Please see <a target='_blank' href='%s'>this FAQ entry</a> for possible causes and resolutions."),
							'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-005-q');
						break;

					case DUP_PRO_License_Activation_Response::INVALID_RESPONSE:
					default:
						$error_response = DUP_PRO_U::__('Error deactivating license.');
						break;
				}
			}


			if ($action == 'clear_key') {
				update_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');

                $global->license_key_visible = true;
                $sglobal->lkp				 = '';

                $global->save();
                $sglobal->save();
			}

            

			break;

		case 'hide_key':

			DUP_PRO_U::verifyNonce($_POST['_wpnonce'], 'duplicator-pro-licence');

			$password				 = sanitize_text_field($_POST['_key_password']);
			$password_confirmation	 = sanitize_text_field($_POST['_key_password_confirmation']);

			if (empty($password)) {
				$error_response = DUP_PRO_U::__('Password cannot be empty.');
			} else {
				if ($password == $password_confirmation) {
					$global->license_key_visible = false;
					$sglobal->lkp				 = $password;
					$global->save();
					$sglobal->save();
					$action_response = DUP_PRO_U::__("Key now hidden.");
				} else {
					$error_response = DUP_PRO_U::__("Passwords don't match.");
				}
			}
			break;

		case 'show_key':

			DUP_PRO_U::verifyNonce($_POST['_wpnonce'], 'duplicator-pro-licence');
			$password = sanitize_text_field($_POST['_key_password']);

			if ($password == $sglobal->lkp) {
				$global->license_key_visible = true;
				$sglobal->lkp				 = '';

				$global->save();
				$sglobal->save();

				$action_response = DUP_PRO_U::__("Key now visible.");
			} else {
				$error_response = DUP_PRO_U::__("Wrong password entered. Key remains hidden.");
			}

			break;
	}

	$force_refresh = true;
}

$license_status			 = DUP_PRO_License_U::getLicenseStatus($force_refresh);
$license_text_disabled	 = false;
$activate_button_text	 = DUP_PRO_U::__('Activate');
$license_status_text_alt = '';

if ($license_status == DUP_PRO_License_Status::Valid) {

	$license_status_style	= 'color:#509B18';
	$activate_button_text	= DUP_PRO_U::__('Deactivate');
	$license_text_disabled	= true;
	$license_key			= get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME);

	if (DUP_PRO_License_U::isValidOvrKey($license_key)) {
		$standard_key		 = DUP_PRO_License_U::getStandardKeyFromOvrKey($license_key);
		$license_status_text = DUP_PRO_U::__("Status: Active (Using license override for key {$standard_key})");
	} else {
		$license_status_text = DUP_PRO_U::__('Status: Active');
	}

//INACTIVE
} else if (($license_status == DUP_PRO_License_Status::Inactive)) {
	$license_status_style	 = 'color:#dd3d36;';
	$license_status_text	 = DUP_PRO_U::__('Status: Inactive');

//SITE-INACTIVE
} else if ($license_status == DUP_PRO_License_Status::Site_Inactive) {
	$license_status_style	 = 'color:#dd3d36;';
	$global					 = DUP_PRO_Global_Entity::get_instance();
	$license_status_text	 = ($global->license_no_activations_left) ? DUP_PRO_U::__('Status: Inactive (out of site licenses).<br/> Please use the link above to login to your snapcreek.com dashboard to manage your licenses.') : DUP_PRO_U::__('Status: Inactive');

//EXPIRED
} else if ($license_status == DUP_PRO_License_Status::Expired) {
	$license_key			 = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');
	$renewal_url			 = 'https://snapcreek.com/checkout?edd_license_key='.$license_key;
	$license_status_style	 = 'color:#dd3d36;';
	$license_status_text	 = sprintf('Your Duplicator Pro license key has expired so you aren\'t getting important updates! <a target="_blank" href="%1$s">Renew your license now</a>', $renewal_url);

//DEFAULT
} else {
	$license_status_string	 = DUP_PRO_License_U::getLicenseStatusString($license_status);
	$license_status_style	 = 'color:#dd3d36;';
	$license_status_text	 = DUP_PRO_U::__('Status: ').$license_status_string.'<br/>';
	$license_status_text_alt = DUP_PRO_U::__('If the license fails to activate please wait a few minutes and try again.');
	$license_status_text_alt .= '<br/><br/>';
	$license_status_text_alt .= sprintf(DUP_PRO_U::__('- Failure to activate after several attempts please review %1$sfaq activation steps%2$s'),
		'<a target="_blank" href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-005-q">', '</a>.<br/>');
	$license_status_text_alt .= sprintf(DUP_PRO_U::__('- To upgrade or renew your license visit %1$ssnapcreek.com%2$s'), '<a target="_blank" href="https://snapcreek.com">', '</a>.<br/>');
}

$license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');

function DUP_PRO_Type_Viewer($opts)
{
	$opts['mu1'] = '<i class="far fa-check-square"></i>  ';
	$opts['mu2'] = $opts['mu2'] == 1 ? '<i class="far fa-check-square"></i>  ' : '<i class="far fa-square"></i>  ';

	$txt_lic_hdr = DUP_PRO_U::__('Site Licenses');
	$txt_lic_msg = DUP_PRO_U::__('Number of site licenses indicates the number of sites the plugin can be active on at any one time. At any point you may deactivate/uninstall the plugin to free up the license and use the plugin elsewhere if needed.');
	$txt_mu1_hdr = DUP_PRO_U::__('Multisite Basic');
	$txt_mu1_msg = DUP_PRO_U::__('Can backup & migrate standalone sites and full multisite networks.');
	$txt_mu2_hdr = DUP_PRO_U::__('Multisite Plus+');
	$txt_mu2_msg = DUP_PRO_U::__('Ability to install a subsite as a standalone site. Additional subsite features are planned for Multisite Plus+ in the future.  This option is only available in Business and Gold.');

	//ARRAY:
	echo '<div>';
	echo "<i class='far fa-check-square'></i>  {$txt_lic_hdr} ({$opts['lic']}) <i class='fa fa-question-circle' data-tooltip-title='{$txt_lic_hdr}' data-tooltip='{$txt_lic_msg}'></i><br/>";
	echo $opts['mu1'] . "{$txt_mu1_hdr} <i class='fa fa-question-circle' data-tooltip-title='{$txt_mu1_hdr}' data-tooltip='{$txt_mu1_msg}'></i><br/>";
	echo $opts['mu2'] . "{$txt_mu2_hdr} <i class='fa fa-question-circle' data-tooltip-title='{$txt_mu2_hdr}' data-tooltip='{$txt_mu2_msg}'></i><br/>";
	echo '</div>';
}

?>

<form id="dup-settings-form" action="<?php echo self_admin_url('admin.php?page=' . DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ); ?>" method="post" data-parsley-validate>
<?php // wp_nonce_field($nonce_action); ?>
<input type="hidden" name="action" value="save" id="action">
<input type="hidden" name="page"   value="<?php echo DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ?>">
<input type="hidden" name="tab"   value="licensing">

<?php if ($action_response != null) : ?>
	<div class="notice notice-success is-dismissible dpro-wpnotice-box"><p><?php echo $action_response; ?></p></div>
<?php endif; ?>

<?php if ($error_response != null) : ?>
	<div class="notice notice-error is-dismissible dpro-wpnotice-box"><p><?php echo $error_response; ?></p></div>
<?php endif; ?>

<h3 class="title"><?php DUP_PRO_U::esc_html_e("Activation") ?> </h3>
<hr size="1" />
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php DUP_PRO_U::esc_html_e("Manage") ?></th>
		<td><?php echo sprintf(DUP_PRO_U::__('%1$sManage/Upgrade Licenses%2$s'), '<a target="_blank" href="https://snapcreek.com/dashboard">', '</a>'); ?></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php DUP_PRO_U::esc_html_e("Type") ?></th>
		<td class="dpro-license-type">
			<?php
				$license = DUP_PRO_License_U::getLicenseType();
				$global = DUP_PRO_Global_Entity::get_instance();

				switch ($license) {
					case DUP_PRO_License_Type::Personal:
						DUP_PRO_U::esc_html_e("Personal");
						DUP_PRO_Type_Viewer(array('mu2' => 0, 'lic' => $global->license_limit));
						break;
					case DUP_PRO_License_Type::Freelancer:
						DUP_PRO_U::esc_html_e("Freelancer");
						DUP_PRO_Type_Viewer(array('mu2' => 0, 'lic' => $global->license_limit));
						break;
					case DUP_PRO_License_Type::BusinessGold:
						DUP_PRO_U::esc_html_e("Business/Gold");
						DUP_PRO_Type_Viewer(array('mu2' => 1, 'lic' => 'Unlimited'));
						break;
					default:
						 DUP_PRO_U::esc_html_e("Unlicensed");
				}
			?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("License Key"); ?></label></th>
		<td>
			<input type="<?php echo $global->license_key_visible ? 'text' : 'password'; ?>"
                   class="wide-input"
                   name="_license_key"
                   id="_license_key"
                    <?php DUP_PRO_UI::echoDisabled($license_text_disabled || !$global->license_key_visible ); ?>
                   value="<?php echo $global->license_key_visible ? $license_key : '**********************'; ?>" />
			<br/>
			<p class="description">
				<?php
					echo "<span style='$license_status_style'>$license_status_text</span>";
					echo $license_status_text_alt;
				?>
			</p>
			<br/><br/>
			<button class="button" onclick="DupPro.Licensing.ChangeActivationStatus(<?php echo (($license_status != DUP_PRO_License_Status::Valid) ? 'true' : 'false'); ?>);return false;"><?php echo $activate_button_text; ?></button>
						
            <button  class="button" onclick="DupPro.Licensing.ClearActivationStatus();return false;"><?php  DUP_PRO_U::esc_html_e("Clear Key") ?></button>
		</td>
	</tr>
</table>

<h3 class="title"><?php DUP_PRO_U::esc_html_e("Key Visibility") ?> </h3>
<small>
	<?php
		DUP_PRO_U::esc_html_e("This is an optional setting that prevents the 'License Key' from being copied.  Enter a password and hit the 'Hide Key' button.");
		echo '<br/>';
		DUP_PRO_U::esc_html_e("To show the 'License Key' and allow for it to be copied to your clipboard enter in the password and hit the 'Show Key' button.");
	?>
</small>
<hr size="1" />
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Password"); ?></label></th>
		<td>
			<input type="password" class="wide-input" name="_key_password" id="_key_password"  />
		</td>
	</tr>
	<tr style="display:<?php echo $global->license_key_visible ? 'table-row' : 'none'; ?>" valign="top">
		<th scope="row"><label><?php DUP_PRO_U::esc_html_e("Retype Password"); ?></label></th>
		<td>
			<input type="password" class="wide-input" name="_key_password_confirmation" id="_key_password_confirmation" data-parsley-equalto="#_key_password"  />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"></th>
		<td>
			<?php
			wp_nonce_field('duplicator-pro-licence');
			?>
			<button class="button" id="show_hide" 
				onclick="DupPro.Licensing.ChangeKeyVisibility(<?php DUP_PRO_UI::echoBoolean(!$global->license_key_visible); ?>); return false;">
				<?php echo $global->license_key_visible ? DUP_PRO_U::__('Hide Key') : DUP_PRO_U::__('Show Key'); ?></button>
		</td>
	</tr>
</table>
</form>

<script>
jQuery(document).ready(function($)
{
	DupPro.Licensing = new Object();

	// Ensure if they hit enter in one of the password boxes the correct action takes place
	$("#_key_password, #_key_password_confirmation").keyup(function(event){

		if(event.keyCode == 13){
			$("#show_hide").click();
		}
	});

	DupPro.Licensing.ChangeActivationStatus = function (activate)
	{
		if(activate){
			$('#action').val('activate');
		} else  {
			$('#action').val('deactivate');
		}
		$('#dup-settings-form').submit();
	}

	DupPro.Licensing.ClearActivationStatus = function ()
	{
		$('#action').val('clear_key');
		$('#dup-settings-form').submit();
	}

	DupPro.Licensing.ChangeKeyVisibility = function (show)
	{
		if(show){
			$('#action').val('show_key');
		} else  {
			$('#action').val('hide_key');
		}
		$('#dup-settings-form').submit();
	}
});
</script>