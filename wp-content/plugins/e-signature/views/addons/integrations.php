<?php
$esigAddons = new WP_E_Addon();

$wpList = $esigAddons->wordpressRepoPlugins();

if ($wpList) { // check wp object is valid
    $pluginList = $wpList->plugins;


    foreach ($pluginList as $plugin) {

        $pluginFile = $esigAddons->esig_get_addons_file_path($plugin->slug, true);

        if (!$pluginFile) {
            $esig_action_link = '<div class="esig-add-on-disabled"><a  href="?page=esign-addons&tab=integration&default=1&esig_action=install&download_url=' . WP_E_Addon::base64_url_encode($plugin->download_link) . '&download_name=' . $plugin->slug . '" ' . $esig_permission . ' class="eisg-addons-update">' . __('Install Now', 'esig') . '</a></div>';
        } elseif ($pluginFile && is_plugin_active($pluginFile)) {
            $esig_action_link = '<div class="esig-add-on-enabled"><a data-text-disable="Disable" data-text-enabled="Enabled" href="?page=esign-addons&tab=integration&esig_action=disable&plugin_url=' . urlencode($pluginFile) . '&plugin_name=' . $plugin->slug . '" ' . $esig_permission . '>' . __('Enabled', 'esig') . '</a></div>';
        } else {
            $esig_action_link = '<div class="esig-add-on-disabled"><a data-text-enable="Enable" data-text-disabled="Disabled" href="?page=esign-addons&tab=integration&esig_action=enable&plugin_url=' . urlencode($pluginFile) . '&plugin_name=' . $plugin->slug . '" ' . $esig_permission . '>' . __('Disabled', 'esig') . '</a></div>';
        }
        ?>

        <div class="esig-add-on-block">

            <div class="esig-add-on-icon">
                <div class="esig-image-wrapper">
                    <img src="https://ps.w.org/<?php echo $plugin->slug; ?>/assets/icon-128x128.png?rev=1242135" width="50px" height="50px" alt="">
                </div>
            </div>

            <div class="esig-add-on-info">
                <h4><?php echo $plugin->name; ?></h4>
                <span class="esig-add-on-author"> <?php _e('by', 'esig'); ?> <a href="http://approveme.com"><?php _e('Approveme', 'esig'); ?></a></span>
                <a href="https://wordpress.org/plugins/<?php echo $plugin->slug; ?>" target="_blank"><?php _e('Documentation', 'esig'); ?></a>
                <span class="esig-add-on-author"> <?php echo "Version " . $plugin->version; ?> </span>
                <p class="esig-add-on-description"><?php echo $plugin->short_description; ?></p>
            </div>


            <div class="esig-add-on-actions">
        <?php echo $esig_action_link; ?> 
            </div>

        </div>

                <?php
            }
        } // foreach end here 




