

<?php
$active_theme = wp_get_theme();

$super_admin_wp_data = get_userdata(WP_E_Sig()->user->esig_get_super_admin_id());

$plugins = get_plugins();
$active_plugins = get_option('active_plugins', array());

$license_result = Esign_licenses::check_license();



?>


<img src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/logo.svg">

<p><?php _e('Please include this information when requesting support:', 'esig'); ?> </p>
<form action="" method="POST">
    <p class="submit">
        <a href="#" onclick="copyToClipboard()" id="esig-copy-clipboard" class="button-primary esig-debug-report"><?php _e('Copy To Clipboard', 'esig'); ?></a>
        <button type="submit" value="download-system-info" class="button-primary esig-debug-report"><?php _e('Download System Status', 'esig'); ?></button>
    </p>

    <textarea readonly id="esig-system-info-textarea" name="esig_system_info">
            
### Begin System Status Report ###

        <?php do_action('edd_system_info_before'); ?>

===== Site Info =====

SITE_URL:                <?php echo site_url() . "\n"; ?>
HOME_URL:                <?php echo home_url() . "\n"; ?>
Multisite:               <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n"; ?>



===== Hosting Provider =====

HOST :                   <?php echo $data['hosting_info'] . "\n"; ?>


===== WordPress Configuration =====

Version:                <?php echo get_bloginfo('version') . "\n"; ?>
Language:               <?php echo ( defined('WPLANG') && WPLANG ? WPLANG : 'en_US' ) . "\n"; ?>
Timezone:               <?php echo date_default_timezone_get() . "\n"; ?>
Permalink Structure:    <?php echo ( get_option('permalink_structure') ? get_option('permalink_structure') : 'Default' ) . "\n"; ?>
Active Theme:           <?php echo $active_theme->Name . "\n"; ?>
Theme Version:          <?php echo $active_theme->Version . "\n"; ?>
Author Url:             <?php echo $active_theme->{'Author URI'} . "\n"; ?>
Remote Post:            <?php echo $data['remote_post'] . "\n"; ?>
WP_DEBUG:               <?php echo ( defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n"; ?>
WP_DEBUG_LOG:           <?php echo ( WP_DEBUG_LOG ? 'Enabled' : 'Disabled' ) . "\n"; ?>
Memory Limit:           <?php echo WP_MEMORY_LIMIT . "- We recommend setting memory to at least 64MB \n\t\t\t See: http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" . "\n"; ?>


===== WP E-Signature Configuration =====

Version:                <?php echo esigGetVersion() . "\n"; ?>
DB Version:             <?php echo get_option("esig_db_version") . "\n"; ?>
License :               <?php echo $license_result->license . "\n";?>
License Type:           <?php echo $license_result->license_type . "\n"; ?>
Super Admin:            <?php echo $super_admin_wp_data->user_login . "(WP Username)" . "\n"; ?>
Timezone:               <?php echo WP_E_Sig()->setting->get_generic('esig_timezone_string') . "\n"; ?>
Force SSL:              <?php echo (WP_E_Sig()->setting->get_generic('force_ssl_enabled') ? "Yes" : "NO") . "\n\n"; ?>
Active Add-ons:         <?php
        foreach (Esig_Addons::get_active_addons() as $addon_file) {
            if(empty($addon_file)){
                continue;
            }
            if($addon_file == 'e-signature-business-add-ons.php'){
                continue;
            }
            
            list($folder_name, $file) = explode("/", $addon_file);
            
            $addon_data = Esig_Addons::get_addon_data(Esig_Addons::get_installed_directory($folder_name) . $addon_file);
            if($addon_data){
                echo $addon_data['Name']  . "\n\t\t\t";
            }    
        }
        ?>

E-signature Pages:       <?php echo $data['esign_pages'] . "\n"; ?>


===== WordPress Active Plugins =====
                      
    <?php
        foreach ($plugins as $plugin_path => $plugin) {
            if (!in_array($plugin_path, $active_plugins))
                continue;

            echo "\t\t\t" . $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
        }
        ?>

===== WordPress Inactive Plugins =====
    
    <?php
        foreach ($plugins as $plugin_path => $plugin) {
            if (in_array($plugin_path, $active_plugins))
                continue;

            echo "\t\t\t" . $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
        }
        ?>

    <?php
        if (is_multisite()) {
            // WordPress Multisite active plugins
            echo "\n" . '-- Network Active Plugins' . "\n\n";

            $plugins = wp_get_active_network_plugins();
            $active_plugins = get_site_option('active_sitewide_plugins', array());

            foreach ($plugins as $plugin_path) {
                $plugin_base = plugin_basename($plugin_path);

                if (!array_key_exists($plugin_base, $active_plugins))
                    continue;

                $plugin = get_plugin_data($plugin_path);
                echo "\t\t\t" . $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
            }
        }
        ?>

===== Webserver Configuration =====

PHP Version:            <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:          <?php
        global $wpdb;
        echo $wpdb->db_version() . "\n";
        ?>
Webserver Info:         <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>
Port:                   <?php echo $_SERVER['SERVER_PORT'] . "\n"; ?>
Document Root:          <?php echo $_SERVER['DOCUMENT_ROOT'] . "\n\n";?>


===== PHP Configuration =====

Safe Mode:              <?php echo ( ini_get('safe_mode') ? 'Enabled' : 'Disabled' . "\n" ); ?>
Memory Limit:           <?php echo ini_get('memory_limit') . "\n"; ?>
Upload Max Size:        <?php echo ini_get('upload_max_filesize') . "\n"; ?>
Post Max Size:          <?php echo ini_get('post_max_size') . "\n"; ?>
Upload Max Filesize:    <?php echo ini_get('upload_max_filesize') . "\n"; ?>
Time Limit:             <?php echo ini_get('max_execution_time') . "\n"; ?>
Max Input Vars:         <?php echo ini_get('max_input_vars') . "\n"; ?>
Display Errors:         <?php echo ( ini_get('display_errors') ? 'On (' . ini_get('display_errors') . ')' : 'N/A' ) . "\n"; ?>


===== PHP Extensions =====

cURL:                    <?php echo ( function_exists('curl_init') ? 'Supported' : 'Not Supported***' ) . "\n"; ?>
fsockopen:               <?php echo ( function_exists('fsockopen') ? 'Supported' : 'Not Supported***' ) . "\n"; ?>
SOAP Client:             <?php echo ( class_exists('SoapClient') ? 'Installed' : 'Not Installed' ) . "\n"; ?>
Suhosin:                 <?php echo ( extension_loaded('suhosin') ? 'Installed' : 'Not Installed' ) . "\n"; ?>
Openssl Encryption:      <?php echo ( function_exists('openssl_encrypt') ? 'Supported' : 'Not Supported***' ) . "\n"; ?>
MCrypt:                  <?php echo ( function_exists('mcrypt_create_iv') ? 'Supported' : 'Not Supported***' ) . "\n"; ?>
MbString:                <?php echo ( function_exists('mb_split') ? 'Supported' : 'Not Supported' ) . "\n"; ?>
BCMath:                  <?php echo ( function_exists('bcmod') ? 'Supported' : 'Not Supported***' ) . "\n"; ?>


===== Session Configuration =====
	
Session:                <?= ( isset($_SESSION) ? 'Enabled' : 'Disabled' ) . "\n"; ?>
<?php
        if (isset($_SESSION)) {
            echo 'Session Name:           ' . esc_html(ini_get('session.name')) . "\n";
            echo 'Cookie Path:            ' . esc_html(ini_get('session.cookie_path')) . "\n";
            echo 'Save Path:              ' . esc_html(ini_get('session.save_path')) . "\n";
            echo 'Use Cookies:            ' . ( ini_get('session.use_cookies') ? 'On' : 'Off' ) . "\n";
            echo 'Use Only Cookies:       ' . ( ini_get('session.use_only_cookies') ? 'On' : 'Off' ) . "\n";
        }
        ?>




### End System Status Report ###

    </textarea>


</form>


<script type="text/javascript">
    function copyToClipboard() {

        //var text = document.getElementById('#esig-system-info-textarea').innerHTML;
        var copyTextarea = document.querySelector('#esig-system-info-textarea');
        copyTextarea.select();

        try {
            var successful = document.execCommand('copy');
           // var msg = successful ? 'successful' : 'unsuccessful';
            //console.log('Copying text command was ' + msg);
            document.getElementById("esig-copy-clipboard").innerHTML = 'Copied Successfully';
            //alert();
        } catch (err) {
            alert("Unable to copy");
        }
    }
</script>

