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

require_once(DUPX_INIT.'/classes/utilities/params/class.u.param.item.form.option.php');

/**
 * This class extends DUPX_Param_item describing how the parameter should be handled within the form.
 * 
 */
class DUPX_Param_item_form extends DUPX_Param_item
{

    const FORM_TYPE_HIDDEN     = 'hidden';
    const FORM_TYPE_TEXT       = 'text';
    const FORM_TYPE_NUMBER     = 'number';
    const FORM_TYPE_SELECT     = 'sel';
    const FORM_TYPE_CHECKBOX   = 'check';
    const FORM_TYPE_SWITCH     = 'switch';
    const FORM_TYPE_M_CHECKBOX = 'mcheck';
    const FORM_TYPE_RADIO      = 'radio';
    const STATUS_ENABLED       = 'st_enabled';
    const STATUS_READONLY      = 'st_readonly';
    const STATUS_DISABLED      = 'st_disabled';
    const STATUS_INFO_ONLY     = 'st_infoonly';
    const STATUS_SKIP          = 'st_skip';

    protected $formType = null;
    protected $formAttr = array();

    /**
     * 
     * @param string $name  // param identifier
     * @param string $type  // TYPE_STRING | TYPE_ARRAY_STRING | ...
     * @param type $formType // FORM_TYPE_HIDDEN | FORM_TYPE_TEXT | ...
     * @param array $attr   // list of attributes
     * @param type $formAttr // list of form attributes
     * 
     * @throws Exception
     */
    public function __construct($name, $type, $formType, $attr = null, $formAttr = array())
    {
        if ($type == self::TYPE_ARRAY_MIXED) {
            throw new Exception('array myxed can\'t be a DUPX_Param_item_form');
        }

        parent::__construct($name, $type, $attr);

        $defaultAttr    = static::getDefaultAttrForFormType($formType);
        $this->formAttr = array_merge($defaultAttr, (array) $formAttr);

        if (isset($formAttr['classes'])) {
            $this->formAttr['classes'] = array_merge($defaultAttr['classes'], (array) $formAttr['classes']);
        }

        if (isset($formAttr['labelClasses'])) {
            $this->formAttr['labelClasses'] = array_merge($defaultAttr['labelClasses'], (array) $formAttr['labelClasses']);
        }

        if (isset($formAttr['wrapperClasses'])) {
            $this->formAttr['wrapperClasses'] = array_merge($defaultAttr['wrapperClasses'], (array) $formAttr['wrapperClasses']);
        }

        if (isset($formAttr['inputContainerClasses'])) {
            $this->formAttr['inputContainerClasses'] = array_merge($defaultAttr['inputContainerClasses'], (array) $formAttr['inputContainerClasses']);
        }

        if (!empty($this->formAttr['label'])) {
            $this->formAttr['wrapperClasses'][] = 'has-main-label';
        }

        $this->formType = $formType;

        if (empty($this->formAttr['id'])) {
            $this->formAttr['id'] = 'param_item_'.$name;
        }

        if (empty($this->formAttr['wrapperId'])) {
            $this->formAttr['wrapperId'] = 'wrapper_item_'.$name;
        }

        //DUPX_Log::infoObject('PARAM INIZIALIZED ['.$this->name.']', $this, DUPX_Log::LV_DEFAULT);
    }

    public function getLabel()
    {
        if (!empty($this->formAttr['label'])) {
            return $this->formAttr['label'];
        } else {
            return parent::getLabel();
        }
    }

    /**
     * get the input id (input, select ... )
     * normally it depends on the name of the object but can be perosnalizzato through formAttrs
     * 
     * @return string
     */
    public function getFormItemId()
    {
        return $this->formAttr['id'];
    }

    /**
     * return the input wrapper id if isn't empty or false
     * normally it depends on the name of the object but can be perosnalizzato through formAttrs
     * 
     * @return string
     */
    public function getFormWrapperId()
    {
        return empty($this->formAttr['wrapperId']) ? false : $this->formAttr['wrapperId'];
    }

    /**
     * return current form status 
     * 
     * @return string // STATUS_ENABLED | STATUS_READONLY ...
     */
    public function getFormStatus()
    {
        if (is_callable($this->formAttr['status'])) {
            return call_user_func($this->formAttr['status'], $this);
        } else {
            return $this->formAttr['status'];
        }
    }

    /**
     * add html class at param wrapper container
     * 
     * @param string $class
     * 
     */
    public function addWrapperClass($class)
    {
        if (!in_array($class, $this->formAttr['wrapperClasses'])) {
            $this->formAttr['wrapperClasses'][] = $class;
        }
    }

    /**
     * remove html class at param wrapper container
     * 
     * @param string $class
     * 
     */
    public function removeWrapperClass($class)
    {
        if (in_array($class, $this->formAttr['wrapperClasses'])) {
            unset($this->formAttr['wrapperClasses'][$class]);
        }
    }

    /**
     * this function can be extended in child classes
     * 
     * @return string
     */
    protected function getAttrName()
    {
        return $this->name;
    }

    /**
     * this function can be extended in child classes
     * 
     * @return string
     */
    protected function getInputValue()
    {
        return $this->value;
    }

    /**
     * return a copy of this object with a new name ad overwrite attr
     * 
     * @param string $newName
     * @return self
     */
    public function getCopyWithNewName($newName, $attr = array(), $formAttr = array())
    {
        $copy = parent::getCopyWithNewName($newName, $attr);

        $reflect = new ReflectionObject($copy);

        $formAttrProp = $reflect->getProperty('formAttr');
        $formAttrProp->setAccessible(true);

        $newAttr              = $formAttrProp->getValue($copy);
        $newAttr['id']        = 'param_item_'.$newName;
        $newAttr['wrapperId'] = 'wrapper_item_'.$newName;
        $newAttr              = array_merge($newAttr, $formAttr);

        if (isset($newAttr['options']) && is_array($newAttr['options'])) {
            $options            = $newAttr['options'];
            $newAttr['options'] = array();
            foreach ($options as $key => $option) {
                if (is_object($option)) {
                    $newAttr['options'][$key] = clone $option;
                } else {
                    $newAttr['options'][$key] = $option;
                }
            }
        }

        $formAttrProp->setValue($copy, $newAttr);

        return $copy;
    }

    /**
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getFormStatus() == self::STATUS_ENABLED;
    }

    /**
     * 
     * @return bool
     */
    public function isSkip()
    {
        return $this->getFormStatus() == self::STATUS_SKIP;
    }

    /**
     * 
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getFormStatus() == self::STATUS_DISABLED;
    }

    /**
     * 
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getFormStatus() == self::STATUS_READONLY;
    }

    /**
     * return true if the passed value is in current value if type is array or equal if is scalar
     * 
     * @param mixed $value
     * @return bool
     */
    protected function isValueInValue($value)
    {
        if (is_null($this->getInputValue()) || is_scalar($this->getInputValue())) {
            return $value == $this->getInputValue();
        } else {
            return in_array($value, $this->getInputValue());
        }
    }

    /**
     * display the html input of current item
     * 
     * @throws Exception
     */
    protected function htmlItem()
    {
        switch ($this->formType) {
            case self::FORM_TYPE_HIDDEN:
                $this->hiddenHtml();
                break;
            case self::FORM_TYPE_TEXT:
                $this->inputHtml('text');
                break;
            case self::FORM_TYPE_NUMBER:
                $this->inputHtml('number');
                break;
            case self::FORM_TYPE_SELECT:
                $this->selectHtml();
                break;
            case self::FORM_TYPE_CHECKBOX:
                $this->checkBoxHtml(false);
                break;
            case self::FORM_TYPE_SWITCH:
                $this->checkBoxHtml(true);
                break;
            case self::FORM_TYPE_M_CHECKBOX:
                $this->mCheckBoxHtml();
                break;
            case self::FORM_TYPE_RADIO:
                $this->radioHtml();
                break;
            default:
                throw new Exception('ITEM ERROR '.$this->name.' Invalid form type '.$this->formType);
        }
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getFormAttr($key)
    {
        return $this->formAttr[$key];
    }

    /**
     * set form attribute
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setFormAttr($key, $value)
    {
        $this->formAttr[$key] = $value;
    }

    /**
     * 
     * @return DUPX_Param_item_form_option[]
     */
    protected function getOptions()
    {
        if (!isset($this->formAttr['options'])) {
            return array();
        } elseif (is_callable($this->formAttr['options'])) {
            return call_user_func($this->formAttr['options'], $this);
        } else {
            return $this->formAttr['options'];
        }
    }

    /**
     * 
     * @param int $index
     * @param string $status
     */
    public function setOptionStatus($index, $status)
    {
        if (is_array($this->formAttr['options']) && isset($this->formAttr['options'][$index])) {
            $this->formAttr['options'][$index]->setStatus($status);
        }
    }

    /**
     * html of current item if the status if info only
     */
    protected function infoOnlyHtml()
    {
        $attrs          = array(
            'id' => $this->formAttr['id']
        );
        $classes        = array_merge(array('input-info-only'), $this->formAttr['classes']);
        $attrs['class'] = implode(' ', $classes);
        ?>
        <span <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
            <?php echo DUPX_U::esc_html($this->valueToInfo()); ?>
        </span>
        <?php
    }

    /**
     * return the text of current object fot info only status
     * 
     * @return string
     */
    protected function valueToInfo()
    {
        switch ($this->formType) {
            case self::FORM_TYPE_SELECT:
            case self::FORM_TYPE_M_CHECKBOX:
                $optionsLabels = array();
                foreach ($this->getOptions() as $option) {
                    if ($this->isValueInValue($option->value)) {
                        $optionsLabels[] = $option->label;
                    }
                }
                return implode(', ', $optionsLabels);
            case self::FORM_TYPE_CHECKBOX:
                $result = '';
                if ($this->isValueInValue($this->formAttr['checkedValue'])) {
                    $result = '[enabled]';
                } else {
                    $result = '[disabled]';
                }
                return $result.' '.$this->formAttr['checkboxLabel'];
            case self::FORM_TYPE_RADIO:
                $optionsLabels = array();
                foreach ($this->getOptions() as $option) {
                    if ($this->isValueInValue($option->value)) {
                        return $option->label;
                    }
                }
                return '[disabled]';
            case self::FORM_TYPE_HIDDEN:
            case self::FORM_TYPE_TEXT:
            case self::FORM_TYPE_NUMBER:
            default:
                if (is_null($this->getInputValue()) || is_scalar($this->getInputValue())) {
                    return DUPX_U::esc_html($this->getInputValue());
                } else {
                    return DUPX_U::esc_html(implode(',', $this->getInputValue()));
                }
        }
    }

    /**
     * get html form option of current item
     * 
     * @param bool $echo
     * @return string
     * @throws Exception
     */
    public function getHtml($echo = true)
    {
        if ($this->isSkip() === true) {
            return '';
        }
        ob_start();

        try {
            $this->htmlItemBefore();
            if ($this->getFormStatus() == self::STATUS_INFO_ONLY) {
                $this->infoOnlyHtml();
            } else {
                $this->htmlItem();
            }
            $this->htmlItemAfter();
        }
        catch (Exception $e) {
            ob_end_flush();
            throw $e;
        }


        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return ob_get_clean();
        }
    }

    /**
     * input item before (wrapper input and label)
     */
    protected function htmlItemBefore()
    {
        if (!empty($this->formAttr['wrapperTag'])) {
            $wrapperAttrs = array();
            if (!empty($this->formAttr['wrapperId'])) {
                $wrapperAttrs['id'] = $this->formAttr['wrapperId'];
            }

            if (!empty($this->formAttr['wrapperClasses'])) {
                $wrapperAttrs['class'] = implode(' ', $this->formAttr['wrapperClasses']);
            }

            foreach ($this->formAttr['wrapperAttr'] as $attrName => $attrVal) {
                $wrapperAttrs[$attrName] = $attrVal;
            }

            echo '<'.$this->formAttr['wrapperTag'].' '.DUPX_U_Html::arrayAttrToHtml($wrapperAttrs).' >';

            if (!empty($this->formAttr['wrapperContainerTag'])) {
                echo '<'.$this->formAttr['wrapperContainerTag'].' class="container" >';
            }
        }

        $this->getLabelHtml();
        $this->htmlInputContBefore();
        if (!empty($this->formAttr['inputContainerTag'])) {
            echo '<span class="'.implode(' ', $this->formAttr['inputContainerClasses']).'">';
        }
    }

    /**
     * function called  between label and input container
     * used on extended classes
     */
    protected function htmlInputContBefore()
    {
        
    }

    /**
     * function calle after input container
     * used on extended classes
     * 
     */
    protected function htmlInputContAfter()
    {
        
    }

    /**
     * input item after (close wrapper) 
     */
    protected function htmlItemAfter()
    {
        if (!empty($this->formAttr['inputContainerTag'])) {
            echo '</span>';
        }

        $subNote = empty($this->formAttr['subNote']) ? '' : '<div class="sub-note" >'.$this->formAttr['subNote'].'</div>';

        $this->htmlInputContAfter();
        if (!empty($this->formAttr['wrapperTag'])) {
            if (!empty($this->formAttr['wrapperContainerTag'])) {
                echo '</'.$this->formAttr['wrapperContainerTag'].'>';
            }
            echo $subNote;
            echo '</'.$this->formAttr['wrapperTag'].'>';
        } else {
            echo $subNote;
        }
    }

    /**
     * html if type is hidden
     */
    protected function hiddenHtml()
    {
        $attrs = array(
            'id'    => $this->formAttr['id'],
            'name'  => $this->getAttrName(),
            'value' => $this->getInputValue()
        );

        if ($this->isDisabled()) {
            $attrs['disabled'] = 'disabled';
        }

        if (!empty($this->formAttr['classes'])) {
            $attrs['class'] = implode(' ', $this->formAttr['classes']);
        }

        $attrs = array_merge($attrs, $this->formAttr['attr']);
        ?>
        <input type="hidden" <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
        <?php
    }

    /**
     * html if type is input (text/number)
     */
    protected function inputHtml($type)
    {
        $attrs = array(
            'type'  => $type,
            'id'    => $this->formAttr['id'],
            'name'  => $this->getAttrName(),
            'value' => $this->getInputValue()
        );

        if ($this->isDisabled()) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->isReadonly()) {
            $attrs['readonly'] = 'readonly';
        }

        if (!is_null($this->formAttr['maxLength'])) {
            $attrs['maxLength'] = $this->formAttr['maxLength'];
        }

        if (!is_null($this->formAttr['size'])) {
            $attrs['size'] = $this->formAttr['size'];
        }

        if (isset($this->formAttr['min']) && !is_null($this->formAttr['min'])) {
            $attrs['min'] = $this->formAttr['min'];
        }

        if (isset($this->formAttr['max']) && !is_null($this->formAttr['max'])) {
            $attrs['max'] = $this->formAttr['max'];
        }

        if (isset($this->formAttr['step']) && !is_null($this->formAttr['step'])) {
            $attrs['step'] = $this->formAttr['step'];
        }

        if (!empty($this->formAttr['classes'])) {
            $attrs['class'] = implode(' ', $this->formAttr['classes']);
        }

        $isPrePostFix = false;
        if ($this->formAttr['postfixElement'] != 'none') {
            $isPrePostFix = true;
            $postfixAttrs = array('class' => 'postfix');
            switch ($this->formAttr['postfixElement']) {
                case 'button':
                    $postfixTag           = 'button';
                    $postfixAttrs['type'] = 'button';
                    if (!empty($this->formAttr['postfixBtnAction'])) {
                        $postfixAttrs['onclick'] = $this->formAttr['postfixBtnAction'];
                    }
                    break;
                case 'label':
                    $postfixTag = 'span';
                    break;
            }
            if (!empty($this->formAttr['postfixElemId'])) {
                $postfixAttrs['id'] = $this->formAttr['postfixElemId'];
            }
            $postFixHtml = '<'.$postfixTag.' '.DUPX_U_Html::arrayAttrToHtml($postfixAttrs).'>'.$this->formAttr['postfixElemLabel'].'</'.$postfixTag.'>';
        } else {
            $postFixHtml = '';
        }

        if ($this->formAttr['prefixElement'] != 'none') {
            $isPrePostFix = true;
            $prefixAttrs  = array('class' => 'prefix');
            switch ($this->formAttr['prefixElement']) {
                case 'button':
                    $prefixTag           = 'button';
                    $prefixAttrs['type'] = 'button';
                    if (!empty($this->formAttr['prefixBtnAction'])) {
                        $prefixAttrs['onclick'] = $this->formAttr['prefixBtnAction'];
                    }
                    break;
                case 'label':
                    $prefixTag = 'span';
                    break;
            }
            if (!empty($this->formAttr['prefixElemId'])) {
                $prefixAttrs['id'] = $this->formAttr['prefixElemId'];
            }
            $preFixHtml = '<'.$prefixTag.' '.DUPX_U_Html::arrayAttrToHtml($prefixAttrs).'>'.$this->formAttr['prefixElemLabel'].'</'.$prefixTag.'>';
        } else {
            $preFixHtml = '';
        }

        $attrs = array_merge($attrs, $this->formAttr['attr']);
        if ($isPrePostFix) {
            ?>
            <span class="input-postfix-btn-group">
                <?php
            }
            echo $preFixHtml;
            ?>
            <input <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
            <?php
            echo $postFixHtml;
            if ($isPrePostFix) {
                ?>
            </span>
            <?php
        }
    }

    /**
     * html if type is select
     */
    protected function selectHtml()
    {
        $attrs = array(
            'id'   => $this->formAttr['id'],
            'name' => $this->getAttrName().($this->formAttr['multiple'] ? '[]' : ''),
        );

        if (!empty($this->formAttr['classes'])) {
            $attrs['class'] = implode(' ', $this->formAttr['classes']);
        }

        if ($this->isDisabled()) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->isReadonly()) {
            $attrs['readonly'] = 'readonly';
        }

        if ($this->formAttr['multiple']) {
            $attrs['multiple'] = '';
        }

        $attrs['size'] = $this->formAttr['size'] == 0 ? count($this->getOptions()) : $this->formAttr['size'];

        $attrs = array_merge($attrs, $this->formAttr['attr']);
        ?>
        <select <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
            <?php
            foreach ($this->getOptions() as $option) {
                if ($option->isHidden()) {
                    continue;
                }

                $optAttr = array(
                    'value' => $option->value
                );

                if ($option->isDisabled()) {
                    $optAttr['disabled'] = 'disabled';
                } else if ($this->isValueInValue($option->value)) {
                    // can't be selected if is disabled
                    $optAttr['selected'] = 'selected';
                }

                $optAttr = array_merge($optAttr, (array) $option->attrs);
                ?>
                <option <?php echo DUPX_U_Html::arrayAttrToHtml($optAttr); ?> >
                    <?php echo DUPX_U::esc_html($option->label); ?>
                </option>
            <?php }
            ?>
        </select>
        <?php
    }

    /**
     * html if type is checkbox
     */
    protected function checkBoxHtml($switch)
    {
        $attrs = array(
            'id'    => $this->formAttr['id'],
            'name'  => $this->getAttrName(),
            'value' => $this->formAttr['checkedValue']
        );

        if (!empty($this->formAttr['classes'])) {
            $attrs['class'] = implode(' ', $this->formAttr['classes']);
        }

        if ($this->isDisabled()) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->isReadonly()) {
            $attrs['readonly'] = 'readonly';
        }

        if ($this->isValueInValue($this->formAttr['checkedValue'])) {
            $attrs['checked'] = 'checked';
        }

        $attrs = array_merge($attrs, $this->formAttr['attr']);
        if ($switch) {
            DUPX_U_Html::checkboxSwitch($attrs);
        } else {
            ?>
            <input type="checkbox" <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
        <?php } ?>
        <span class="label-checkbox" ><?php echo $this->formAttr['checkboxLabel']; ?></span>
        <?php
    }

    /**
     *  html if type is multiple checkboxes
     */
    protected function mCheckBoxHtml()
    {
        /*
         * for radio don't use global attr but option attr
         * $attrs = array_merge($attrs, $this->formAttr['attr']);
         */
        foreach ($this->getOptions() as $index => $option) {
            $attrs = array(
                'id'    => $this->formAttr['id'].'_'.$index,
                'name'  => $this->getAttrName().'[]',
                'value' => $option->value
            );

            if (!empty($this->formAttr['classes'])) {
                $attrs['class'] = implode(' ', $this->formAttr['classes']);
            }

            if ($this->isValueInValue($option->value)) {
                $attrs['checked'] = 'checked';
            }

            if ($option->isDisabled() == true) {
                $attrs['disabled'] = 'disabled';
            }

            $attrs = array_merge($attrs, $option->attrs);
            if (!empty($attrs['title'])) {
                $labelTtile = ' title="'.DUPX_U::esc_attr($attrs['title']).'"';
                unset($attrs['title']);
            } else {
                $labelTtile = '';
            }
            ?>
            <label class="option-group" <?php echo $labelTtile; ?>>
                <input type="checkbox" <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> > <span class="label-checkbox" ><?php echo DUPX_U::esc_html($option->label); ?></span>
            </label>
            <?php
        }
    }

    /**
     *  html if type is radio
     */
    protected function radioHtml()
    {
        /*
         * for radio don't use global attr but option attr
         * $attrs = array_merge($attrs, $this->formAttr['attr']);
         */
        foreach ($this->getOptions() as $index => $option) {
            if ($option->isHidden()) {
                continue;
            }

            $attrs = array(
                'id'    => $this->formAttr['id'].'_'.$index,
                'name'  => $this->getAttrName(),
                'value' => $option->value
            );

            if (!empty($this->formAttr['classes'])) {
                $attrs['class'] = implode(' ', $this->formAttr['classes']);
            }

            if ($this->isValueInValue($option->value)) {
                $attrs['checked'] = 'checked';
            }

            if ($option->isDisabled() == true) {
                $attrs['disabled'] = 'disabled';
            }

            $attrs = array_merge($attrs, $option->attrs);
            if (!empty($attrs['title'])) {
                $labelTtile = ' title="'.DUPX_U::esc_attr($attrs['title']).'"';
                unset($attrs['title']);
            } else {
                $labelTtile = '';
            }
            ?>
            <label class="option-group" <?php echo $labelTtile; ?>>
                <input type="radio" <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> > <span class="label-checkbox" ><?php echo DUPX_U::esc_html($option->label); ?></span>
            </label>
            <?php
        }
    }

    /**
     * get current label html
     * 
     * @param bool $echo
     * @return string
     */
    protected function getLabelHtml($echo = true)
    {
        if (!empty($this->formAttr['label'])) {
            $attrs = array();

            if (!empty($this->formAttr['labelClasses'])) {
                $attrs['class'] = implode(' ', $this->formAttr['labelClasses']);
            }
            ob_start();
            ?>
            <span <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
                <?php echo DUPX_U::esc_html($this->formAttr['label']); ?>
            </span>
            <?php
            if ($echo) {
                ob_end_flush();
                return '';
            } else {
                return ob_get_clean();
            }
        } else {
            return '';
        }
    }

    /**
     * set value from array. This function is used to set data from json array
     * 
     * @param array $data
     * @return boolean
     */
    public function fromArrayData($data)
    {
        $result = parent::fromArrayData($data);
        if (isset($data['formStatus'])) {
            $this->formAttr['status'] = $data['formStatus'];
        }
        return $result;
    }

    /**
     * return array dato to store in json array data
     * @return array
     */
    public function toArrayData()
    {
        $result = parent::toArrayData();
        if (!is_callable($this->formAttr['status'])) {
            $result['formStatus'] = $this->getFormStatus();
        }
        return $result;
    }

    /**
     * update the value from input if exists ot set the default
     * sanitation and validation are performed
     * skip set value if current status is disabled, info only or skip
     * 
     * @param string $method
     * @return boolean  // false if value isn't validated
     * @throws Exception
     */
    public function setValueFromInput($method = self::INPUT_POST)
    {
        // if input is disabled don't reads from input.
        if (
            $this->formAttr['status'] == self::STATUS_INFO_ONLY ||
            $this->formAttr['status'] == self::STATUS_SKIP) {
            return true;
        }

        // prevent overwrite by default if item is disable and isn't enable in client by js
        $superObject = self::getSuperObjectByMethod($method);
        if ($this->formAttr['status'] == self::STATUS_DISABLED && !$this->isValueInInput($superObject)) {
            return true;
        }

        return parent::setValueFromInput($method);
    }

    /**
     * this function return the default formAttr for each type.
     * in the constructor an array merge is made between the result of this function and the parameters passed.
     * In this way the values ​​in $ this -> ['attr'] are always consistent.
     * 
     * @param string $formType
     * @return array
     * @throws Exception
     */
    protected static function getDefaultAttrForFormType($formType)
    {
        $attrs = array(
            'label'                 => null, // input main label
            'labelClasses'          => array('label', 'main-label'), // label classes (the label html is <span class="classes" >label</span>
            'id'                    => null, // input id , if null the default is 'param_item_'.$name
            'classes'               => array('input-item'), // input classes
            'status'                => self::STATUS_ENABLED, // form status
            'title'                 => null, // input title
            'attr'                  => array(), // custom input attributes key="VALUE"
            'subNote'               => null, // sub note container (html string),
            'wrapperTag'            => 'div', // if null the input haven't the wrapper tag. input tag wrapper, wrapper html is ~ <TAG class="classes" ><CONTAINER><LABEL><INPUT CONTAINER></CONTAINER></TAG>
            'wrapperId'             => null, // input wrapper id, if null the default is 'wrapper_item_'.$name
            'wrapperClasses'        => array(// wrapper classes, param-wrapper generic class plus 'param-form-type-'.$formType type class
                'param-wrapper',
                'param-form-type-'.$formType),
            'wrapperAttr'           => array(), // custom wrapper attributes key="VALUE"
            'wrapperContainerTag'   => 'label',
            'inputContainerTag'     => 'span',
            'inputContainerClasses' => array('input-container'),
        );

        switch ($formType) {
            case self::FORM_TYPE_HIDDEN:
                $attrs['wrapperTag']          = null; // disable wrapper for hidden inputs
                $attrs['wrapperContainerTag'] = null;
                $attrs['inputContainerTag']   = null;
                break;
            case self::FORM_TYPE_NUMBER:
                $attrs['min']                 = null; // attr min
                $attrs['max']                 = null; // attr max
                $attrs['step']                = null; // attr step
            // continue form type text
            case self::FORM_TYPE_TEXT:
                $attrs['maxLength']           = null;     // if null have no limit
                $attrs['size']                = null;
                $attrs['prefixElement']       = 'none';  // none | button | label
                $attrs['prefixElemLabel']     = null;
                $attrs['prefixElemId']        = null;
                $attrs['prefixBtnAction']     = null;
                $attrs['postfixElement']      = 'none';  // none | button | label
                $attrs['postfixElemLabel']    = null;
                $attrs['postfixElemId']       = null;
                $attrs['postfixBtnAction']    = null;
                break;
            case self::FORM_TYPE_SELECT:
                $attrs['multiple']            = false;
                $attrs['options']             = array();  // DUPX_Param_item_form_option[] | callback
                $attrs['size']                = 1;        // select size if 0 get num options
                break;
            case self::FORM_TYPE_CHECKBOX:
            case self::FORM_TYPE_SWITCH:
                $attrs['checkboxLabel']       = null;
                $attrs['checkedValue']        = true;
                break;
            case self::FORM_TYPE_M_CHECKBOX:
                $attrs['options']             = array();  // DUPX_Param_item_form_option[]  | callback
                $attrs['wrapperContainerTag'] = 'div';

                break;
            case self::FORM_TYPE_RADIO:
                $attrs['options']             = array();  // DUPX_Param_item_form_option[]  | callback
                $attrs['wrapperContainerTag'] = 'div';
                break;
            default:
            // accepts unknown values ​​because this class can be extended
        }

        return $attrs;
    }
}