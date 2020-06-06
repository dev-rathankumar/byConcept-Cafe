<?php
defined("ABSPATH") or die("");

/**
 * Gets the view state of UI elements to remember its viewable state
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.3.0
 *
 */
class DUP_PRO_UI_ViewState
{

    /**
     * The key used in the wp_options table
     */
    private static $optionsTableKey = 'duplicator_pro_ui_view_state';

    /**
     * Save the view state of UI elements
     *
     * @param string $key A unique key to define the UI element
     * @param string $value A generic value to use for the view state
     *
     * @return bool Returns true if the value was successfully saved
     */
    public static function save($key, $value)
    {
        $view_state       = array();
        $view_state       = get_option(self::$optionsTableKey);
        $view_state[$key] = $value;
        $success          = update_option(self::$optionsTableKey, $view_state);
        return $success;
    }

    /**
     * Saves the state of a UI element via post params
     *
     * @return json result string
     *
     * <code>
     * //JavaScript Ajax Request
     * DupPro.UI.SaveViewStateByPost('dup-pack-archive-panel', 1);
     *
     * //Call PHP Code
     * $view_state       = DUP_PRO_UI_ViewState::getValue('dup-pack-archive-panel');
     * $ui_css_archive   = ($view_state == 1)   ? 'display:block' : 'display:none';
     * </code>
     *
     * @todo: Move this method to a controller see dlite (ctrl)
     */
    public static function saveByPost()
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_UI_ViewState_SaveByPost', 'nonce');
        DUP_PRO_U::hasCapability('export');

        $json    = array(
            'update-success' => false,
            'error-message'  => '',
            'key'            => '',
            'value'          => ''
        );
        $isValid = true;

        $inputData = filter_input_array(INPUT_POST, array(
            'states' => array(
                'filter'  => FILTER_SANITIZE_STRING,
                'flags'   => FILTER_FORCE_ARRAY,
                'options' => array(
                    'default' => array()
                )
            ),
            'key'    => array(
                'filter'  => FILTER_SANITIZE_STRING,
                'options' => array(
                    'default' => false
                )
            ),
            'value'  => array(
                'filter'  => FILTER_SANITIZE_STRING,
                'options' => array(
                    'default' => false
                )
            )
        ));

        foreach ($inputData['states'] as $index => $state) {
            $filteredState = filter_var_array($state, array(
                'key'   => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'options' => array(
                        'default' => false
                    )
                ),
                'value' => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'options' => array(
                        'default' => false
                    )
            )));
            if ($filteredState['key'] === false && $filteredState['value']) {
                $isValid = false;
                break;
            }
            $inputData['states'][$index] = $filteredState;
        }
        if ($inputData['key'] === false || $inputData['value'] === false) {
            $isValid = false;
        }
        // VALIDATIO END

        if ($isValid) {
            if (!empty($inputData['states'])) {
                $view_state = self::getArray();
                $last_key = '';
                foreach ($inputData['states'] as $state) {
                    $view_state[$state['key']] = $state['value'];
                    $last_key = $state['key'];
                }
                $json['update-success'] = self::setArray($view_state);
                $json['key']            = esc_html($last_key);
                $json['value']          = esc_html($view_state[$last_key]);
            } else {
                $json['update-success'] = self::save($inputData['key'], $inputData['value']);
                $json['key']            = esc_html($inputData['key']);
                $json['value']          = esc_html($inputData['value']);
            }
        } else {
            $json['update-success'] = false;
            $json['error-message']  = "Sent data is not valid.";
        }

        die(json_encode($json));
    }

    /**
     * 	Gets all the values from the settings array
     *
     *  @return array Returns and array of all the values stored in the settings array
     */
    public static function getArray()
    {
        return get_option(self::$optionsTableKey);
    }

    /**
     * Sets all the values from the settings array
     * @param array $view_state states
     * 
     * @return boolean Returns whether updated or not
     */
    public static function setArray($view_state)
    {
        return update_option(self::$optionsTableKey, $view_state);
    }

    /**
     * Return the value of the of view state item
     *
     * @param type $searchKey The key to search on
     * 
     * @return string Returns the value of the key searched or null if key is not found
     */
    public static function getValue($searchKey)
    {
        $view_state = get_option(self::$optionsTableKey);
        if (is_array($view_state)) {
            foreach ($view_state as $key => $value) {
                if ($key == $searchKey) {
                    return $value;
                }
            }
        }
        return null;
    }
}