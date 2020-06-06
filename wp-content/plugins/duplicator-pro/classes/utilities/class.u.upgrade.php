<?php
defined("ABSPATH") or die("");
/**
 * Utility class managing when the plugin is updated
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.secure.global.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.schedule.entity.php');

class DUP_PRO_Upgrade_U
{

    /* @var $global DUP_PRO_Global_Entity */
    /* @var $sglobal DUP_PRO_Secure_Global_Entity */

    public static function PerformUpgrade($currentVersion, $newVersion)
    {
        error_log("Performing upgrade from {$currentVersion} to {$newVersion}");

        self::MoveDataToSecureGlobal();
        self::InitializeGift();
        self::UpdateArchiveEngine();
    }

    public static function InitializeGift()
    {
        $global                              = DUP_PRO_Global_Entity::get_instance();
        $global->dupHidePackagesGiftFeatures = !DUPLICATOR_PRO_GIFT_THIS_RELEASE;
        $global->save();
    }
    /* UpdateArchiveEngine : Introduced in v3.7.1
     * Between v3.5 and v3.7 a temporary setting was created in the packages settings, that allowed for an archive engine (DA, ZA, Shell)
     * to be assigned at either manual mode or schedule mode.  After v3.7.1 the setting for schedules was removed but in order to have backwards
     * compatibility. The schedule settings had to take priority over the manual setting if it was enabled and rolled back into the default
     * setting for manual mode.  As of now there is only one mode that is used for both schedules and manual modes  */

    public static function UpdateArchiveEngine()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        if ($global->archive_build_mode == $global->archive_build_mode_schedule) {
            // Do nothing
        } else {

            if ($global->archive_build_mode_schedule != DUP_PRO_Archive_Build_Mode::Unconfigured) {

                $schedules = DUP_PRO_Schedule_Entity::get_all();

                if (count($schedules) > 0) {
                    $global->archive_build_mode  = $global->archive_build_mode_schedule;
                    $global->archive_compression = $global->archive_compression_schedule;
                } else {
                    // If there aren't schedules just keep archive build mode the same as it has been
                }

                $global->archive_build_mode_schedule = DUP_PRO_Archive_Build_Mode::Unconfigured;
                $global->save();
            }
        }
    }

    public static function MoveDataToSecureGlobal()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        if (($global->lkp !== '') || ($global->basic_auth_password !== '')) {
            error_log('setting sglobal');
            $sglobal = DUP_PRO_Secure_Global_Entity::getInstance();

            $sglobal->lkp                 = $global->lkp;
            $sglobal->basic_auth_password = $global->basic_auth_password;

            $global->lkp                 = '';
            $global->basic_auth_password = '';

            $sglobal->save();
            $global->save();
        }
    }
}