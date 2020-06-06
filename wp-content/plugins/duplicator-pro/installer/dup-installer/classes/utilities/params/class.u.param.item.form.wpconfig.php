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
class DUPX_Param_item_form_wpconfig extends DUPX_Param_item_form
{

    const IN_WP_CONF_POSTFIX = '_inwpc';

    public function __construct($name, $type, $formType, $attr = null, $formAttr = array())
    {
        parent::__construct($name, $type, $formType, $attr, $formAttr);
        $this->attr['defaultFromInput']               = $this->attr['default'];
        $this->attr['defaultFromInput']['inWpConfig'] = false;

        if ($type === self::TYPE_BOOL) {
            $this->attr['defaultFromInput']['value'] = false;
        }
    }

    /**
     * transform input in right array key
     * 
     * @param array $superObject
     * @return boolean|array
     */
    protected function getValueFilter($superObject)
    {
        $result = array(
            'value'      => parent::getValueFilter($superObject),
            'inWpConfig' => filter_var($superObject[$this->name.self::IN_WP_CONF_POSTFIX], FILTER_VALIDATE_BOOLEAN)
        );

        if (!parent::isValueInInput($superObject)) {
            $result['value'] = $this->attr['defaultFromInput']['value'];
        }

        return $result;
    }

    /**
     * return sanitized value
     * 
     * @param mixed $value
     * @return mixed
     * @throws Exception
     */
    public function getSanitizeValue($value)
    {
        $result          = (array) $value;
        $result['value'] = parent::getSanitizeValue($result['value']);
        return $result;
    }

    /**
     * 
     * @return string
     */
    protected function valueToInfo()
    {
        if ($this->value['inWpConfig']) {
            return 'Set in wp config with value '.parent::valueToInfo();
        } else {
            return 'Not set in wp config';
        }
    }

    /**
     * 
     * @return mixed
     */
    protected function getInputValue()
    {
        return $this->value['value'];
    }

    protected function isValueInInput($superObject)
    {
        return parent::isValueInInput($superObject) || isset($superObject[$this->name.self::IN_WP_CONF_POSTFIX]);
    }

    /**
     * 
     * @param mixed $value
     * @param mixed $validateValue
     * @return boolean
     */
    public function isValid($value, &$validateValue = null)
    {
        if (!is_array($value) || !isset($value['value']) || !isset($value['inWpConfig'])) {
            DUPX_Log::info('WP CONFIG INVALID ARRAY VAL:'.DUPX_Log::varToString($value));
            return false;
        }

        // IF isn't in wp config the value isn't validate
        if ($value['inWpConfig'] === false) {
            $validateValue = $value;
            return true;
        } else {
            $confValidValue = $value['value'];
            if (parent::isValid($value['value'], $confValidValue) === false) {
                DUPX_Log::info('WP CONFIG INVALID VALUE:'.DUPX_Log::varToString($confValidValue));
                return false;
            } else {
                $validateValue          = $value;
                $validateValue['value'] = $confValidValue;
                return true;
            }
        }
    }

    protected function htmlInputContBefore()
    {
        if ($this->getFormStatus() == self::STATUS_INFO_ONLY) {
            return;
        }

        if (!$this->value['inWpConfig']) {
            $this->formAttr['inputContainerClasses'][] = 'no-display';
            if ($this->formAttr['status'] == self::STATUS_ENABLED) {
                $this->formAttr['status'] = self::STATUS_DISABLED;
            }
        }

        $inputAttrs = array(
            'name'  => $this->name.self::IN_WP_CONF_POSTFIX,
            'value' => 1
        );
        if ($this->value['inWpConfig']) {
            $inputAttrs['checked'] = 'checked';
        }
        echo '<span class="wpinconf-check-wrapper" >';
        DUPX_U_Html::checkboxSwitch(
            $inputAttrs,
            array(
                'title' => 'Add in wp config'
            )
        );
        echo '</span>';
    }

    protected static function getDefaultAttrForType($type)
    {
        $attrs        = parent::getDefaultAttrForType($type);
        $valFromInput = $attrs['defaultFromInput'];

        $attrs['defaultFromInput'] = array(
            'value'      => $valFromInput,
            'inWpConfig' => false
        );
        return $attrs;
    }

    protected static function getDefaultAttrForFormType($formType)
    {
        $attrs = parent::getDefaultAttrForFormType($formType);

        $attrs['wrapperClasses'][]    = 'wp-config-item';
        $attrs['wrapperContainerTag'] = 'div';
        return $attrs;
    }
}