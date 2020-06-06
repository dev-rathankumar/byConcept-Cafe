<?php
defined("ABSPATH") or die("");

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.screen.base.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.secure.global.entity.php');

/*
Because the default way is overwriting the option names in the hidden input wp_screen_options[option]
I added all inputs via one option name and saved them with the update_user_meta function.
Also, the set-screen-option is not being triggered inside the class, that's why it's here. -TG
*/
add_filter('set-screen-option', 'packages_set_option', 10, 3);
function packages_set_option($status, $option, $value) {
    if('package_screen_options' == $option){
        $user_id = get_current_user_id();
        update_user_meta( $user_id, 'duplicator_pro_opts_per_page', $_POST['duplicator_pro_opts_per_page'] );
        update_user_meta( $user_id, 'duplicator_pro_created_format', $_POST['duplicator_pro_created_format']);
    }
    return false;
}

class DUP_PRO_Package_Screen extends DUP_PRO_UI_Screen
{
	
	public function __construct($page)
    {
       add_action('load-'.$page, array($this, 'Init'));
       add_filter('screen_settings', array($this,'show_created_format'), 10, 2 );
    }

	public function Init() 
	{
		$active_tab = isset($_GET['inner_page']) ? $_GET['inner_page'] : 'list';
		$active_tab = isset($_GET['action']) && $_GET['action'] == 'detail' ? 'detail' : $active_tab;
		$this->screen = get_current_screen();
		
		switch (strtoupper($active_tab)) {
			case 'LIST':	$content = $this->get_list_help();		break;	
			case 'NEW1':	$content = $this->get_step1_help();		break;	
			case 'NEW2':	$content = $this->get_step2_help(); 	break;	
			case 'DETAIL':	$content = $this->get_details_help(); 	break;	
			default:
				$content = $this->get_list_help(); 
				break;
		}
		
		$guide = '#guide-packs';
		$faq   = '#faq-package';
		$content .= "<b>References:</b><br/>"
					. "<a href='https://snapcreek.com/duplicator/docs/guide/{$guide}' target='_sc-guide'>User Guide</a> | "
					. "<a href='https://snapcreek.com/duplicator/docs/faqs-tech/{$faq}' target='_sc-guide'>FAQs</a> | "
					. "<a href='https://snapcreek.com/duplicator/docs/quick-start/' target='_sc-guide'>Quick Start</a>";
		
		$this->screen->add_help_tab( array(
				'id'        => 'dpro_help_package_overview',
				'title'     => DUP_PRO_U::__('Overview'),
				'content'   => "<p>{$content}</p>"
			)
		);
		
		$this->getSupportTab($guide, $faq);
		$this->getHelpSidbar();
	}	
	
	public function get_list_help() 
	{
		return  DUP_PRO_U::__("<b><i class='fa fa-archive'></i> Packages » All</b><br/> The 'Packages' section is the main interface for managing all the packages that have been created.  A Package consists "
				. "of two core files. The first is the 'installer.php' file and the second is the 'archive.zip/daf' file.  The installer file is a php file that when browsed to via "
				. "a web browser presents a wizard that redeploys or installs the website by extracting the archive file.  The archive file is a zip/daf file containing "
				. "all your WordPress files and a copy of your WordPress database. To create a package, click the 'Create New' button and follow the prompts. <br/><br/>"

                . "<b><i class='fa fa-download'></i> Downloads</b><br/>" 
			    . "To download the package files click on the Download button.  Choosing the 'Both Files' option will popup two separate save dialogs.
					On some browsers you may have to enable popups on this site.  In order to download just the 'Installer' or 'Archive' click on that menu item. <i>Note:
					the archive file will have a copy of the installer inside of it named installer-backup.php</i><br/><br/>"

				. "<b><i class='fa fa-bars'></i> More Items</b><br/>"
				. " To see the details, transfer or view remote store locations of a package click the 'More Items' menu button.  If a package contains remote storage endpoints a
					blue dot will show as &nbsp; <i class='fa fa-bars remote-data-pass'></i> &nbsp; on the more items menu button.  If a red icon shows 
					<i class='fa fa-bars remote-data-fail'></i> &nbsp; then one or more of the storage locations failed during the transfer phase.<br/><br/>"

				. "<b><i class='far fa-file-archive fa-sm'></i> Archive Types</b><br/>"
				. "An archive file can be saved as either a .zip file or .daf file.  A zip file is a common archive format used to compress and group files.  The daf file short for "
				. "'Duplicator Archive Format' is a custom format used specifically  for working with larger packages and scale-ability issues on many shared hosting platforms.  Both "
				. "formats work very similar the main difference is that the daf file can only be extracted using the installer.php file or the "
				. "<a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-052-q' target='_blank'>DAF extraction tool</a>.  The zip file can be used by other zip "
				. "tools like winrar/7zip/winzip or other client-side tools. <br/><br/>"

				. "<b><i class='fa fa-bolt'></i> How to Install a Package</b><br/> "
			    . "Installing a package is pretty straight forward, however it does require a quick primer if you have never done it before.  To get going with a step by step "
			    . "guide and quick video check out the <a href='https://snapcreek.com/duplicator/docs/quick-start/' target='_blank'>quick start guide.</a> <br/><br/>"
			);
	}			
	

	public function get_step1_help() 
	{
		return DUP_PRO_U::__("<b>Packages New » 1 Setup</b> <br/>"
				. "The setup screen allows users to choose where they would like to store thier package, such as Google Drive, Dropbox, on the local server or a combination of both."
				. "Setup also allow users to setup optional filtered directory paths, files and database tables to change what is included in the archive file.  The optional option "
				. "to also have the installer pre-filled can be used.  To expedited the workflow consider using a Template. <br/><br/>");
	}		
	
	
	public function get_step2_help() 
	{
		return DUP_PRO_U::__("<b>Packages » 2 Scan</b> <br/>"
				. "The plugin will scan your system, files and database to let you know if there are any concerns or issues that may be present.  All items in green mean the checks "
				. "looked good.  All items in red indicate a warning.  Warnings will not prevent the build from running, however if you do run into issues with the build then checking "
				. "the warnings should be considered. <br/><br/>");
	}
	
	public function get_details_help() 
	{
		return DUP_PRO_U::__("<b>Packages » Details</b> <br/>"
				. "The details view will give you a full break-down of the package including any errors that may have occured during the install. <br/><br/>");
	}


	/**
	*  Packages List: Screen Options Tab
	*/

    public function show_created_format($status, $args)
    {
        $return = $status;

        if (isset($_GET['page']) && $_GET['page'] == "duplicator-pro") {
            if (isset($_GET['tab']) && $_GET['tab'] == "packages") {
                if (count($_GET) > 2) return $status;
            }
            else if (isset($_GET['tab']) && $_GET['tab'] != "packages")
                    return $status;
        }
        
        //Check Screen
        if ( $args->base == 'toplevel_page_duplicator-pro' ) {

            //Setting current values of fields to display in controls
            $global = DUP_PRO_Global_Entity::get_instance();
            $user_id = get_current_user_id();
            $created_format_key = 'duplicator_pro_created_format';
            $pk_per_page_key = 'duplicator_pro_opts_per_page';

            //Inheriting the value of the old created format option to the screen option
            if(!is_numeric(get_user_meta($user_id,$created_format_key,true))){
                $cf_default_val = is_numeric($global->package_ui_created) ? $global->package_ui_created : 1;
                update_user_meta($user_id,$created_format_key,$cf_default_val);
            }
            $current_created_format =  get_user_meta($user_id,$created_format_key,true);
            $current_per_page = get_user_meta($user_id,$pk_per_page_key,true) != NULL ? get_user_meta($user_id,$pk_per_page_key,true) : 10;

            $button = get_submit_button(DUP_PRO_U::__('Apply'),'primary','screen-options-apply',false);
            $return .= '
            <fieldset class="screen-options" style="float:left;">
		    <legend>Pagination</legend>
				<label for="'.$pk_per_page_key.'">'.DUP_PRO_U::__("Packages Per Page").'</label>
				<input type="number" step="1" min="1" max="999" class="screen-per-page" name="'.$pk_per_page_key.'" id="'.$pk_per_page_key.'" maxlength="3" value="'.$current_per_page.'">
		    </fieldset>
            <fieldset class="screen-options">
            <legend>'.DUP_PRO_U::__("Created Format").'</legend>
            <div class="metabox-prefs">
            <div><input type="hidden" name="wp_screen_options[option]" value="package_screen_options" /></div>
            <div><input type="hidden" name="wp_screen_options[value]" value="val" /></div>
            <div class="created-format-wrapper">
                <select name="'.$created_format_key.'" >
				<!-- YEAR -->
				<optgroup label="'.DUP_PRO_U::__("By Year").'">
					<option value="1" '.selected($current_created_format,1,false).'>Y-m-d H:i &nbsp;	[2000-01-05 12:00]</option>
					<option value="2" '.selected($current_created_format,2,false).'>Y-m-d H:i:s		[2000-01-05 12:00:01]</option>
					<option value="3" '.selected($current_created_format,3,false).'>y-m-d H:i &nbsp;	[00-01-05   12:00]</option>
					<option value="4" '.selected($current_created_format,4,false).'>y-m-d H:i:s		[00-01-05   12:00:01]</option>
				</optgroup>
				<!-- MONTH -->
				<optgroup label="'.DUP_PRO_U::__("By Month").'">
					<option value="5" '.selected($current_created_format,5,false).'>m-d-Y H:i  &nbsp; [01-05-2000 12:00]</option>
					<option value="6" '.selected($current_created_format,6,false).'>m-d-Y H:i:s		[01-05-2000 12:00:01]</option>
					<option value="7" '.selected($current_created_format,7,false).'>m-d-y H:i  &nbsp; [01-05-00   12:00]</option>
					<option value="8" '.selected($current_created_format,8,false).'>m-d-y H:i:s		[01-05-00   12:00:01]</option>
				</optgroup>
				<!-- DAY -->
				<optgroup label="'. DUP_PRO_U::__("By Day") .'">
					<option value="9" '.selected($current_created_format,9,false).'> d-m-Y H:i &nbsp;	[05-01-2000 12:00]</option>
					<option value="10" '.selected($current_created_format,10,false).'>d-m-Y H:i:s		[05-01-2000 12:00:01]</option>
					<option value="11" '.selected($current_created_format,11,false).'>d-m-y H:i &nbsp;	[05-01-00	12:00]</option>
					<option value="12" '.selected($current_created_format,12,false).'>d-m-y H:i:s		[05-01-00	12:00:01]</option>
				</optgroup>
			</select>
            </div>
            </div>
            </fieldset>
            <br class="clear">'.$button;
        }
        return $return;
    }
}


