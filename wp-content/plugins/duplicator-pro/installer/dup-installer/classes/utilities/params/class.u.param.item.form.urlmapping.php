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
 * this class handles the entire block selection block.
 */
class DUPX_Param_item_form_urlmapping extends DUPX_Param_item_form
{

    const FORM_TYPE_URL_MAPPING = 'url_mapping';

    protected $currentSubsiteId = -1;

    protected function htmlItem()
    {
        if ($this->formType == self::FORM_TYPE_URL_MAPPING) {
            $this->mappingForm();
        } else {
            parent::htmlItem();
        }
    }

    protected function getAttrName()
    {
        return $this->name.'['.$this->currentSubsiteId.']';
    }

    /**
     * 
     * @return mixed
     */
    protected function getInputValue()
    {
        return $this->value[$this->currentSubsiteId];
    }

    protected function mappingForm($infoOnly = false)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $oldList        = $archive_config->getOldUrlsArrayIdVal();

        $subsiteIds = array_keys($this->value);

        $mainInputId = $this->formAttr['id'];
        $mainStatus  = $this->formAttr['status'];

        foreach ($subsiteIds as $subsiteId) {
            $this->currentSubsiteId = $subsiteId;
            $this->formAttr['id']   = $mainInputId.'_'.$this->currentSubsiteId;
            if ($this->currentSubsiteId == $archive_config->main_site_id) {
                $mainSiteClass            = 'main-site';
                $this->formAttr['status'] = self::STATUS_READONLY;
            } else {
                $mainSiteClass            = '';
                $this->formAttr['status'] = $mainStatus;
            }
            ?>
            <span class="url-mapping-entry <?php echo $mainSiteClass; ?>" >
                <span class="from-input-wrapper" >
                    <input type="text" readonly="readonly" class="old_url_mapping" value="<?php echo DUPX_U::esc_attr($oldList[$subsiteId]) ?>">
                </span><span class="to-label-wrapper">to
                </span><span class="from-input-wrapper" ><?php
                    if ($infoOnly) {
                        parent::infoOnlyHtml();
                    } else {
                        parent::inputHtml('text');
                    }
                    ?></span>
            </span>
            <?php
        }
        $this->formAttr['id']     = $mainInputId;
        $this->formAttr['status'] = $mainStatus;
    }

    protected function infoOnlyHtml()
    {
        $this->mappingForm(true);
    }

    protected static function getDefaultAttrForFormType($formType)
    {
        $attrs = parent::getDefaultAttrForFormType(self::FORM_TYPE_TEXT);
        if ($formType == self::FORM_TYPE_URL_MAPPING) {
            $attrs['wrapperContainerTag'] = 'div';
            $attrs['inputContainerTag']   = 'div';
        }
        return $attrs;
    }
}