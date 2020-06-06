<?php
/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class DUPX_Paramas_Descriptors
{

    const INVALID_PATH_EMPTY = 'can\'t be empty';
    const INVALID_URL_EMPTY  = 'can\'t be empty';

    public static function initPriorityParams(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $paths          = $archive_config->getRealValue('archivePaths');

        $oldMainPath = $paths->home;
        $newMainPath = DUPX_ROOT;

        $oldHomeUrl = rtrim($archive_config->getRealValue('homeUrl'), '/');
        $newHomeUrl = rtrim(DUPX_ROOT_URL, '/');

        $oldSiteUrl      = rtrim($archive_config->getRealValue('siteUrl'), '/');
        $oldContentUrl   = rtrim($archive_config->getRealValue('contentUrl'), '/');
        $oldUploadUrl    = rtrim($archive_config->getRealValue('uploadBaseUrl'), '/');
        $oldPluginsUrl   = rtrim($archive_config->getRealValue('pluginsUrl'), '/');
        $oldMuPluginsUrl = rtrim($archive_config->getRealValue('mupluginsUrl'), '/');

        $oldWpAbsPath       = $paths->abs;
        $oldContentPath     = $paths->wpcontent;
        $oldUploadsBasePath = $paths->uploads;
        $oldPluginsPath     = $paths->plugins;
        $oldMuPluginsPath   = $paths->muplugins;

        $EditOldUrlMsg = "This is the URL that was generated when the package was created.\n"
            ."Changing this value may cause issues with the install process.\n\n"
            ."Only modify  this value if you know exactly what the value should be.\n"
            ."See \"General Settings\" in the WordPress Administrator for more details.\n\n"
            ."Are you sure you want to continue?";

        $oldPathMsg = "This is a OLD PATH that was generated when the package was created.\n"
            ."Changing this value may cause issues with the install process.\n\n"
            ."Only modify this value if you know exactly what the value should be.\n"
            ."Are you sure you want to continue?";

        $defValEdit = "This default value is automatically generated.\n"
            ."Change it only if you're sure you know what you're doing!";


        $params[DUPX_Paramas_Manager::PARAM_URL_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldHomeUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_INFO_ONLY,
            'label'            => 'Old Site URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($EditOldUrlMsg).');'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $newHomeUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'New Site URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'get',
            'postfixBtnAction' => 'DUPX.getNewUrlByDomObj(this);',
            'wrapperAttr'      => array(
                'data-original-default-value' => $newHomeUrl
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldMainPath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_INFO_ONLY,
            'label'            => 'Old Path:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($oldPathMsg).');'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $newMainPath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => function ($value) {
                if (!is_dir($value)) {
                    return false;
                }

                // don't check the return of chmod, if fail the installer must continue
                DupProSnapLibIOU::chmod($value, 'u+rwx');
                return true;
            },
            'invalidMessage' => 'The new path must be an existing folder on the server.<br>'
            .'It is not possible to continue the installation without first creating the folder.'
            ), array(// FORM ATTRIBUTES
            'label'       => 'New Path:',
            'classes'     => array('revalidate'),
            'wrapperAttr' => array(
                'data-original-default-value' => $newMainPath
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SITE_URL_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SITE_URL_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldSiteUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old WP core URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($EditOldUrlMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldSiteUrl)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_SITE_URL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SITE_URL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => (string) $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => ' WP core URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldSiteUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldContentPath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old wp-content path:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($oldPathMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $oldContentPath)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'wp-content path:',
            'classes'          => array('revalidate'),
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldContentPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldWpAbsPath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old WP core path:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($oldPathMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $oldWpAbsPath)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'WP core path:',
            'classes'          => array('revalidate'),
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldWpAbsPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldUploadsBasePath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old uploads path:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($oldPathMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $oldUploadsBasePath)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'uploads path:',
            'classes'          => array('revalidate'),
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldUploadsBasePath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldContentUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old wp-content URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($EditOldUrlMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldContentUrl)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'wp-content URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldContentUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );


        $params[DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldUploadUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old uploads URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($EditOldUrlMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldUploadUrl)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'uploads URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldUploadUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldPluginsUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old plugins URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($EditOldUrlMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldPluginsUrl)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'plugins URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldPluginsUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldPluginsPath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old plugins path:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($oldPathMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $oldPluginsPath)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'plugins path:',
            'classes'          => array('revalidate'),
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldPluginsPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldMuPluginsUrl,
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old mu-plugins URL:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($EditOldUrlMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldMuPluginsUrl)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizeUrl'),
            'validateCallback' => array(__CLASS__, 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'mu-plugins URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldMuPluginsUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $oldMuPluginsPath,
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'status'           => DUPX_Param_item_form::STATUS_SKIP,
            'label'            => 'Old mu-plugins path:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($oldPathMsg).');'
            )
        );

        if (($default = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $oldMuPluginsPath)) === false) {
            $wrapClasses    = array();
            $postFixElement = 'none';
            $status         = DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            $wrapClasses    = array('auto-updatable', 'autoupdate-enabled');
            $postFixElement = 'button';
            $status         = DUPX_Param_item_form::STATUS_READONLY;
        }
        $params[DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $default, // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array(__CLASS__, 'sanitizePath'),
            'validateCallback' => array(__CLASS__, 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'mu-plugins path:',
            'classes'          => array('revalidate'),
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldMuPluginsPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId(),
                'data-original-default-value' => (string) $default
            )
            )
        );
    }

    /**
     * sanitize path
     *
     * @param string $value
     * @return string
     */
    public static function sanitizePath($value)
    {
        $result = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
        return DupProSnapLibIou::safePathUntrailingslashit($result);
    }

    /**
     * the path can't be empty
     *
     * @param string $value
     * @return bool
     */
    public static function validatePath($value)
    {
        return strlen($value) > 1;
    }

    /**
     * sanitize URL
     *
     * @param string $value
     * @return string
     */
    public static function sanitizeUrl($value)
    {
        $result = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
        if (empty($value)) {
            return '';
        }
        // if scheme not set add http by default
        if (!preg_match('/^[a-zA-Z]+\:\/\//', $result)) {
            $result = 'http://'.ltrim($result, '/');
        }
        return rtrim($result, '/\\');
    }

    /**
     * the url can't be empty
     *
     * @param string $value
     * @return bool
     */
    public static function validateUrlWithScheme($value)
    {
        if (empty($value)) {
            return false;
        }
        if (($parsed = parse_url($value)) === false) {
            return false;
        }
        if (!isset($parsed['host']) || empty($parsed['host'])) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function initGenericParams(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA,
            DUPX_Param_item::TYPE_ARRAY_MIXED,
            array(
            'default' => array(
                'extraction' => array(
                    'table_count' => 0,
                    'table_rows'  => 0,
                    'query_errs'  => 0,
                ),
                'replace'    => array(
                    'scan_tables' => 0,
                    'scan_rows'   => 0,
                    'scan_cells'  => 0,
                    'updt_tables' => 0,
                    'updt_rows'   => 0,
                    'updt_cells'  => 0,
                    'errsql'      => 0,
                    'errser'      => 0,
                    'errkey'      => 0,
                    'errsql_sum'  => 0,
                    'errser_sum'  => 0,
                    'errkey_sum'  => 0,
                    'err_all'     => 0,
                    'warn_all'    => 0,
                    'warnlist'    => array()
                )
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_INSTALLER_MODE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_INSTALLER_MODE,
            DUPX_Param_item::TYPE_INT,
            array(
            'default'      => DUPX_InstallerState::MODE_UNKNOWN,
            'acceptValues' => array(
                DUPX_InstallerState::MODE_UNKNOWN,
                DUPX_InstallerState::MODE_STD_INSTALL,
                DUPX_InstallerState::MODE_OVR_INSTALL,
                DUPX_InstallerState::MODE_BK_RESTORE
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA,
            DUPX_Param_item::TYPE_ARRAY_MIXED,
            array(
            'default' => array('db')
            )
        );


        $params[DUPX_Paramas_Manager::PARAM_DEBUG] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DEBUG,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'persistence' => true,
            'default'     => false
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DEBUG_PARAMS] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DEBUG_PARAMS,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'persistence' => true,
            'default'     => false
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CTRL_ACTION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CTRL_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'persistence'  => false,
            'default'      => '',
            'acceptValues' => array(
                '',
                'ajax',
                'secure',
                'ctrl-step1',
                'ctrl-step2',
                'ctrl-step3',
                'ctrl-step4',
                'help'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_STEP_ACTION] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_STEP_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'persistence'  => false,
            'default'      => '',
            'acceptValues' => array(
                '',
                DUPX_CTRL::ACTION_STEP_INIZIALIZED,
                DUPX_CTRL::ACTION_STEP_REVALIDATE
            ))
        );

        $params[DUPX_Security::CTRL_TOKEN] = new DUPX_Param_item_form(
            DUPX_Security::CTRL_TOKEN,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'persistence'      => false,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_ROUTER_ACTION] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_ROUTER_ACTION,
            DUPX_Param_item::TYPE_STRING,
            array(
            'persistence'  => false,
            'default'      => 'router',
            'acceptValues' => array(
                'router'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_SECURE_PASS] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_SECURE_PASS,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'persistence'      => false,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'Enter Password'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SECURE_OK] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_SECURE_OK,
            DUPX_Param_item_form::TYPE_BOOL,
            array(
            'default' => false
            )
        );

        $subSiteOptions  = self::getSubSiteIdsOptions();
        $muInstOptions   = self::getMultisiteInstallerTypeOptions();
        $standaloneLabel = 'Convert subsite to standalone'.(empty($muInstOptions['subNote']) ? '' : ' *');

        $params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SUBSITE_ID,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => -1,
            'acceptValues' => $subSiteOptions['acceptValues']
            ),
            array(
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE) != 1) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'label'          => 'Subsite:',
            'wrapperClasses' => $muInstOptions['default'] == 0 ? array('no-display') : array(),
            'options'        => $subSiteOptions['options'],
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => $muInstOptions['default'],
            'acceptValues' => $muInstOptions['acceptValues']
            ),
            array(
            'status'         => (!DUPX_Conf_Utils::showMultisite() || $muInstOptions['acceptValues'][0] == -1) ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'label'          => 'Install Type:',
            'wrapperClasses' => array('group-block'),
            'options'        => array(
                new DUPX_Param_item_form_option(0, 'Restore multisite network',
                    !$archive_config->mu_is_filtered ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED,
                    array(
                    'onchange' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormItemId()."').prop('disabled', true);"
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormWrapperId()."').addClass('no-display');"
                    )),
                new DUPX_Param_item_form_option(1, $standaloneLabel,
                    DUPX_Conf_Utils::multisitePlusEnabled() ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED,
                    array(
                    'onchange' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormItemId()."').prop('disabled', false);"
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormWrapperId()."').removeClass('no-display');"
                    ))
            ),
            'subNote'        => $muInstOptions['subNote'])
        );

        $params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => DUP_PRO_Extraction::ACTION_DO_NOTHING,
            'acceptValues' => array(
                DUP_PRO_Extraction::ACTION_DO_NOTHING,
                DUP_PRO_Extraction::ACTION_SKIP_CORE_FILES
            )
            ),
            array(
            'label'   => 'Extraction action:',
            'options' => array(
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::ACTION_DO_NOTHING, 'Default'),
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::ACTION_SKIP_CORE_FILES, 'Don\'t extract wp core files')
            )
            )
        );

        $engineOptions                                      = self::getArchiveEngineOptions();
        $params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => $engineOptions['default'],
            'acceptValues' => $engineOptions['acceptValues']
            ),
            array(
            'label'   => 'Extraction mode:',
            'options' => $engineOptions['options'],
            'size'    => 0,
            'subNote' => $engineOptions['subNote'],
            'attr'    => array(
                'onchange' => 'DUPX.onSafeModeSwitch();'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'          => '644',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => '/^[ugorwx,\s\+\-0-7]+$/' // octal + ugo rwx,
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'wrapperClasses' => array('display-inline-block')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => !DupProSnapLibOSU::isWindows()
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'label'          => 'File permissions:',
            'checkboxLabel'  => 'All files',
            'wrapperClasses' => array('display-inline-block'),
            'attr'           => array(
                'onclick' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE]->getFormItemId()."').prop('disabled', !jQuery(this).is(':checked'));"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'          => '755',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => '/^[ugorwx,\s\+\-0-7]+$/' // octal + ugo rwx
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'wrapperClasses' => array('display-inline-block')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => !DupProSnapLibOSU::isWindows()
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'label'          => 'Dir permissions:',
            'checkboxLabel'  => 'All Directories',
            'wrapperClasses' => array('display-inline-block'),
            'attr'           => array(
                'onclick' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE]->getFormItemId()."').prop('disabled', !jQuery(this).is(':checked'));"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SAFE_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SAFE_MODE,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => 0,
            'acceptValues' => array(0, 1, 2)),
            array(
            'label'   => 'Safe Mode:',
            'options' => array(
                new DUPX_Param_item_form_option(0, 'Light'),
                new DUPX_Param_item_form_option(1, 'Basic'),
                new DUPX_Param_item_form_option(2, 'Advanced')
            ),
            'attr'    => array(
                'onchange' => 'DUPX.onSafeModeSwitch();'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_CLIENT_KICKOFF] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CLIENT_KICKOFF,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Client-Kickoff:',
            'checkboxLabel' => 'Browser drives the archive engine.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FILE_TIME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_FILE_TIME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'current',
            'acceptValues' => array(
                'current',
                'original'
            )),
            array(
            'label'   => 'File Times:',
            'options' => array(
                new DUPX_Param_item_form_option('current', 'Current', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Set the files current date time to now')),
                new DUPX_Param_item_form_option('original', 'Original', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Keep the files date time the same'))
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_LOGGING] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_LOGGING,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => DUPX_Log::LV_DEFAULT,
            'acceptValues' => array(
                DUPX_Log::LV_DEFAULT,
                DUPX_Log::LV_DETAILED,
                DUPX_Log::LV_DEBUG,
                DUPX_Log::LV_HARD_DEBUG,
            )),
            array(
            'label'   => 'Logging:',
            'options' => array(
                new DUPX_Param_item_form_option(DUPX_Log::LV_DEFAULT, 'Light'),
                new DUPX_Param_item_form_option(DUPX_Log::LV_DETAILED, 'Detailed'),
                new DUPX_Param_item_form_option(DUPX_Log::LV_DEBUG, 'Debug'),
                // enabled only with overwrite params
                new DUPX_Param_item_form_option(DUPX_Log::LV_HARD_DEBUG, 'Hard debug', DUPX_Param_item_form_option::OPT_HIDDEN)
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Remove Inactive Plugins and Themes:',
            'checkboxLabel' => 'Remove inactive themes and plugins.',
            'wrapperId'     => 'remove-redundant-row',
            'subNote'       => DUPX_Conf_Utils::showMultisite() ? 'When checked during a subsite to standalone migration, inactive users will also be removed.' : null
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => '',
            'checkboxLabel' => 'I have read and accept all <a href="#" onclick="DUPX.viewTerms()" >terms &amp; notices</a>',
            'subNote'       => '* required to continue',
            'attr'          => array(
                'onclick' => 'DUPX.acceptWarning();'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONFIG] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_CONFIG,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'modify',
            'acceptValues' => array(
                'modify',
                'nothing',
                'new'
            )),
            array(
            'label'   => 'wp-config:',
            'options' => array(
                new DUPX_Param_item_form_option('modify', 'Modify original'),
                new DUPX_Param_item_form_option('nothing', 'Do nothing'),
                new DUPX_Param_item_form_option('new', 'Create new from wp-config sample')
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'new',
            'acceptValues' => array(
                'new',
                'original',
                'nothing'
            )),
            array(
            'label'   => 'htaccess:',
            'options' => array(
                new DUPX_Param_item_form_option('new', 'Create new'),
                new DUPX_Param_item_form_option('original', 'Retain original'),
                new DUPX_Param_item_form_option('nothing', 'Do nothing')
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_OTHER_CONFIG] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_OTHER_CONFIG,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'new',
            'acceptValues' => array(
                'new',
                'original',
                'nothing'
            )),
            array(
            'label'   => 'Other config (web.config, user.ini):',
            'options' => array(
                new DUPX_Param_item_form_option('new', 'Reset'),
                new DUPX_Param_item_form_option('original', 'Retain original'),
                new DUPX_Param_item_form_option('nothing', 'Do nothing')
            ))
        );
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function initDatabaseParams(&$params)
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_DB_TEST_OK] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DB_TEST_OK,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'default' => false
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'persistence'  => true,
            'default'      => 'basic',
            'acceptValues' => array(
                'basic',
                'cpnl'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_ACTION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => 'empty',
            'acceptValues' => array(
                DUPX_DBInstall::DBACTION_CREATE,
                DUPX_DBInstall::DBACTION_EMPTY,
                DUPX_DBInstall::DBACTION_RENAME,
                DUPX_DBInstall::DBACTION_MANUAL,
                DUPX_DBInstall::DBACTION_ONLY_CONNECT
            )
            ),
            array(
            'label'   => 'Action:',
            'options' => array(
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_CREATE,
                    'Create New Database',
                    function () {
                        if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_STD_INSTALL) {
                            return DUPX_Param_item_form_option::OPT_ENABLED;
                        } else {
                            return DUPX_Param_item_form_option::OPT_DISABLED;
                        }
                    }),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_EMPTY, 'Connect and Remove All Data'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_RENAME, 'Connect and Backup Any Existing Data'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_ONLY_CONNECT, 'Connect and Do Nothing (Advanced)'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_MANUAL, 'Manual SQL Execution (Advanced)')
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_HOST] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_HOST,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'persistence'      => true,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'Host:',
            'attr'  => array(
                'required'    => 'required',
                'placeholder' => 'localhost'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'persistence'      => true,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'   => 'Database:',
            'attr'    => array(
                'required'    => 'required',
                'placeholder' => 'new or existing database name'
            ),
            'subNote' => <<<NOTE
<span class="s2-warning-emptydb">
    Warning: The selected 'Action' above will remove <u>all data</u> from this database!
</span>
<span class="s2-warning-renamedb">
    Notice: The selected 'Action' will rename <u>all existing tables</u> from the database name above with a prefix {$GLOBALS['DB_RENAME_PREFIX']}
    The prefix is only applied to existing tables and not the new tables that will be installed.
</span>
<span class="s2-warning-manualdb">
    Notice: The 'Manual SQL execution' action will prevent the SQL script in the archive from running. The database above should already be
    pre-populated with data which will be updated in the next step. No data in the database will be modified until after Step 3 runs.
</span>
NOTE
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_USER] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_USER,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'persistence'      => true,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'User:',
            'attr'  => array(
                'required'     => 'required',
                'placeholder'  => 'valid database username',
                // Can be written field wise
                // Ref. https://developer.mozilla.org/en-US/docs/Web/Security/Securing_your_site/Turning_off_form_autocompletion
                'autocomplete' => "off"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_PASS] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_DB_PASS,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'persistence'      => true,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'Password:',
            'attr'  => array(
                'placeholder'  => 'valid database user password',
                // Can be written field wise
                // Ref. https://developer.mozilla.org/en-US/docs/Web/Security/Securing_your_site/Turning_off_form_autocompletion
                'autocomplete' => "off"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHARSET] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHARSET,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => $archiveConfig->getWpConfigDefineValue('DB_CHARSET', $GLOBALS['DBCHARSET_DEFAULT']),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item_form::VALIDATE_REGEX_AZ_NUMBER_SEP
            ),
            array(
            'label' => 'Charset:'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_COLLATE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_COLLATE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => $archiveConfig->getWpConfigDefineValue('DB_COLLATE', ''),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item_form::VALIDATE_REGEX_AZ_NUMBER_SEP
            ),
            array(
            'label' => 'Collation:'
            )
        );

        $tablePrefixWarning = "Changing this setting alters the database table prefix by renaming all tables and references to them.\n"
            ."Change it only if you're sure you know what you're doing!";

        $params[DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => DUPX_ArchiveConfig::getInstance()->wp_tableprefix,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item_form::VALIDATE_REGEX_AZ_NUMBER_SEP
            ),
            array(
            'status'           => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? DUPX_Param_item_form::STATUS_READONLY : DUPX_Param_item_form::STATUS_INFO_ONLY,
            'label'            => 'Table Prefix:',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($tablePrefixWarning).');',
            'subNote'          => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? '' : 'Changing the prefix is only available with Freelancer, Business or Gold licenses'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => 0,
            'sanitizeCallback' => function ($value) {
                // disable keep users for some db actions
                switch (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION)) {
                    case DUPX_DBInstall::DBACTION_CREATE:
                    case DUPX_DBInstall::DBACTION_MANUAL:
                    case DUPX_DBInstall::DBACTION_ONLY_CONNECT:
                        return 0;
                    case DUPX_DBInstall::DBACTION_EMPTY:
                    case DUPX_DBInstall::DBACTION_RENAME:
                        return (int) $value;
                }
            },
            'validateCallback' => function ($value) {
                if ($value == 0) {
                    return true;
                }
                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                foreach ($overwriteData['adminUsers'] as $user) {
                    if ($value == $user['id']) {
                        return true;
                    }
                }
                return false;
            }
            ),
            array(
            'status' => function () {
                if (DUPX_ArchiveConfig::getInstance()->isNetworkInstall()) {
                    return DUPX_Param_item_form::STATUS_SKIP;
                }

                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                if (!empty($overwriteData['adminUsers'])) {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                }
            },
            'label'   => 'Keep users:',
            'options' => function ($item) {
                $result        = array(
                    new DUPX_Param_item_form_option(0, ' - DISABLED - '),
                );
                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                if (!empty($overwriteData['adminUsers'])) {
                    foreach ($overwriteData['adminUsers'] as $userData) {
                        $result[] = new DUPX_Param_item_form_option($userData['id'], $userData['user_login']);
                    }
                }
                return $result;
            },
            'subNote' => 'Keep users of the current site and eliminates users of the original site.<br><b>Assign all pages and posts to the selected user.</b>'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Legacy Character set:',
            'checkboxLabel' => 'Enable legacy character set support for unknown character sets.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default' => '',
            ),
            array(
            'label'   => ' ',
            'options' => array(),
            'subNote' => 'This option is populated after clicking on the "Test Database" button.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Legacy Collation:',
            'checkboxLabel' => 'Enable legacy collation fallback support for unknown collations types.',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default' => '',
            ),
            array(
            'label'   => ' ',
            'options' => array(),
            'subNote' => 'This option is populated after clicking on the "Test Database" button.',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHUNK] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHUNK,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Chunking:',
            'checkboxLabel' => 'Enable multi-threaded requests to chunk SQL file.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_SPACING] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_SPACING,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Spacing:',
            'checkboxLabel' => 'Enable non-breaking space characters fix.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Views:',
            'checkboxLabel' => 'Enable View Creation.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Store procedures:',
            'checkboxLabel' => 'Enable Stored Procedure Creation.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'validateRegex'    => '/^[A-Za-z0-9_\-,]*$/', // db options with , and can be empty
            'sanitizeCallback' => function ($value) {
                $value = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
                return str_replace(' ', '', $value);
            },
            ),
            array(
            'label'          => ' ',
            'wrapperClasses' => 'no-display',
            'subNote'        => 'Separate additional '.DUPX_View_Funcs::helpLink('step2', 'sql modes', false).' with commas &amp; no spaces.<br>'
            .'Example: <i>NO_ENGINE_SUBSTITUTION,NO_ZERO_IN_DATE,...</i>.</small>'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'DEFAULT',
            'acceptValues' => array(
                'DEFAULT',
                'DISABLE',
                'CUSTOM'
            )
            ),
            array(
            'label'   => 'Mode:',
            'options' => array(
                new DUPX_Param_item_form_option('DEFAULT', 'Default', DUPX_Param_item_form_option::OPT_ENABLED, array(
                    'onchange' => "if ($(this).is(':checked')) { "
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS]->getFormWrapperId()."').addClass('no-display');"
                    ."}"
                    )),
                new DUPX_Param_item_form_option('DISABLE', 'Disable', DUPX_Param_item_form_option::OPT_ENABLED, array(
                    'onchange' => "if ($(this).is(':checked')) { "
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS]->getFormWrapperId()."').addClass('no-display');"
                    ."}"
                    )),
                new DUPX_Param_item_form_option('CUSTOM', 'Custom', DUPX_Param_item_form_option::OPT_ENABLED, array(
                    'onchange' => "if ($(this).is(':checked')) { "
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS]->getFormWrapperId()."').removeClass('no-display');"
                    ."}")),
            ))
        );
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function initCpanelParams(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_CPNL_HOST] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_HOST,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => $GLOBALS['DUPX_AC']->cpnl_host,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'            => 'Host:',
            'attr'             => array(
                'required'    => 'true',
                'placeholder' => 'cPanel url'
            ),
            'subNote'          => '<span id="cpnl-host-warn">'
            .'Caution: The cPanel host name and URL in the browser address bar do not match, '
            .'in rare cases this may be intentional.'
            .'Please be sure this is the correct server to avoid data loss.</span>',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'get',
            'postfixBtnAction' => 'DUPX.getcPanelURL(this);'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_USER] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_USER,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => $GLOBALS['DUPX_AC']->cpnl_user,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'Username:',
            'attr'  => array(
                'required'             => 'required',
                'data-parsley-pattern' => '/^[\w.-~]+$/',
                'placeholder'          => 'cPanel username'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_PASS] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_CPNL_PASS,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'default'          => $GLOBALS['DUPX_AC']->cpnl_pass,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'Password:',
            'attr'  => array(
                'required'    => 'true',
                'placeholder' => 'cPanel password'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION] = $params[DUPX_Paramas_Manager::PARAM_DB_ACTION]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION,
            array(),
            array(
                'status' => DUPX_Param_item_form::STATUS_DISABLED
        ));
        // force create database enable for cpanel
        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION]->setOptionStatus(0, DUPX_Param_item_form_option::OPT_ENABLED);

        $params[DUPX_Paramas_Manager::PARAM_CPNL_PREFIX] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_PREFIX,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $tableOptions                                  = self::getTableOptions();
        $params[DUPX_Paramas_Manager::PARAM_DB_TABLES] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_TABLES,
            DUPX_Param_item_form::TYPE_ARRAY_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(// ITEM ATTRIBUTES
            'default'          => $tableOptions['default'],
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            ), array(// FORM ATTRIBUTES
            'multiple' => true,
            'size'     => 10,
            'options'  => $tableOptions['options']
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_HOST] = $params[DUPX_Paramas_Manager::PARAM_DB_HOST]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_HOST,
            array(
                'default' => $GLOBALS['DUPX_AC']->cpnl_dbhost
            ),
            array(
                'status' => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'   => array(
                    'required' => 'true'
                )
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_SEL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_SEL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'   => 'Database:',
            'status'  => DUPX_Param_item_form::STATUS_DISABLED,
            'attr'    => array(
                'required'             => 'true',
                'data-parsley-pattern' => '^((?!-- Select Database --).)*$'
            ),
            'subNote' => '<span class="s2-warning-emptydb">'
            .'Warning: The selected "Action" above will remove <u>all data</u> from this database!'
            .'</span>'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_TXT] = $params[DUPX_Paramas_Manager::PARAM_DB_NAME]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_TXT,
            array(
                'default' => $GLOBALS['DUPX_AC']->cpnl_dbname
            ),
            array(
                'label'           => 'Database:',
                'status'          => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'            => array(
                    'required'                      => 'true',
                    'data-parsley-pattern'          => '/^[\w.-~]+$/',
                    'data-parsley-errors-container' => '#cpnl-dbname-txt-error'
                ),
                'subNote'         => '<span id="cpnl-dbname-txt-error"></span>',
                'prefixElement'   => 'label',
                'prefixElemLabel' => '',
                'prefixElemId'    => 'cpnl-prefix-dbname'
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_SEL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_SEL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'  => 'User:',
            'status' => DUPX_Param_item_form::STATUS_DISABLED,
            'attr'   => array(
                'required'             => 'true',
                'data-parsley-pattern' => '^((?!-- Select User --).)*$'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_TXT] = $params[DUPX_Paramas_Manager::PARAM_DB_USER]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_TXT,
            array(
                'default' => $GLOBALS['DUPX_AC']->cpnl_dbuser
            ),
            array(
                'label'           => 'User:',
                'status'          => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'            => array(
                    'required'                      => 'true',
                    'data-parsley-pattern'          => '/^[a-zA-Z0-9-_]+$/',
                    'data-parsley-errors-container' => '#cpnl-dbuser-txt-error',
                    'data-parsley-cpnluser'         => "16"
                ),
                'subNote'         => '<span id="cpnl-dbuser-txt-error"></span>',
                'prefixElement'   => 'label',
                'prefixElemLabel' => '',
                'prefixElemId'    => 'cpnl-prefix-dbuser',
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => ' ',
            'checkboxLabel' => 'Create New Database User'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_PASS] = $params[DUPX_Paramas_Manager::PARAM_DB_PASS]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_PASS,
            array(),
            array(
                'status' => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'   => array(
                    'required' => 'true'
                )
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_IGNORE_PREFIX] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_IGNORE_PREFIX,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'CPanel Prefix',
            'checkboxLabel' => 'Ignore',
            'attr'          => array(
                'onclick' => 'DUPX.cpnlPrefixIgnore();'
            )
            )
        );
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function initScanParams(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_BLOGNAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_BLOGNAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => function ($value) {
                $value = DupProSnapLibUtil::sanitize_non_stamp_chars_and_newline($value);
                return htmlspecialchars_decode((empty($value) ? 'No Blog Title Set' : $value), ENT_QUOTES);
            }
            ), array(
            'label' => 'Title:'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_REPLACE_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_REPLACE_MODE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'legacy',
            'acceptValues' => array(
                'legacy',
                'mapping'
            )),
            array(
            'label'   => 'Replace Mode:',
            'options' => array(
                new DUPX_Param_item_form_option('legacy', 'Standard', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Set the files current date time to now')),
                new DUPX_Param_item_form_option('mapping', 'Mapping', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Keep the files date time the same'))
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => DUPX_S3_Funcs::MODE_CHUNK,
            'acceptValues' => array(
                DUPX_S3_Funcs::MODE_NORMAL,
                DUPX_S3_Funcs::MODE_CHUNK,
                DUPX_S3_Funcs::MODE_SKIP,
            )),
            array(
            'label'   => 'Engine Mode:',
            'options' => array(
                new DUPX_Param_item_form_option(DUPX_S3_Funcs::MODE_NORMAL, 'Normal'),
                new DUPX_Param_item_form_option(DUPX_S3_Funcs::MODE_CHUNK, 'Chunking mode'),
                new DUPX_Param_item_form_option(DUPX_S3_Funcs::MODE_SKIP, 'Skip replace database')
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_MU_REPLACE] = new DUPX_Param_item_form_urlmapping(
            DUPX_Paramas_Manager::PARAM_MU_REPLACE,
            DUPX_Param_item_form_urlmapping::TYPE_ARRAY_STRING,
            DUPX_Param_item_form_urlmapping::FORM_TYPE_URL_MAPPING,
            array(
            'default' => $archive_config->getNewUrlsArrayIdVal()),
            array()
        );

        $params[DUPX_Paramas_Manager::PARAM_CUSTOM_SEARCH] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_CUSTOM_SEARCH,
            DUPX_Param_item_form_urlmapping::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CUSTOM_REPLACE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_CUSTOM_REPLACE,
            DUPX_Param_item_form_urlmapping::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Cleanup:',
            'checkboxLabel' => 'Remove schedules and storage endpoints'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PLUGINS] = new DUPX_Param_item_form_plugins(
            DUPX_Paramas_Manager::PARAM_PLUGINS,
            DUPX_Param_item_form_plugins::TYPE_ARRAY_STRING,
            DUPX_Param_item_form_plugins::FORM_TYPE_PLUGINS_SELECT,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_IGNORE_PLUGINS] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_IGNORE_PLUGINS,
            DUPX_Param_item::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FORCE_DIABLE_PLUGINS] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_FORCE_DIABLE_PLUGINS,
            DUPX_Param_item::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Email Domains:',
            'checkboxLabel' => 'Update'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FULL_SEARCH] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_FULL_SEARCH,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Database Search:',
            'checkboxLabel' => 'Full Search Mode'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_POSTGUID] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_POSTGUID,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Post GUID:',
            'checkboxLabel' => 'Keep Unchanged'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default' => DUPX_Constants::DEFAULT_MAX_STRLEN_SERIALIZED_CHECK_IN_M
            ),
            array(
            'min'              => 0,
            'max'              => 99,
            'step'             => 1,
            'wrapperClasses'   => array('small'),
            'label'            => 'Max size check for serialize objects:',
            'postfixElement'   => 'label',
            'postfixElemLabel' => 'MB'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => (count($archive_config->subsites) <= MAX_SITES_TO_DEFAULT_ENABLE_CORSS_SEARCH)
            ),
            array(
            'status' => function($paramObj) {
                return (DUPX_ArchiveConfig::getInstance()->isNetworkInstall() ?
                    DUPX_Param_item_form::STATUS_ENABLED :
                    DUPX_Param_item_form::STATUS_SKIP);
            },
            'label'                                                     => 'Database search:',
            'checkboxLabel'                                             => 'Cross-search between the sites of the network.'
            )
        );
    }

    /**
     * @param  null  $newPrefix The new prefix you want the database tables to have
     * @param  bool  $subsiteId If it's a standalone migration provide subsite id to rename tables
     * @return array
     */
    public static function getTableOptions($newPrefix = null, $subsiteId = false)
    {
        $result        = array(
            'options' => array(),
            'default' => array()
        );
        $sharedTables  = array();
        $subSiteTables = array();
        $finalTables   = array();

        $archive_config = DUPX_ArchiveConfig::getInstance();
        $oldPrefix      = $archive_config->wp_tableprefix;
        $tablePrefix    = is_null($newPrefix) ? $oldPrefix : $newPrefix;
        $tables         = (array) $archive_config->dbInfo->tablesList;
        $installType    = ($subsiteId !== false && $subsiteId > 0) ? 1 : 0;

        // there is only one `users` and `usermeta` table in multisite installation
        $generalTables = array(
            $tablePrefix.'commentmeta',
            $tablePrefix.'comments',
            $tablePrefix.'links',
            $tablePrefix.'options',
            $tablePrefix.'postmeta',
            $tablePrefix.'posts',
            $tablePrefix.'term_relationships',
            $tablePrefix.'term_taxonomy',
            $tablePrefix.'terms',
            $tablePrefix.'termmeta'
        );

        $multisiteOnlyTables = array(
            $tablePrefix.'blogmeta',
            $tablePrefix.'blogs',
            $tablePrefix.'blog_versions',
            $tablePrefix.'registration_log',
            $tablePrefix.'signups',
            $tablePrefix.'site',
            $tablePrefix.'sitemeta'
        );

        /**
         * $pattern_shared_tables: match tables starting with $tablePrefix and not followed
         * by a number following a `_` character e.g. `wp_users`, `wp_duplicator_pro_entities`
         * $pattern_subsite_tables: match tables starting with $subsiteTablePrefix
         * and what the one above does e.g. `wp_3_posts`, `wp_3_comments`, `wp_users` tables
         */
        $subsiteTablePrefix     = "{$tablePrefix}{$subsiteId}_";
        $qTablePrefix           = preg_quote($tablePrefix, "/");
        $qSubsiteTablePrefix    = preg_quote($subsiteTablePrefix, "/");
        $pattern_shared_tables  = "^{$qTablePrefix}(?!\d+_)";
        $pattern_subsite_tables = "^{$qSubsiteTablePrefix}(.+)";

        foreach ($tables as $table => $tableInfo) {
            if ($oldPrefix == $tablePrefix || (strpos($table, $oldPrefix) !== 0)) {
                $tableNew = $table;
            } else {
                $tableNew = $tablePrefix.substr($table, strlen($oldPrefix));
            }

            //rename subsite tables before adding options if it's a standalone installation
            if ($installType === 1) {
                //skip multi-site only and general tables
                if (in_array($tableNew, $multisiteOnlyTables) || in_array($tableNew, $generalTables)) {
                    continue;
                }

                //get tables shared between all sub-sites
                if (preg_match("/{$pattern_shared_tables}/", $tableNew)) {
                    $sharedTables[$tableNew.' ('.$tableInfo->rows.')'] = $tableNew;
                }

                if ($subsiteId !== 1) {
                    //get tables unique to the given sub-site
                    if (preg_match("/{$pattern_subsite_tables}/", $tableNew)) {
                        $tableNew                                           = preg_replace("/^{$qSubsiteTablePrefix}/", $tablePrefix, $tableNew);
                        $subSiteTables[$tableNew.' ('.$tableInfo->rows.')'] = $tableNew;
                    }
                }
            } else {
                //if not sub-site to standalone installation keep all tables
                $finalTables[$tableNew.' ('.$tableInfo->rows.')'] = $tableNew;
            }
        }

        if ($installType === 1) {
            if ($subsiteId === 1) {
                //if root site, just add general tables to shared tables
                $finalTables = array_merge($sharedTables, $generalTables);
            } else {
                //remove tables that are duplicates e.g. wp_2_table and wp_table
                $uniqueSharedTables = array_diff($sharedTables, $subSiteTables);
                $finalTables        = array_merge($subSiteTables, $uniqueSharedTables);
            }
        }

        foreach ($finalTables as $label => $tableName) {
            $result['default'][] = $tableName;
            $result['options'][] = new DUPX_Param_item_form_option($tableName, $label);
        }

        return $result;
    }

    /**
     *
     * @return string
     */
    public static function getStatuOfNewAdminParams()
    {
        if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
            return DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            return DUPX_Param_item_form::STATUS_DISABLED;
        }
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function initNewAdminParams(&$params)
    {

        $params[DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET] = new DUPX_Param_item_form_users_pass_reset(
            DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET,
            DUPX_Param_item_form_users_pass_reset::TYPE_ARRAY_STRING,
            DUPX_Param_item_form_users_pass_reset::FORM_TYPE_USERS_PWD_RESET,
            array(// ITEM ATTRIBUTES
            'default' => array_map(function ($value) {
                    return '';
                }, DUPX_ArchiveConfig::getInstance()->getUsersLists()),
            'sanitizeCallback'                                   => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback'                                   => function ($value) {
                return strlen($value) == 0 || strlen($value) >= DUPX_Constants::MIN_NEW_PASSWORD_LEN;
            },
            'invalidMessage' => 'can\'t have less than '.DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters'
            ), array(// FORM ATTRIBUTES
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'label'   => 'Existing user reset password:',
            'classes' => 'strength-pwd-check',
            'attr'    => array(
                'title'       => DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters minimum',
                'placeholder' => "Reset user password"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => false
            ),
            array(
            'label'  => 'Create new user:',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'checkboxLabel' => ''
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
                    return strlen($value) >= 4;
                } else {
                    $value = '';
                    return true;
                }
            }
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Username:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => '4 characters minimum',
                'placeholder' => "(4 or more characters)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'default'          => $GLOBALS['DUPX_AC']->cpnl_pass,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
                    return strlen($value) >= DUPX_Constants::MIN_NEW_PASSWORD_LEN;
                } else {
                    $value = '';
                    return true;
                }
            },
            'invalidMessage' => 'can\'t have less than '.DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters'
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Password:',
            'classes' => array('strength-pwd-check', 'new-admin-field'),
            'attr'    => array(
                'placeholder' => '('.DUPX_Constants::MIN_NEW_PASSWORD_LEN.' or more characters)',
                'title'       => DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters minimum'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
                    if (strlen($value) < 4 || strpos($value, '@') < 1) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    $value = '';
                    return true;
                }
            }
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Email:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => '4 characters minimum',
                'placeholder' => "(4 or more characters)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_NICKNAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_NICKNAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Nickname:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => 'if username is empty',
                'placeholder' => "(if username is empty)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_FIRST_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_FIRST_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'First name:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => 'optional',
                'placeholder' => "(optional)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_LAST_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_LAST_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Last name:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => 'optional',
                'placeholder' => "(optional)"
            )
            )
        );
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function initWpConfigParams(&$params)
    {
        $archiveConfig                                                  = DUPX_ArchiveConfig::getInstance();
        /**
         *
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_SITEURL');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_HOME');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_URL');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_PLUGIN_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_PLUGIN_URL');
          $this->addDefineIfExists($wpInfo->configs->defines, 'PLUGINDIR');

          $this->addDefineIfExists($wpInfo->configs->defines, 'UPLOADS');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_POST_REVISIONS');
          $this->addDefineIfExists($wpInfo->configs->defines, 'COOKIE_DOMAIN');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ALLOW_MULTISITE');
          $this->addDefineIfExists($wpInfo->configs->defines, 'NOBLOGREDIRECT');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_DEBUG');
          $this->addDefineIfExists($wpInfo->configs->defines, 'SCRIPT_DEBUG');
          $this->addDefineIfExists($wpInfo->configs->defines, 'CONCATENATE_SCRIPTS');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_DEBUG_LOG');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_DEBUG_DISPLAY');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_MEMORY_LIMIT');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_MAX_MEMORY_LIMIT');

          $this->addDefineIfExists($wpInfo->configs->defines, 'CUSTOM_USER_TABLE');
          $this->addDefineIfExists($wpInfo->configs->defines, 'CUSTOM_USER_META_TABLE');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WPLANG');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_LANG_DIR');

          $this->addDefineIfExists($wpInfo->configs->defines, 'SAVEQUERIES');

          $this->addDefineIfExists($wpInfo->configs->defines, 'FS_CHMOD_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FS_CHMOD_FILE');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FS_METHOD');

          $this->addDefineIfExists($wpInfo->configs->defines, 'ALTERNATE_WP_CRON');
          $this->addDefineIfExists($wpInfo->configs->defines, 'DISABLE_WP_CRON');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CRON_LOCK_TIMEOUT');

          $this->addDefineIfExists($wpInfo->configs->defines, 'COOKIEPATH');
          $this->addDefineIfExists($wpInfo->configs->defines, 'SITECOOKIEPATH');
          $this->addDefineIfExists($wpInfo->configs->defines, 'ADMIN_COOKIE_PATH');
          $this->addDefineIfExists($wpInfo->configs->defines, 'PLUGINS_COOKIE_PATH');
          $this->addDefineIfExists($wpInfo->configs->defines, 'TEMPLATEPATH');
          $this->addDefineIfExists($wpInfo->configs->defines, 'STYLESHEETPATH');

          $this->addDefineIfExists($wpInfo->configs->defines, 'EMPTY_TRASH_DAYS');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ALLOW_REPAIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'DO_NOT_UPGRADE_GLOBAL_TABLES');

          $this->addDefineIfExists($wpInfo->configs->defines, 'DISALLOW_FILE_MODS');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_HTTP_BLOCK_EXTERNAL');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ACCESSIBLE_HOSTS');

          $this->addDefineIfExists($wpInfo->configs->defines, 'AUTOMATIC_UPDATER_DISABLED');
          $this->addDefineIfExists($wpInfo->configs->defines, 'IMAGE_EDIT_OVERWRITE');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_URL');

          $this->addDefineIfExists($wpInfo->configs->defines, 'WPMU_PLUGIN_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'WPMU_PLUGIN_URL');
          $this->addDefineIfExists($wpInfo->configs->defines, 'MUPLUGINDIR');


         */
        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('DISALLOW_FILE_EDIT')
            ),
            array(
            'label'         => 'DISALLOW_FILE_EDIT:',
            'checkboxLabel' => 'Disable the Plugin/Theme Editor'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('DISALLOW_FILE_MODS', array(
                'value'      => false,
                'inWpConfig' => false
                )
            ),
            ),
            array(
            'label'         => 'DISALLOW_FILE_MODS:',
            'checkboxLabel' => 'This will block users being able to use the plugin and theme installation/update functionality from the WordPress admin area'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(// ITEM ATTRIBUTES
            'default' => $archiveConfig->getDefineArrayValue('AUTOSAVE_INTERVAL', array(
                'value'      => 60,
                'inWpConfig' => false
                )
            ),
            ), array(// FORM ATTRIBUTES
            'label'            => 'AUTOSAVE_INTERVAL:',
            'subNote'          => 'Auto-save interval in seconds (default:60)',
            'min'              => 5,
            'step'             => 1,
            'wrapperClasses'   => array('small'),
            'postfixElement'   => 'label',
            'postfixElemLabel' => 'Sec.',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue('WP_POST_REVISIONS', array(
                'value'      => true,
                'inWpConfig' => false
                )
            ),
            'sanitizeCallback' => function ($value) {
                //convert bool on int
                if ($value === true) {
                    $value = PHP_INT_MAX;
                }
                if ($value === false) {
                    $value = 0;
                }
                return $value;
            },
            ), array(// FORM ATTRIBUTES
            'label'          => 'WP_POST_REVISIONS:',
            'subNote'        => 'Number of article revisions. Select 0 to disable revisions. Disable the field to enable revisions.',
            'min'            => 0,
            'step'           => 1,
            'wrapperClasses' => array('small')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('FORCE_SSL_ADMIN')
            ),
            array(
            'label'         => 'FORCE_SSL_ADMIN:',
            'checkboxLabel' => 'Enforce Admin SSL'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_GEN_WP_AUTH_KEY] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_GEN_WP_AUTH_KEY,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Auth Keys:',
            'checkboxLabel' => 'Generate New Unique Authentication Keys and Salts',
            'status'        => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? DUPX_Param_item_form::STATUS_ENABLED : DUPX_Param_item_form::STATUS_DISABLED,
            'subNote'       => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? '' : 'Available only in Freelancer and above'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOMATIC_UPDATER_DISABLED] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOMATIC_UPDATER_DISABLED,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('AUTOMATIC_UPDATER_DISABLED', array(
                'value'      => false,
                'inWpConfig' => false
                )
            ),
            ),
            array(
            'label'         => 'AUTOMATIC_UPDATER_DISABLED:',
            'checkboxLabel' => 'Disable automatic updater'
            )
        );

        $autoUpdateValue = $archiveConfig->getWpConfigDefineValue('WP_AUTO_UPDATE_CORE');
        if (is_bool($autoUpdateValue)) {
            $autoUpdateValue = ($autoUpdateValue ? 'true' : 'false');
        }
        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => array(
                'value'      => $autoUpdateValue,
                'inWpConfig' => $archiveConfig->inWpConfigDefine('WP_AUTO_UPDATE_CORE')
            ),
            'acceptValues' => array('', 'false', 'true', 'minor')),
            array(
            'label'   => 'WP_AUTO_UPDATE_CORE:',
            'options' => array(
                new DUPX_Param_item_form_option('minor', 'Enable only core minor updates - Default'),
                new DUPX_Param_item_form_option('false', 'Disable all core updates'),
                new DUPX_Param_item_form_option('true', 'Enable all core updates')
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_IMAGE_EDIT_OVERWRITE] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_IMAGE_EDIT_OVERWRITE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('IMAGE_EDIT_OVERWRITE', array(
                'value'      => true,
                'inWpConfig' => false
                )
            ),
            ),
            array(
            'label'         => 'IMAGE_EDIT_OVERWRITE:',
            'checkboxLabel' => 'Create only one set of image edits'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_CACHE')
            ),
            array(
            'label'         => 'WP_CACHE:',
            'checkboxLabel' => 'Keep Enabled'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => array(
                'value'      => '',
                'inWpConfig' => false
            ),
            'sanitizeCallback' => function ($value) {
                $value = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
                return DupProSnapLibIou::safePathTrailingslashit($value);
            },
            'validateCallback'                                       => function ($value) {
                return strlen($value) > 1;
            }
            ), array(// FORM ATTRIBUTES
            'label'   => 'WPCACHEHOME:',
            'subNote' => 'This define is not part of the WordPress core but is a define used by WP Super Cache. <br>'
            .'By default, if it exists, it is set to the new root path.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_DEBUG')
            ),
            array(
            'label'         => 'WP_DEBUG:',
            'checkboxLabel' => 'Display errors and warnings'
            )
        );

        $debugLogValue = $archiveConfig->getWpConfigDefineValue('WP_DEBUG_LOG');
        if (is_string($debugLogValue)) {
            $debugLogValue = empty($debugLogValue) ? false : true;
        }
        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => array(
                'value'      => $debugLogValue,
                'inWpConfig' => $archiveConfig->inWpConfigDefine('WP_DEBUG_LOG')
            )
            ),
            array(
            'label'         => 'WP_DEBUG_LOG:',
            'checkboxLabel' => 'Log errors and warnings',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_DISABLE_FATAL_ERROR_HANDLER')
            ),
            array(
            'label'         => 'WP_DISABLE_FATAL_ERROR_HANDLER:',
            'checkboxLabel' => 'Disable fatal error handler',
            'status'        => version_compare($archiveConfig->version_wp, '5.2.0', '<') ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_DEBUG_DISPLAY')
            ),
            array(
            'label'         => 'WP_DEBUG_DISPLAY:',
            'checkboxLabel' => 'Display errors and warnings'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('SCRIPT_DEBUG')
            ),
            array(
            'label'         => 'SCRIPT_DEBUG:',
            'checkboxLabel' => 'JavaScript or CSS errors'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('CONCATENATE_SCRIPTS', array(
                'value'      => false,
                'inWpConfig' => false
                )
            ),
            ),
            array(
            'label'         => 'CONCATENATE_SCRIPTS:',
            'checkboxLabel' => 'Concatenate all JavaScript files into one URL'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('SAVEQUERIES')
            ),
            array(
            'label'         => 'SAVEQUERIES:',
            'checkboxLabel' => 'Save database queries in an array ($wpdb->queries)'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('ALTERNATE_WP_CRON', array(
                'value'      => false,
                'inWpConfig' => false
                )
            ),
            ),
            array(
            'label'         => 'ALTERNATE_WP_CRON:',
            'checkboxLabel' => 'Use an alternative Cron with WP'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('DISABLE_WP_CRON', array(
                'value'      => false,
                'inWpConfig' => false
                )
            ),
            ),
            array(
            'label'         => 'DISABLE_WP_CRON:',
            'checkboxLabel' => 'Disable cron entirely'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'   => $archiveConfig->getDefineArrayValue('WP_CRON_LOCK_TIMEOUT', array(
                'value'      => 60,
                'inWpConfig' => false
                )
            ),
            'min_range' => 1
            ),
            array(
            'min'            => 1,
            'step'           => 1,
            'label'          => 'WP_CRON_LOCK_TIMEOUT:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'Cron process cannot run more than once every WP_CRON_LOCK_TIMEOUT seconds',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_EMPTY_TRASH_DAYS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_EMPTY_TRASH_DAYS,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'   => $archiveConfig->getDefineArrayValue('EMPTY_TRASH_DAYS', array(
                'value'      => 30,
                'inWpConfig' => false
                )
            ),
            'min_range' => 0
            ),
            array(
            'min'            => 0,
            'step'           => 1,
            'label'          => 'EMPTY_TRASH_DAYS:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'How many days deleted post should be kept in trash before being deleted permanently',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => array(
                'value'      => $archiveConfig->getNewCookyeDomainFromOld(),
                'inWpConfig' => $archiveConfig->inWpConfigDefine('COOKIE_DOMAIN')
            ),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ), array(// FORM ATTRIBUTES
            'label'   => 'COOKIE_DOMAIN:',
            'subNote' => 'Set <a href="http://www.askapache.com/htaccess/apache-speed-subdomains.html" target="_blank">different domain</a> for cookies.subdomain.example.com'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue('WP_MEMORY_LIMIT'),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item::VALIDATE_REGEX_AZ_NUMBER
            ),
            array(// FORM ATTRIBUTES
            'label'          => 'WP_MEMORY_LIMIT:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'PHP memory limit (default:30M; Multisite default:64M)'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue('WP_MAX_MEMORY_LIMIT'),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item::VALIDATE_REGEX_AZ_NUMBER
            ),
            array(// FORM ATTRIBUTES
            'label'          => 'WP_MAX_MEMORY_LIMIT:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'Wordpress admin maximum memory limit (default:256M)'
            )
        );
    }

    private static function getSubSiteIdsOptions()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $options        = array();
        $acceptValues   = array(-1);
        foreach ($archive_config->subsites as $subsite) {
            $label          = $subsite->blogname.' ['.$subsite->domain.$subsite->path.']';
            $options[]      = new DUPX_Param_item_form_option($subsite->id, $label);
            $acceptValues[] = $subsite->id;
        }
        return array(
            'options'      => $options,
            'acceptValues' => $acceptValues,
        );
    }

    private static function getMultisiteInstallerTypeOptions()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $acceptValues   = array();
        if (!$archive_config->mu_is_filtered) {
            $acceptValues[] = 0;
        }
        if (DUPX_Conf_Utils::multisitePlusEnabled()) {
            $acceptValues[] = 1;
        }
        if (!empty($acceptValues)) {
            $default = $acceptValues[0];
        } else {
            $acceptValues[] = -1;
            $default        = -1;
        }

        if (($license = $archive_config->getLicenseType()) !== DUPX_LicenseType::BusinessGold) {
            $subNote = '* Requires Business or Gold license. This installer was created with ';
            switch ($archive_config->getLicenseType()) {
                case DUPX_LicenseType::Unlicensed:
                    $subNote .= "an Unlicensed Duplicator Pro.";
                    break;
                case DUPX_LicenseType::Personal:
                    $subNote .= "a Personal license.";
                    break;
                case DUPX_LicenseType::Freelancer:
                    $subNote .= "a Freelancer license.";
                    break;
                default:
                    $subNote .= 'an unknown license type';
            }
        } else {
            $subNote = '';
        }

        return array(
            'default'      => $acceptValues[0],
            'acceptValues' => $acceptValues,
            'subNote'      => $subNote
        );
    }

    private static function getArchiveEngineOptions()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        $acceptValues = array();
        $subNote      = null;
        if (($manualEnable = DUPX_Conf_Utils::isManualExtractFilePresent()) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_MANUAL;
        } else {
            $subNote = <<<SUBNOTEHTML
* Option enabled when archive has been pre-extracted
<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q" target="_blank">[more info]</a>               
SUBNOTEHTML;
        }
        if (($zipEnable = ($archive_config->isZipArchive() && DUPX_Conf_Utils::archiveExists() && DUPX_Conf_Utils::classZipArchiveEnable())) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_ZIP;
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_ZIP_CHUNK;
        }
        if (($shellZipEnable = ($archive_config->isZipArchive() && DUPX_Conf_Utils::archiveExists() && DUPX_Conf_Utils::shellExecUnzipEnable())) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_ZIP_SHELL;
        }
        if (($dupEnable = (!$archive_config->isZipArchive() && DUPX_Conf_Utils::archiveExists())) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_DUP;
        }

        $options   = array();
        $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_MANUAL,
            'Manual Archive Extraction',
            $manualEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);

        if ($archive_config->isZipArchive()) {
            //ZIP-ARCHIVE
            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_ZIP,
                'PHP ZipArchive',
                $zipEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);

            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_ZIP_CHUNK,
                'PHP ZipArchive Chunking',
                $zipEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);
            //SHELL-EXEC UNZIP
            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_ZIP_SHELL,
                'Shell Exec Unzip',
                function () {
                $archive_config = DUPX_ArchiveConfig::getInstance();
                $pathsMapping   = $archive_config->getPathsMapping();
                if (is_array($pathsMapping) && count($pathsMapping) > 1) {
                    return DUPX_Param_item_form_option::OPT_DISABLED;
                }
                if ($archive_config->isZipArchive() && DUPX_Conf_Utils::archiveExists() && DUPX_Conf_Utils::shellExecUnzipEnable()) {
                    DUPX_Param_item_form_option::OPT_ENABLED;
                } else {
                    DUPX_Param_item_form_option::OPT_DISABLED;
                }
            }
            );
        } else {
            // DUPARCHIVE
            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_DUP,
                'DupArchive',
                $dupEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);
        }

        if ($zipEnable) {
            $default = DUP_PRO_Extraction::ENGINE_ZIP_CHUNK;
        } else if ($shellZipEnable) {
            $default = DUP_PRO_Extraction::ENGINE_ZIP_SHELL;
        } else if ($dupEnable) {
            $default = DUP_PRO_Extraction::ENGINE_DUP;
        } else if ($manualEnable) {
            $default = DUP_PRO_Extraction::ENGINE_MANUAL;
        } else {
            $default = null;
        }

        return array(
            'options'      => $options,
            'acceptValues' => $acceptValues,
            'default'      => $default,
            'subNote'      => $subNote
        );
    }
}