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

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.json.entity.base.php');

abstract class DUP_PRO_Recommended_Fix_Type
{
    const Text = 0;
    const QuickFix = 1;
}

/**
 * @copyright 2016 Snap Creek LLC
 * recommendation_type = Text; parameter1 = {text to display to user}; parameter2=n/a
 */
class DUP_PRO_Recommended_Fix
{		
    public $recommended_fix_type;
    public $error_text = '';
    public $parameter1 = '';
    public $parameter2 = '';
}


class DUP_PRO_System_Global_Entity extends DUP_PRO_JSON_Entity_Base
{	
    const NAME_IN_GLOBALS = 'dup_pro_system_global';
    public $recommended_fixes;
    public $schedule_failed = false;
	public $package_check_ts = 0;

    public static function initialize_plugin_data()
    {
        $system_globals = parent::get_by_type(get_class());
        if (count($system_globals) == 0)
        {
            $system_global = new DUP_PRO_System_Global_Entity();
            $system_global->recommended_fixes = array();
            $system_global->save();
        }
    }

    public function add_recommended_quick_fix($error_text, $fix_text, $javascript_callback)
    {
        if($this->is_text_fix_dupe($fix_text) === false)
        {
            $id = str_shuffle(substr(uniqid('',true),0,14) . mt_rand(0,1000000));

            $fix = new DUP_PRO_Recommended_Fix();

            $fix->recommended_fix_type = DUP_PRO_Recommended_Fix_Type::QuickFix;
            $fix->error_text = $error_text;
            $fix->parameter1 = $fix_text;
            $fix->parameter2 = $javascript_callback;
            $fix->id         = $id;

            array_push($this->recommended_fixes, $fix);
        }
    }

    public function add_recommended_text_fix($error_text, $fix_text)
    {
        if($this->is_text_fix_dupe($fix_text) === false)
        {
            $fix = new DUP_PRO_Recommended_Fix();

            $fix->recommended_fix_type = DUP_PRO_Recommended_Fix_Type::Text;
            $fix->error_text = $error_text;
            $fix->parameter1 = $fix_text;

            array_push($this->recommended_fixes, $fix);
        }
    }

    public function remove_by_id($id){
        if(!empty($id))
        {
            $system_global = DUP_PRO_System_Global_Entity::get_instance();
            if(isset($system_global->recommended_fixes) && is_array($system_global->recommended_fixes) && count($system_global->recommended_fixes) > 0)
            {
                $save = array();
                foreach ($system_global->recommended_fixes as $key => $find) {
                    if($find->id != $id)
                    {
                        $save[]=$find;
                    }
                }
                $this->recommended_fixes = $save;
                return $this;
            }
        }
        return false;
    }

    private function is_text_fix_dupe($fix_text)
    {
        $existing_strings = $this->get_recommended_text_fix_strings();

        $present = false;

        foreach($existing_strings as $existing_string)
        {
            if(strcmp($existing_string, $fix_text) == 0)
            {
                $present = true;
                break;
            }
        }

        return $present;
    }

    public function clear_recommended_fixes()
    {
        unset($this->recommended_fixes);

        $this->recommended_fixes = array();
    }

    public function get_recommended_text_fix_strings()
    {
        $text_fix_strings = array();

        /* @var $$fix DUP_PRO_Recommended_Fix */
        foreach($this->recommended_fixes as $fix)
        {
            array_push($text_fix_strings, $fix->parameter1);
        }

        return $text_fix_strings;
    }

    /**
     *
     * @return DUP_PRO_System_Global_Entity
     */
    public static function get_instance()
    {
        static $system_global = null;
        if (is_null($system_global)) {
            /* @var $system_globals DUP_PRO_System_Global_Entity */
            $system_globals = DUP_PRO_JSON_Entity_Base::get_by_type(get_class());

            if (count($system_globals) > 0) {
                $system_global = $system_globals[0];
            } else {
                DUP_PRO_LOG::traceError("System Global entity is null!");
            }
        }

        return $system_global;
    }
}
