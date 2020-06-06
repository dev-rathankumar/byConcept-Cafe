<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>



<?php
$this->setting = new WP_E_Setting();
if (array_key_exists('messages', $data)) {
    echo $data['messages'];
}
?>

<?php
$esig_update = isset($_GET['esig-update']) ? $_GET['esig-update'] : null;

if ($esig_update == "success") {
    ?>

    <div class="alert alert e-sign-alert esig-updated"><div class="title"></div><p class="message"><?php _e('Hey there, congrats!  It looks like your recent E-Signature add-on update has been successful.', 'esig'); ?></p></div>

    <?php
}

$esig_permission = '';

if (!current_user_can('install_plugins')) {
    ?>

    <div class="alert alert e-sign-alert e-sign-red-alert" style="padding: 5px;"><p class="message"><?php _e('You do not have sufficient permission to install/activate and deactivate plugins.', 'esig'); ?> </p></div> 
    <?php
    $esig_permission = "onclick=\"javascript: return false ;\"";
}


if (current_user_can('activate_plugins')) {
    
    if(!empty($esig_permission)){
         $esig_permission = "";
    }
   
}
?>    

<h3><?php _e('Premium Add-on Extensions', 'esig'); ?></h3>

<p class="esig-add-on-description"><?php _e('Add-ons are customizable features that you can add or remove depending on your specific needs. Signing documents should only be as automated/customizable as you need it to be. Visit the Get More tab to see what else ApproveMe can do for you.', 'esig'); ?></p>

<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

$documentation_page = '';
$settings_page = '';


//

$this->model->esig_addons_tabs($tab);
echo '<div class="esig-add-ons-wrapper">';
// tab content start here 
if ($tab == 'all') {

    if (Esign_licenses::is_license_active()) {
        $license_key = 'yes';
        $all_addons_list = $this->model->esig_get_premium_addons_list();
    } else {
        $license_key = 'no';
        $all_addons_list = $this->model->esig_get_addons_list();
    }

    if ($all_addons_list) {

        $total = 0;

        $all_addon_install = true;

        $all_install = array();

        if ($license_key == "no") {
            ?>

            <div class="esig-add-on-block esig-pro-pack open">

                <h3><?php _e("A Valid E-Signature License is Required", "esig"); ?></h3>
                <p style="display:block;"><?php _e("A valid WP E-Signature license is required for access to critical updates and support. Without a license you are putting you and your signers at risk. To protect yourself and your documents please update your license.", "esig"); ?></p><a href="https://www.approveme.com/email-limited-pricing/" class="esig-btn-pro" target="_blank"><?php _e("Purchase a license", "esig"); ?> </a>
            </div>

            <?php
        }



        $business_pack_option = $this->model->one_click_installation_option($all_addons_list);


        if (empty($business_pack_option)) {
            WP_E_Sig()->common->force_to_check_update();
            WP_E_Sig()->common->esign_check_update();
            $business_pack_option = $this->model->one_click_installation_option($all_addons_list);
        }

        if ($business_pack_option && !Esig_Addons::is_updates_available()) {
            ?>

            <div class="esig-add-on-block esig-pro-pack open" id="esig-all_install">
                <?php _e(' <h3>Save Time...Install everything with one click</h3>
					                    <p style="display:block;">Since you have access to the Buisness Pack you can save time by installing 
                                        all add-ons at once . 
                                        Please Note: The installation process can take few minutes to complete.</p>', 'esig'); ?>
                <a class="esig-btn-pro" id="esig-install-alladdons" href="<?php echo $business_pack_option ?>"><?php _e('Install all Add-ons Now', 'esig'); ?></a>
            </div>

            <?php
        } elseif ($business_pack_option && Esig_Addons::is_updates_available()) {
            ?>

            <div class="esig-add-on-block esig-pro-pack open" id="esig-all_install">
                <?php _e(' <h3>Save Time...Update everything with one click</h3>
					                    <p style="display:block;">Since you have access to the Buisness Pack you can save time by updating 
                                        all add-ons at once . 
                                        Please Note: The Update process can take few minutes to complete.</p>', 'esig'); ?>
                <a class="esig-btn-pro" id="esig-install-alladdons" href="<?php echo $business_pack_option ?>"><?php _e('Update all Add-ons Now', 'esig'); ?></a>
            </div>
            <?php
        }

        $all_addons = Esig_Addons::esig_object_sort($all_addons_list);

        foreach ($all_addons as $addonlist => $addons) {

            $update_available = '';


            if (WP_E_Addon::is_business_pack_list($addons)) {
                continue;
            }



            if ($addonlist == "esig-price") {

                if (($esig_license_type = $this->setting->get_generic('esig_wp_esignature_license_type') ) != 'Business License') {

                    $buisness_price = is_array($addons) ? $addons[0]->amount : null;
                    $professional_price = is_array($addons) ? $addons[1]->amount : null;
                    $individual_price = is_array($addons) ? $addons[2]->amount : null;


                    if (Esign_licenses::get_license_type() == 'Professional License' && $license_key != 'no') {
                        $price = $buisness_price - $professional_price;
                    } elseif (Esign_licenses::get_license_type() == 'individual-license' && $license_key != 'no') {
                        $price = $buisness_price - $individual_price;
                    } else {
                        $price = $buisness_price;
                    }

                    if (Esign_licenses::get_license_type() != 'business-license'):
                        ?>


                        <div class="esig-add-on-block esig-pro-pack open">
                            <?php echo sprintf(__('<h3>Get the E-Signature Business Pack<span><a href="#">Learn More</a></span></h3>

					        <p>The Business Pack gets you access to WP E-Signature add-ons that unlock so much more functionality and features that WP E-Signature can do... like Dropbox Sync, Signing Reminders, Save as PDF, Stand Alone Documents, URL Redirect After Signing, Custom Fields and more. With the Business Pack, you get access to all our ApproveMe built WP E-Signature Add-ons plus any more we build in the next year (which will be a ton).</p>
					        <a class="esig-btn-pro" href="http://www.approveme.com/e-signature-upgrade-license/" target="_blank">Get all our add-ons for $%s </php></php></a><a href="#" class="esig-dismiss">No thanks</a>', $price, 'esig'), $price); ?>

                        </div>


                        <?php
                    endif;
                }
            } elseif ($addons->addon_name != 'WP E-Signature') {


                $plugin_root_folder = trim($addons->download_name, ".zip");



                $plugin_file = $this->model->esig_get_addons_file_path($plugin_root_folder);


                $esig_update_link = '';
                if ($license_key == 'no') {

                    $price = isset($price) ? $price : 197;
                    $esig_action_link = '<div class="esig-add-on-actions"><div class="esig-add-on-buy-now"><a href="https://www.approveme.com/wp-digital-e-signature#pricingPlans" target="_blank" class="eisg-addons-upgrade">' . __('Upgrade Now', 'esig') . '</a></div></div>';
                } elseif ($plugin_file) {

                    $plugin_data = Esig_Addons::get_addon_data(Esig_Addons::get_installed_directory($plugin_file) . $plugin_file);

                    $plugin_name = $plugin_data['Name'];
                    $update_available = '';
                    $settings_page = '';
                    $documentation_page = '<span class="esig-add-on-author"><a href="' . $addons->download_page_link . '" target="_blank">' . __('Documentation', 'esig') . '</a></span>';
                    if (!empty($plugin_data['Documentation'])) {
                        $documentation_page = '<span class="esig-add-on-author"><a href="' . $plugin_data['Documentation'] . '" target="_blank">' . __('Documentation', 'esig') . '</a></span>';
                    }
                    if (Esig_Addons::is_enabled($plugin_file)) {
                        $esig_name = preg_replace('/[^a-zA-Z0-9_\s]/', '', str_replace(' ', '_', "WP E-Signature - " . $addons->addon_name));



                        // settings page .

                        if (is_callable('esig_addon_setting_page_' . str_replace('-', '_', $plugin_root_folder))) {
                            $settings_page = call_user_func('esig_addon_setting_page_' . str_replace('-', '_', $plugin_root_folder), $settings_page);
                        }

                        $esig_action_link = '<div class="esig-add-on-enabled"><a data-text-disable="Disable" data-text-enabled="Enabled" href="?page=esign-addons&tab=enable&esig_action=disable&plugin_url=' . urlencode($plugin_file) . '&plugin_name=' . $plugin_name . '" ' . $esig_permission . '>' . __('Enabled', 'esig') . '</a></div>';
                    } elseif (!Esig_Addons::is_enabled($plugin_file)) {
                        $esig_action_link = '<div class="esig-add-on-disabled"><a data-text-enable="Enable" data-text-disabled="Disabled" href="?page=esign-addons&tab=disable&esig_action=enable&plugin_url=' . urlencode($plugin_file) . '&plugin_name=' . $plugin_name . '" ' . $esig_permission . '>' . __('Disabled', 'esig') . '</a></div>';
                    }

                    if (version_compare($plugin_data['Version'], $addons->new_version, '<')) {
                        $update_available = __('Update Available', 'esig');
                        $esig_action_link = '<div class="esig-add-on-disabled"><a  href="' . $business_pack_option . '" ' . $esig_permission . ' class="eisg-addons-update">' . __('Update Now', 'esig') . '</a></div>';
                    }
                } else {
                    if ($addons->download_access == 'yes') {
                        // set all addon transients 

                        $all_addon_install = false;

                        $all_install[$addons->download_name] = $addons->download_link;

                        $esig_action_link = '<div class="esig-add-on-disabled"><a  href="?page=esign-addons&esig_action=install&download_url=' . WP_E_Addon::base64_url_encode($addons->download_link) . '&download_name=' . $addons->download_name . '" ' . $esig_permission . ' class="eisg-addons-install">' . __('Install Now', 'esig') . '</a></div>';
                        // $esig_action_link = '<div class="esig-add-on-disabled">'. WP_E_Addon::get_install_link($addons->download_link, $addons->download_name, $esig_permission) .'</div>';
                    } else {
                        $esig_action_link = '<div class="esig-add-on-actions"><div class="esig-add-on-buy-now"><a href="https://www.approveme.com/wp-digital-e-signature#pricingPlans" target="_blank" class="eisg-addons-upgrade">' . __('Upgrade Now', 'esig') . '</a></div></div>';
                    }
                }

                $total++;
                ?>


                <div class="esig-add-on-block">


                    <div class="esig-add-on-icon">
                        <div class="esig-image-wrapper">
                            <img src="<?php echo $addons->addon_image[0]; ?>" width="50px" height="50px" alt="">
                        </div>
                    </div>

                    <div class="esig-add-on-info">
                        <h4><a href="<?php echo $addons->download_page_link; ?>" target="_blank"><?php echo "WP E-Signature - " . $addons->addon_name; ?></a></h4>
                        <span class="esig-add-on-author"> <?php _e('by', 'esig'); ?> <a href="https://www.approveme.com/" target="_blank"><?php _e('Approveme', 'esig'); ?></a></span>
                        <?php echo $documentation_page; ?>



                        <p class="esig-add-on-description"><?php echo $addons->addon_description; ?></p>
                    </div>

                    <div class="esig-add-on-actions">

                        <?php echo $esig_action_link; ?>
                        <?php echo $settings_page; ?>

                    </div>
                </div>


                <?php
            }
        } //foreach end here 
        // setting transient for all addons array . 
        set_transient('esig-all-addons-install', json_encode($all_install), 12 * HOUR_IN_SECONDS);


        if ($total == 0) {

            echo '<div> ' . _e('You have already installed all addons.', 'esig') . '</div>';
        }
    }
}
// all tab end here 
// enable tab start here 
if ($tab == "enable") {

    //$array_Plugins = get_plugins();
    $array_Plugins = Esig_Addons::get_all_addons();

    asort($array_Plugins);

    $total = 0;
    if (!empty($array_Plugins)) {

        foreach ($array_Plugins as $plugin_file => $plugin_data) {

            if (Esig_Addons::is_enabled($plugin_file)) {

                $plugin_name = $plugin_data['Name'];

                if (preg_match("/esig/", $plugin_file)) {

                    if ($plugin_name != "WP E-Signature") {
                        $total++;

                        list($folder_name, $file_name) = explode('/', $plugin_file);

                        // $plugin_name= trim($plugin_name, "WP E-Signature");
                        $esig_name = preg_replace('/[^a-zA-Z0-9_\s]/', '', str_replace(' ', '_', $plugin_name));

                        $plugin_data = Esig_Addons::get_addon_data(Esig_Addons::get_installed_directory($plugin_file) . $plugin_file);
                        if (!empty($plugin_data['Documentation'])) {
                            $documentation_page = '<span class="esig-add-on-author"><a href="' . $plugin_data['Documentation'] . '" target="_blank">' . __('Documentation', 'esig') . '</a></span>';
                        }


                        // settings page .
                        $settings_page = '';
                        if (is_callable('esig_addon_setting_page_' . str_replace('-', '_', $folder_name))) {
                            $settings_page = call_user_func('esig_addon_setting_page_' . str_replace('-', '_', $folder_name), $settings_page);
                        }


                        /* if (get_option($esig_name . "_setting_page")) {
                          $settings_page = '<div class="esig-add-on-settings"><a href="' . get_option($esig_name . "_setting_page") . '"></a></div>';
                          } else {
                          $settings_page = '';
                          } */
                        ?>
                        <div class="esig-add-on-block">

                            <div class="esig-add-on-icon">
                                <div class="esig-image-wrapper">
                                    <img src="<?php echo ESIGN_ASSETS_DIR_URI . '/images/add-ons/' . $folder_name . '.png'; ?>" width="50px" height="50px" alt="">
                                </div>
                            </div>

                            <div class="esig-add-on-info">
                                <h4><?php echo $plugin_name; ?></h4>
                                <span class="esig-add-on-author"> <?php _e('by', 'esig'); ?> <a href="http://approveme.com"><?php _e('Approveme', 'esig'); ?></a></span>
                                <?php echo $documentation_page; ?>

                                <p class="esig-add-on-description"><?php echo $plugin_data['Description']; ?></p>
                            </div>


                            <div class="esig-add-on-actions">
                                <?php if (Esig_Addons::isAlwaysEnabled($plugin_file)) { ?>
                                    <div class="esig-add-on-enabled-fixed"><?php echo '<a href="#" ' . $esig_permission . ' class="eisg-addons-disable-fixed">' . __('Enabled', 'esig') . '</a>'; ?></div>
                                <?php } else { ?>
                                   <div class="esig-add-on-enabled"><?php echo '<a data-text-disable="Disable" data-text-enabled="Enabled" href="?page=esign-addons&tab=enable&esig_action=disable&plugin_url=' . urlencode($plugin_file) . '&plugin_name=' . $plugin_name . '" ' . $esig_permission . ' class="eisg-addons-disable">' . __('Enabled', 'esig') . '</a>'; ?></div> 
                                <?php } ?>
                                <?php echo $settings_page; ?>
                            </div>

                        </div>


                        <?php
                    }
                }
            }
        }
    }

    if ($total == 0) {
        echo '<div class="esig-addons-achievement">
				<p><h2>' . _e('No add-ons are currently enabled', 'esig') . '</h2></p>
				<p class="esig-addon-enable-now"><a href="?page=esign-addons&tab=disable" class="esig-addon-enable-now">' . __('Go enable Add-Ons', 'esig') . '</a></p>
				
			    </div>';
    }
    ?>

    <?php
} // enable tab end here 
// disable tab start here 
if ($tab == 'disable') {

    $array_Plugins = Esig_Addons::get_all_addons();
    asort($array_Plugins);
    $total = 0;
    if (!empty($array_Plugins)) {
        foreach ($array_Plugins as $plugin_file => $plugin_data) {
            if (!Esig_Addons::is_enabled($plugin_file)) {
                $plugin_name = $plugin_data['Name'];

                if (preg_match("/esig/", $plugin_file)) {
                    if ($plugin_name != "WP E-Signature") {
                        $total++;
                        // $plugin_name= trim($plugin_name, "WP E-Signature");
                        list($folder_name, $file_name) = explode('/', $plugin_file);
                        //$plugin_data = Esig_Addons::get_addon_data(Esig_Addons::get_installed_directory($plugin_file) . $plugin_file);
                        $documentation_page = '';
                        if (!empty($plugin_data['Documentation'])) {
                            $documentation_page = '<span class="esig-add-on-author"><a href="' . $plugin_data['Documentation'] . '" target="_blank">' . __('Documentation', 'esig') . '</a></span>';
                        }
                        ?>
                        <div class="esig-add-on-block">

                            <div class="esig-add-on-icon">
                                <div class="esig-image-wrapper">
                                    <img src="<?php echo ESIGN_ASSETS_DIR_URI . '/images/add-ons/' . $folder_name . '.png'; ?>" width="50px" height="50px" alt="">
                                </div>
                            </div>

                            <div class="esig-add-on-info">
                                <h4><?php echo $plugin_name; ?></h4>
                                <span class="esig-add-on-author"> <?php _e('by', 'esig'); ?> <a href="http://approveme.com"><?php _e('Approveme', 'esig'); ?></a></span>

                                <?php echo $documentation_page; ?>

                                <p class="esig-add-on-description"><?php echo $plugin_data['Description']; ?></p>
                            </div>

                            <div class="esig-add-on-actions">
                                <div class="esig-add-on-disabled"><?php echo '<a data-text-enable="Enable" data-text-disabled="Disabled" href="?page=esign-addons&tab=disable&esig_action=enable&plugin_url=' . urlencode($plugin_file) . '&plugin_name=' . $plugin_name . '" ' . $esig_permission . ' class="eisg-addons-enable">' . __('Disabled', 'esig') . '</a>'; ?></div>

                                <div class="esig-add-on-delete" title="Delete this plugin?" ><a href="#" data-url="?page=esign-addons&tab=enable&esig_action=delete&plugin_url=<?php echo urlencode($plugin_file); ?>&plugin_name=<?php echo $plugin_name; ?>" data-name="<?php echo $plugin_name; ?>" id="esig-addon-delete"></a></div>
                            </div>



                        </div>


                        <?php
                    }
                }
            }
        }
    }
    if ($total == 0) {
        echo '<div class="esig-addons-achievement">
				<h2>' . __('No add-ons are currently disabled', 'esig') . '</h2>
				
			    </div>';
    }
    ?>

    <?php
} // disable tab end here 
// get-more tab start here 
if ($tab == 'get-more') {


    if (Esign_licenses::is_license_active()) {
        $license_key = 'yes';
    } else {
        $license_key = 'no';
    }

    $all_addons_list = $this->model->esig_get_addons_list();

    if ($all_addons_list) {
        $total = 0;
        $all_addons = Esig_Addons::esig_object_sort($all_addons_list);
        foreach ($all_addons as $addonlist => $addons) {

            if (WP_E_Addon::is_business_pack_list($addons)) {
                continue;
            }
            if ($addonlist == "esig-price") {


                if ((Esign_licenses::get_license_type()) != "business-license" && (Esign_licenses::get_license_type()) != "Business License") {

                    $buisness_price = is_array($addons) ? $addons[0]->amount : null;
                    $professional_price = is_array($addons) ? $addons[1]->amount : null;
                    $individual_price = is_array($addons) ? $addons[2]->amount : null;


                    if (($esig_license_type = $this->setting->get_generic('esig_wp_esignature_license_type') ) == 'Professional License' && $license_key != 'no') {
                        $price = $buisness_price - $professional_price;
                    } elseif (($esig_license_type = $this->setting->get_generic('esig_wp_esignature_license_type') ) == 'Individual License' && $license_key != 'no') {
                        $price = $buisness_price - $individual_price;
                    } else {
                        $price = $buisness_price;
                    }
                    ?>


                    <div class="esig-add-on-block esig-pro-pack open">
                        <?php echo sprintf(__('<h3>Get the E-Signature Buisness Pack</h3>
					        <p style="display:block;">The Business Pack gets you access to WP E-Signature add-ons that unlock so much more functionality and features that WP E-Signature can do... like Dropbox Sync, Signing Reminders, Save as PDF, Stand Alone Documents, URL Redirect After Signing, Custom Fields and more. With the Business Pack, you get access to all our ApproveMe built WP E-Signature Add-ons plus any more we build in the next year (which will be a ton).</p>
					        <a class="esig-btn-pro" href="http://www.approveme.com/e-signature-upgrade-license/" target="_blank">Get all our add-ons for $%s</a> ', $price, 'esig'), $price); ?>

                    </div>


                    <?php
                } else {
                    // installed business license 
                    ?>
                    <div class="esig-add-on-block esig-pro-pack open">
                        <?php _e('<h3>Are there any features you\'d like to see?</h3>
					        <p style="display:block;">WP E-Signature is a powerful (and highly customizable) document signing application powered by WordPress. We LOVE customer feedback and would love to hear from you. Let us know how we can improve your experience.</p>
					        <a class="esig-btn-pro" href="http://approveme.uservoice.com/admin/forums/243780-general" target="_blank">Submit a feature request</a> ', 'esig'); ?>

                    </div>
                    <?php
                }
            } elseif ($addons->addon_name != 'WP E-Signature') {
                $plugin_root_folder = trim($addons->download_name, ".zip");

                $plugin_file = $this->model->esig_get_addons_file_path($plugin_root_folder);
                $esig_update_link = '';
                if ($plugin_file) {

                    if (is_plugin_active($plugin_file)) {
                        continue;
                    }
                    if (is_plugin_inactive($plugin_file)) {
                        continue;
                    }
                } else {
                    if ($addons->download_access == 'yes') {


                        $esig_action_link = '<div class="esig-add-on-disabled"><a  href="?page=esign-addons&esig_action=install&download_url=' . urlencode($addons->download_link) . '&download_name=' . $addons->download_name . '" class="eisg-addons-install">' . __('Install Now', 'esig') . '</a></div>';
                    } else {
                        $esig_action_link = '<div class="esig-add-on-actions"><div class="esig-add-on-price">
<span class="esig-regular-price">$' . $price . '</span>
</div><div class="esig-add-on-buy-now"><a href="https://www.approveme.com/wp-digital-e-signature#pricingPlans" target="_blank" class="eisg-addons-upgrade">' . __('Buy Now', 'esig') . '</a></div></div>';
                    }

                    $total++;
                    ?>

                    <div class="esig-add-on-block">

                        <div class="esig-add-on-icon">
                            <div class="esig-image-wrapper">
                                <img src="<?php echo $addons->addon_image[0]; ?>" width="50px" height="50px" alt="">
                            </div>
                        </div>

                        <div class="esig-add-on-info">
                            <h4><a href="<?php echo $addons->download_page_link; ?>" target="_blank"><?php echo "WP E-Signature - " . $addons->addon_name; ?></a></h4>
                            <span class="esig-add-on-author"> <?php _e('by', 'esig'); ?> <a href="https://www.approveme.com/"><?php _e('Approveme', 'esig'); ?></a></span>
                            <span class="esig-add-on-author"> <?php echo "Version " . $addons->new_version; ?> </span>

                            <p class="esig-add-on-description"><?php echo $addons->addon_description; ?></p>
                        </div>

                        <div class="esig-add-on-actions">
                            <div class="esig-add-on-disabled"><?php echo $esig_action_link; ?></div>

                        </div>
                    </div>


                    <?php
                }
            }
        }

        if ($total == 0) {
            echo '<div class="esig-addons-achievement">
				<h2>' . __('Awesome! Looks like you have everything installed. Well done.', 'esig') . '</h2>
				<p><img src="' . ESIGN_ASSETS_DIR_URI . '/images/boss.svg" width="244" height="245"></p>
				<p><img src="' . ESIGN_ASSETS_DIR_URI . '/images/logo.png" width="243" height="55"></p>
				
			    </div>';
        }
    }
    ?>

    <?php
} // get-more tab end here 

if ($tab == 'integration') {
    include_once "integrations.php";
}
?>


</div>

<div class="esig-addon-devbox" style="display:none;">
    <div class="esig-addons-wrap">
        <div class="progress-wrap">
            <div class="progress">
                <span class="countup"></span>
            </div>  
        </div>
    </div>
</div>

<div id="esig-addon-dialog-confirm" style="display:none;">
    <div class="esig-dialog-header">
        <div class="esig-alert">
            <span class="icon-esig-alert"></span>
        </div>
        <h3><?php _e("Delete", "esig"); ?> <span id="esig-addon-name"> </span>?</h3>

        <p class="esig-updater-text"><?php
$esig_user = new WP_E_User();

$wpid = get_current_user_id();

$users = $esig_user->getUserByWPID($wpid);
echo $users->first_name . ",";

_e('it looks like you are about to permanently delete this add-on.  <br>Some industries require companies to keep legal records for up to 7 years.  By deleting this add-on you could affect various aspects of previously signed (and future) documents.', 'esig');
?></p>

        <hr>

        <p><strong><?php _e('I understand that by deleting this add-on...', 'esig'); ?></strong></p>
        <p><input type="checkbox" id="esig-addon-agree-one"> <span id="esig-addon-agree"> </span> <?php _e('will be removed from WP E-Signature', 'esig'); ?> </p>
        <p><input type="checkbox" id="esig-addon-agree-two"> <?php _e('All documents that used this feature could be affected permanently', 'esig'); ?></p>
        <p><input type="checkbox" id="esig-addon-agree-three"><?php _e('All memory and history related to this add-on will be erased', 'esig'); ?> </p>


        <p id="esig-addon-error" style="display: none;">  </p>
    </div>

</div>


<?php
$esign_auto_update = $this->setting->get_generic("esign_auto_update");

if (isset($esign_auto_update) && empty($esign_auto_update)) {
    if (!get_transient('esign-update-remind')) {
        if (get_transient('esign-auto-downloads')) {
            include_once ESIGN_PLUGIN_PATH . "/views/about/update.php";
        }
    }
}
?>
