<?php
/**
 * Global System Enity
 *
 * Standard: Missing
 *
 * @package DUP_PRO
 * @subpackage classes/entities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.0.0
 *
 * @todo Finish Docs
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPLICATOR_PRO_PLUGIN_PATH   . '/classes/entities/class.json.entity.base.php');


class DUP_PRO_Profile_Logs_Entity extends DUP_PRO_JSON_Entity_Base
{
    public $profileLogs;

    public static function clear()
    {
        $instance = self::get_instance();

        if($instance == null)
        {
            $instance = new DUP_PRO_Profile_Logs_Entity();
        }

        $instance->profileLogs = array();

        $instance->save();

        return $instance;
    }

    public static function get_instance()
    {
        $instance = null;

        $profileLogObjects = DUP_PRO_JSON_Entity_Base::get_by_type(get_class());

        if (count($profileLogObjects) > 0)
        {
            $instance = $profileLogObjects[0];
        }

        return $instance;
    }
}
