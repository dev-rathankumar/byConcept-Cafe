<?php

/**
 *
 * @package ESIG_ACCESS_CONTROL_Admin
 * @author  Abu Shoaib 
 */
class ESIG_ACCESS_CONTROL_Admin extends Access_Control_Setting {

    /**
     * Instance of this class.
     * @since    0.1
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     * @since    0.1
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     * @since     0.1
     */
    public static function Init() {

        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
        add_action('esig_display_right_sidebar', array(__CLASS__, 'esig_access_control_sidebar'), 10, 1);
       // add_action('esig_display_right_sidebar', array(__CLASS__, 'esig_access_control_sidebar'), 10, 1);
        //add_action('esig_document_before_save', array($this, 'esig_add_image_featured_sidebar'), 10, 1);
        //add_action('esig_document_before_edit_save', array($this, 'esig_add_image_featured_sidebar'), 10, 1);
        add_action('esig_document_after_save', array(__CLASS__, 'esig_access_control_document_after_save'), 10, 1);

        // adding sad legal sad fname ;
        add_action('esig_template_basic_document_create', array(__CLASS__, 'template_basic_doc_create'), 10, 1);

        add_action('esig_document_after_delete', array(__CLASS__, 'document_after_delete'), 10, 1);
    }

    public static function document_after_delete($args) {

        $docId = esigget("document_id", $args);
        if (current_user_can('delete_posts')) {
            $tableName = Esign_Query::dbconnect()->prefix . "usermeta";
            Esign_Query::dbconnect()->delete($tableName, array("meta_key" => "esig-stand-alone-" . $docId), array("%s"));
        }
    }

    public static function template_basic_doc_create($args) {

        $document_id = $args['document_id'];
        $template_id = $args['template_id'];
        // $doc_type = $args['document_type'];

        if (self::is_access_control_enabled($template_id)) {
            self::save_access_meta($document_id, self::get_access_meta($template_id));
        } else {
            return;
        }
    }

    public static function enqueue_admin_scripts() {
        $screen = get_current_screen();
        $admin_screens = array(
            'admin_page_esign-add-document',
            'admin_page_esign-edit-document',
            'e-signature_page_esign-view-document'
        );
        if (in_array($screen->id, $admin_screens)) {
            wp_enqueue_script('esig-access-control-admin-script', ESIGN_AC_URL . '/assets/js/esig-access-control.js', false, ESIGN_AC_VERSION, true
            );
        }
    }

    public static function esig_access_control_document_after_save($args) {

        $document_id = $args['document']->document_id;
        //getting value from post  
        $esig_required_wpmember = ESIG_POST('esig_required_wpmember');
        
       
        
        $esig_access_control_role = ESIG_POST('esig_access_control_role', true);
        $esig_document_permission = ESIG_POST('esig_document_permission');
        $esig_document_description = ESIG_POST('esig_document_description');
        $esig_users_permission = ESIG_POST('esig_access_roles_option', true);

        // recieving user roles option in array 
        self::save_access_users_permission();


        if (!$esig_document_description) {
            $esig_document_description = __('Welcome to our site, so we can better serve you please sign this agreement.', 'esig');
        }
        $esig_image_thumbnail_src = ESIG_POST('esig_image_thumbnail_src');

        $access_control = array(
            'esig_required_wpmember' => $esig_required_wpmember,
            'esig_access_control_role' => $esig_access_control_role,
            'esig_document_permission' => $esig_document_permission,
            'esig_document_description' => $esig_document_description,
            'esig_users_permission' => $esig_users_permission,
            'esig_image_thumbnail_src' => $esig_image_thumbnail_src,
        );

        //save document with meta
        WP_E_Sig()->meta->add($document_id, 'esig_wpaccess_control', json_encode($access_control));
    }

    public static function esig_access_control_sidebar() {

        // $api = new WP_E_Api();

        $content = '';

        $file_name = ESIGN_ASSETS_DIR_URI . '/images/help.png';

        $title = ' <a href="#" class="tooltip">
                                                    <img src="' . $file_name . '" height="20px" align="left" />
                                          <span>
                                              ' . __('The Document Portal feature lets you assign Stand Alone Documents to a specific Wordpress user role (like: editor, subscriber, etc). When you insert the shortcode [esig-doc-dashboard status="required"] on any WordPress page your users will see their required docs.', 'esig') . '
                                          </span>
                                    </a> ' . __('Document Access Control', 'esig');

        $document_id = ESIG_GET('document_id');
        $access_control = null;
        if ($document_id) {
            $access_control = json_decode(WP_E_Sig()->meta->get($document_id, 'esig_wpaccess_control'));
        }
        $esig_required_wpmember_checked = (isset($access_control) && $access_control->esig_required_wpmember) ? "checked" : "";
        $sub_array = (isset($access_control) && $access_control->esig_access_control_role) ? $access_control->esig_access_control_role : array();
        $display = ($esig_required_wpmember_checked == "checked") ? "block" : "none";
        $users_role_option = '';
        $wp_users = get_users();


        foreach ($wp_users as $key => $user) {
            $users_role_option .= '<option value="' . $user->ID . '" ';
            $esig_users_permission_selected = (isset($access_control) && $access_control->esig_users_permission) ? "selected" : "";
            if (in_array($user->ID, self::get_access_users_permission()))
                $users_role_option .= $esig_users_permission_selected;
            $users_role_option .='> ' . $user->display_name . '</option>';
        }

        //$content .='';

        $content .= ' <div style="width:250px;">
                                        <div ><input type="checkbox" id="esig_required_wpmember" name="esig_required_wpmember" value="1" ' . $esig_required_wpmember_checked . '>Required a Specific Wordpress member (or) user role to sign this document.</div>
                                      
                             <div id="esig_wpaccess_control_role" name="esig_wpaccess_control_role" style="display:' . $display . ';" > <hr>
                                        
                                     <div id="esig-valid-message" style="display:none;"> ' . __("Oops! It looks like you haven't yet selected your user role. Please do it now, and try saving again.", "esig") . ' </div>   
                                     
                                            <div class="container-fluid esig-chosen-drop">  
                                                   <div class="row">
                                                   <div class="col-md-2 noPadding">
                                                    <a href="#" class="tooltip">
                                                    <img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png' . '" height="10px" align="left" style="background: rgb(255, 255, 255);width:20px;height: 20px;"/>
                                          <span>
                                              ' . __('Select one (or multiple) USERS that will need to sign this document.', 'esig') . '
                                          </span>
                                            </a> 
                                            </div>
                                            <div class="col-md-10 noPadding">
					    <label>' . __('Select one (or multiple) USERS that will need to sign this document.', 'esig') . '</label>
                                                </div>
                                                </div>
                                                <div class="row">
                                                  <div class="col-md-12">
                                            <select style="width:230px;" id="esig_access_roles_option" name="esig_access_roles_option[]" multiple class="esig-select2">
                                            ' . $users_role_option . '
                                            </select>
                                                    </div>
                                                </div>
                                            </div>';
        $content .= ' <div style="margin-left:46px;" > ';

        foreach (get_editable_roles() as $role => $role_name) {
            $checked = (in_array($role, $sub_array)) ? "checked" : "";

            $content .= '<input id="esig_access_control_role" type="checkbox" name="esig_access_control_role[]" ' . $checked . ' value="' . $role . '" > ' . $role . '<br>';
        }
        $permission_array = array("required" => __("This Document is required", "esig"), "optional" => __("This Document is Optional", "esig"));
        $content .= ' </div>
                                                <br>  
                                              <select name="esig_document_permission" >
                                                     ';
        foreach ($permission_array as $key => $value) {
            if (isset($access_control) && $access_control->esig_document_permission) {
                $selected = ($access_control->esig_document_permission == $key) ? "selected" : "";
            } else {
                $selected = "";
            }

            $content .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }


        $content .= '</select><br><hr>';

        $content .= '<h6><span>' . __("Document Description", "esig") . '</span></h6>';
        if ($document_id) {
            $access_control = json_decode(WP_E_Sig()->meta->get($document_id, 'esig_wpaccess_control'));
        }

        $noimage = ESIGN_ASSETS_DIR_URI . '/images/noimage.jpg';

        $esig_image_thumbnail_src = (isset($access_control) && isset($access_control->esig_image_thumbnail_src)) ? $access_control->esig_image_thumbnail_src : "$noimage";

        $esig_document_description = (isset($access_control) && $access_control->esig_document_description) ? $access_control->esig_document_description : "";


        $count = (!empty($esig_document_description)) ? 75 - strlen($esig_document_description) : 75;

        $content .='<textarea  id="esig_document_description" name="esig_document_description" rows="4" cols="28" style="width:100%" maxlength="75" placeholder="Welcome to our site, so we can better serve you please sign this agreement.">' . $esig_document_description . '</textarea></br>'
                . '<div id="esig-char-limit">' . sprintf(__('The document description will be limited to 75 chars, <span id="esig-char-count"> %d </span> chars left', 'esig'), $count) . '</div>';
        $content.='<div id="esig-featured-image-container" class="hidden">
                                                                   <img src="' . $esig_image_thumbnail_src . '">
                                                                    </div><!-- #esig-featured-image-container -->


                                                                    <p class="hide-if-no-js" >
                                                                       <a title="Set Footer Image" href="javascript:;" id="esig-set-image-thumbnail">' . __('Set featured image', 'esign') . '</a>
                                                                    </p>

                                                                    <p class="hide-if-no-js">
                                                                            <a title="Remove Footer Image" href="javascript:;" id="esig-remove-image-thumbnail">' . __('Remove featured image', 'esign') . '</a><br><a href=" https://www.approveme.com/wordpress-document-portal/" class="button-secondary" target="_blank">Learn about this feature</a>
                                                                    </p><!-- .hide-if-no-js -->


                                                                    <p id="esig-featured-image-info">
                                                                            <input type="hidden" id="esig-image-thumbnail-src" name="esig_image_thumbnail_src" value="' . $esig_image_thumbnail_src . '">
                                                                    </p>';

        $content.= '</div></div>';

        WP_E_View::instance()->setSidebar($title, $content, "acesscontrol", "acesscontrolbody");
        echo WP_E_View::instance()->renderSidebar();
    }

    public static function get_access_users_permission() {

        $esig_access_users_option = json_decode(wp_e_sig()->setting->get_generic('esig_access_users_option'));
        if ($esig_access_users_option == null) {
            $esig_access_users_option = array();
        }

        return $esig_access_users_option;
    }

    public static function save_access_users_permission() {
        $esig_access_roles_option = ESIG_POST('esig_access_roles_option', true);
        if (!$esig_access_roles_option) {
            return false;
        }
        foreach ($esig_access_roles_option as $key => $value) {
            $esig_roles_user_option[$key] = $value;
        }

        return wp_e_sig()->setting->set('esig_access_users_option', json_encode($esig_roles_user_option));
    }

    /**
     * Return an instance of this class.
     * @since     0.1
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

// If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
