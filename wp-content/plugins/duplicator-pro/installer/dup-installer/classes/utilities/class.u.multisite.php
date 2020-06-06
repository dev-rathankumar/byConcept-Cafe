<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
require_once(DUPX_INIT.'/classes/class.db.php');
require_once(DUPX_INIT.'/classes/config/class.archive.config.php');

/**
 * Utility class for setting up Multi-site data
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\MU
 *
 */
class DUPX_MU
{

    public static function convertSubsiteToStandalone($subsite_id, $dbh, $ac, $remove_redundant = false)
    {
        DUPX_Log::info("STANDALONE: Convert to standalone subsite id ".DUPX_Log::varToString($subsite_id));
        $base_prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        //Had to move this up, so we can update the active_plugins option before it gets moved.
        self::makeSubsiteFilesStandalone($subsite_id, $dbh, $ac, $remove_redundant);
        self::makeSubsiteDatabaseStandalone($subsite_id, $dbh, $base_prefix, $remove_redundant);
    }

    // Convert subsite tables to be standalone by proper renaming (both core and custom subsite table)
    public static function renameSubsiteTablesToStandalone($subsite_id, $dbh, $base_prefix)
    {
        $s3Funcs  = DUPX_S3_Funcs::getInstance();
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        // For non-main subsite we need to move around some tables and files
        $subsite_prefix      = DUPX_ArchiveConfig::getInstance()->getSubsitePrefixByParam($subsite_id);
        $subsite_table_names = self::getSubsiteTables($subsite_id, $dbh);

        $all_table_names = DUPX_DB::queryColumnToArray($dbh, "SHOW TABLES");
        DUPX_Log::info("STANDALONE: rename tables");
        DUPX_Log::incIndent();
        DUPX_Log::info("all table names \n".print_r($all_table_names, true)."----------------------------", DUPX_Log::LV_DETAILED);
        DUPX_Log::info("subsite tables to standalone. table names\n".print_r($subsite_table_names, true)."----------------------------", DUPX_Log::LV_DETAILED);

        foreach ($subsite_table_names as $table_name) {
            $new_table_name = str_ireplace($subsite_prefix, $base_prefix, $table_name);
            DUPX_DB::renameTable($dbh, $table_name, $new_table_name, true);
            $s3Funcs->addTable($new_table_name);
            DUPX_Log::info("table ".$table_name.' renamed to '.$new_table_name);
        }
        DUPX_Log::resetIndent();
    }

    public static function getTablesByPrefix($prefix, $dbh)
    {
        $escaped_prefix = self::escSQLSimple($prefix);
        return DUPX_DB::queryColumnToArray($dbh, "SHOW TABLES LIKE '{$escaped_prefix}%'");
    }

    public static function getSubsiteTables($subsite_id, $dbh)
    {
        return self::getTablesByPrefix(DUPX_ArchiveConfig::getInstance()->getSubsitePrefixByParam($subsite_id), $dbh);
    }

    // <editor-fold defaultstate="collapsed" desc="PRIVATE METHODS">

    private static function makeSubsiteFilesStandalone($subsite_id, $dbh, $ac, $remove_redundant)
    {
        $success        = true;
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $nManager       = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager  = DUPX_Paramas_Manager::getInstance();

        $is_old_mu         = $archive_config->mu_generation === 1 ? true : false;
        $wp_content_dir    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);
        $subsite_blogs_dir = $wp_content_dir.'/blogs.dir';
        $uploads_dir       = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW);
        $uploads_sites_dir = $is_old_mu ? $subsite_blogs_dir : $uploads_dir.'/sites';
        $subsite_id        = (int) $subsite_id;

        DUPX_Log::info("STANDALONE: wp content dir ".DUPX_Log::varToString($wp_content_dir), DUPX_Log::LV_DETAILED);
        if ($subsite_id === 1) {
            try {
                if (!$is_old_mu) {
                    DUPX_U::deleteDirectory($uploads_sites_dir, true);
                } else {
                    DUPX_U::deleteDirectory($subsite_blogs_dir, true);
                }
            }
            catch (Exception $ex) {
                //RSR TODO: Technically it can complete but this should be brought to their attention more than just writing info
                DUPX_Log::info("STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)." MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Problem deleting sites directory',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)."\nMSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }
        } else {
            $subsite_uploads_dir = $is_old_mu ? "{$uploads_sites_dir}/{$subsite_id}/files" : "{$uploads_sites_dir}/{$subsite_id}";
            DUPX_Log::info("STANDALONE: uploads dir ".DUPX_Log::varToString($subsite_uploads_dir));

            try {
                // Get a list of all files/subdirectories within the core uploads dir. For all 'non-sites' directories do a recursive delete. For all files, delete.
                if (file_exists($uploads_dir)) {
                    $filenames = array_diff(scandir($uploads_dir), array('.', '..'));
                    foreach ($filenames as $filename) {
                        $full_path = "$uploads_dir/$filename";
                        if (is_dir($full_path)) {
                            if ($filename != 'sites' || $is_old_mu) {
                                DUPX_Log::info("STANDALONE: Recursively deleting ".DUPX_Log::varToString($full_path), DUPX_Log::LV_DETAILED);
                                DUPX_U::deleteDirectory($full_path, true);
                            } else {
                                DUPX_Log::info("STANDALONE: Skipping ".DUPX_Log::varToString($full_path), DUPX_Log::LV_DETAILED);
                            }
                        } else {
                            $success = @unlink($full_path);
                        }
                    }
                }
            }
            catch (Exception $ex) {
                DUPX_Log::info("STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_dir)." MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Problem deleting sites directory',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_dir)."\nMSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }

            DUPX_Log::info("STANDALONE: copy ".DUPX_Log::varToString($subsite_uploads_dir).' TO '.DUPX_Log::varToString($uploads_dir));
            // Recursively copy files in /wp-content/uploads/sites/$subsite_id to /wp-content/uploads
            DUPX_U::copyDirectory($subsite_uploads_dir, $uploads_dir);

            try {
                DUPX_Log::info("STANDALONE: Recursively deleting ".DUPX_Log::varToString($uploads_sites_dir), DUPX_Log::LV_DETAILED);
                // Delete /wp-content/uploads/sites (will get rid of all subsite directories)
                DUPX_U::deleteDirectory($uploads_sites_dir, true);
            }
            catch (Exception $ex) {
                DUPX_Log::info("STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)." MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Problem deleting sites directory',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)."\nMSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }
        }
        if ($remove_redundant) {
            DUPX_Log::info("STANDALONE: remove rendundant");
            try {
                self::setRetainPlugins($subsite_id, $dbh, $ac);
            }
            catch (Exception $ex) {
                DUPX_Log::error("Problem setting retain plugins");
                DUPX_Log::info("STANDALONE ERROR : Problem setting retain plugins MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => ' Problem setting retain plugins',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem setting retain plugins MSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }

            try {
                self::setRetainThemes($subsite_id, $dbh, $ac);
            }
            catch (Exception $ex) {
                DUPX_Log::error("Problem setting retain themes");
                DUPX_Log::info("STANDALONE ERROR : Problem setting retain plugins MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => ' Problem setting retain themes',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem setting retain themes MSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }
        }
    }

    private static function setRetainPlugins($subsite_id, $dbh, $ac)
    {
        DUPX_Log::info("Setting active plugins");
        //Get active plugins paths
        //active_sitewide_plugins in wp_sitemeta
        $plugins     = array();
        $base_prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);

        $table           = $base_prefix."sitemeta";
        $sql             = "SELECT meta_value FROM $table WHERE meta_key ='active_sitewide_plugins'";
        $col             = DUPX_DB::queryColumnToArray($dbh, $sql);
        $str_plugins     = stripslashes($col[0]);
        $network_plugins = unserialize($str_plugins);
        foreach ($network_plugins as $key => $val) {
            $plugins[] = $key;
        }
        DUPX_Log::info("Network activated plugins ".print_r($plugins, true));

        $table        = DUPX_ArchiveConfig::getInstance()->getSubsitePrefixByParam($subsite_id).'options';
        $sql          = "SELECT option_value FROM $table WHERE option_name ='active_plugins'";
        $col          = DUPX_DB::queryColumnToArray($dbh, $sql);
        $str_plugins  = stripslashes($col[0]);
        $site_plugins = unserialize($str_plugins);
        DUPX_Log::info("Site activated plugins ".print_r($site_plugins, true));
        $plugins      = array_merge($plugins, $site_plugins);

        //Elements in $plugins have the format {$plugin_name}/{$plugin_name}.php
        $plugins     = array_unique($plugins);
        DUPX_Log::info("all retain plugins ".print_r($plugins, true));
        $plugins_ser = serialize($plugins);

        // Delete first if any exists
        $sql = "DELETE FROM $table WHERE option_name='dupx_retain_plugins'";
        DUPX_DB::queryNoReturn($dbh, $sql);

        $sql = "INSERT INTO $table (option_name, option_value) VALUES('dupx_retain_plugins', '".$plugins_ser."')";
        DUPX_DB::queryNoReturn($dbh, $sql);
    }

    private static function setRetainThemes($subsite_id, $dbh, $ac)
    {
        $base_prefix = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);

        $table          = $base_prefix."sitemeta";
        $sql            = "SELECT meta_value FROM $table WHERE meta_key ='allowedthemes'";
        $col            = DUPX_DB::queryColumnToArray($dbh, $sql);
        $themes_str     = $col[0];
        $network_themes = unserialize($themes_str);
        $network_themes = array_keys($network_themes);

        DUPX_Log::info("Network activated themes ".print_r($network_themes, true));

        $table      = DUPX_ArchiveConfig::getInstance()->getSubsitePrefixByParam($subsite_id).'options';
        $themes_ser = serialize($network_themes);
        // Delete first if any exists
        $sql        = "DELETE FROM $table WHERE option_name='dupx_retain_themes'";
        DUPX_DB::queryNoReturn($dbh, $sql);

        $sql = "INSERT INTO $table (option_name, option_value) VALUES('dupx_retain_themes', '".$themes_ser."')";
        DUPX_DB::queryNoReturn($dbh, $sql);
    }

    // If necessary, removes extra tables and renames
    public static function makeSubsiteDatabaseStandalone($subsite_id, $dbh, $base_prefix, $remove_redundant)
    {
        $subsite_id = (int) $subsite_id;
        self::purgeOtherSubsiteTables($subsite_id, $dbh, $base_prefix);
        if ($subsite_id !== 1) {
            // RSR DO THIS??		self::copy_data_to_subsite_table($subsite_id, $dbh, $base_prefix);
            self::renameSubsiteTablesToStandalone($subsite_id, $dbh, $base_prefix);
            //self::removeUsermetaDuplicates($dbh);
            // **RSR TODO COMPLICATION: How plugins running in single mode would behave when it was installed in multisite mode. Could be other data complications
        }


        self::purgeMultisiteTables($dbh, $base_prefix);

        return true;
    }

    /**
     * 
     * @return array
     */
    public static function getSuperAdminsUserIds($dbh)
    {
        $result        = array();
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $base_prefix      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $users_table_name = "{$base_prefix}users";

        // Super admin should remain
        $siteAdmins = is_array($archiveConfig->mu_siteadmins) ? $archiveConfig->mu_siteadmins : array();
        if (!empty($siteAdmins)) {
            $sql                  = "SELECT ID FROM {$users_table_name} WHERE user_login IN ('".implode("','", $siteAdmins)."')";
            $super_admins_results = DUPX_DB::queryToArray($dbh, $sql);
            foreach ($super_admins_results as $super_admins_result) {
                $result[] = $super_admins_result[0];
            }
        }
        return $result;
    }

    public static function updateOptionsTable($retained_subsite_id, $dbh)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $base_prefix             = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $retained_subsite_prefix = $archiveConfig->getSubsitePrefixByParam($retained_subsite_id);
        $options_table_name      = "{$base_prefix}options";

        if ($retained_subsite_prefix != $base_prefix) {
            DUPX_UpdateEngine::updateTablePrefix($dbh, $options_table_name, 'option_name', $retained_subsite_prefix, $base_prefix);
        }
    }

    // Purge non_site where meta_key in wp_usermeta starts with data from other subsite or root site,
    public static function purgeRedundantData($retained_subsite_id, $dbh)
    {
        $paramsManager    = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
            DUPX_Log::info("STANDALONE: skip purging redundant data beacause keep target site users is enable ");
            return;
        }
        
        DUPX_Log::info("STANDALONE: purging redundant data. Considering ");
        
        $archiveConfig    = DUPX_ArchiveConfig::getInstance();
        $base_prefix      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $remove_redundant = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT);

        $users_table_name        = "{$base_prefix}users";
        $usermeta_table_name     = "{$base_prefix}usermeta";
        $retained_subsite_prefix = $archiveConfig->getSubsitePrefixByParam($retained_subsite_id);
        $superAdminUsersIds      = self::getSuperAdminsUserIds($dbh);
        DUPX_Log::info("SUPER USER IDS: ".DUPX_Log::varToString($superAdminUsersIds), DUPX_Log::LV_DETAILED);

        //Remove all users which are not associated with the subsite that is being installed
        if ($remove_redundant) {
            $sql             = "SELECT user_id,meta_key FROM {$usermeta_table_name} WHERE meta_key LIKE '{$base_prefix}%_capabilities' OR meta_key = '{$base_prefix}capabilities'";
            $retain_meta_key = $retained_subsite_prefix."capabilities";
            $results         = DUPX_DB::queryToArray($dbh, $sql);
            DUPX_Log::info(print_r($results, true));
            $keep_users      = $superAdminUsersIds;
            foreach ($results as $result) {
                //$result[0] - user_id
                //$result[1] - meta_key
                if ($result[1] == $retain_meta_key) {
                    $keep_users[] = $result[0];
                }
            }
            $keep_users     = array_unique($keep_users);
            $keep_users_str = '('.implode(',', $keep_users).')';

            DUPX_Log::info("KEEP USERS IDS: ".DUPX_Log::varToString($keep_users), DUPX_Log::LV_DETAILED);
            $sql = "DELETE FROM {$users_table_name} WHERE id  NOT IN ".$keep_users_str;
            DUPX_DB::queryNoReturn($dbh, $sql);

            $sql = "DELETE FROM {$usermeta_table_name} WHERE user_id NOT IN ".$keep_users_str;
            DUPX_DB::queryNoReturn($dbh, $sql);
        }

        $escPergPrefix        = mysqli_real_escape_string($dbh, preg_quote($base_prefix, null /* no delimiter */));
        $escPergSubsitePrefix = mysqli_real_escape_string($dbh, preg_quote($retained_subsite_prefix, null /* no delimiter */));
        if ($retained_subsite_prefix == $base_prefix) {
            $sql = "DELETE FROM $usermeta_table_name WHERE meta_key REGEXP '^".$escPergPrefix."[0-9]+_';";
        } else {
            $sql = "DELETE FROM $usermeta_table_name WHERE meta_key NOT REGEXP '^".$escPergSubsitePrefix."' AND meta_key REGEXP '^".$escPergPrefix."';";
        }
        DUPX_DB::queryNoReturn($dbh, $sql);

        if ($retained_subsite_prefix != $base_prefix) {
            DUPX_UpdateEngine::updateTablePrefix($dbh, $usermeta_table_name, 'meta_key', $retained_subsite_prefix, $base_prefix);
        }

        if (!empty($superAdminUsersIds)) {
            $adminSecurity       = mysqli_real_escape_string($dbh, DUPX_WPConfig::ADMIN_SERIALIZED_SECURITY_STRING);
            $adminLevel          = DUPX_WPConfig::ADMIN_LEVEL;
            $cap_meta_key        = $base_prefix.'capabilities';
            $user_level_meta_key = $base_prefix.'user_level';

            // Ad permission for superadmin users
            foreach ($superAdminUsersIds as $suId) {
                $sql_values_array[] = "('{$suId}', '{$cap_meta_key}', '{$adminSecurity}')";
                $sql_values_array[] = "('{$suId}', '{$user_level_meta_key}', '{$adminLevel}')";
            }

            $sql = "INSERT IGNORE INTO ".$usermeta_table_name." (user_id, meta_key, meta_value) VALUES ".implode(', ', $sql_values_array);
            DUPX_DB::mysqli_query($dbh, $sql);
        }
    }

    private static function mysqlEscapeMimic($inp)
    {
        if (is_array($inp))
            return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }

    private static function escSQLSimple($sql)
    {
        $sql = addcslashes($sql, "%_");

        $sql = self::mysqlEscapeMimic($sql);

        return $sql;
        //return str_replace('_', "\\_", $sql);
        //	return str_replace(array($e, '_', '%'), array($e.$e, $e.'_', $e.'%'), $s);
    }

    // Purge all subsite tables other than the one indicated by $retained_subsite_id
    private static function purgeOtherSubsiteTables($retained_subsite_id, $dbh, $base_prefix)
    {
        // termmeta table introduced in WP 4.4
        $common_table_names = array('commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'terms', 'termmeta', 'term_relationships', 'term_taxonomy');
        $nManager           = DUPX_NOTICE_MANAGER::getInstance();

        $subsite_ids = DUPX_ArchiveConfig::getInstance()->getSubsitesIds();

        DUPX_Log::info('STANDALONE: subsite ids: '.implode(', ', $subsite_ids), DUPX_Log::LV_DETAILED);

        // Purge all tables belonging to other subsites
        foreach ($subsite_ids as $subsite_id) {
            if (($subsite_id != $retained_subsite_id) && ($subsite_id > 1)) {
                DUPX_Log::info('STANDALONE: deleting subsite '.DUPX_Log::varToString($subsite_id), DUPX_Log::LV_DETAILED);
                $subsite_prefix         = "{$base_prefix}{$subsite_id}_";
                $escaped_subsite_prefix = self::escSQLSimple($subsite_prefix);

                DUPX_Log::info("STANDALONE: subsite prefix ".DUPX_Log::varToString($subsite_prefix)." escaped prefix ".DUPX_Log::varToString($escaped_subsite_prefix), DUPX_Log::LV_DETAILED);
                $subsite_table_names = DUPX_DB::queryColumnToArray($dbh, "SHOW TABLES LIKE '{$escaped_subsite_prefix}%'");

                foreach ($subsite_table_names as $subsite_table_name) {
                    //$subsite_table_name = "{$subsite_prefix}{$common_table_name}";

                    try {
                        DUPX_Log::info("STANDALONE: drop table ".DUPX_Log::varToString($subsite_table_name));
                        DUPX_DB::dropTable($dbh, $subsite_table_name);
                    }
                    catch (Exception $e) {
                        DUPX_Log::info("STANDALONE: Error dropping table ".DUPX_Log::varToString($subsite_table_name).' MSG:'.$e->getMessage());
                        $nManager->addFinalReportNotice(array(
                            'shortMsg' => 'Error dropping table '.$subsite_table_name,
                            'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                            'longMsg'  => "STANDALONE: Error dropping table ".DUPX_Log::varToString($subsite_table_name)."\nMSG:".$e->getMessage(),
                            'sections' => 'database'
                        ));
                    }
                }
            } else {
                DUPX_Log::info('STANDALONE: skipping subsite '.DUPX_Log::varToString($subsite_id), DUPX_Log::LV_DETAILED);
            }
        }

        if ($retained_subsite_id != 1) {
            // If we are dealing with anything other than the main subsite then we need to purge its core tables
            foreach ($common_table_names as $common_table_name) {
                $subsite_table_name = "$base_prefix$common_table_name";

                DUPX_DB::dropTable($dbh, $subsite_table_name);
            }
        }
    }

    // Purge all subsite tables other than the one indicated by $retained_subsite_id
    private static function purgeMultisiteTables($dbh, $base_prefix)
    {
        $multisite_table_names = array('blogs', 'blog_versions', 'blogmeta', 'registration_log', 'signups', 'site', 'sitemeta');
        $nManager              = DUPX_NOTICE_MANAGER::getInstance();

        // Remove multisite specific tables
        foreach ($multisite_table_names as $multisite_table_name) {
            $full_table_name = "$base_prefix$multisite_table_name";

            try {
                DUPX_DB::dropTable($dbh, $full_table_name);
            }
            catch (Exception $e) {
                DUPX_Log::info("STANDALONE: Error dropping table ".DUPX_Log::varToString($full_table_name).' MSG:'.$e->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Error dropping table '.$full_table_name,
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE: Error dropping table ".DUPX_Log::varToString($full_table_name)."\nMSG:".$e->getMessage(),
                    'sections' => 'database'
                ));
            }
        }
    }

    private static function removeUsermetaDuplicates($dbh)
    {
        // RSR TODO: Remove duplicate user meta data
        throw new Exception("Not implemented yet.");
    }

    public static function getAllSiteIdsinWP()
    {
        $siteIds = array();
        if (function_exists('get_sites')) {
            $sites = get_sites();
            foreach ($sites as $site) {
                $siteIds[] = $site->blog_id;
            }
        } else {
            $sites = wp_get_sites();
            foreach ($sites as $site) {
                $siteIds[] = $site['blog_id'];
            }
        }
        return $siteIds;
    }
}