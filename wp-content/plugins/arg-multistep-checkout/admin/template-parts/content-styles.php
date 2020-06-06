<h2 class="argmc-top-heading"><?php _e('Multistep Checkout Styles', 'argMC'); ?></h2>
<p class="argmc-top-text"><?php _e('Here you can find the options to change your checkout steps styles:', 'argMC'); ?></p>


<h3><?php _e('Wizard styles', 'argMC'); ?></h3>

<table class="form-table argmc-table-style">
    <tbody>
        <tr>
            <th>
                <?php _e('Wizard Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the color of wizard footer custom text.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="wizard_color" class="color-field" value="<?php echo $options['wizard_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Wizard Accent Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the accent color of the wizard.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="accent_color" class="color-field" value="<?php echo $options['accent_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Wizard Border Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the color of the wizard footer border line.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="border_color" class="color-field" value="<?php echo $options['border_color']; ?>" /></td>
        </tr>
        
        <tr>
            <th>
                <?php _e('Wizard Validation Error Messages Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the color of validation error messages.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="wizard_text_errors_color" class="color-field" value="<?php echo $options['wizard_text_errors_color']; ?>" /></td>
        </tr>
        
        <tr>
            <th>
                <?php _e('Change Wizard Buttons Styles (skip login, next, previous, place order)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('By default your theme buttons styles will be applied. Enable this option if you want to change the text/background color of these buttons.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="overwrite-theme-buttons-yes" class="input-radio-button overwrite-wizard-buttons" data-style="overwrite-buttons" type="radio" name="overwrite_wizard_buttons" value="1" <?php checked($options['overwrite_wizard_buttons'], 1); ?>>
                    <label class="input-label-button label-button-left" for="overwrite-theme-buttons-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="overwrite-theme-buttons-no" class="input-radio-button overwrite-wizard-buttons" type="radio" data-style="overwrite-buttons-no" name="overwrite_wizard_buttons" value="0" <?php checked($options['overwrite_wizard_buttons'], 0); ?>>
                    <label class="input-label-button label-button-right" for="overwrite-theme-buttons-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>	

        <tr class="wizard-overwrite-buttons-option">
            <th>
                <?php _e('Wizard Button Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the color of the button text. The theme button text color will be inherited if you leave the input empty.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="wizard_button_text_color" class="color-field" value="<?php echo $options['wizard_button_text_color']; ?>" /></td>
        </tr>
        <tr class="wizard-overwrite-buttons-option">
            <th>
                <?php _e('Wizard Button Text Transparency on Hover', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the opacity of the text button on hover. The opacity-level describes the transparency-level, where 1 is not transparent at all, 0.5 is 50% see-through, and 0 is completely transparent.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="wizard_button_text_opacity" class="input-field" value="<?php echo $options['wizard_button_text_opacity']; ?>" /></td>
        </tr>
        <tr class="wizard-overwrite-buttons-option">
            <th>
                <?php _e('Wizard Next/Skip Login Buttons Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the background color of the next/skip login buttons.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="next_button_bkg" class="color-field" value="<?php echo $options['next_button_bkg']; ?>" /></td>
        </tr>
        <tr class="wizard-overwrite-buttons-option">
            <th>
                <?php _e('Wizard Previous Button Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the background color of the previous button.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="prev_button_bkg" class="color-field" value="<?php echo $options['prev_button_bkg']; ?>" /></td>
        </tr>
        <tr class="wizard-overwrite-buttons-option">
            <th>
                <?php _e('Wizard Place Order Button Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the background color of the place order button.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="place_order_button_bkg" class="color-field" value="<?php echo $options['place_order_button_bkg']; ?>" /></td>
        </tr>
    </tbody>
</table>


<h3><?php _e('Tabs Styles', 'argMC'); ?></h3>

<table class="form-table argmc-table-style" style="margin: 0;">
    <tbody>
        <tr>
            <th>
                <?php _e('Tabs Layouts', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the layout of your tabs.', 'argMC'); ?></span>
            </th>
            <td>
                <select name="tabs_layout" class="argmc-tabs-layout">
                    <option value="tabs-square" <?php selected($options['tabs_layout'], 'tabs-square', true); ?>><?php _e('Square', 'argMC'); ?></option>
                    <option value="tabs-arrow" <?php selected($options['tabs_layout'], 'tabs-arrow', true); ?>><?php _e('Arrow', 'argMC'); ?></option>
                    <option value="tabs-arrow-alt" <?php selected($options['tabs_layout'], 'tabs-arrow-alt', true); ?>><?php _e('Arrow Alt (Includes Vertical Orientation Layout)', 'argMC'); ?></option>
                    <option value="tabs-progress-bar" <?php selected($options['tabs_layout'], 'tabs-progress-bar', true); ?>><?php _e('Progress Bar', 'argMC'); ?></option>
                    <option value="tabs-outline" <?php selected($options['tabs_layout'], 'tabs-outline', true); ?>><?php _e('Progress Bar Outline', 'argMC'); ?></option>
                </select>
            </td>	
        </tr>							
    </tbody>
 </table>

<table class="form-table argmc-table-style argmc-tab-style argmc-tab-default-style">
    <tbody>					
        <tr>
            <th>
                <?php _e('Tabs Text Styles', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the text styles of your tabs.', 'argMC'); ?></span>
            </th>
            <td>
                <select name="tabs_template">
                    <option value="tabs-default" <?php selected($options['tabs_template'], 'tabs-default', true); ?>><?php _e('Text Inline', 'argMC'); ?></option>
                    <option value="tabs-text-under" <?php selected($options['tabs_template'], 'tabs-text-under', true); ?>><?php _e('Text Under Number', 'argMC'); ?></option>
                    <option value="tabs-hide-numbers" <?php selected($options['tabs_template'], 'tabs-hide-numbers', true); ?>><?php _e('Hide Number on Tab', 'argMC'); ?></option>
                </select>
            </td>	
        </tr>
        <tr>
            <th>
                <?php _e('Tabs Width', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change your tabs width.', 'argMC'); ?></span>
            </th>
            <td>
                <select name="tabs_width">
                    <option value="tabs-equal-width" <?php selected($options['tabs_width'], 'tabs-equal-width', true); ?>><?php _e('Equals', 'argMC'); ?></option>
                    <option value="tabs-width-auto" <?php selected($options['tabs_width'], 'tabs-width-auto', true); ?>><?php _e('Auto', 'argMC'); ?></option>
                </select>
            </td>	
        </tr>
        <tr>
            <th>
                <?php _e('Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="number_text_color" class="color-field" value="<?php echo $options['number_text_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number background color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_number_bkg_color" class="color-field" value="<?php echo $options['tab_number_bkg_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_text_color" class="color-field" value="<?php echo $options['tab_text_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs background color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_bkg_color" class="color-field" value="<?php echo $options['tab_bkg_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Border Left Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the border color between the tabs.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_border_left_color" class="color-field" value="<?php echo $options['tab_border_left_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Border Bottom Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the border color under the tabs.', 'argMC'); ?></span>										
            </th>
            <td><input type="text" name="tab_border_bottom_color" class="color-field" value="<?php echo $options['tab_border_bottom_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current / Completed / On Hover Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number color of the completed/current/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_number_color_hover" class="color-field" value="<?php echo $options['tab_number_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current / Completed / On Hover Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number background color of the completed/current/hovered tab.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_number_bkg_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current / Completed / On Hover Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of the completed/current/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_text_color_hover" class="color-field" value="<?php echo $options['tab_text_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current / Completed / On Hover Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the background color of the completed/current/hovered tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_bkg_color_hover" class="color-field" value="<?php echo $options['tab_bkg_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current / Completed / On Hover Tab Border Bottom Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the border color under the tabs of the completed/current/hovered tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_border_bottom_color_hover" class="color-field" value="<?php echo $options['tab_border_bottom_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Arrow Right Color (option applied only for the "Arrow" Tab Layout)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the arrow right color. Usually it is the color of your page background, but you can choose any color.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_before_arrow_color" class="color-field" value="<?php echo $options['tab_before_arrow_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Show number instead of checkmark', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="show_number_checkmark" value="1" <?php checked($options['show_number_checkmark'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-number-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="show_number_checkmark" value="0" <?php checked($options['show_number_checkmark'], 0); ?>>
                    <label class="input-label-button label-button-right" for="show-number-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_adjust_number_position" class="input-field" value="<?php echo $options['tab_adjust_number_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_adjust_checkmark_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_adjust_text_position" class="input-field" value="<?php echo $options['tab_adjust_text_position']; ?>" /></td>
        </tr>
    </tbody>
</table>		
        
        
<table class="form-table argmc-table-style argmc-tab-style argmc-tab-arrow-alt-style">
    <tbody>
        <tr>
            <th>
                <?php _e('Hide The Tails Of The Arrows', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Turn on this option to hide the tails of all arrows.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="tab-arrow-alt-hide-tails-yes" class="input-radio-button hide-tails" data-style="hide-tails" type="radio" name="tab_arrow_alt_hide_tails" value="1" <?php checked($options['tab_arrow_alt_hide_tails'], 1); ?>>
                    <label class="input-label-button label-button-left" for="tab-arrow-alt-hide-tails-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="tab-arrow-alt-hide-tails-no" class="input-radio-button hide-tails" type="radio" data-style="hide-tails-no" name="tab_arrow_alt_hide_tails" value="0" <?php checked($options['tab_arrow_alt_hide_tails'], 0); ?>>
                    <label class="input-label-button label-button-right" for="tab-arrow-alt-hide-tails-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>						
        <tr>
            <th>
                <?php _e('Tabs Orientation - Horizontal/Vertical (available only if <em>"Hide The Tails Of The Arrows"</em> option is turned on)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to display the tabs horizontal or vertical. This option is available only if you hide the tails of the arrows.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="tab-arrow-alt-orientation-horizontal-yes" class="input-radio-button tabs-orientation" type="radio" name="tab_arrow_alt_orientation" value="horizontal" <?php checked($options['tab_arrow_alt_orientation'], 'horizontal'); ?>>
                    <label class="input-label-button label-button-left" for="tab-arrow-alt-orientation-horizontal-yes">
                        <span class="label-button-text"><?php _e('Horizontal', 'argMC'); ?></span>
                    </label>

                    <input id="tab-arrow-alt-orientation-horizontal-no" class="input-radio-button tabs-orientation" type="radio" name="tab_arrow_alt_orientation" value="vertical" <?php checked($options['tab_arrow_alt_orientation'], 'vertical'); ?>>
                    <label class="input-label-button label-button-right" for="tab-arrow-alt-orientation-horizontal-no">
                        <span class="label-button-text"><?php _e('Vertical', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_arrow_alt_text_color" class="color-field" value="<?php echo $options['tab_arrow_alt_text_color']; ?>" /></td>
        </tr>		
        <tr>
            <th>
                <?php _e('Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs background color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_arrow_alt_bkg_color" class="color-field" value="<?php echo $options['tab_arrow_alt_bkg_color']; ?>" /></td>
        </tr>                            
        <tr>
            <th>
                <?php _e('Tab Border Bottom Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the border color under the tabs.', 'argMC'); ?></span>										
            </th>
            <td><input type="text" name="tab_arrow_alt_border_bottom_color" class="color-field" value="<?php echo $options['tab_arrow_alt_border_bottom_color']; ?>" /></td>
        </tr>                           
        <tr>
            <th>
                <?php _e('Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_arrow_alt_number_text_color" class="color-field" value="<?php echo $options['tab_arrow_alt_number_text_color']; ?>" /></td>
        </tr>                           
        <tr>
            <th>
                <?php _e('Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number background color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_arrow_alt_number_bkg_color" class="color-field" value="<?php echo $options['tab_arrow_alt_number_bkg_color']; ?>" /></td>
        </tr>
        
        <tr>
            <th>
                <?php _e('Hide Numbers Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Turn on this option to hide the background color of the numbers. Please remember to change the current/on hover tab number color everytime you turn on/off this option (from below option "Current / On Hover Tab Number Color": add color #000 if numbers have background and color #fff if numbers haven\'t background).', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="tab-arrow-alt-hide-numbers-background-yes" class="input-radio-button hide-number-bkg" type="radio" name="tab_arrow_alt_hide_number_bkg" value="1" <?php checked($options['tab_arrow_alt_hide_number_bkg'], 1); ?>>
                    <label class="input-label-button label-button-left" for="tab-arrow-alt-hide-numbers-background-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="tab-arrow-alt-hide-numbers-background-no" class="input-radio-button hide-number-bkg" type="radio" name="tab_arrow_alt_hide_number_bkg" value="0" <?php checked($options['tab_arrow_alt_hide_number_bkg'], 0); ?>>
                    <label class="input-label-button label-button-right" for="tab-arrow-alt-hide-numbers-background-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>
        
        <tr>
            <th>
                <?php _e('Current / On Hover Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number color of the current/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_arrow_alt_number_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_number_color_hover']; ?>" /></td>
        </tr>                           
        <tr>
            <th>
                <?php _e('Current / On Hover Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number background color of the current/hovered tab.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_arrow_alt_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_number_bkg_color_hover']; ?>" /></td>
        </tr>                         
        <tr>
            <th>
                <?php _e('Visited / On Hover Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of the visited/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_arrow_alt_text_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_text_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current / On Hover Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the background color of the current/hovered tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_arrow_alt_bkg_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_bkg_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Visited Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the background color of the visited tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_arrow_alt_completed_bkg_color" class="color-field" value="<?php echo $options['tab_arrow_alt_completed_bkg_color']; ?>" /></td>
        </tr>                            
        <tr>
            <th>
                <?php _e('Visited / On Hover Tab Border Bottom Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the border color under the tabs of the visited/hovered tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_arrow_alt_border_bottom_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_border_bottom_color_hover']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Arrow Right Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the arrow right color. Usually it is the color of your page background, but you can choose any color.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_arrow_alt_before_arrow_color" class="color-field" value="<?php echo $options['tab_arrow_alt_before_arrow_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Show number instead of checkmark', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="tab-arrow-alt-show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="tab_arrow_alt_show_number_checkmark" value="1" <?php checked($options['tab_arrow_alt_show_number_checkmark'], 1); ?>>
                    <label class="input-label-button label-button-left" for="tab-arrow-alt-show-number-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="tab-arrow-alt-show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="tab_arrow_alt_show_number_checkmark" value="0" <?php checked($options['tab_arrow_alt_show_number_checkmark'], 0); ?>>
                    <label class="input-label-button label-button-right" for="tab-arrow-alt-show-number-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>  
        <tr>
            <th>
                <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_arrow_alt_adjust_number_position" class="input-field" value="<?php echo $options['tab_arrow_alt_adjust_number_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_arrow_alt_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_arrow_alt_adjust_checkmark_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_arrow_alt_adjust_text_position" class="input-field" value="<?php echo $options['tab_arrow_alt_adjust_text_position']; ?>" /></td>
        </tr>
    </tbody>        
</table>        
        
<table class="form-table argmc-table-style argmc-tab-style argmc-tab-progress-bar-style">
    <tbody>
        <tr>
            <th>
                <?php _e('Progress Bar Layout Styles', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the progress bar layout styles.', 'argMC'); ?></span>
            </th>
            <td>
                <select name="tab_progress_template" style="min-width:150px;">
                    <option value="progress-boxed" <?php selected($options['tab_progress_template'], 'progress-boxed', true); ?>><?php _e('Progress Bar Boxed', 'argMC'); ?></option>
                    <option value="progress-not-boxed" <?php selected($options['tab_progress_template'], 'progress-not-boxed', true); ?>><?php _e('Progress Bar Not Boxed', 'argMC'); ?></option>
                </select>
            </td>	
        </tr>
        <tr>
            <th>
                <?php _e('Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_progress_bar_text_color" class="color-field" value="<?php echo $options['tab_progress_bar_text_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_progress_bar_number_text_color" class="color-field" value="<?php echo $options['tab_progress_bar_number_text_color']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number background color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name=tab_progress_bar_number_bkg_color class="color-field" value="<?php echo $options['tab_progress_bar_number_bkg_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Border Top Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the border color above the tabs.', 'argMC'); ?></span>										
            </th>
            <td><input type="text" name="tab_progress_bar_border_bottom_color" class="color-field" value="<?php echo $options['tab_progress_bar_border_bottom_color']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs background color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_progress_bar_bkg_color" class="color-field" value="<?php echo $options['tab_progress_bar_bkg_color']; ?>" /></td>
        </tr>   
        <tr>
            <th>
                <?php _e('Current Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number color of the last visited tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_progress_bar_number_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_number_color_hover']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Current Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number background color of the last visited tab.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_progress_bar_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_number_bkg_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of the last visited/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_progress_bar_text_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_text_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Last Visited / Completed Tab Border Top Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the border color above the tabs of the completed/current tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_progress_bar_border_bottom_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_border_bottom_color_hover']; ?>" /></td>
        </tr>
          <tr>
            <th>
                <?php _e('Completed Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of a completed tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_progress_bar_completed_text_color" class="color-field" value="<?php echo $options['tab_progress_bar_completed_text_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Completed Tab Number Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of a completed tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_progress_bar_completed_number_color" class="color-field" value="<?php echo $options['tab_progress_bar_completed_number_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Completed Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of a completed tab.', 'argMC'); ?></span>																																																		
            </th>
            <td><input type="text" name="tab_progress_bar_completed_number_bkg_color" class="color-field" value="<?php echo $options['tab_progress_bar_completed_number_bkg_color']; ?>" /></td>
        </tr>  
        <tr>
            <th>
                <?php _e('Show number instead of checkmark', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="tab-progress-bar-show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="tab_progress_bar_show_number_checkmark" value="1" <?php checked($options['tab_progress_bar_show_number_checkmark'], 1); ?>>
                    <label class="input-label-button label-button-left" for="tab-progress-bar-show-number-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="tab-progress-bar-show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="tab_progress_bar_show_number_checkmark" value="0" <?php checked($options['tab_progress_bar_show_number_checkmark'], 0); ?>>
                    <label class="input-label-button label-button-right" for="tab-progress-bar-show-number-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr> 
        <tr>
            <th>
                <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_progress_bar_adjust_number_position" class="input-field" value="<?php echo $options['tab_progress_bar_adjust_number_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_progress_bar_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_progress_bar_adjust_checkmark_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_progress_bar_adjust_text_position" class="input-field" value="<?php echo $options['tab_progress_bar_adjust_text_position']; ?>" /></td>
        </tr>        
    </tbody>
</table>

<table class="form-table argmc-table-style argmc-tab-style argmc-tab-outline-style">
    <tbody>
        <tr>
            <th>
                <?php _e('Outline Layout Styles', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the outline layout styles.', 'argMC'); ?></span>
            </th>
            <td>
                <select name="tab_outline_orientation" style="min-width:150px;">
                    <option value="horizontal-text-under" <?php selected($options['tab_outline_orientation'], 'horizontal-text-under', true); ?>><?php _e('Horizontal - Text Under', 'argMC'); ?></option>
                    <option value="horizontal-text-inline" <?php selected($options['tab_outline_orientation'], 'horizontal-text-inline', true); ?>><?php _e('Horizontal - Text Inline', 'argMC'); ?></option>
                    <option value="vertical" <?php selected($options['tab_outline_orientation'], 'vertical', true); ?>><?php _e('Vertical', 'argMC'); ?></option>
                </select>
            </td>	
        </tr>     
        <tr>
            <th>
                <?php _e('Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_text_color" class="color-field" value="<?php echo $options['tab_outline_text_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_outline_number_text_color" class="color-field" value="<?php echo $options['tab_outline_number_text_color']; ?>" /></td>
        </tr> 
         <tr>
            <th>
                <?php _e('Tab Border Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the border color of the tabs.', 'argMC'); ?></span>										
            </th>
            <td><input type="text" name="tab_outline_border_color" class="color-field" value="<?php echo $options['tab_outline_border_color']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of the last visited/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_outline_text_color_hover" class="color-field" value="<?php echo $options['tab_outline_text_color_hover']; ?>" /></td>
        </tr>  
        <tr>
            <th>
                <?php _e('Current Tab Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the background color of the last visited/hovered tab.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_bkg_color_hover" class="color-field" value="<?php echo $options['tab_outline_bkg_color_hover']; ?>" /></td>
        </tr>   
        <tr>
            <th>
                <?php _e('Current Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number color of the last visited/hovered tab.', 'argMC'); ?></span>																																								
            </th>
            <td><input type="text" name="tab_outline_number_color_hover" class="color-field" value="<?php echo $options['tab_outline_number_color_hover']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Current Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number background color of the last visited/hovered tab.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_outline_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_outline_number_bkg_color_hover']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Current Tab Number Border Left Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the border left color of the last visited tab.', 'argMC'); ?></span>																				
            </th>
            <td><input type="text" name="tab_outline_current_border_left" class="color-field" value="<?php echo $options['tab_outline_current_border_left']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Completed Tab Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the text color of the completed tab.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_completed_text_color" class="color-field" value="<?php echo $options['tab_outline_completed_text_color']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Completed Tab Number Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the number color of the completed tab.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_number_color_completed" class="color-field" value="<?php echo $options['tab_outline_number_color_completed']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Completed Tab Number Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the background color of the completed tab.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_number_bkg_color_completed" class="color-field" value="<?php echo $options['tab_outline_number_bkg_color_completed']; ?>" /></td>
        </tr> 
        <tr>
            <th>
                <?php _e('Show number instead of checkmark', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="tab-outline-show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="tab_outline_show_number_checkmark" value="1" <?php checked($options['tab_outline_show_number_checkmark'], 1); ?>>
                    <label class="input-label-button label-button-left" for="tab-outline-show-number-yes">
                        <span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                    </label>

                    <input id="tab-outline-show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="tab_outline_show_number_checkmark" value="0" <?php checked($options['tab_outline_show_number_checkmark'], 0); ?>>
                    <label class="input-label-button label-button-right" for="tab-outline-show-number-no">
                        <span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr> 
        <tr>
            <th>
                <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_adjust_number_position" class="input-field" value="<?php echo $options['tab_outline_adjust_number_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_outline_adjust_checkmark_position']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="tab_outline_adjust_text_position" class="input-field" value="<?php echo $options['tab_outline_adjust_text_position']; ?>" /></td>
        </tr>        
    </tbody>
</table>

<h3><?php _e('Checkout Forms Styles', 'argMC'); ?></h3>

<table class="form-table argmc-table-style">
    <tbody>
        <tr>
            <th>
                <?php _e('Inherit Checkout Forms Styles From Your Theme or Plugin', 'argMC'); ?>
                <span class="argmc-description"><?php _e('If you prefer the plugin checkout forms styles then select the "Plugin" option (the styles will be inherited from the plugin).
                <br>If not, your theme checkout form styles will be inherited.', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="overwrite-woo-styles-yes" class="input-radio-button arg-checkout-option-button" data-style="plugin" type="radio" name="overwrite_woo_styles" value="1" <?php checked($options['overwrite_woo_styles'], 1); ?>>
                    <label class="input-label-button label-button-left" for="overwrite-woo-styles-yes">
                        <span class="label-button-text"><?php _e('Plugin', 'argMC'); ?></span>
                    </label>

                    <input id="overwrite-woo-styles-no" class="input-radio-button arg-checkout-option-button" type="radio" data-style="theme" name="overwrite_woo_styles" value="0" <?php checked($options['overwrite_woo_styles'], 0); ?>>
                    <label class="input-label-button label-button-right" for="overwrite-woo-styles-no">
                        <span class="label-button-text"><?php _e('Theme', 'argMC'); ?></span>
                    </label>																						
                </div>			
            </td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Forms Text Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change forms text color.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_text_color" class="color-field" value="<?php echo $options['woo_text_color']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
               <?php _e('Forms Headings/Table Headings/Labels Color', 'argMC'); ?>
               <span class="argmc-description"><?php _e('Change the color of the labels(used on form fields)/form headings/table headings.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_label_color" class="color-field" value="<?php echo $options['woo_label_color']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Form Fields Border Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change form fields border colors.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_input_border_color" class="color-field" value="<?php echo $options['woo_input_border_color']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Form Fields Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change form fields background colors.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_input_bkg_color" class="color-field" value="<?php echo $options['woo_input_bkg_color']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Form Fields Border Radius', 'argMC'); ?>
                <span class="argmc-description"><?php _e('With this option you can give any form field "rounded corners".', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_field_border_radius" class="input-field" value="<?php echo $options['woo_field_border_radius']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Invalid Form Fields Border Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change border color for invalid form fields.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_invalid_required_field_border" class="color-field" value="<?php echo $options['woo_invalid_required_field_border']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Invalid Form Fields Background', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change background color for invalid form fields.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_invalid_required_field_bkg" class="color-field" value="<?php echo $options['woo_invalid_required_field_bkg']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Validated Form Fields Border', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change border color for validated form fields.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_validated_field_border" class="color-field" value="<?php echo $options['woo_validated_field_border']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Buttons Background Color', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Change the background color of the wizard buttons. Starting with version 1.8 this option is deprecated. Please use "Wizard styles" options instead to change buttons background colors.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_button_bkg_color" class="color-field" value="<?php echo $options['woo_button_bkg_color']; ?>" /></td>
        </tr>
        <tr class="checkout-form-options">
            <th>
                <?php _e('Button Background Color on Login and Coupon Forms', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the background color of Login and Coupon buttons.', 'argMC'); ?></span>
            </th>
            <td><input type="text" name="woo_button_bkg_color_login" class="color-field" value="<?php echo $options['woo_button_bkg_color_login']; ?>" /></td>
        </tr>
    </tbody>
</table>