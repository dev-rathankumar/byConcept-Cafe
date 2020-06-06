<?php
defined("ABSPATH") or die("");

/**
 * Used to display notices in the WordPress Admin area
 * This class takes advantage of the 'admin_notice' action.
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
class DUP_PRO_UI_Notice
{

	const OPTION_KEY_INSTALLER_HASH_NOTICE			 = 'duplicator_pro_inst_hash_notice';
	const OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL	 = 'duplicator_pro_activate_plugins_after_installation';

	/**
	 * init notice actions
	 */
	public static function init()
	{
		$methods = array(
			'showReservedFilesNotice',
			'newInstallerHashOption'
		);
		$action	 = is_multisite() ? 'network_admin_notices' : 'admin_notices';
		foreach ($methods as $method) {
			add_action($action, array('DUP_PRO_UI_Notice', $method));
		}
	}

	public static function newInstallerHashOption()
	{
		if (get_option(self::OPTION_KEY_INSTALLER_HASH_NOTICE) != true) {
			return;
		}

		$screen = get_current_screen();
		if (!in_array($screen->parent_base, array('plugins', 'duplicator-pro'))) {
			return;
		}

		$action			 = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
		$installerMode	 = filter_input(INPUT_POST, 'installer_name_mode', FILTER_SANITIZE_STRING);
		if ($screen->id == 'duplicator-pro_page_duplicator-pro-settings' && $action == 'save' && $installerMode == DUP_PRO_Global_Entity::INSTALLER_NAME_MODE_WITH_HASH) {
			delete_option(self::OPTION_KEY_INSTALLER_HASH_NOTICE);
			return;
		}

		if (DUP_PRO_Global_Entity::get_instance()->installer_name_mode == DUP_PRO_Global_Entity::INSTALLER_NAME_MODE_WITH_HASH) {
			delete_option(self::OPTION_KEY_INSTALLER_HASH_NOTICE);
			return;
		}
		?>
		<div class="dup-notice-success notice notice-success duplicator-pro-admin-notice is-dismissible" data-to-dismiss="<?php echo esc_attr(self::OPTION_KEY_INSTALLER_HASH_NOTICE); ?>" > 
			<p>
				<?php DUP_PRO_U::esc_html_e('Duplicator PRO now includes a new option that helps secure the installer.php file.'); ?><br>
				<?php DUP_PRO_U::esc_html_e('After this option is enabled, a security hash will be added to the name of the installer when it\'s downloaded.'); ?>
			</p>
			<p>
				<?php
				echo sprintf(
					DUP_PRO_U::__('To enable this option or to get more information, open the <a href="%s">Package Settings</a> and visit the Installer section.'),
					'admin.php?page=duplicator-pro-settings&tab=package#duplicator-pro-installer-settings');
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Shows a display message in the wp-admin if any reserved files are found
	 *
	 * @return null
	 */
	public static function showReservedFilesNotice()
	{
		echo "<style>div.notice-safemode{color:maroon;}</style>";
		$dpro_active = is_plugin_active('duplicator-pro/duplicator-pro.php');
		$dup_perm	 = current_user_can('manage_options');
		if (!$dpro_active || !$dup_perm) {
			return;
		}

		//Hide free error message if Pro is active
		if (is_plugin_active('duplicator/duplicator.php')) {
			echo "<style>div#dup-global-error-reserved-files {display:none}</style>";
		}

		$screen = get_current_screen();
		if (!isset($screen)) {
			return;
		}

		$on_active_tab					 = isset($_GET['section']) ? $_GET['section'] : '';
		$is_lite_installer_cleanup_req	 = ($screen->id == 'duplicator_page_duplicator-tools' && isset($_GET['action']) && $_GET['action'] == 'installer');
		$onDiagnosticsCreanupPage		 = (($screen->id == 'duplicator-pro_page_duplicator-pro-tools' || $screen->id == 'duplicator-pro_page_duplicator-pro-tools-network') && ($on_active_tab == "diagnostic" || $on_active_tab == ''));
		$wrapperClass					 = ($onDiagnosticsCreanupPage) ? 'diagnostic-site-page' : 'general-site-page';
		$actionId						 = 'dpro-notice-action-'.$wrapperClass;

		if (DUP_PRO_Server::hasInstallFiles() && !$is_lite_installer_cleanup_req) {


			echo '<div class="dup-updated notice-success '.$wrapperClass.'" id="dpro-global-error-reserved-files" ><p>';

			//Safe Mode Notice
			$safe_html = '';
			if (get_option("duplicator_pro_exe_safe_mode", 0) > 0) {
				$safe_msg1	 = DUP_PRO_U::__('Safe Mode:');
				$safe_msg2	 = DUP_PRO_U::__('During the install safe mode was enabled deactivating all plugins.<br/> Please be sure to ');
				$safe_msg3	 = DUP_PRO_U::__('re-activate the plugins');
				$safe_html	 = "<div class='notice-safemode'><b>{$safe_msg1}</b><br/>{$safe_msg2} <a href='plugins.php'>{$safe_msg3}</a>!</div><br/>";
			}

			//On Diagnostics > Cleanup Page
			if ($onDiagnosticsCreanupPage) {

				$title		 = DUP_PRO_U::__('This site has been successfully migrated!');
				$msg1		 = DUP_PRO_U::__('Final step:');
				$msg2		 = DUP_PRO_U::__('This message will be removed after all installer files are removed.  Installer files must be removed to maintain a secure site.<br/>'
						.'Click the link above or button below to remove all installer files and complete the migration.');
				$linkLabel	 = DUP_PRO_U::esc_html__('Remove Installation Files Now!');

				echo "<b class='pass-msg'><i class='fa fa-check-circle'></i> {$title}</b> <br/> {$safe_html} <b>".esc_html($msg1)."</b> <br/>";
				echo "<a id=\"".$actionId."\" href='javascript:void(0)' onclick='jQuery(\"#dpro-remove-installer-files-btn\").click()'>".$linkLabel."</a><br/>";
				echo "<div class='pass-msg'>".esc_html($msg2)."</div>";

				//All other Pages
			} else {

				$title	 = DUP_PRO_U::__('Migration Almost Complete!');
				$msg	 = DUP_PRO_U::esc_html__('Reserved Duplicator Pro installation files have been detected in the root directory.  Please delete these installation files to '
						.'avoid security issues.').' <br/> '.DUP_PRO_U::esc_html__('Go to: Tools > Diagnostics > Stored Data > and click the "Remove Installation Files" button');

				$nonce		 = wp_create_nonce('duplicator_pro_cleanup_page');
				$url		 = self_admin_url('admin.php?page=duplicator-pro-tools&tab=diagnostics&_wpnonce='.$nonce);
				$linkLabel	 = DUP_PRO_U::esc_html__('Take me there now!');
				echo "<b>".esc_html($title)."</b><br/> {$safe_html} {$msg}";
				echo "<br/><a id=\"".$actionId."\" href='".$url."'>".$linkLabel."</a>";
			}
			echo "</p></div>";
		}
	}
}