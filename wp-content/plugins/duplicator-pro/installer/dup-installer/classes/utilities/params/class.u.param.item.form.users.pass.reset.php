<?php
/**
 * param descriptor
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * this class manages a password type input with the hide / show passwrd button
 */
class DUPX_Param_item_form_users_pass_reset extends DUPX_Param_item_form_pass
{

    const FORM_TYPE_USERS_PWD_RESET = 'usrpwdreset';

    protected $currentUserId = -1;

    public function getHtml($echo = true)
    {
        if ($this->formType == self::FORM_TYPE_USERS_PWD_RESET) {
            $result = '';
            if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                $users         = array();
                foreach ($overwriteData['adminUsers'] as $user) {
                    $users[$user['id']] = $user['user_login'];
                }
            } else {
                $users = DUPX_ArchiveConfig::getInstance()->getUsersLists();
            }
            $mainInputId = $this->formAttr['id'];
            foreach ($users as $userId => $login) {
                $this->currentUserId     = $userId;
                $this->formAttr['id']    = $mainInputId.'_'.$this->currentUserId;
                $this->formAttr['label'] = $login;
                $result                  .= parent::getHtml($echo);
            }
            $this->formAttr['id'] = $mainInputId;
            return $result;
        } else {
            return parent::getHtml($echo);
        }
    }

    protected function htmlItem()
    {
        if ($this->formType == self::FORM_TYPE_USERS_PWD_RESET) {
            $this->pwdToggleHtml();
        } else {
            parent::htmlItem();
        }
    }

    protected function getAttrName()
    {
        return $this->name.'['.$this->currentUserId.']';
    }

    /**
     * 
     * @return mixed
     */
    protected function getInputValue()
    {
        return $this->value[$this->currentUserId];
    }

    protected static function getDefaultAttrForFormType($formType)
    {
        $attrs = parent::getDefaultAttrForFormType($formType);
        if ($formType == self::FORM_TYPE_USERS_PWD_RESET) {
            $attrs['maxLength'] = null;     // if null have no limit
            $attrs['size']      = null;
        }
        return $attrs;
    }
}