<?php
/**
 * Enity layer for the packate template
 *
 * Standard: Missing
 *
 * @package DUP_PRO
 * @subpackage classes/entities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.0.0
 *
 * @todo Finish Docs
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once('class.json.entity.base.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . '/classes/class.crypt.custom.php');

class DUP_PRO_Package_Template_Entity extends DUP_PRO_JSON_Entity_Base
{
    public $name = '';        
    public $notes;

    //MULTISITE:Filter
    public $filter_sites = array();

    //ARCHIVE:Files
	public $archive_export_onlydb = 0;
    public $archive_filter_on = 0;      
    public $archive_filter_dirs = '';   
    public $archive_filter_exts = '';
    public $archive_filter_files = '';  	

    //ARCHIVE:Database
    public $database_filter_on = 0;					// Enable Table Filters
    public $database_filter_tables = '';			// List of filtered tables               
    public $database_compatibility_modes = array();	// Older style sql compatibility

    //INSTALLER
    //Setup
    public $installer_opts_secure_on;		// Enable Password Protection
    public $installer_opts_secure_pass;		// Password Protection password
    public $installer_opts_skip_scan;		// Skip Scanner
    //Basic DB
    public $installer_opts_db_host;			// MySQL Server Host
    public $installer_opts_db_name;			// Database
    public $installer_opts_db_user;			// User
    //cPanel Login
    public $installer_opts_cpnl_enable = false;
    public $installer_opts_cpnl_host = '';
    public $installer_opts_cpnl_user = '';
    public $installer_opts_cpnl_pass = '';
    //cPanel DB
    public $installer_opts_cpnl_db_action = 'create';
    public $installer_opts_cpnl_db_host = '';
    public $installer_opts_cpnl_db_name = '';
    public $installer_opts_cpnl_db_user = '';
    //Brand
    public $installer_opts_brand = -2;
    

    public $is_default = false;
    public $is_manual = false;

    function __construct()
    {
        parent::__construct();

        $this->verifiers['name'] = new DUP_PRO_Required_Verifier('Name must not be blank');     
        $this->name = DUP_PRO_U::__('New Template');
    }

    public static function create_from_data($template_data, $restore_id = false)
    {
        $instance = new DUP_PRO_Package_Template_Entity();

        $instance->name = $template_data->name;
        $instance->notes;

        //MULTISITE
		if(isset($template_data->filter_sites)) {
			$instance->filter_sites = $template_data->filter_sites;
		}

        //ARCHIVE:Files
        $instance->archive_export_onlydb = $template_data->archive_export_onlydb;
		$instance->archive_filter_on = $template_data->archive_filter_on;     
        $instance->archive_filter_dirs = $template_data->archive_filter_dirs;   
        $instance->archive_filter_exts = $template_data->archive_filter_exts;   
        $instance->archive_filter_files = $template_data->archive_filter_files;

        //BRAND
        $instance->installer_opts_brand = $template_data->installer_opts_brand;

        //ARCHIVE:Database
        $instance->database_filter_on = $template_data->database_filter_on;					// Enable Table Filters
        $instance->database_filter_tables = $template_data->database_filter_tables;			// List of filtered tables
        $instance->database_compatibility_modes = $template_data->database_compatibility_modes;	//r style sql compatibility

        //INSTALLER
        //Setup
        $instance->installer_opts_secure_on = $template_data->installer_opts_secure_on;		// Enable Password Protection
        $instance->installer_opts_secure_pass = $template_data->installer_opts_secure_pass;		// Password Protection password
        $instance->installer_opts_skip_scan = $template_data->installer_opts_skip_scan;		// Skip Scanner

        //Basic DB
        $instance->installer_opts_db_host = $template_data->installer_opts_db_host;			// MySQL Server Host
        $instance->installer_opts_db_name = $template_data->installer_opts_db_name;			// Database
        $instance->installer_opts_db_user = $template_data->installer_opts_db_user;			// User

        //cPanel Login
        $instance->installer_opts_cpnl_enable = $template_data->installer_opts_cpnl_enable;
        $instance->installer_opts_cpnl_host = $template_data->installer_opts_cpnl_host;
        $instance->installer_opts_cpnl_user = $template_data->installer_opts_cpnl_user;
        $instance->installer_opts_cpnl_pass = $template_data->installer_opts_cpnl_pass;

        //cPanel DB
        $instance->installer_opts_cpnl_db_action = $template_data->installer_opts_cpnl_db_action;
        $instance->installer_opts_cpnl_db_host = $template_data->installer_opts_cpnl_db_host;
        $instance->installer_opts_cpnl_db_name = $template_data->installer_opts_cpnl_db_name;
        $instance->installer_opts_cpnl_db_user = $template_data->installer_opts_cpnl_db_user;

        $instance->is_default = $template_data->is_default;
        $instance->is_manual = $template_data->is_manual;

        if($restore_id)
        {
            $instance->id = $template_data->id;
        }

        return $instance;
    }

    public static function create_default()
    {
        if(self::get_default_template() == null)
        {
            $template = new DUP_PRO_Package_Template_Entity();

            $template->name = DUP_PRO_U::__('Default');
            $template->notes = DUP_PRO_U::__('The default template.');
            $template->is_default = true;

            $template->save();
            DUP_PRO_LOG::trace('Created default template');
        }
        else
        {
            // Update it
            DUP_PRO_LOG::trace('Default template already exists so not creating');    
        }
    }

    public static function create_manual()
    {
        if(self::get_manual_template() == null)
        {
            $template = new DUP_PRO_Package_Template_Entity();

            $template->name = DUP_PRO_U::__('[Manual Mode]');
            $template->notes = '';
            $template->is_manual = true;

            // Copy over the old temporary template settings into this - required for legacy manual
            $temp_package = DUP_PRO_Package::get_temporary_package(false);

            if($temp_package != null)
            {
                $template->filter_sites = $temp_package->Multisite->FilterSites;
				$template->archive_export_onlydb = $temp_package->Archive->ExportOnlyDB;
                $template->archive_filter_on = $temp_package->Archive->FilterOn;
                $template->archive_filter_dirs = $temp_package->Archive->FilterDirs;
                $template->archive_filter_exts = $temp_package->Archive->FilterExts;
                $template->archive_filter_files = $temp_package->Archive->FilterFiles;

                $template->installer_opts_brand = $temp_package->Brand_ID;

                $template->database_filter_on = $temp_package->Database->FilterOn;
                $template->database_filter_tables = $temp_package->Database->FilterTables;
                $template->database_compatibility_modes = $temp_package->Database->Compatible;

                $template->installer_opts_db_host = $temp_package->Installer->OptsDBHost;
                $template->installer_opts_db_name = $temp_package->Installer->OptsDBName;
                $template->installer_opts_db_user = $temp_package->Installer->OptsDBUser;
                $template->installer_opts_secure_on = $temp_package->Installer->OptsSecureOn;
                $template->installer_opts_secure_pass = $temp_package->Installer->OptsSecurePass;
                $template->installer_opts_skip_scan = $temp_package->Installer->OptsSkipScan;

                /* @var $global DUP_PRO_Global_Entity */
                $global = DUP_PRO_Global_Entity::get_instance();
                if(!($global instanceof  DUP_PRO_Global_Entity)){
                    if(is_admin()){
                        add_action('admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                        add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                    }
                    throw new Exception("Global Entity is null!");
                }
                $global->manual_mode_storage_ids = array();

                foreach($temp_package->get_storages() as $storage)
                {
                    /* @var $storage DUP_PRO_Storage_Entity */
                    array_push($global->manual_mode_storage_ids, $storage->id);
                }

                $global->save();
            }

            $template->save();
            DUP_PRO_LOG::trace('Created manual mode template');
        }
        else
        {
            // Update it
            DUP_PRO_LOG::trace('Manual mode template already exists so not creating');    
        }
    }

    public function set_post_variables($post)
    {
        if (isset($post['_database_filter_tables'])) {
            $this->database_filter_tables = implode(',', $post['_database_filter_tables']);
        } else {
            $this->database_filter_tables = '';
        }

        if (isset($post['_archive_filter_dirs'])) {
            $post_filter_dirs          = DupProSnapLibUtil::sanitize_non_stamp_chars($post['_archive_filter_dirs']);
            $this->archive_filter_dirs = DUP_PRO_Archive::parseDirectoryFilter($post_filter_dirs);
        } else {
            $this->archive_filter_dirs = '';
        }

        if (isset($post['_archive_filter_exts'])) {
            $post_filter_exts          = sanitize_text_field($post['_archive_filter_exts']);
            $this->archive_filter_exts = DUP_PRO_Archive::parseExtensionFilter($post_filter_exts);
        } else {
            $this->archive_filter_exts = '';
        }

        if (isset($post['_archive_filter_files'])) {
            $post_filter_files          = DupProSnapLibUtil::sanitize_non_stamp_chars($post['_archive_filter_files']);
            $this->archive_filter_files = DUP_PRO_Archive::parseFileFilter($post_filter_files);
        } else {
            $this->archive_filter_files = '';
        }
        $this->filter_sites = !empty($post['_mu_exclude']) ? $post['_mu_exclude'] : '';

        //Archive
        $this->set_checkbox_variable($post, '_archive_export_onlydb', 'archive_export_onlydb');
        $this->set_checkbox_variable($post, '_archive_filter_on', 'archive_filter_on');
        $this->set_checkbox_variable($post, '_database_filter_on', 'database_filter_on');

        //Installer
        $this->set_checkbox_variable($post, '_installer_opts_secure_on', 'installer_opts_secure_on');
        $this->set_checkbox_variable($post, '_installer_opts_skip_scan', 'installer_opts_skip_scan');
        $this->set_checkbox_variable($post, '_installer_opts_cpnl_enable', 'installer_opts_cpnl_enable');

        $post_installer_opts_secure_pass  = sanitize_text_field($post['_installer_opts_secure_pass']);
        $this->installer_opts_secure_pass = base64_encode($post_installer_opts_secure_pass);

        // Replaces any \n \r or \n\r from the package notes
        $unwanted_chars = array("\n", "\r", "\n\r");
        $post['notes']  = str_replace($unwanted_chars, '', sanitize_textarea_field($post['notes']));

        parent::set_post_variables($post);
    }

    private function set_checkbox_variable($post, $key, $name)
    {
        if(isset($post[$key]))
        {
            $this->$name = 1;
        }
        else
        {
            $this->$name = 0;
        }
    }  

    public function copy_from_source_id($source_template_id)
    {
        $source_template = self::get_by_id($source_template_id);

        $this->filter_sites = $source_template->filter_sites;

		$this->archive_export_onlydb = $source_template->archive_export_onlydb;
        $this->archive_filter_on = $source_template->archive_filter_on;
		$this->archive_filter_dirs = $source_template->archive_filter_dirs;
        $this->archive_filter_exts = $source_template->archive_filter_exts;
        $this->archive_filter_files = $source_template->archive_filter_files;

        $this->installer_opts_brand = $source_template->installer_opts_brand;
        	

        $this->installer_opts_secure_on = $source_template->installer_opts_secure_on;
        $this->installer_opts_secure_pass = $source_template->installer_opts_secure_pass;
        $this->database_filter_on = $source_template->database_filter_on;
        $this->database_filter_tables = $source_template->database_filter_tables;
        $this->database_compatibility_modes = $source_template->database_compatibility_modes;

        $this->installer_opts_db_host = $source_template->installer_opts_db_host;
        $this->installer_opts_db_name = $source_template->installer_opts_db_name;
        $this->installer_opts_db_user = $source_template->installer_opts_db_user;

        //CPANEL
        $this->installer_opts_cpnl_host = $source_template->installer_opts_cpnl_host;
        $this->installer_opts_cpnl_user = $source_template->installer_opts_cpnl_user;
        $this->installer_opts_cpnl_pass = $source_template->installer_opts_cpnl_pass;
        $this->installer_opts_cpnl_enable = $source_template->installer_opts_cpnl_enable;

        //CPANEL DB
        //1 = Create New, 2 = Connect Remove
        $this->installer_opts_cpnl_db_action = $source_template->installer_opts_cpnl_db_action;
        $this->installer_opts_cpnl_db_host = $source_template->installer_opts_cpnl_db_host;
        $this->installer_opts_cpnl_db_name = $source_template->installer_opts_cpnl_db_name;
        $this->installer_opts_cpnl_db_user = $source_template->installer_opts_cpnl_db_user;

        $source_template_name = $source_template->is_manual ? DUP_PRO_U::__("Active Build Settings") : $source_template->name;
        $this->name = sprintf(DUP_PRO_U::__('%1$s - Copy'), $source_template_name);

        $this->notes = $source_template->notes;
    }
/*
    public function populate_from_post($post)
    {
        $tablelist		= isset($post['dbtables']) ? implode(',', $post['dbtables']) : '';
        $compatlist		= isset($post['dbcompat']) ? implode(',', $post['dbcompat']) : '';

        // Archive
        $this->archive_export_onlydb = isset($post['export-onlydb']) ? 1 : 0;
		$this->archive_filter_on = isset($post['filter-on']) ? 1 : 0;
        if (isset($post['filter-dirs'])) {
            $post_filter_dirs = DupProSnapLibUtil::sanitize_non_stamp_chars($post['filter-dirs']);
            $this->archive_filter_dirs = DUP_PRO_Archive::parseDirectoryFilter($post_filter_dirs);
        } else {
            $this->archive_filter_dirs = '';
        }

        if (isset($post['filter-exts'])) {
            $post_filter_exts = sanitize_text_field($post['filter-exts']);
            $this->archive_filter_exts = DUP_PRO_Archive::parseExtensionFilter($post_filter_exts);
        } else {
            $this->archive_filter_exts = '';
        }

        if (isset($post['filter-files'])) {
            $post_filter_files = DupProSnapLibUtil::sanitize_non_stamp_chars($post['filter-files']);
            $this->archive_filter_files = DUP_PRO_Archive::parseFileFilter($post_filter_files);
        } else {
            $this->archive_filter_files = '';
        }

        // Brand
        $this->installer_opts_brand = isset($post['brand']) ? (is_numeric($post['brand']) && (int)$post['brand'] > 0 ? (int)$post['brand'] : -2 ) : -2;

        // Installer
        $this->installer_opts_db_host = sanitize_text_field($post['dbhost']);
        $this->installer_opts_db_name = sanitize_text_field($post['dbname']);
        $this->installer_opts_db_user = sanitize_text_field($post['dbuser']);
        
        //CPANEL
        $this->installer_opts_cpnl_host = sanitize_text_field($post['installer_opts_cpnl_host']);
        $this->installer_opts_cpnl_user = sanitize_text_field($post['installer_opts_cpnl_user']);
        $this->installer_opts_cpnl_pass = sanitize_text_field($post['installer_opts_cpnl_pass']);
        //$this->installer_opts_cpnl_enable = $source_template->installer_opts_cpnl_enable;

        //CPANEL DB
        //1 = Create New, 2 = Connect Remove
        $this->installer_opts_cpnl_db_action = sanitize_text_field($post['installer_opts_cpnl_db_action']);
        $this->installer_opts_cpnl_db_host = sanitize_text_field($post['installer_opts_cpnl_db_host']);
        $this->installer_opts_cpnl_db_name = sanitize_text_field($post['installer_opts_cpnl_db_name']);
        $this->installer_opts_cpnl_db_user = sanitize_text_field($post['installer_opts_cpnl_db_user']);

        // Database
        $this->database_filter_on = isset($post['dbfilter-on']) ? 1 : 0;
        $this->database_filter_tables = esc_html($tablelist);
        $this->database_compatibility_modes = $compatlist;
    }*/

    public static function compare_templates($a, $b)
    {
        /* @var $a DUP_PRO_Package_Template_Entity */
        /* @var $b DUP_PRO_Package_Template_Entity */

        if($a->is_default)
        {
            return -1;
        }
        else if($b->is_default)
        {
            return 1;
        }
        else
        {
            return strcasecmp($a->name, $b->name);
        }            
    }

    public static function get_all($include_manual_mode = false)
    {           
        $templates =  self::get_by_type(get_class());

        if($include_manual_mode === false)
        {
            $filtered_templates = array();

            foreach($templates as $template)
            {
                /* @var $template DUP_PRO_Package_Template_Entity */
                if($template->is_manual === false)
                {
                    array_push($filtered_templates, $template);
                }
            }
        }
        else
        {
            $filtered_templates = $templates;
        }

        usort($filtered_templates, array('DUP_PRO_Package_Template_Entity', 'compare_templates'));

        return $filtered_templates;
    }    

    public static function delete_by_id($template_id)
    {    
        $schedules = DUP_PRO_Schedule_Entity::get_by_template_id($template_id);

        foreach($schedules as $schedule)
        {                
            /* @var $schedule DUP_PRO_Schedule_Entity */
            $schedule->template_id = self::get_default_template()->id;

            $schedule->save();
        }

        parent::delete_by_id_base($template_id);
    }     

    public static function get_default_template()
    {
        $templates = self::get_all();

        foreach($templates as $template)
        {
            /* @var $template DUP_PRO_Package_Template_Entity */
            if($template->is_default)
            {
                return $template;
            }
        }

        return null;
    }

    public static function get_manual_template()
    {
        $templates = self::get_all(true);

        foreach($templates as $template)
        {
            /* @var $template DUP_PRO_Package_Template_Entity */
            if($template->is_manual)
            {
                return $template;
            }
        }

        return null;
    }

    /**
     * 
     * @param type $id
     * @return DUP_PRO_Package_Template_Entity
     */
    public static function get_by_id($id)
    {
        return self::get_by_id_and_type($id, get_class());
    }
}           
