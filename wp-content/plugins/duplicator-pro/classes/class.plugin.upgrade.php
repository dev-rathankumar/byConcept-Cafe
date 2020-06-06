<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**

 * Upgrade logic of plugin resides here

 */
class DUP_PRO_Plugin_Upgrade
{

	const DUP_VERSION_OPT_KEY = 'duplicator_pro_plugin_version';

	public static function onActivationAction()
	{
		if (($oldDupVersion = get_option(self::DUP_VERSION_OPT_KEY, false)) === false) {
			self::newInstallation();
		} else {
			self::updateInstallation($oldDupVersion);
		}

		//Setup All Directories
		DUP_PRO_U::initStorageDirectory();
	}

	protected static function newInstallation()
	{
		self::environmentChecks();

		self::updateDatabase();

		update_option(DUP_PRO_UI_Notice::OPTION_KEY_INSTALLER_HASH_NOTICE, true);

		DUP_PRO_Upgrade_U::PerformUpgrade(false, DUPLICATOR_PRO_VERSION);

		//WordPress Options Hooks
		self::updateOptionVersion();
	}

	protected static function updateInstallation($oldVersion)
	{
		self::environmentChecks();

		self::updateDatabase();

		if (version_compare($oldVersion, '3.8.8.1', '<=')) {
			update_option(DUP_PRO_UI_Notice::OPTION_KEY_INSTALLER_HASH_NOTICE, true);
		}
		
		DUP_PRO_Upgrade_U::PerformUpgrade($oldVersion, DUPLICATOR_PRO_VERSION);

		//WordPress Options Hooks
		self::updateOptionVersion();
	}

	protected static function updateOptionVersion()
	{
		//WordPress Options Hooks
		if (update_option(self::DUP_VERSION_OPT_KEY, DUPLICATOR_PRO_VERSION) === false) {
			DUP_PRO_LOG::trace("Couldn't update duplicator_pro_plugin_version so deleting it.");

			delete_option(self::DUP_VERSION_OPT_KEY);

			if (update_option(self::DUP_VERSION_OPT_KEY, DUPLICATOR_PRO_VERSION) === false) {
				DUP_PRO_LOG::trace("Still couldn\'t update the option!");
			} else {
				DUP_PRO_LOG::trace("Option updated.");
			}
		}
	}

	protected static function updateDatabase()
	{
		global $wpdb;
		$table_name = $wpdb->base_prefix."duplicator_pro_packages";

		//PRIMARY KEY must have 2 spaces before for dbDelta to work
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			   id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			   name VARCHAR(250) NOT NULL,
			   hash VARCHAR(50) NOT NULL,
			   status INT(11) NOT NULL,
			   created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			   owner VARCHAR(60) NOT NULL,
			   package LONGTEXT NOT NULL,
			   PRIMARY KEY  (id),
			   KEY hash (hash))";

		require_once(ABSPATH.'wp-admin/includes/upgrade.php');

		@dbDelta($sql);

		DUP_PRO_JSON_Entity_Base::init_table();
		DUP_PRO_Global_Entity::initialize_plugin_data();
		DUP_PRO_Secure_Global_Entity::initialize_plugin_data();
		DUP_PRO_System_Global_Entity::initialize_plugin_data();
		DUP_PRO_Package_Template_Entity::create_default();
		DUP_PRO_Package_Template_Entity::create_manual();
	}

	protected static function environmentChecks()
	{
		require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/environment/class.environment.checker.php');

		$env_checker = new DUP_PRO_Environment_Checker();

		$status = $env_checker->check();

		$messages = $env_checker->getHelperMessages();

		if (!$status) {
			if (!empty($messages)) {
				$msg_str = '';
				foreach ($messages as $id => $msgs) {
					foreach ($msgs as $key => $msg) {
						$msg_str .= '<br/>'.$msg;
					}
				}
				die($msg_str);
			}
		}
	}
}