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
class DUPX_Param_item_form_plugins extends DUPX_Param_item_form
{

    const FORM_TYPE_PLUGINS_SELECT = 'pluginssel';

    protected function htmlItem()
    {
        if ($this->formType == self::FORM_TYPE_PLUGINS_SELECT) {
            $this->pluginSelectHtml();
        } else {
            parent::htmlItem();
        }
    }
    
    protected function pluginSelectHtml()
    {
        $pluginsManager = DUPX_Plugins_Manager::getInstance();
        $plugns_list    = $pluginsManager->getPlugins();

        $attrs                       = array(
            'id'       => $this->formAttr['id'],
            'name'     => $this->getAttrName().'[]',
            'multiple' => ''
        );
        $this->formAttr['classes'][] = 'no-display';

        if (!empty($this->formAttr['classes'])) {
            $attrs['class'] = implode(' ', array_unique($this->formAttr['classes']));
        }

        if ($this->isDisabled()) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->isReadonly()) {
            $attrs['readonly'] = 'readonly';
        }

        $attrs = array_merge($attrs, $this->formAttr['attr']);
        ?>
        <select <?php echo DUPX_U_Html::arrayAttrToHtml($attrs); ?> >
            <?php
            foreach ($plugns_list as $pluginSlug => $plugin) {
                if ($plugin->isIgnore() || $plugin->isForceDisabled()) {
                    continue;
                }
                $optAttr = array(
                    'value' => $pluginSlug
                );
                if ($this->isValueInValue($pluginSlug)) {
                    // can't be selected if is disabled
                    $optAttr['selected'] = 'selected';
                }
                ?>
                <option <?php echo DUPX_U_Html::arrayAttrToHtml($optAttr); ?> >
                    <?php echo DUPX_U::esc_html($plugin->name); ?>
                </option>
                <?php
            }
            ?>
        </select>
        <?php
        if (!empty($this->formAttr['subNote'])) {
            ?><span class="sub-note" ><?php echo $this->formAttr['subNote']; ?></span><?php
        }
        self::pluginsSelector();
    }

    protected function pluginsSelector()
    {
        $pluginsManager = DUPX_Plugins_Manager::getInstance();
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $plugns_list    = $pluginsManager->getPlugins();
        $paramsManager  = DUPX_Paramas_Manager::getInstance();
        $subsiteId      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
        $safe_mode      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SAFE_MODE);
        ?>
        <b>Activate<?php echo $archive_config->isNetworkInstall() ? ' Network ' : ' '; ?>Plugins:</b>
        <?php echo ($safe_mode > 0) ? '<small class="s3-warn">Safe Mode Enabled</small>' : ''; ?>
        <div class="s3-allnonelinks" style="<?php echo ($safe_mode > 0) ? 'display:none' : ''; ?>">
            <button type="button" id="select-all-plugins" class="no-layout">[All]</button>
            <button type="button" id="unselect-all-plugins" class="no-layout">[None]</button>
        </div><br style="clear:both" />

        <ul id="plugins-filters" >
            <li class="all" data-filter-target="all" >
                <a href="#" class="current">
                    All<span class="count">(<?php echo count($plugns_list); ?>)</span>
                </a>
            </li>
            <?php
            foreach ($pluginsManager->getStatusCounts($subsiteId) as $status => $count) {
                if ($count) {
                    ?>
                    <li class="<?php echo DUPX_U::esc_attr($status); ?>" data-filter-target="orig-<?php echo DUPX_U::esc_attr($status); ?>" >
                        <a href="#">
                            <?php echo DUPX_U::esc_html(DUPX_Plugin_item::getStatusLabel($status)); ?><span class="count">(<?php echo $count; ?>)</span>
                        </a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>

        <table id="plugins_list_table_selector" classes="<?php echo ($safe_mode > 0) ? 'disabled' : ''; ?>" >
            <thead>
                <tr>
                    <th class="check_input" ></th>
                    <th class="name" >Plugin name</th>
                    <th class="info" >Info</th>
                    <th class="orig_status" >original<br>status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($plugns_list as $pluginObj) {
                    if ($pluginObj->isIgnore() || $pluginObj->isForceDisabled()) {
                        continue;
                    }
                    $this->pluginHtmlItem($pluginObj, $subsiteId);
                }
                ?>
            </tbody>
        </table>
        <?php
        $this->pluginTableSelectorJs();
    }

    /**
     * 
     * @param DUPX_Plugin_item $pluginObj
     * @param int $subsiteId
     */
    protected function pluginHtmlItem($pluginObj, $subsiteId)
    {
        $itemClasses   = array(
            'plugin-item',
        );
        $orgiStats     = $pluginObj->getOrgiStatus($subsiteId);
        $itemClasses[] = 'orig-'.$orgiStats;
        $itemClasses[] = $this->isValueInValue($pluginObj->slug) ? 'active' : 'inactive';

        //$authorURI = $pluginObj->authorURI;
        if (empty($pluginObj->authorURI)) {
            $author = DUPX_U::esc_html($pluginObj->author);
        } else {
            $author = '<a href="'.DUPX_U::esc_attr($pluginObj->authorURI).'" target="_blank">'.DUPX_U::esc_html($pluginObj->author).'</a>';
        }
        ?>
        <tr class="<?php echo implode(' ', $itemClasses); ?>" data-plugin-slug="<?php echo DUPX_U::esc_attr($pluginObj->slug); ?>">
            <td class="check_input" ><input type="checkbox" ></td>
            <td class="name" ><?php echo DUPX_U::esc_html($pluginObj->name); ?></td>
            <td class="info" >
                Version: <?php echo DUPX_U::esc_html($pluginObj->version); ?><br>
                Plugin URL: <a href=" <?php echo DUPX_U::esc_attr($pluginObj->pluginURI); ?>" target="_blank" ><?php echo DUPX_U::esc_html($pluginObj->pluginURI); ?></a><br>
                Author: <?php echo $author; ?><br>
            </td>
            <td class="orig_status" ><?php echo DUPX_U::esc_html($pluginObj->getStatusLabel($orgiStats)); ?></td>
        </tr>
        <?php
    }

    protected function pluginTableSelectorJs()
    {
        ?>
        <script>
            (function ($) {
                var pluginsSelect = $('#' + <?php echo DupProSnapJsonU::wp_json_encode($this->formAttr['id']); ?>);
                var tableSelect = $('#plugins_list_table_selector');

                function setItemTable(item, enable) {
                    if (enable) {
                        item.removeClass('inactive').addClass('active');
                        item.find('.check_input input').prop('checked', true);
                        pluginsSelect.find('option[value="' + item.data('plugin-slug') + '"]').prop('selected', true);
                    } else {
                        item.removeClass('active').addClass('inactive');
                        item.find('.check_input input').prop('checked', false);
                        pluginsSelect.find('option[value="' + item.data('plugin-slug') + '"]').prop('selected', false);
                    }
                }

                // prevent select on unselect on external link click
                tableSelect.find('.plugin-item a').click(function (event) {
                    event.stopPropagation();
                    return true;
                });

                tableSelect.find('.plugin-item').each(function () {
                    var current = $(this);

                    // init select element
                    if (current.hasClass('active')) {
                        setItemTable(current, true);
                    } else {
                        setItemTable(current, false);
                    }

                    // change on click  
                    current.click(function () {
                        if (current.hasClass('active')) {
                            setItemTable(current, false);
                        } else {
                            setItemTable(current, true);
                        }
                    });
                });

                $('#select-all-plugins').click(function () {
                    tableSelect.find('.plugin-item').each(function () {
                        setItemTable($(this), true);
                    });
                });

                $('#unselect-all-plugins').click(function () {
                    tableSelect.find('.plugin-item').each(function () {
                        setItemTable($(this), false);
                    });
                });

                $('#plugins-filters a').click(function () {
                    var obj = $(this);
                    if (obj.hasClass('current')) {
                        return false;
                    }

                    $('#plugins-filters a').removeClass('current');
                    obj.addClass('current');

                    var filterTarget = obj.parent().data('filter-target');
                    if (filterTarget === 'all') {
                        tableSelect.find('.plugin-item').removeClass('no-display');
                    } else {
                        tableSelect.find('.plugin-item').removeClass('no-display').not('.' + filterTarget).addClass('no-display');
                    }

                    return false;
                });
            })(jQuery);
        </script>
        <?php
    }

    protected static function getDefaultAttrForFormType($formType)
    {
        $attrs = parent::getDefaultAttrForFormType($formType);
        if ($formType == self::FORM_TYPE_PLUGINS_SELECT) {
            $attrs['wrapperContainerTag'] = 'div';
            $attrs['inputContainerTag']   = 'div';
        }
        return $attrs;
    }
}