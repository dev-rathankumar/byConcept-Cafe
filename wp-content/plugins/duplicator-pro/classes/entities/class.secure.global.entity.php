<?php
/**
 * Secure Global Entity. Used to store settings requiring encryption.
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
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.crypt.blowfish.php');

class DUP_PRO_Secure_Global_Entity extends DUP_PRO_JSON_Entity_Base
{
    const SGLOBAL_NAME = 'dup_pro_sglobal';

    public $basic_auth_password = '';
    public $lkp             = '';

    public static function initialize_plugin_data()
    {
        $sglobals = parent::get_by_type(get_class());

        if (count($sglobals) == 0) {
            $sglobal = new DUP_PRO_Secure_Global_Entity();

            $sglobal->save();
        }
    }

    public function setFromData($global_data)
    {
        $this->basic_auth_password = $global_data->basic_auth_password;
        $this->lkp                 = $global_data->lkp;
    }

    public function save()
    {
        $result = false;
        $global = DUP_PRO_Global_Entity::get_instance();  
        if ($global->crypt) {
            $this->encrypt();
        }
        $result = parent::save();
        if ($global->crypt) {
            $this->decrypt();   // Whenever its in memory its unencrypted
        }
        return $result;
    }

    private function encrypt()
    {
        if (!empty($this->basic_auth_password)) {
            $this->basic_auth_password = DUP_PRO_Crypt_Blowfish::encrypt($this->basic_auth_password);
        }

        if (!empty($this->lkp)) {
            $this->lkp = DUP_PRO_Crypt_Blowfish::encrypt($this->lkp);
        }
    }

    private function decrypt()
    {
        if (!empty($this->basic_auth_password)) {
            $this->basic_auth_password = DUP_PRO_Crypt_Blowfish::decrypt($this->basic_auth_password);
        }

        if (!empty($this->lkp)) {
            $this->lkp = DUP_PRO_Crypt_Blowfish::decrypt($this->lkp);
        }
    }

    public static function &getInstance()
    {
        if (isset($GLOBALS[self::SGLOBAL_NAME]) === false) {
            $sglobal  = null;
            $sglobals = DUP_PRO_JSON_Entity_Base::get_by_type(get_class());

            if (count($sglobals) > 0) {
                $sglobal = $sglobals[0];
                $global = DUP_PRO_Global_Entity::get_instance();   
                if ($global->crypt) {
                    $sglobal->decrypt();
                }
            } else {
                DUP_PRO_LOG::traceError("Secure Global entity is null!");
            }
            $GLOBALS[self::SGLOBAL_NAME] = $sglobal;
        }

        return $GLOBALS[self::SGLOBAL_NAME];
    }
}