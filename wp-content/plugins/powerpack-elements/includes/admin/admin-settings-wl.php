<?php
use PowerpackElements\Classes\PP_Admin_Settings;

$settings = PP_Admin_Settings::get_settings();
?>
<?php if ( 'off' == $settings['hide_wl_settings'] ) { ?>

    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Plugin Name', 'powerpack'); ?>
                </th>
                <td>
                    <input id="pp_plugin_name" name="pp_plugin_name" type="text" class="regular-text" value="<?php esc_attr_e( $settings['plugin_name'] ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Plugin Description', 'powerpack'); ?>
                </th>
                <td>
                    <textarea id="pp_plugin_desc" name="pp_plugin_desc" style="width: 25em;"><?php echo $settings['plugin_desc']; ?></textarea>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Developer / Agency Name', 'powerpack'); ?>
                </th>
                <td>
                    <input id="pp_plugin_author" name="pp_plugin_author" type="text" class="regular-text" value="<?php echo $settings['plugin_author']; ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Website URL', 'powerpack'); ?>
                </th>
                <td>
                    <input id="pp_plugin_uri" name="pp_plugin_uri" type="text" class="regular-text" value="<?php echo esc_url( $settings['plugin_uri'] ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Admin Label', 'powerpack'); ?>
                </th>
                <td>
                    <input id="pp_admin_label" name="pp_admin_label" type="text" class="regular-text" value="<?php echo $settings['admin_label']; ?>" placeholder="PowerPack" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Support Link', 'powerpack'); ?>
                </th>
                <td>
                    <input id="pp_support_link" name="pp_support_link" type="text" class="regular-text" value="<?php echo $settings['support_link']; ?>" placeholder="https://powerpackelements.com/contact/" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Hide Support Message / Help Links in Widgets', 'powerpack'); ?>
                </th>
                <td>
                    <label for="pp_hide_support_msg">
                        <input id="pp_hide_support_msg" name="pp_hide_support_msg" type="checkbox" value="1" <?php echo $settings['hide_support'] == 'on' ? 'checked="checked"' : '' ?> />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Hide White Label Settings', 'powerpack'); ?>
                </th>
                <td>
                    <label for="pp_hide_wl_settings">
                        <input id="pp_hide_wl_settings" name="pp_hide_wl_settings" type="checkbox" value="1" <?php echo $settings['hide_wl_settings'] == 'on' ? 'checked="checked"' : '' ?> />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php esc_html_e('Hide Plugin', 'powerpack'); ?>
                </th>
                <td>
                    <label for="pp_hide_plugin">
                        <input id="pp_hide_plugin" name="pp_hide_plugin" type="checkbox" value="1" <?php echo $settings['hide_plugin'] == 'on' ? 'checked="checked"' : '' ?> />
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
    <p style="">
        <?php esc_html_e( 'You can hide this form to prevent your client from seeing these settings.', 'powerpack' ); ?><br />
        <?php esc_html_e( 'To re-enable the form, you will need to first deactivate the plugin and activate it again.', 'powerpack' ); ?>
    </p>
    <?php submit_button(); ?>
    <?php wp_nonce_field('pp-wl-settings', 'pp-wl-settings-nonce'); ?>

<?php } else { ?>

    <?php if ( isset($_GET['tab']) && 'white-label' == $_GET['tab'] ) { ?>

        <p style=""><?php esc_html_e( 'Done! To re-enable the form, you will need to first deactivate the plugin and activate it again.', 'powerpack' ); ?></p>

    <?php } ?>

<?php } ?>
