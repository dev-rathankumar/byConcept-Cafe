<h2 class="argmc-top-heading"><?php _e('General Settings', 'argMC'); ?></h2>
<p class="argmc-top-text argmc-text-general-settings"><?php _e('Under the General Settings tab you\'ll find options like: changing buttons text, custom text, wizard width, secondary font family and error messages.', 'argMC'); ?></p>

<h3><?php _e('Buttons and Custom Text', 'argMC'); ?></h3>

<table class="form-table argmc-table-buttons">
    <tbody>
        <tr>
            <th>
                <?php _e('Skip Login Button Text', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the text of login button.', 'argMC'); ?></span>	
            </th>
            <td><input type="text" name="btn_skip_login_text" value="<?php echo $options['btn_skip_login_text']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Next Button Text', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the text of next button.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="btn_next_text" value="<?php echo $options['btn_next_text']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Previous Button Text', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the text of the previous button.', 'argMC'); ?></span>	
            </th>
            <td><input type="text" name="btn_prev_text" value="<?php echo $options['btn_prev_text']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Place Order Button Text', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the text of the place order button.', 'argMC'); ?></span>	
            </th>
            <td><input type="text" name="btn_submit_text" value="<?php echo $options['btn_submit_text']; ?>" /></td>
        </tr>									
        <tr>
            <th>
                <?php _e('Custom Text', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to add an extra text to footer.', 'argMC'); ?></span>
            </th>
            <td><textarea name="footer_text"><?php echo $options['footer_text']; ?></textarea></td>
        </tr>
    </tbody>
</table>		

<h3><?php _e('Wizard width and secondary font family', 'argMC'); ?></h3>

<table class="form-table argmc-table-buttons">
<tbody>	
    <tr>
        <th>
            <?php _e('Wizard Maximum Width', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this option to set the maximum width of the wizard layout. Set input value to <strong>none</strong> if you want the wizard to expand to the entire layout width.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="wizard_max_width" class="input-field" value="<?php echo $options['wizard_max_width']; ?>" /></td>
    </tr>
    <tr>
        <th>
            <?php _e('Wizard Secondary Font Family', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this option to set the font family for wizard tabs, headings, lables, buttons, payment metods labels (example: \'Poppins\',sans-serif). Leave it empty if your theme has only one font family.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="secondary_font" class="input-field" value="<?php echo $options['secondary_font']; ?>" /></td>
    </tr>
    <tr>
        <th>
            <?php _e('Wizard Secondary Font Weight', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this option to set the font weight for wizard tabs, headings, buttons, payment metods labels.', 'argMC'); ?></span>
        </th>
        <td>
            <select name="secondary_font_weight">
                <option value="400" <?php selected($options['secondary_font_weight'], '400', true); ?>><?php _e('400', 'argMC'); ?></option>
                <option value="500" <?php selected($options['secondary_font_weight'], '500', true); ?>><?php _e('500', 'argMC'); ?></option>
                <option value="600" <?php selected($options['secondary_font_weight'], '600', true); ?>><?php _e('600', 'argMC'); ?></option>
                <option value="700" <?php selected($options['secondary_font_weight'], '700', true); ?>><?php _e('700', 'argMC'); ?></option>
                <option value="800" <?php selected($options['secondary_font_weight'], '800', true); ?>><?php _e('800', 'argMC'); ?></option>
                <option value="900" <?php selected($options['secondary_font_weight'], '900', true); ?>><?php _e('900', 'argMC'); ?></option>
            </select>										
        </td>										
    </tr>									
</tbody>
</table>

<h3><?php _e('Validation Error Messages', 'argMC'); ?></h3>

<table class="form-table argmc-table-buttons">
<tbody>
    <tr>
        <th>
           <?php _e('Required Field', 'argMC'); ?>
           <span class="argmc-description"><?php _e('Change the text of the required field error message.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="error_required_text" class="input-field" value="<?php echo $options['error_required_text']; ?>" /></td>
    </tr>
    <tr>
        <th>
           <?php _e('Required Checkbox', 'argMC'); ?>
           <span class="argmc-description"><?php _e('Change the text of the required checkbox error message.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="error_required_checkbox" class="input-field" value="<?php echo $options['error_required_checkbox']; ?>" /></td>
    </tr>																	
    <tr>
        <th>
            <?php _e('Invalid Email Address', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Change the text of the invalid email address error message.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="error_email" class="input-field" value="<?php echo $options['error_email']; ?>" /></td>
    </tr>
    <tr>
        <th>
            <?php _e('Invalid Phone Number', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Change the text of the invalid phone number error message.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="error_phone" class="input-field" value="<?php echo $options['error_phone']; ?>" /></td>
    </tr>
    <tr>
        <th>
            <?php _e('Invalid Postcode', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Change the text of the invalid postcode error message.', 'argMC'); ?></span>
        </th>
        <td><input type="text" name="error_zip" class="input-field" value="<?php echo $options['error_zip']; ?>" /></td>
    </tr>								
</tbody>
</table>

<h3 style="margin: 80px 0 0px;"><?php _e('Scroll to Top Options', 'argMC'); ?></h3>
<table class="form-table argmc-table-buttons">
<tbody>
     <tr>
        <th style="width: 465px;">
            <?php _e('Scroll to the top of the wizard - desktops adjustments', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this options if you want to scroll to the top of the wizard instead the top of the page when the user will click on the navigation buttons. Default value is 0 - that means it will scroll to the top of the page. Change this value to any value you want (usualy 60 - for a better tabs visibility) and it will scroll to the top of the wizard.', 'argMC'); ?></span>
        </th>
        <td><input style="min-width: 86px; width: 86px;" type="text" name="scrollTopDesktops" class="input-field" value="<?php echo $options['scrollTopDesktops']; ?>" /></td>
    </tr>
    <tr>
        <th style="width: 465px;">
            <?php _e('Scroll to the top of the wizard - mobiles adjustments', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this options if you want to scroll to the top of the wizard instead the top of the page when the user will click on the navigation buttons. Default value is 0 - that means it will scroll to the top of the page. Change this value to any value you want (usualy 30 - for a better tabs visibility) and it will scroll to the top of the wizard.', 'argMC'); ?></span>
        </th>
        <td><input style="min-width: 86px; width: 86px;" type="text" name="scrollTopMobiles" class="input-field" value="<?php echo $options['scrollTopMobiles']; ?>" /></td>
    </tr>
</tbody>
</table>

<table class="form-table argmc-table-buttons">
<tbody>
    <tr>
        <th style="width: 465px;">
            <?php _e('Overwrite WooCommerce form-checkout.php plugin template', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Turn on this option to allow the theme to overwrite the plugin form-checkout.php template (in this template we transform the checkout into a multistep and you can copy this file into your theme -> woocommerce -> checkout and make the changes to your needs).', 'argMC'); ?></span>
        </th>
        <td>
            <div class="radio-buttons-wrapper">
                <input id="overwrite-form-checkout-yes" class="input-radio-button" type="radio" name="overwrite_form_checkout" value="1" <?php checked($options['overwrite_form_checkout'], 1); ?>>
                <label class="input-label-button label-button-left" for="overwrite-form-checkout-yes">
                    <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                </label>

                <input id="overwrite-form-checkout-no" class="input-radio-button" type="radio" name="overwrite_form_checkout" value="0" <?php checked($options['overwrite_form_checkout'], 0); ?>>
                <label class="input-label-button label-button-right" for="overwrite-form-checkout-no">
                    <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                </label>																						
            </div>			
        </td>
    </tr>
    <tr>
        <th style="width: 465px;">
            <?php _e('Overwrite WooCommerce form-register.php plugin template', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Turn on this option to allow the theme to overwrite the plugin form-register.php template (you can copy this file into your theme -> woocommerce -> checkout and make the changes to your needs).', 'argMC'); ?></span>
        </th>
        <td>
            <div class="radio-buttons-wrapper">
                <input id="overwrite-form-register-yes" class="input-radio-button" type="radio" name="overwrite_form_register" value="1" <?php checked($options['overwrite_form_register'], 1); ?>>
                <label class="input-label-button label-button-left" for="overwrite-form-register-yes">
                    <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                </label>

                <input id="overwrite-form-register-no" class="input-radio-button" type="radio" name="overwrite_form_register" value="0" <?php checked($options['overwrite_form_register'], 0); ?>>
                <label class="input-label-button label-button-right" for="overwrite-form-register-no">
                    <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                </label>																						
            </div>			
        </td>
    </tr>    
    
</tbody>
</table>

<table class="form-table argmc-table-buttons">
<tbody>
    <tr>
        <th style="width: 465px;">
            <?php _e('Remove all hooks if some steps content doesn\'t show', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Turn on this option to remove all the hooks (before/after step actions) from the checkout page if some of your steps content doesn\'t show.', 'argMC'); ?></span>
        </th>
        <td>
            <div class="radio-buttons-wrapper">
                <input id="remove-all-hooks-yes" class="input-radio-button" type="radio" name="remove_all_hooks" value="1" <?php checked($options['remove_all_hooks'], 1); ?>>
                <label class="input-label-button label-button-left" for="remove-all-hooks-yes">
                    <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                </label>

                <input id="remove-all-hooks-no" class="input-radio-button" type="radio" name="remove_all_hooks" value="0" <?php checked($options['remove_all_hooks'], 0); ?>>
                <label class="input-label-button label-button-right" for="remove-all-hooks-no">
                    <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                </label>																						
            </div>			
        </td>
    </tr>	
</tbody>
</table>