<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_RemoveRedundantData {

    public static function loadWP()
    {
        static $loaded = null;
        if (is_null($loaded)) {
            $wp_root_dir = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW);
            require_once($wp_root_dir.'/wp-load.php');
            if (!class_exists('WP_Privacy_Policy_Content')) {
                require_once($wp_root_dir.'/wp-admin/includes/misc.php');
            }
            if (!function_exists('request_filesystem_credentials')) {
                require_once($wp_root_dir.'/wp-admin/includes/file.php');
            }
            if (!function_exists('get_plugins')) {
                require_once $wp_root_dir.'/wp-admin/includes/plugin.php';
            }
            if (!function_exists('delete_theme')) {
                require_once $wp_root_dir.'/wp-admin/includes/theme.php';
            }
            $GLOBALS['wpdb']->show_errors(false);
            $loaded = true;
        }
        return $loaded;
    }

    public static function isMultiSite($ac) {
        return ($ac->mu_mode > 0 && count($ac->subsites) > 0 && is_multisite());
    }

    private static function appendParentThemes($active_themes) {
        // For adding parent themes of child themes
        foreach ($active_themes as $active_theme) {
            $theme_obj = wp_get_theme($active_theme);
            if ($theme_obj->stylesheet  != $theme_obj->template) {
                $active_themes[] = $theme_obj->template;
            }
        }
        return $active_themes;
    }

    public static function deleteRedundantThemes($wp_content_dir, $ac, $subsite_id) {
        DUPX_Log::info("\n--------------------\n".
            "DELETING INACTIVE THEMES");

        self::loadWP();

        $is_mu = self::isMultiSite($ac);
        if ($is_mu) {
            $active_themes = get_site_option('allowedthemes', array());
            $active_themes = array_keys($active_themes);
            $active_themes = self::appendParentThemes($active_themes);
        } else {
            if ($subsite_id > 0) {
                $active_themes = get_option('dupx_retain_themes');
                $active_themes = self::appendParentThemes($active_themes);
            } else {
                $stylesheet = get_stylesheet();
                DUPX_Log::info("STYLESHEET IS ".DUPX_Log::varToString($stylesheet));
                $template = get_template();
                DUPX_Log::info("TEMPLATE IS ".DUPX_Log::varToString($template));

                $active_themes = array(
                    $stylesheet,
                    $template,
                );
            }
        }

        // We shouldn't remove WP_DEFAULT_THEME defined theme
        $wpConfigPath	= DUPX_WPConfig::getWpConfigPath();
        require_once(DUPX_INIT.'/lib/config/class.wp.config.tranformer.php');
        $config_transformer = new WPConfigTransformer($wpConfigPath);
        if ($config_transformer->exists('constant', 'WP_DEFAULT_THEME')) {
            $default_theme = $config_transformer->get_value('constant', 'WP_DEFAULT_THEME');
            if (is_string($default_theme)) {
                $active_themes[] = $default_theme;
                DUPX_Log::info("WP_DEFAULT_THEME: ".$default_theme);
            }
        }

        $active_themes = array_unique($active_themes);
        $all_themes = wp_get_themes();
        $all_themes = array_keys($all_themes);
        $uninstallable_themes = array_diff($all_themes, $active_themes);

        foreach ($all_themes as $cTheme) {
            DUPX_Log::info('THEME: '.DUPX_Log::varToString($cTheme).' '.(in_array($cTheme,$active_themes) ? '[ACTIVE]' : '').(in_array($cTheme,$uninstallable_themes) ? '[UNINSTALLABLE]' : ''));
        }
        DUPX_Log::info("\n");

        if (!empty($uninstallable_themes)) {
            foreach ($uninstallable_themes as $uninstallable_theme) {
                if (delete_theme($uninstallable_theme, '')) {
                    DUPX_Log::info('THEME: '.DUPX_Log::varToString($uninstallable_theme).' DELETED');
                } else {
                    $nManager = DUPX_NOTICE_MANAGER::getInstance();
                    $errorMsg = "**ERROR** The Inactive theme ".$uninstallable_theme." deletion failed";
                    DUPX_Log::info($errorMsg);
                    $nManager->addFinalReportNotice(array(
                        'shortMsg' => $errorMsg,
                        'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsg' => 'Please delete the path '.$full_path.' manually',
                        'sections' => 'general'
                    ));
                }
            }
        }

        if (!$is_mu && $subsite_id > 0) {
            delete_option('dupx_retain_themes');
        }
    }
}
