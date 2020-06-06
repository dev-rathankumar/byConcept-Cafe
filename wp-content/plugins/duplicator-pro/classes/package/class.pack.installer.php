<?php
/**
 * Classes for building the package installer extra files
 *
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.system.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.shell.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.archive.config.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.brand.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.password.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/config/class.wp.config.tranformer.php');

class DUP_PRO_Installer
{

    const DEFAULT_INSTALLER_FILE_NAME_WITHOUT_HASH = 'installer.php';
    const CONFIG_ORIG_FILE_FOLER_PREFIX = 'source_site_';

    public $File;
    public $Size             = 0;
    //SETUP
    public $OptsSecureOn;
    public $OptsSecurePass;
    public $OptsSkipScan;
    //BASIC
    public $OptsDBHost;
    public $OptsDBName;
    public $OptsDBUser;
    //CPANEL
    public $OptsCPNLHost     = '';
    public $OptsCPNLUser     = '';
    public $OptsCPNLPass     = '';
    public $OptsCPNLEnable   = false;
    public $OptsCPNLConnect  = false;
    //CPANEL DB
    //1 = Create New, 2 = Connect Remove
    public $OptsCPNLDBAction = 'create';
    public $OptsCPNLDBHost   = '';
    public $OptsCPNLDBName   = '';
    public $OptsCPNLDBUser   = '';
    //PROTECTED

    /**
     *
     * @var DupProSnapLibOrigFileManager 
     */
    protected $origFileManger = null;

    /**
     *
     * @var DUP_PRO_Package
     */
    protected $Package;
    public $numFilesAdded = 0;
    public $numDirsAdded  = 0;

    /**
     *
     * @var WPConfigTransformer 
     */
    private $configTransformer = null;

    /**
     *
     * @param DUP_PRO_Package $package
     */
    public function __construct($package)
    {
        $this->Package = $package;
        if (($wpConfigPath = DUP_PRO_Archive::getWPConfigFilePath()) !== false) {
            $this->configTransformer = new WPConfigTransformer($wpConfigPath);
        }
    }

    public function __destruct()
    {
        $this->Package        = null;
        $this->origFileManger = null;
    }

    public function get_safe_filepath()
    {
        $file_path = apply_filters('duplicator_pro_installer_file_path', DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH."/{$this->File}"));
        return $file_path;
    }

    public function get_orig_filename()
    {
        return $this->File;
    }

    public function get_url()
    {
        return DUPLICATOR_PRO_SSDIR_URL."/{$this->File}";
    }

    public function build($package, $build_progress)
    {
        /* @var $package DUP_PRO_Package */
        DUP_PRO_LOG::trace("building installer");

        $this->Package = $package;
        $success       = false;

        if ($this->create_enhanced_installer_files()) {
            $success = $this->add_extra_files($package);
        }

        if ($success) {
            $build_progress->installer_built = true;
        } else {
            $build_progress->failed = true;
        }
    }

    private function create_enhanced_installer_files()
    {
        $success = false;

        if ($this->create_enhanced_installer()) {
            $success = $this->create_archive_config_file();
        }

        return $success;
    }

    private function create_enhanced_installer()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        $success = true;

        $archive_filepath       = DupProSnapLibIOU::safePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
        $installer_filepath     = apply_filters('duplicator_pro_installer_file_path', DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_{$global->installer_base_name}");
        $template_filepath      = DUPLICATOR_PRO_PLUGIN_PATH.'/installer/installer.tpl';
        $mini_expander_filepath = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/dup_archive/classes/class.duparchive.mini.expander.php';

        // Replace the @@ARCHIVE@@ token
        $installer_contents = file_get_contents($template_filepath);
        // $csrf_class_contents = file_get_contents($csrf_class_filepath);

        if ($this->Package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::DupArchive) {
            $mini_expander_string = file_get_contents($mini_expander_filepath);

            if ($mini_expander_string === false) {
                DUP_PRO_Log::error(DUP_PRO_U::__('Error reading DupArchive mini expander'), DUP_PRO_U::__('Error reading DupArchive mini expander'), false);
                return false;
            }
        } else {
            $mini_expander_string = '';
        }

        $search_array           = array('@@ARCHIVE@@', '@@VERSION@@', '@@ARCHIVE_SIZE@@', '@@PACKAGE_HASH@@', '@@SECONDARY_PACKAGE_HASH@@', '@@DUPARCHIVE_MINI_EXPANDER@@');
        $package_hash           = $this->Package->get_package_hash();
        $secondary_package_hash = $this->Package->getSecondaryPackageHash();
        $replace_array          = array($this->Package->Archive->File, DUPLICATOR_PRO_VERSION, @filesize($archive_filepath), $package_hash, $secondary_package_hash, $mini_expander_string);

        $installer_contents = str_replace($search_array, $replace_array, $installer_contents);

        if (@file_put_contents($installer_filepath, $installer_contents) === false) {
            DUP_PRO_Log::error(DUP_PRO_U::__('Error writing installer contents'), DUP_PRO_U::__("Couldn't write to $installer_filepath"), false);
            $success = false;
        }

        if ($success) {
            $storePath  = "{$this->Package->StorePath}/{$this->File}";
            $this->Size = @filesize($storePath);
        }

        return $success;
    }

    /**
     * Create archive.txt file
     * 
     * @global type $wpdb
     * @return boolean
     */
    private function create_archive_config_file()
    {
        global $wpdb;

        $global                  = DUP_PRO_Global_Entity::get_instance();
        $success                 = true;
        $archive_config_filepath = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_archive.txt";
        $ac                      = new DUP_PRO_Archive_Config();
        $extension               = strtolower($this->Package->Archive->Format);
        $hasher                  = new DUP_PRO_PasswordHash(8, FALSE);
        $pass_hash               = $hasher->HashPassword($this->Package->Installer->OptsSecurePass);

        //READ-ONLY: COMPARE VALUES
        $ac->created     = $this->Package->Created;
        $ac->version_dup = DUPLICATOR_PRO_VERSION;
        $ac->version_wp  = $this->Package->VersionWP;
        $ac->version_db  = $this->Package->VersionDB;
        $ac->version_php = $this->Package->VersionPHP;
        $ac->version_os  = $this->Package->VersionOS;
        $ac->dbInfo      = $this->Package->Database->info;
        $ac->fileInfo    = array(
            'dirCount'  => $this->Package->Archive->DirCount,
            'fileCount' => $this->Package->Archive->FileCount,
            'size'      => $this->Package->Archive->Size
        );

        $ac->wpInfo = $this->getWpInfo();

        //READ-ONLY: GENERAL
        $ac->installer_base_name = $global->installer_base_name;
        $ac->package_name        = "{$this->Package->NameHash}_archive.{$extension}";
        $ac->package_hash        = $this->Package->get_package_hash();

        $ac->package_notes        = $this->Package->Notes;
        $ac->opts_delete          = DupProSnapJsonU::wp_json_encode($GLOBALS['DUPLICATOR_PRO_OPTS_DELETE']);
        $ac->blogname             = sanitize_text_field(get_option('blogname'));
        $ac->wproot               = duplicator_pro_get_home_path();
        $ac->relative_content_dir = str_replace(ABSPATH, '', WP_CONTENT_DIR);
        $ac->relative_plugins_dir = str_replace(ABSPATH, '', WP_PLUGIN_DIR);
        $ac->relative_plugins_dir = str_replace($ac->wproot, '', $ac->relative_plugins_dir);
        $ac->relative_theme_dirs  = get_theme_roots();
        if (is_array($ac->relative_theme_dirs)) {
            foreach ($ac->relative_theme_dirs as $key => $dir) {
                if (strpos($dir, $ac->wproot) === false) {
                    $ac->relative_theme_dirs[$key] = $ac->relative_content_dir.$dir;
                } else {
                    $ac->relative_theme_dirs[$key] = str_replace($ac->wproot, '', $dir);
                }
            }
        } else {
            $ac->relative_theme_dirs = array();
            $dir                     = get_theme_roots();
            if (strpos($dir, $ac->wproot) === false) {
                $ac->relative_theme_dirs[] = $ac->relative_content_dir.$dir;
            } else {
                $ac->relative_theme_dirs[] = str_replace($ac->wproot, '', $dir);
            }
        }
        $ac->exportOnlyDB = $this->Package->Archive->ExportOnlyDB;
        $ac->wplogin_url  = wp_login_url();

        //PRE-FILLED: GENERAL
        $ac->secure_on   = (bool) $this->Package->Installer->OptsSecureOn;
        $ac->secure_pass = $ac->secure_on ? $pass_hash : '';
        $ac->skipscan    = $this->Package->Installer->OptsSkipScan;
        $ac->dbhost      = $this->Package->Installer->OptsDBHost;
        $ac->dbname      = $this->Package->Installer->OptsDBName;
        $ac->dbuser      = $this->Package->Installer->OptsDBUser;
        $ac->dbpass      = '';

        //PRE-FILLED: CPANEL
        $ac->cpnl_host     = $this->Package->Installer->OptsCPNLHost;
        $ac->cpnl_user     = $this->Package->Installer->OptsCPNLUser;
        $ac->cpnl_pass     = $this->Package->Installer->OptsCPNLPass;
        $ac->cpnl_enable   = $this->Package->Installer->OptsCPNLEnable;
        $ac->cpnl_connect  = $this->Package->Installer->OptsCPNLConnect;
        $ac->cpnl_dbaction = $this->Package->Installer->OptsCPNLDBAction;
        $ac->cpnl_dbhost   = $this->Package->Installer->OptsCPNLDBHost;
        $ac->cpnl_dbname   = $this->Package->Installer->OptsCPNLDBName;
        $ac->cpnl_dbuser   = $this->Package->Installer->OptsCPNLDBUser;

        //MULTISITE
        $ac->mu_mode        = DUP_PRO_MU::getMode();
        $ac->wp_tableprefix = $wpdb->base_prefix;

        $ac->mu_generation  = DUP_PRO_MU::getGeneration();
        $ac->mu_is_filtered = !empty($this->Package->Multisite->FilterSites) ? true : false;
        $ac->mu_siteadmins  = get_super_admins();

        $ac->subsites = DUP_PRO_MU::getSubsites($this->Package->Multisite->FilterSites);
        if ($ac->subsites === false) {
            $success = false;
        }
        $ac->main_site_id = DUP_PRO_MU::get_main_site_id();

        //BRAND
        $ac->brand = $this->the_brand_setup($this->Package->Brand_ID);

        //LICENSING
        $ac->license_limit = $global->license_limit;

        // OVERWRITE PARAMS
        $ac->overwriteInstallerParams = apply_filters('duplicator_pro_overwrite_params_data', array());

        $ac->wp_content_dir_base_name     = '';
        $ac->is_outer_root_wp_config_file = false;

        $json = DupProSnapJsonU::wp_json_encode_pprint($ac);

        DUP_PRO_LOG::traceObject('json', $json);

        if (file_put_contents($archive_config_filepath, $json) === false) {
            DUP_PRO_Log::error("Error writing archive config", "Couldn't write archive config at $archive_config_filepath", false);
            $success = false;
        }

        return $success;
    }

    /**
     * get wpInfo object 
     * 
     * @return \stdClass
     */
    private function getWpInfo()
    {
        $wpInfo               = new stdClass();
        $wpInfo->version      = $this->Package->VersionWP;
        $wpInfo->is_multisite = is_multisite();

        if (function_exists('get_current_network_id')) {
            $wpInfo->network_id = get_current_network_id();
        } else {
            $wpInfo->network_id = 1;
        }

        $wpInfo->targetRoot  = DUP_PRO_Archive::getTargetRootPath();
        $wpInfo->targetPaths = DUP_PRO_Archive::getScanPaths();

        $wpInfo->adminUsers          = DUP_PRO_WP_U::getAdminUserLists();
        $wpInfo->configs             = new stdClass();
        $wpInfo->configs->defines    = new stdClass();
        $wpInfo->configs->realValues = new stdClass();
        $wpInfo->plugins             = $this->getPluginsInfo();

        $this->addDefineIfExists($wpInfo->configs->defines, 'ABSPATH');

        $this->addDefineIfExists($wpInfo->configs->defines, 'DB_CHARSET');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DB_COLLATE');

        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTH_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SECURE_AUTH_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'LOGGED_IN_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'NONCE_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTH_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SECURE_AUTH_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'LOGGED_IN_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'NONCE_SALT');

        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_SITEURL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_HOME');

        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_URL');

        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_PLUGIN_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_PLUGIN_URL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'PLUGINDIR');

        $this->addDefineIfExists($wpInfo->configs->defines, 'UPLOADS');

        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTOSAVE_INTERVAL');
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
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CACHE');

        // wp super cache define
        $this->addDefineIfExists($wpInfo->configs->defines, 'WPCACHEHOME');

        $this->addDefineIfExists($wpInfo->configs->defines, 'CUSTOM_USER_TABLE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'CUSTOM_USER_META_TABLE');

        $this->addDefineIfExists($wpInfo->configs->defines, 'WPLANG');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_LANG_DIR');

        $this->addDefineIfExists($wpInfo->configs->defines, 'SAVEQUERIES');

        $this->addDefineIfExists($wpInfo->configs->defines, 'FS_CHMOD_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FS_CHMOD_FILE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FS_METHOD');

        /**
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_BASE');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_CONTENT_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PLUGIN_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PUBKEY');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PRIKEY');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_USER');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PASS');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_HOST');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_SSL');
         * */
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
        $this->addDefineIfExists($wpInfo->configs->defines, 'DISALLOW_FILE_EDIT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DISALLOW_FILE_MODS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FORCE_SSL_ADMIN');

        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_HTTP_BLOCK_EXTERNAL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ACCESSIBLE_HOSTS');

        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTOMATIC_UPDATER_DISABLED');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_AUTO_UPDATE_CORE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'IMAGE_EDIT_OVERWRITE');

        $this->addDefineIfExists($wpInfo->configs->defines, 'WPMU_PLUGIN_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WPMU_PLUGIN_URL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'MUPLUGINDIR');

        $updDirs = wp_upload_dir();

        $wpInfo->configs->realValues->siteUrl       = site_url();
        $wpInfo->configs->realValues->homeUrl       = home_url();
        $wpInfo->configs->realValues->contentUrl    = content_url();
        $wpInfo->configs->realValues->uploadBaseUrl = $updDirs['baseurl'];
        $wpInfo->configs->realValues->pluginsUrl    = plugins_url();
        $wpInfo->configs->realValues->mupluginsUrl  = WPMU_PLUGIN_URL;
        $wpInfo->configs->realValues->themesUrl     = get_theme_root_uri();

        $wpInfo->configs->realValues->originalPaths = array();

        $originalpaths = DUP_PRO_Archive::getOriginalPaths();
        foreach ($originalpaths as $key => $val) {
            $wpInfo->configs->realValues->originalPaths[$key] = rtrim($val, '\\/');
        }
        $wpInfo->configs->realValues->archivePaths = array_merge(
            $wpInfo->configs->realValues->originalPaths,
            DUP_PRO_Archive::getArchiveListPaths()
        );

        return $wpInfo;
    }

    /**
     * check if $define is defined and add a prop to $obj
     * 
     * @param object $obj
     * @param string $define
     * @return boolean return true if define is added of false
     * 
     */
    private function addDefineIfExists($obj, $define)
    {
        if (defined($define)) {
            $obj->{$define}             = new StdClass();
            $obj->{$define}->value      = constant($define);
            $obj->{$define}->inWpConfig = $this->configTransformer->exists('constant', $define);
            return true;
        } else {
            return false;
        }
    }

    /**
     * get plugins array info with multisite, must-use and drop-ins
     * 
     * @return array
     */
    public function getPluginsInfo()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        // parse all plugins
        $result = array();
        foreach (get_plugins() as $path => $plugin) {
            $result[$path]                  = self::getPluginArrayData($path, $plugin);
            $result[$path]['networkActive'] = is_plugin_active_for_network($path);
            if (!is_multisite()) {
                $result[$path]['active'] = is_plugin_active($path);
            } else {
                // if is _multisite the active value is an array with the blog ids list where the plugin is active
                $result[$path]['active'] = array();
            }
        }

        // if is _multisite the active value is an array with the blog ids list where the plugin is active
        if (is_multisite()) {
            $sites = DupProSnapLibUtilWp::getSites();
            foreach ($sites as $site) {
                switch_to_blog($site->blog_id);
                foreach ($result as $path => $plugin) {
                    if (!$result[$path]['networkActive'] && is_plugin_active($path)) {
                        $result[$path]['active'][] = $site->blog_id;
                    }
                }
                restore_current_blog();
            }
        }

        // parse all must use plugins
        foreach (get_mu_plugins() as $path => $plugin) {
            $result[$path]            = self::getPluginArrayData($path, $plugin);
            $result[$path]['mustUse'] = true;
        }

        // parse all dropins plugins
        foreach (get_dropins() as $path => $plugin) {
            $result[$path]            = self::getPluginArrayData($path, $plugin);
            $result[$path]['dropIns'] = true;
        }

        return $result;
    }

    /**
     * return plugin formatted data from plugin info
     * plugin info =  Array (
     *      [Name] => Hello Dolly
     *      [PluginURI] => http://wordpress.org/extend/plugins/hello-dolly/
     *      [Version] => 1.6
     *      [Description] => This is not just ...
     *      [Author] => Matt Mullenweg
     *      [AuthorURI] => http://ma.tt/
     *      [TextDomain] =>
     *      [DomainPath] =>
     *      [Network] =>
     *      [Title] => Hello Dolly
     *      [AuthorName] => Matt Mullenweg
     * )
     * 
     * @param string $slug      // plugin slug 
     * @param array $plugin     // pluhin info from get_plugins function
     * @return array
     */
    protected static function getPluginArrayData($slug, $plugin)
    {
        return array(
            'slug'          => $slug,
            'name'          => $plugin['Name'],
            'version'       => $plugin['Version'],
            'pluginURI'     => $plugin['PluginURI'],
            'author'        => $plugin['Author'],
            'authorURI'     => $plugin['AuthorURI'],
            'description'   => $plugin['Description'],
            'title'         => $plugin['Title'],
            'networkActive' => false,
            'active'        => false,
            'mustUse'       => false,
            'dropIns'       => false
        );
    }

    private function the_brand_setup($id)
    {
        // initialize brand
        $brand = DUP_PRO_Brand_Entity::get_by_id((int) $id);

        // Prepare default fields
        $brand_property_default = array(
            'logo'    => '',
            'enabled' => false,
            'style'   => array()
        );

        // Returns property
        $brand_property = array();

        // Set logo and hosted images path
        if (isset($brand->logo)) {
            $brand_property['logo'] = $brand->logo;
            // Find images
            preg_match_all('/<img.*?src="([^"]+)".*?>/', $brand->logo, $arr_img, PREG_PATTERN_ORDER); // https://regex101.com/r/eEyf5S/2
            // Fix hosted image url path
            if (isset($arr_img[1]) && count($brand->attachments) > 0 && count($arr_img[1]) === count($brand->attachments)) {
                foreach ($arr_img[1] as $i => $find) {
                    $brand_property['logo'] = str_replace($find, 'assets/images/brand'.$brand->attachments[$i], $brand_property['logo']);
                }
            }
            $brand_property['logo'] = stripslashes($brand_property['logo']);
        }

        // Set is enabled
        if (!empty($brand_property['logo']) && isset($brand->active) && $brand->active)
            $brand_property['enabled'] = true;

        // Let's include style
        if (isset($brand->style)) {
            $brand_property['style'] = $brand->style;
        }

        // Merge data properly
        if (function_exists("array_replace") && version_compare(phpversion(), '5.3.0', '>='))
            $brand_property = array_replace($brand_property_default, $brand_property); // (PHP 5 >= 5.3.0)
        else
            $brand_property = array_merge($brand_property_default, $brand_property); // (PHP 5 < 5.3.0)

        return $brand_property;
    }

    /**
     *  createZipBackup
     *  Puts an installer zip file in the archive for backup purposes.
     */
    private function add_extra_files($package)
    {
        $success                 = false;
        $global                  = DUP_PRO_Global_Entity::get_instance();
        $installer_filepath      = apply_filters('duplicator_pro_installer_file_path', DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_{$global->installer_base_name}");
        $scan_filepath           = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_scan.json";
        $file_list_filepath      = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP).'/'.$this->Package->NameHash.DUP_PRO_Archive::FILES_LIST_FILE_NAME_SUFFIX;
        $dir_list_filepath       = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP).'/'.$this->Package->NameHash.DUP_PRO_Archive::DIRS_LIST_FILE_NAME_SUFFIX;
        $sql_filepath            = DupProSnapLibIOU::safePath("{$this->Package->StorePath}/{$this->Package->Database->File}");
        $archive_filepath        = DupProSnapLibIOU::safePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
        $archive_config_filepath = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_archive.txt";
        $this->origFileManger = new DupProSnapLibOrigFileManager(DUP_PRO_Archive::getArchiveListPaths('home'), DUPLICATOR_PRO_SSDIR_PATH_TMP, $package->get_package_hash());

        if (file_exists($installer_filepath) == false) {
            DUP_PRO_Log::error("Installer $installer_filepath not present", '', false);
            return false;
        }

        if (file_exists($sql_filepath) == false) {
            DUP_PRO_Log::error("Database SQL file $sql_filepath not present", '', false);
            return false;
        }

        if (file_exists($archive_config_filepath) == false) {
            DUP_PRO_Log::error("Archive configuration file $archive_config_filepath not present", '', false);
            return false;
        }

        $this->initConfigFiles();
        $config_files_folder_path = $this->origFileManger->getMainFolder();
        if (is_readable($config_files_folder_path) == false) {
            DUP_PRO_Log::error("Config files folder $config_files_folder_path not present", '', false);
            return false;
        }

        $this->createManualExtractCheckFile();
        $manual_extract_file_path = $this->getManualExtractFilePath();
        if (!file_exists($manual_extract_file_path)) {
            DUP_PRO_Log::error("Manual extract file $manual_extract_file_path not present", '', false);
            return false;
        }

        if ($package->Archive->file_count != 2) {
            DUP_PRO_LOG::trace("Doing archive file check");
            // Only way it's 2 is if the root was part of the filter in which case the archive won't be there
            if (file_exists($archive_filepath) == false) {
                $error_text = sprintf(DUP_PRO_U::__("Zip archive %1s not present."), $archive_filepath);
                //$fix_text   = DUP_PRO_U::__("Go to: Settings > Packages Tab > Set Archive Engine to ZipArchive.");
                $fix_text   = DUP_PRO_U::__("Click on button to set archive engine to DupArchive.");

                DUP_PRO_Log::error("$error_text. **RECOMMENDATION: $fix_text", '', false);

                $system_global = DUP_PRO_System_Global_Entity::get_instance();
                //$system_global->add_recommended_text_fix($error_text, $fix_text);
                $system_global->add_recommended_quick_fix($error_text, $fix_text, 'global : {archive_build_mode:3}');
                $system_global->save();

                return false;
            }
        }

        DUP_PRO_LOG::trace("Add extra files: Current build mode = ".$package->build_progress->current_build_mode);

        if ($package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::ZipArchive) {
            $success = $this->add_extra_files_using_ziparchive($installer_filepath, $scan_filepath, $file_list_filepath, $dir_list_filepath, $sql_filepath, $archive_filepath, $archive_config_filepath, $package->build_progress->current_build_compression);
        } else if ($package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::Shell_Exec) {
            $success = $this->add_extra_files_using_shellexec($archive_filepath, $installer_filepath, $scan_filepath, $file_list_filepath, $dir_list_filepath, $sql_filepath, $archive_config_filepath, $package->build_progress->current_build_compression);
            // Adding the shellexec fail text fix
            if (!$success) {
                $error_text = DUP_PRO_U::__("Problem adding installer to archive");
                $fix_text   = DUP_PRO_U::__("Click on button to set archive engine to DupArchive.");

                $system_global = DUP_PRO_System_Global_Entity::get_instance();
                $system_global->add_recommended_quick_fix($error_text, $fix_text, 'global : {archive_build_mode:3}');
                $system_global->save();
            }
        } else if ($package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::DupArchive) {
            $success = $this->add_extra_files_using_duparchive($installer_filepath, $scan_filepath, $file_list_filepath, $dir_list_filepath, $sql_filepath, $archive_filepath, $archive_config_filepath);
        }

        // No sense keeping these files
        @unlink($archive_config_filepath);
        $this->origFileManger->deleteMainFolder();
        $this->deleteManualExtractCheckFile();

        $package->Archive->Size = @filesize($archive_filepath);

        return $success;
    }

    private function add_extra_files_using_duparchive($installer_filepath, $scan_filepath, $file_list_filepath, $dir_list_filepath, $sql_filepath, $archive_filepath, $archive_config_filepath)
    {
        $success = false;

        try {
            $logger = new DUP_PRO_Dup_Archive_Logger();

            DupArchiveEngine::init($logger, 'DUP_PRO_LOG::profile');

            $embedded_scan_ark_file_path = $this->getEmbeddedScanFilePath();
            DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $scan_filepath, $embedded_scan_ark_file_path);
            $this->numFilesAdded++;

            $embedded_file_list_file_path = $this->getEmbeddedFileListFilePath();
            DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $file_list_filepath, $embedded_file_list_file_path);
            $this->numFilesAdded++;

            $embedded_dir_list_file_path = $this->getEmbeddedDirListFilePath();
            DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $dir_list_filepath, $embedded_dir_list_file_path);
            $this->numFilesAdded++;

            $embedded_manual_extract_file_path = $this->getEmbeddedManualExtractFilePath();
            $manual_extract_file_path          = $this->getManualExtractFilePath();
            DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $manual_extract_file_path, $embedded_manual_extract_file_path);
            $this->numFilesAdded++;

            $this->add_installer_files_using_duparchive($archive_filepath, $installer_filepath, $archive_config_filepath);

            $success = true;
        }
        catch (Exception $ex) {
            DUP_PRO_Log::error("Error adding installer files to archive. ".$ex->getMessage());
        }

        return $success;
    }
	
	public function getInstallerBackupName() {
		return $this->Package->NameHash.'_'.DUP_PRO_Global_Entity::get_instance()->get_installer_backup_filename();
	}

    private function add_installer_files_using_duparchive($archive_filepath, $installer_filepath, $archive_config_filepath)
    {
        $installer_backup_filename = $this->getInstallerBackupName();

        DUP_PRO_LOG::trace('Adding enhanced installer files to archive using DupArchive');

        if (!is_readable($installer_filepath)) {
            throw new Exception('INSTALLER FILES: file doesn\'t exist '.$installer_filepath);
        }
        DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $installer_filepath, $installer_backup_filename);
        $this->numFilesAdded++;

        $base_installer_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/installer';
        $installer_directory      = "$base_installer_directory/dup-installer";
        if (!is_readable($installer_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$installer_directory);
        }
        $counts              = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $installer_directory, $base_installer_directory, true);
        $this->numFilesAdded += $counts->numFilesAdded;
        $this->numDirsAdded  += $counts->numDirsAdded;

        $archive_config_relative_path = $this->getArchiveTxtFilePath();

        DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $archive_config_filepath, $archive_config_relative_path);
        $this->numFilesAdded++;

        // Include dup archive
        $duparchive_lib_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/dup_archive';
        if (!is_readable($duparchive_lib_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$duparchive_lib_directory);
        }
        $duparchive_lib_counts = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $duparchive_lib_directory, DUPLICATOR_PRO_PLUGIN_PATH, true, 'dup-installer/');
        $this->numFilesAdded   += $duparchive_lib_counts->numFilesAdded;
        $this->numDirsAdded    += $duparchive_lib_counts->numDirsAdded;

        // Include config tranformer classes
        $config_lib_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/config';
        if (!is_readable($config_lib_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$config_lib_directory);
        }
        $config_lib_counts   = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $config_lib_directory, DUPLICATOR_PRO_PLUGIN_PATH, true, 'dup-installer/');
        $this->numFilesAdded += $config_lib_counts->numFilesAdded;
        $this->numDirsAdded  += $config_lib_counts->numDirsAdded;

        // Include snaplib
        $snaplib_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/snaplib';
        if (!is_readable($snaplib_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$snaplib_directory);
        }
        $snaplib_counts      = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $snaplib_directory, DUPLICATOR_PRO_PLUGIN_PATH, true, 'dup-installer/');
        $this->numFilesAdded += $snaplib_counts->numFilesAdded;
        $this->numDirsAdded  += $snaplib_counts->numDirsAdded;

        // Include fileops
        $fileops_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/fileops';
        if (!is_readable($fileops_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$fileops_directory);
        }
        $fileops_counts      = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $fileops_directory, DUPLICATOR_PRO_PLUGIN_PATH, true, 'dup-installer/');
        $this->numFilesAdded += $fileops_counts->numFilesAdded;
        $this->numDirsAdded  += $fileops_counts->numDirsAdded;

        // Include config files
        $config_files_directory = $this->origFileManger->getMainFolder();
        if (!is_readable($config_files_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$config_files_directory);
        }
        $config_files_counts = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $config_files_directory, DUPLICATOR_PRO_SSDIR_PATH_TMP, true, 'dup-installer/');
        $this->numFilesAdded += $config_files_counts->numFilesAdded;
        $this->numDirsAdded  += $config_files_counts->numDirsAdded;
    }

    private function add_extra_files_using_ziparchive($installer_filepath, $scan_filepath, $file_list_filepath, $dir_list_filepath, $sql_filepath, $zip_filepath, $archive_config_filepath, $is_compressed)
    {
        $success = false;

        $zipArchive = new ZipArchive();

        if ($zipArchive->open($zip_filepath, ZIPARCHIVE::CREATE) === TRUE) {
            DUP_PRO_LOG::trace("Successfully opened zip $zip_filepath");

            $embedded_scan_ark_file_path = $this->getEmbeddedScanFilePath();
            if (DUP_PRO_Zip_U::addFileToZipArchive($zipArchive, $scan_filepath, $embedded_scan_ark_file_path, $is_compressed)) {
                if (DUP_PRO_Zip_U::addFileToZipArchive($zipArchive, $file_list_filepath, $this->getEmbeddedScanFileList(), $is_compressed)) {
                    if (DUP_PRO_Zip_U::addFileToZipArchive($zipArchive, $dir_list_filepath, $this->getEmbeddedScanDirList(), $is_compressed)) {
                        if (DUP_PRO_Zip_U::addFileToZipArchive($zipArchive, $this->getManualExtractFilePath(), $this->getEmbeddedManualExtractFilePath(), $is_compressed)) {
                            if ($this->add_installer_files_using_zip_archive($zipArchive, $installer_filepath, $archive_config_filepath, $is_compressed)) {
                                DUP_PRO_Log::info("Installer files added to archive");
                                DUP_PRO_LOG::trace("Added to archive");

                                $success = true;
                            } else {
                                DUP_PRO_Log::error("Unable to add enhanced enhanced installer files to archive.", '', false);
                            }
                        } else {
                            DUP_PRO_Log::error("Unable to add manual extract file to archive.", '', false);
                        }
                    } else {
                        DUP_PRO_Log::error("Unable to add dir list file to archive.", '', false);
                    }
                } else {
                    DUP_PRO_Log::error("Unable to add file list file to archive.", '', false);
                }
            } else {
                DUP_PRO_Log::error("Unable to add scan file to archive.", '', false);
            }

            if ($zipArchive->close() === false) {
                DUP_PRO_Log::error("Couldn't close archive when adding extra files.");
                $success = false;
            }

            if (!empty($temp_conf_ark_file_path)) {
                @unlink($temp_conf_ark_file_path);
            }

            DUP_PRO_LOG::trace('After ziparchive close when adding installer');
        }

        return $success;
    }

    private function add_extra_files_using_shellexec($zip_filepath, $installer_filepath, $scan_filepath, $file_list_filepath, $dir_list_filepath, $sql_filepath, $archive_config_filepath, $is_compressed)
    {
        $success = false;
        $global  = DUP_PRO_Global_Entity::get_instance();

        $installer_source_directory      = DUPLICATOR_PRO_PLUGIN_PATH.'/installer';
        $installer_dpro_source_directory = "$installer_source_directory/dup-installer";
        if (!is_readable($installer_dpro_source_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$installer_dpro_source_directory);
        }
        $extras_directory           = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP).'/extras';
        $extras_installer_directory = $extras_directory.'/dup-installer';

        $snaplib_source_directory = DUPLICATOR_PRO_LIB_PATH.'/snaplib';
        if (!is_readable($snaplib_source_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$snaplib_source_directory);
        }

        $fileops_source_directory = DUPLICATOR_PRO_LIB_PATH.'/fileops';
        if (!is_readable($fileops_source_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$fileops_source_directory);
        }

        $config_source_directory = DUPLICATOR_PRO_LIB_PATH.'/config';
        if (!is_readable($config_source_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$config_source_directory);
        }

        $config_files_source_directory = $this->origFileManger->getMainFolder();
        if (!is_readable($config_files_source_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$config_files_source_directory);
        }

        $duparchive_source_directory = DUPLICATOR_PRO_LIB_PATH.'/dup_archive';
        if (!is_readable($duparchive_source_directory)) {
            throw new Exception('INSTALLER FILES: folder doesn\'t exist '.$duparchive_source_directory);
        }

        $extras_snaplib_directory      = $extras_installer_directory.'/lib/snaplib';
        $extras_fileops_directory      = $extras_installer_directory.'/lib/fileops';
        $extras_duparchive_directory   = $extras_installer_directory.'/lib/dup_archive';
        $extras_config_directory       = $extras_installer_directory.'/lib/config';
        $extras_config_files_directory = $extras_installer_directory.'/'.basename($config_files_source_directory);

        $installer_backup_filepath = "$extras_directory/".$this->getInstallerBackupName();;

        $package_hash                 = $this->Package->get_package_hash();
        $dest_sql_filepath            = "$extras_installer_directory/dup-database__{$package_hash}.sql";
        $dest_archive_config_filepath = "$extras_installer_directory/dup-archive__{$package_hash}.txt";
        $dest_scan_filepath           = "$extras_installer_directory/dup-scan__{$package_hash}.json";
        $dest_manual_extract_filepath = "$extras_installer_directory/dup-manual-extract__{$package_hash}";
        //$dest_sql_filepath            = "$extras_directory/database.sql";
        //$dest_archive_config_filepath = "$extras_installer_directory/archive.cfg";
        //$dest_scan_filepath           = "$extras_directory/scan.json";

        $dest_file_list_filepath = "$extras_installer_directory/dup-scanned-files__{$package_hash}.txt";
        $dest_dir_list_filepath  = "$extras_installer_directory/dup-scanned-dirs__{$package_hash}.txt";

        if (file_exists($extras_directory)) {
            if (DUP_PRO_IO::deleteTree($extras_directory) === false) {
                DUP_PRO_Log::error("Error deleting $extras_directory", '', false);
                return false;
            }
        }

        if (!@mkdir($extras_directory)) {
            DUP_PRO_Log::error("Error creating extras directory", "Couldn't create $extras_directory", false);
            return false;
        }

        if (!@mkdir($extras_installer_directory)) {
            DUP_PRO_Log::error("Error creating extras directory", "Couldn't create $extras_installer_directory", false);
            return false;
        }

        if (@copy($installer_filepath, $installer_backup_filepath) === false) {
            DUP_PRO_Log::error("Error copying $installer_filepath to $installer_backup_filepath", '', false);
            return false;
        }

        if (@copy($sql_filepath, $dest_sql_filepath) === false) {
            DUP_PRO_Log::error("Error copying $sql_filepath to $dest_sql_filepath", '', false);
            return false;
        }

        if (@copy($archive_config_filepath, $dest_archive_config_filepath) === false) {
            DUP_PRO_Log::error("Error copying $archive_config_filepath to $dest_archive_config_filepath", '', false);
            return false;
        }

        if (@copy($scan_filepath, $dest_scan_filepath) === false) {
            DUP_PRO_Log::error("Error copying $scan_filepath to $dest_scan_filepath", '', false);
            return false;
        }

        $manual_extract_filepath = $this->getManualExtractFilePath();
        if (@copy($manual_extract_filepath, $dest_manual_extract_filepath) === false) {
            DUP_PRO_Log::error("Error copying $manual_extract_filepath to $dest_manual_extract_filepath", '', false);
            return false;
        }

        if (@copy($file_list_filepath, $dest_file_list_filepath) === false) {
            DUP_PRO_Log::error("Error copying $file_list_filepath to $dest_file_list_filepath", '', false);
            return false;
        }

        if (@copy($dir_list_filepath, $dest_dir_list_filepath) === false) {
            DUP_PRO_Log::error("Error copying $dir_list_filepath to $dest_dir_list_filepath", '', false);
            return false;
        }

        $one_stage_add = strtoupper($global->get_installer_extension()) == 'PHP';

        if ($one_stage_add) {

            if (!@mkdir($extras_snaplib_directory, 0755, true)) {
                DUP_PRO_Log::error("Error creating extras snaplib directory", "Couldn't create $extras_snaplib_directory", false);
                return false;
            }

            if (!@mkdir($extras_fileops_directory, 0755, true)) {
                DUP_PRO_Log::error("Error creating extras fileops directory", "Couldn't create $extras_fileops_directory", false);
                return false;
            }

            if (!@mkdir($extras_duparchive_directory, 0755, true)) {
                DUP_PRO_Log::error("Error creating extras duparchive directory", "Couldn't create $extras_duparchive_directory", false);
                return false;
            }

            // If the installer has the PHP extension copy the installer files to add all extras in one shot since the server supports creation of PHP files
            if (DUP_PRO_IO::copyDir($installer_dpro_source_directory, $extras_installer_directory) === false) {
                DUP_PRO_Log::error("Error copying installer file directory to extras directory", "Couldn't copy $installer_dpro_source_directory to $extras_installer_directory", false);
                return false;
            }

            if (DUP_PRO_IO::copyDir($snaplib_source_directory, $extras_snaplib_directory) === false) {
                DUP_PRO_Log::error("Error copying installer snaplib directory to extras directory", "Couldn't copy $snaplib_source_directory to $extras_snaplib_directory", false);
                return false;
            }

            if (DUP_PRO_IO::copyDir($fileops_source_directory, $extras_fileops_directory) === false) {
                DUP_PRO_Log::error("Error copying installer fileops directory to extras directory", "Couldn't copy $fileops_source_directory to $extras_fileops_directory", false);
                return false;
            }

            if (DUP_PRO_IO::copyDir($duparchive_source_directory, $extras_duparchive_directory) === false) {
                DUP_PRO_Log::error("Error copying installer duparchive directory to extras directory", "Couldn't copy $duparchive_source_directory to $extras_duparchive_directory", false);
                return false;
            }

            if (DUP_PRO_IO::copyDir($config_source_directory, $extras_config_directory) === false) {
                DUP_PRO_Log::error("Error copying installer config directory to extras directory", "Couldn't copy $config_source_directory to $extras_config_directory", false);
                return false;
            }

            if (DUP_PRO_IO::copyDir($config_files_source_directory, $extras_config_files_directory) === false) {
                DUP_PRO_Log::error("Error copying config directory to extras directory", "Couldn't copy $config_files_source_directory to $extras_config_files_directory", false);
                return false;
            }
        }

        //-- STAGE 1 ADD
        $compression_parameter = DUP_PRO_Shell_U::getCompressionParam($is_compressed);

        $command = 'cd '.escapeshellarg(DupProSnapLibIOU::safePath($extras_directory));
        $command .= ' && '.escapeshellcmd(DUP_PRO_Zip_U::getShellExecZipPath())." $compression_parameter".' -g -rq ';
        $command .= escapeshellarg($zip_filepath).' ./* ./.[^.]*';

        DUP_PRO_LOG::trace("Executing Shell Exec Zip Stage 1 to add extras: $command");

        $stderr = shell_exec($command);

        //-- STAGE 2 ADD - old code until we can figure out how to add the snaplib library within dup-installer/lib/snaplib
        if ($stderr == '') {
            if (!$one_stage_add) {
                // Since we didn't bundle the installer files in the earlier stage we have to zip things up right from the plugin source area
                $command = 'cd '.escapeshellarg($installer_source_directory);
                $command .= ' && '.escapeshellcmd(DUP_PRO_Zip_U::getShellExecZipPath())." $compression_parameter".' -g -rq ';
                $command .= escapeshellarg($zip_filepath).' dup-installer/*';

                DUP_PRO_LOG::trace("Executing Shell Exec Zip Stage 2 to add installer files: $command");
                $stderr = shell_exec($command);

                $command = 'cd '.escapeshellarg(DUPLICATOR_PRO_LIB_PATH);
                $command .= ' && '.escapeshellcmd(DUP_PRO_Zip_U::getShellExecZipPath())." $compression_parameter".' -g -rq ';
                $command .= escapeshellarg($zip_filepath).' snaplib/* fileops/*';

                DUP_PRO_LOG::trace("Executing Shell Exec Zip Stage 2 to add installer files: $command");
                $stderr = shell_exec($command);
            }
        }

        //rsr temp      DUP_PRO_IO::deleteTree($extras_directory);

        if ($stderr == '') {
            if (DUP_PRO_U::getExeFilepath('unzip') != NULL) {
                $installer_backup_filename = basename($installer_backup_filepath);

                $filesToValidate = array(
                    $installer_backup_filename,
                    "dup-installer/dup-scan__{$package_hash}.json",
                    "dup-installer/dup-database__{$package_hash}.sql",
                    "archive__{$package_hash}.txt"
                );

                // Verify the essential extras got in there
                $extra_count_string = "unzip -Z1 '$zip_filepath' | grep '".implode("\|", $filesToValidate)."' | wc -l";
                DUP_PRO_LOG::trace("Executing extra count string $extra_count_string");

                $extra_count = DUP_PRO_Shell_U::runAndGetResponse($extra_count_string, 1);

                if (is_numeric($extra_count)) {
                    // Accounting for the sql and installer back files
                    if ($extra_count == count($filesToValidate)) {
                        // Since there could be files with same name accept when there are m
                        DUP_PRO_LOG::trace("Core extra files confirmed to be in the archive");
                        $success = true;
                    } else {
                        DUP_PRO_Log::error("Tried to verify core extra files but one or more were missing. Count = $extra_count", '', false);
                    }
                } else {
                    DUP_PRO_LOG::trace("Executed extra count string of $extra_count_string");
                    DUP_PRO_Log::error("Error retrieving extra count in shell zip ".$extra_count, '', false);
                }
            } else {
                DUP_PRO_LOG::trace("unzip doesn't exist so not doing the extra file check");
                $success = true;
            }
        }

        if (file_exists($extras_directory)) {
            try {
                if (!DupProSnapLibIOU::rrmdir($extras_directory)) {
                    throw Exception('Failed to delete: '.$extras_directory);
                }
            }
            catch (Exception $ex) {
                DUP_PRO_LOG::trace("Couldn't recursively delete {$extras_directory}");
            }
        }

        return $success;
    }

    // Add installer directory to the archive and the archive.txt
    private function add_installer_files_using_zip_archive(&$zip_archive, $installer_filepath, $archive_config_filepath, $is_compressed)
    {
        $installer_backup_filename = $this->getInstallerBackupName();

        DUP_PRO_LOG::trace('Adding enhanced installer files to archive using ZipArchive');

        if (!DUP_PRO_Zip_U::addFileToZipArchive($zip_archive, $installer_filepath, $installer_backup_filename, $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add file error on file '.$installer_filepath);
        }

        $installer_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/installer/dup-installer';
        if (!DUP_PRO_Zip_U::addDirWithZipArchive($zip_archive, $installer_directory, true, '', $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add folder error on folder '.$installer_directory);
        }

        $archive_config_local_name = $this->getArchiveTxtFilePath();
        if (!DUP_PRO_Zip_U::addFileToZipArchive($zip_archive, $archive_config_filepath, $archive_config_local_name, $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add file error on file '.$archive_config_filepath);
        }

        $snaplib_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/snaplib';
        if (!DUP_PRO_Zip_U::addDirWithZipArchive($zip_archive, $snaplib_directory, true, 'dup-installer/lib/', $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add folder error on folder '.$snaplib_directory);
        }

        // Include dup archive
        $duparchive_lib_directory = DUPLICATOR_PRO_LIB_PATH.'/dup_archive';
        if (!DUP_PRO_Zip_U::addDirWithZipArchive($zip_archive, $duparchive_lib_directory, true, 'dup-installer/lib/', $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add folder error on folder '.$duparchive_lib_directory);
        }

        $fileops_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/fileops';
        if (!DUP_PRO_Zip_U::addDirWithZipArchive($zip_archive, $fileops_directory, true, 'dup-installer/lib/', $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add folder error on folder '.$fileops_directory);
        }

        $config_directory = DUPLICATOR_PRO_PLUGIN_PATH.'/lib/config';
        if (!DUP_PRO_Zip_U::addDirWithZipArchive($zip_archive, $config_directory, true, 'dup-installer/lib/', $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add folder error on folder '.$config_directory);
        }

        $config_files_directory = $this->origFileManger->getMainFolder();
        if (!DUP_PRO_Zip_U::addDirWithZipArchive($zip_archive, $config_files_directory, true, 'dup-installer/', $is_compressed)) {
            throw new Exception('INSTALLER FILES: zip add folder error on folder '.$config_files_directory);
        }

        return true;
    }

    /**
     * Creates the original_files_ folder in the tmp directory where all config files are saved
     * to be later added to the archives
     *
     * @throws Exception
     */
    public function initConfigFiles()
    {
        $this->origFileManger->init();
        $configFilePaths = $this->getConfigFilePaths();
        foreach ($configFilePaths as $identifier => $path) {
            if ($path !== false) {
                $this->origFileManger->addEntry($identifier, $path, DupProSnapLibOrigFileManager::MODE_COPY, self::CONFIG_ORIG_FILE_FOLER_PREFIX.$identifier);
            }
        }

        //Clean sensitive information from wp-config.php file.
        self::cleanTempWPConfArkFilePath($this->origFileManger->getEntryStoredPath('wpconfig'));
    }

    /**
     * Gets config files path
     *
     * @return string[] array of config files in identifier => path format
     */
    public function getConfigFilePaths()
    {
        $home        = DUP_PRO_Archive::getArchiveListPaths('home');
        $configFiles = array(
            'userini'   => $home.'/.user.ini',
            'phpini'    => $home.'/php.ini',
            'webconfig' => $home.'/web.config',
            'htaccess'  => $home.'/.htaccess',
            'wpconfig'  => DUP_PRO_Archive::getWPConfigFilePath()
        );

        foreach ($configFiles as $identifier => $path) {
            if (!file_exists($path)) {
                unset($configFiles[$identifier]);
            }
        }

        return $configFiles;
    }

    private function createManualExtractCheckFile()
    {
        $file_path = $this->getManualExtractFilePath();
        return DupProSnapLibIOU::filePutContents($file_path, '');
    }

    private function getManualExtractFilePath()
    {
        $tmp = DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH_TMP);
        return $tmp.'/dup-manual-extract__'.$this->Package->get_package_hash();
    }

    private function getEmbeddedManualExtractFilePath()
    {
        $embedded_filepath = 'dup-installer/dup-manual-extract__'.$this->Package->get_package_hash();
        return $embedded_filepath;
    }

    private function deleteManualExtractCheckFile()
    {
        DupProSnapLibIOU::rm($this->getManualExtractFilePath());
    }

    /**
     * Clear out sensitive database connection information
     *
     * @param $temp_conf_ark_file_path Temp config file path
     * @throws Exception
     */
    private static function cleanTempWPConfArkFilePath($temp_conf_ark_file_path)
    {
        if (function_exists('token_get_all')) {
            require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/lib/config/class.wp.config.tranformer.php');
            $transformer = new WPConfigTransformer($temp_conf_ark_file_path);
            $constants   = array('DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST');
            foreach ($constants as $constant) {
                if ($transformer->exists('constant', $constant)) {
                    $transformer->update('constant', $constant, '');
                }
            }
        }
    }

    private function getEmbeddedScanFileList()
    {
        $package_hash      = $this->Package->get_package_hash();
        $embedded_filepath = 'dup-installer/dup-scanned-files__'.$package_hash.'.txt';
        return $embedded_filepath;
    }

    private function getEmbeddedScanDirList()
    {
        $package_hash      = $this->Package->get_package_hash();
        $embedded_filepath = 'dup-installer/dup-scanned-dirs__'.$package_hash.'.txt';
        return $embedded_filepath;
    }

    /**
     * Get scan.json file path along with name in archive file
     */
    private function getEmbeddedScanFilePath()
    {
        $package_hash                = $this->Package->get_package_hash();
        $embedded_scan_ark_file_path = 'dup-installer/dup-scan__'.$package_hash.'.json';
        return $embedded_scan_ark_file_path;
    }

    /**
     * Get archive.txt file path along with name in archive file
     */
    private function getArchiveTxtFilePath()
    {
        $package_hash          = $this->Package->get_package_hash();
        $archive_txt_file_path = 'dup-installer/dup-archive__'.$package_hash.'.txt';
        return $archive_txt_file_path;
    }

    /**
     * Get scanned_files.txt file path along with name in archive file
     * 
     * @return string scanned_files.txt file path
     */
    private function getEmbeddedFileListFilePath()
    {
        $package_hash                 = $this->Package->get_package_hash();
        $embedded_file_list_file_path = 'dup-installer/dup-scanned-files__'.$package_hash.'.txt';
        return $embedded_file_list_file_path;
    }

    /**
     * Get scanned_dirs.txt file path along with name in archive file
     * 
     * @return string scanned_dirs.txt file path
     */
    private function getEmbeddedDirListFilePath()
    {
        $package_hash                = $this->Package->get_package_hash();
        $embedded_dir_list_file_path = 'dup-installer/dup-scanned-dirs__'.$package_hash.'.txt';
        return $embedded_dir_list_file_path;
    }
}