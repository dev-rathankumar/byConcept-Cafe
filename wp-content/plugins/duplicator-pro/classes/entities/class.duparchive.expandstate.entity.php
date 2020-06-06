<?php
/**
 * Entity for duparchive expand state
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

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.json.entity.base.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/entities/class.system.global.entity.php');

class DUP_PRO_DupArchive_Expand_State_Entity extends DUP_PRO_JSON_Entity_Base
{
    public $package_id;

    // DupArchiveStateBase Properties
    public $basePath          = '';
    public $archivePath       = '';
    public $isCompressed      = false;
    public $currentFileOffset = 0;
    public $archiveOffset     = 0;
    public $timeSliceInSecs   = -1;
    public $working           = false;

    public $startTimestamp    = -1;
    public $throttleDelayInUs = 0;
    public $timeoutTimestamp  = -1;
    public $timerEnabled      = true;

    // DupArchiveExpandState Properties
    public $archiveHeaderString = null;
    public $currentFileHeaderString = null;
    public $failuresString          = null;
    
    public $validateOnly= false;
    public $validationType = DupArchiveValidationTypes::Standard;
    public $fileWriteCount = 0;
    public $directoryWriteCount = 0;
    public $expectedFileCount = -1;
    public $expectedDirectoryCount = -1;

    public $isRobust = false;

    // rsr todo fill in standard expand state variables

    function __construct()
    {
        parent::__construct();
        $global                  = DUP_PRO_Global_Entity::get_instance();
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();
    }

    public static function get_all()
    {
        return self::get_by_type(get_class());
    }

    public static function delete_by_id($id)
    {
        parent::delete_by_id_base($id);
    }

    public static function delete_all()
    {
        $instances = self::get_all();

        foreach($instances as $instance)
        {
            $instance->delete();
        }
    }
    
    public static function get_by_id($id)
    {
        //Schedule Run Now = -1 don't search for id
        if ($id != -1) {
            return self::get_by_id_and_type($id, get_class());
        } else {
            return null;
        }
    }

    public static function get_by_package_id($package_id)
    {
        $expandStateEntities = self::get_all();
        
        foreach($expandStateEntities as $expandStateEntity)
        {
            /* @var $expandStateEntity DUP_PRO_DupArchive_Expand_State_Entity */
            if($expandStateEntity->package_id == $package_id)
            {
                return $expandStateEntity;
            }
        }

        return null;
    }
}
