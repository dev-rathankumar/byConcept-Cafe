<?php
	use PowerpackElements\Classes\PP_Helper;
	use PowerpackElements\Classes\PP_Admin_Settings;

	$settings   = PP_Admin_Settings::get_settings();
	$languages 	= PP_Helper::get_google_map_languages();
?>
<table class="form-table">
    <tbody>
		<?php if ( ! defined( 'PP_ELEMENTS_LICENSE_KEY' ) ) {
			$license 	= PP_Admin_Settings::get_option( 'pp_license_key' );
			$status 	= PP_Admin_Settings::get_option( 'pp_license_status' );
			?>
			<tr valign="top">
				<th scope="row" valign="top">
					<?php esc_html_e('License Key', 'powerpack'); ?>
				</th>
				<td>
					<input id="pp_license_key" name="pp_license_key" type="password" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
					<p class="description"><?php echo sprintf(__('Enter your <a href="%s" target="_blank">license key</a> to enable remote updates and support.', 'powerpack'), 'https://powerpackelements.com/my-account/'); ?>
				</td>
			</tr>
			<?php if ( false !== $license && ! empty( $license ) ) { ?>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php esc_html_e( 'License Status', 'powerpack' ); ?>
					</th>
					<td>
						<?php if ( $status == 'valid' ) { ?>
							<span style="color: #267329; background: #caf1cb; padding: 5px 10px; text-shadow: none; border-radius: 3px; display: inline-block; text-transform: uppercase;"><?php esc_html_e('active'); ?></span>
							<?php wp_nonce_field( 'pp_license_deactivate_nonce', 'pp_license_deactivate_nonce' ); ?>
							<input type="submit" class="button-secondary" name="pp_license_deactivate" value="<?php esc_html_e('Deactivate License', 'powerpack'); ?>" />
						<?php } else { ?>
							<?php if ( $status == '' ) { $status = 'inactive'; } ?>
							<span style="<?php echo $status == 'inactive' ? 'color: #fff; background: #b1b1b1;' : 'color: red; background: #ffcdcd;'; ?> padding: 5px 10px; text-shadow: none; border-radius: 3px; display: inline-block; text-transform: uppercase;"><?php echo $status; ?></span>
							<?php
							wp_nonce_field( 'pp_license_activate_nonce', 'pp_license_activate_nonce' ); ?>
							<input type="submit" class="button-secondary" name="pp_license_activate" value="<?php esc_html_e( 'Activate License', 'powerpack' ); ?>"/>
							<p class="description"><?php esc_html_e( 'Please click the Activate License button to activate your license.', 'powerpack' ); ?>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
        <tr valign="top">
            <th scope="row" valign="top">
                <?php esc_html_e('Google Map API Key', 'powerpack'); ?>
            </th>
            <td>
                <input id="pp_google_map_api" name="pp_google_map_api" type="text" class="regular-text" value="<?php echo $settings['google_map_api']; ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" valign="top">
                <?php esc_html_e('Google Map Localization Language', 'powerpack'); ?>
            </th>
            <td>
				<select name="pp_google_map_lang" id="pp-google-map-language" class="placeholder placeholder-active">
					<option value=""><?php _e( 'Default', 'powerpack' ); ?></option>
					<?php foreach ( $languages as $key => $value ) { ?>
						<?php
						$selected = '';
						if ( $key === $settings['google_map_lang'] ) {
							$selected = 'selected="selected" ';
						}
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo esc_attr( $value ); ?></option>
					<?php } ?>
				</select>
            </td>
        </tr>
    </tbody>
</table>
<?php submit_button(); ?>