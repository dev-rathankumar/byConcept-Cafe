<h2 class="argmc-top-heading"><?php _e('Wizard Steps Options', 'argMC'); ?></h2>
<p class="argmc-top-text"><?php _e('These options refer to your checkout steps and all their content can be found here:', 'argMC'); ?></p>

<table class="form-table argmc-table-steps">
    <thead>
        <tr>
            <th><?php _e('Step Name', 'argMC'); ?></th>
            <th><?php _e('Template', 'argMC'); ?></th>
            <th><?php _e('Show/Hide Step', 'argMC'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><input type="text" name="steps[login][text]" value="<?php echo $options['steps']['login']['text']; ?>" /></td>
            <td><input type="text" name="steps[login][template]" readonly value="{login_form}" /></td>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-login" class="input-radio-button" type="radio" name="show_login" value="1" <?php checked($options['show_login'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-login">
                        <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                    </label>

                    <input id="hide-login" class="input-radio-button" type="radio" name="show_login" value="0" <?php checked($options['show_login'], 0); ?>>
                    <label class="input-label-button label-button-right" for="hide-login">
                        <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><input type="text" name="steps[coupon][text]" value="<?php echo $options['steps']['coupon']['text']; ?>" /></td>
            <td><input type="text" name="steps[coupon][template]" readonly value="{coupon_form}" /></td>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-coupon" class="input-radio-button" type="radio" name="show_coupon" value="1" <?php checked($options['show_coupon'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-coupon">
                         <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                    </label>

                    <input id="hide-coupon" class="input-radio-button" type="radio" name="show_coupon" value="0" <?php checked($options['show_coupon'], 0); ?>>
                    <label class="input-label-button label-button-right" for="hide-coupon">
                        <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><input type="text" name="steps[billing][text]" value="<?php echo $options['steps']['billing']['text']; ?>" /></td>
            <td><input type="text" name="steps[billing][template]" readonly value="{billing_form}" /></td>
            <td></td>
        </tr>
        <tr>
            <td><input type="text" name="steps[shipping][text]" value="<?php echo $options['steps']['shipping']['text']; ?>" /></td>
            <td><input type="text" name="steps[shipping][template]" readonly value="{shipping_form}" /></td>
            <td></td>
        </tr>
        <tr>
            <td><input type="text" name="steps[order][text]" value="<?php echo $options['steps']['order']['text']; ?>" /></td>
            <td><input type="text" name="steps[order][template]" readonly value="{order_details}" /></td>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-order" class="input-radio-button" type="radio" name="show_order" value="1" <?php checked($options['show_order'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-order">
                        <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                    </label>

                    <input id="hide-order" class="input-radio-button" type="radio" name="show_order" value="0" <?php checked($options['show_order'], 0); ?>>
                    <label class="input-label-button label-button-right" for="hide-order">
                        <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><input type="text" name="steps[payment][text]" value="<?php echo $options['steps']['payment']['text']; ?>" /></td>
            <td><input type="text" name="steps[payment][template]" readonly value="{payment_details}" /></td>
            <td></td>
        </tr>
        <tr>
            <td><input type="text" name="steps[order_review][text]" value="<?php echo $options['steps']['order_review']['text']; ?>" /></td>
            <td><input type="text" name="steps[order_review][template]" readonly value="{order_review}" /></td>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-order-review" class="input-radio-button" type="radio" name="show_order_review" value="1" <?php checked($options['show_order_review'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-order-review">
                        <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                    </label>

                    <input id="hide-order-review" class="input-radio-button" type="radio" name="show_order_review" value="0" <?php checked($options['show_order_review'], 0); ?>>
                    <label class="input-label-button label-button-right" for="hide-order-review">
                        <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                    </label>
                </div>
            </td>
        </tr>									
                                                                        
    </tbody>
</table>

<table class="form-table combine-tabs-table">
    <tbody>
        <tr>
            <th>
                <?php _e('Register Form', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to show the registration form on the login step. On the login step will be a single content area with two sections(login form and registration form), each associated with a heading.</br>', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-register" class="input-radio-button" type="radio" name="show_register" value="1" <?php checked($options['show_register'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-register">
                        <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                    </label>

                    <input id="hide-register" class="input-radio-button" type="radio" name="show_register" value="0" <?php checked($options['show_register'], 0); ?>>
                    <label class="input-label-button label-button-right" for="hide-register">
                        <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                    </label>
                </div>
            </td>	
        </tr>
        <tr>
            <th>
                <?php _e('Login & Register Layouts', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the login & register layouts(1. Register Switched with Login - The user can click on headings to swap between content that is separated into logical sections; 2. Register on Right Side).', 'argMC'); ?></span>
            </th>
            <td>
                <select style="width: 210px;" name="login_layout" class="argmc-login-layout">
                    <option value="register-switched-with-login" <?php selected($options['login_layout'], 'register-switched-with-login', true); ?>><?php _e('Register Switched with Login', 'argMC'); ?></option>										
                    <option value="register-on-right-side" <?php selected($options['login_layout'], 'register-on-right-side', true); ?>><?php _e('Register on Right Side', 'argMC'); ?></option>
                </select>
            </td>	
        </tr>
        <tr>
            <th>
                <?php _e('Login & Register Top Message', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option if you want to show a top message above the login/register sections (this option will apply only if you show the registration form on the login step).', 'argMC'); ?></span>
            </th>
            <td><textarea name="login_register_top_message" class="input-field"><?php echo $options['login_register_top_message']; ?></textarea></td>
        </tr>
        
        <tr>
            <th>
                <?php _e('Login Heading', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the login heading. This option will apply only if you show the registration form on the login step.', 'argMC'); ?></span>
            </th>
            <td><input style="width: 210px;" type="text" name="login_heading" class="input-field" value="<?php echo $options['login_heading']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Register Heading', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to change the register heading. This option will apply only if you show the registration form on the login step.', 'argMC'); ?></span>
            </th>
            <td><input style="width: 210px;" type="text" name="register_heading" class="input-field" value="<?php echo $options['register_heading']; ?>" /></td>
        </tr>
        <tr>
            <th>
                <?php _e('Force Users to Login or Register', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option if you want to force users to login/register before navigate to the next step (this option will apply only if you enter an error message and show the registration form on the login step). Leave the box empty and this option will have no effect.', 'argMC'); ?></span>
            </th>
            <td><textarea name="force_login_message" class="input-field"><?php echo $options['force_login_message']; ?></textarea></td>
        </tr>							
    </tbody>
</table>

<table class="form-table combine-tabs-table">
    <tbody>
        <tr>
            <th>
                <?php _e('Additional Information', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to hide additional information block from <br> the shipping section.</br>', 'argMC'); ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show-additional-information" class="input-radio-button" type="radio" name="show_additional_information" value="1" <?php checked($options['show_additional_information'], 1); ?>>
                    <label class="input-label-button label-button-left" for="show-additional-information">
                        <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                    </label>

                    <input id="hide-additional-information" class="input-radio-button" type="radio" name="show_additional_information" value="0" <?php checked($options['show_additional_information'], 0); ?>>
                    <label class="input-label-button label-button-right" for="hide-additional-information">
                        <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                    </label>
                </div>
            </td>	
        </tr>								
    </tbody>
</table>

<table class="form-table combine-tabs-table">
    <tbody>
        <tr>
            <th>
                <?php _e('Move the Coupon Form to Another Step', 'argMC'); ?>
                <span class="argmc-description"><?php _e('Use this option to move the coupon form on a different step. This option only applies if the coupon step is hidden. Please <b>read more</b> about this option in the documentation before using it.', 'argMC'); ?></span>									
            </th>								
            <td>
                <select style="width: 210px;" name="coupon_position" class="argmc-tabs-layout">
                    <option value="default" <?php selected($options['coupon_position'], 'default', true); ?>><?php _e('Default - Coupon Step', 'argMC'); ?></option>
                    <option value="before-billing-section" <?php selected($options['coupon_position'], 'before-billing-section', true); ?>><?php _e('Before Billing Section', 'argMC'); ?></option>
                    <option value="after-shipping-section" <?php selected($options['coupon_position'], 'after-shipping-section', true); ?>><?php _e('After Shipping Section', 'argMC'); ?></option>
                    <option value="after-additional-fields" <?php selected($options['coupon_position'], 'after-additional-fields', true); ?>><?php _e('After Additional Fields', 'argMC'); ?></option>
                    <option value="before-order-review-table" <?php selected($options['coupon_position'], 'before-order-review-table', true); ?>><?php _e('Before Order Review Table', 'argMC'); ?></option>
                    <option value="after-order-review-table" <?php selected($options['coupon_position'], 'after-order-review-table', true); ?>><?php _e('After Order Review Table', 'argMC'); ?></option>
                    <option value="before-payment" <?php selected($options['coupon_position'], 'before-payment', true); ?>><?php _e('Before Payment Methods', 'argMC'); ?></option>
                    <option value="after-payment" <?php selected($options['coupon_position'], 'after-payment', true); ?>><?php _e('After Payment Methods', 'argMC'); ?></option>
                </select>
            </td>
        </tr>
    </tbody>
</table>
<table class="form-table combine-tabs-table">
    <tbody>
        <tr class="first-row">
            <th>
                <?php _e('Combine Billing and Shipping Steps?', 'argMC'); ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="merge-billing-shipping-yes" class="input-radio-button" type="radio" name="merge_billing_shipping" value="1" <?php checked($options['merge_billing_shipping'], 1); ?>>
                    <label class="input-label-button label-button-left" for="merge-billing-shipping-yes">
                       <span class="label-button-text"><?php _e('Yes', 'argMC'); ?></span>
                    </label>

                    <input id="merge-billing-shipping-no" class="input-radio-button" type="radio" name="merge_billing_shipping" value="0" <?php checked($options['merge_billing_shipping'], 0); ?>>
                    <label class="input-label-button label-button-right" for="merge-billing-shipping-no">
                        <span class="label-button-text"><?php _e('No', 'argMC'); ?></span>
                    </label>
                </div>
            </td>	
        </tr>									
        <tr class="second-row">
            <td colspan="2">
                <div class="combine-tables-step-name"><?php _e('If so, define your new step name:', 'argMC'); ?></div>
                <input type="text" name="steps[billing_shipping][text]" value="<?php echo $options['steps']['billing_shipping']['text']; ?>" />
                <input type="text" name="steps[billing_shipping][template]" readonly value="{billing_form} {shipping_form}" />
            </td>
        </tr>
    </tbody>
</table>

<table class="form-table combine-tabs-table">
    <tbody>
        <tr class="first-row">
            <th>
                <?php _e('Combine Payment and Order Details Steps?', 'argMC'); ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="merge-order-payment-yes" class="input-radio-button" type="radio" name="merge_order_payment" value="1" <?php checked($options['merge_order_payment'], 1); ?>>
                    <label class="input-label-button label-button-left" for="merge-order-payment-yes">
                       <span class="label-button-text"><?php _e('Yes', 'argMC'); ?></span>
                    </label>

                    <input id="merge-order-payment-no" class="input-radio-button" type="radio" name="merge_order_payment" value="0" <?php checked($options['merge_order_payment'], 0); ?>>
                    <label class="input-label-button label-button-right" for="merge-order-payment-no">
                        <span class="label-button-text"><?php _e('No', 'argMC'); ?></span>
                    </label>
                </div>
            </td>	
        </tr>									
        <tr class="second-row">
            <td colspan="2">
                <div class="combine-tables-step-name"><?php _e('If so, define your new step name:', 'argMC'); ?></div>
                <input type="text" name="steps[order_payment][text]" value="<?php echo $options['steps']['order_payment']['text']; ?>" />
                <input type="text" name="steps[order_payment][template]" readonly value="{order_details} {payment_details}" />
            </td>
        </tr>				
    </tbody>
</table>

<table class="form-table combine-tabs-table">
    <tbody>
    
    <tr>
        <th>
            <?php _e('Product Thumbnail on Order Table', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this option to show/hide product thumbnail in the checkout order table.', 'argMC'); ?></span>
        </th>
        <td>
            <div class="radio-buttons-wrapper">
                <input id="show-product-image" class="input-radio-button" type="radio" name="show_product_image" value="1" <?php checked($options['show_product_image'], 1); ?>>
                <label class="input-label-button label-button-left" for="show-product-image">
                    <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                </label>

                <input id="hide-product-image" class="input-radio-button" type="radio" name="show_product_image" value="0" <?php checked($options['show_product_image'], 0); ?>>
                <label class="input-label-button label-button-right" for="hide-product-image">
                    <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                </label>
            </div>
        </td>	
    </tr>	
        
    <tr>
        <th>
            <?php _e('Customer Details Review after the Payment Methods', 'argMC'); ?>
            <span class="argmc-description"><?php _e('Use this option to show customer details review (email, phone, addresses) after the payment methods.', 'argMC'); ?></span>
        </th>
        <td>
            <div class="radio-buttons-wrapper">
                <input id="show-customer-details-review" class="input-radio-button" type="radio" name="show_customer_details_review" value="1" <?php checked($options['show_customer_details_review'], 1); ?>>
                <label class="input-label-button label-button-left" for="show-customer-details-review">
                    <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                </label>

                <input id="hide-customer-details-review" class="input-radio-button" type="radio" name="show_customer_details_review" value="0" <?php checked($options['show_customer_details_review'], 0); ?>>
                <label class="input-label-button label-button-right" for="hide-customer-details-review">
                    <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                </label>
            </div>
        </td>	
    </tr>							

    <tr>
        <th>
            <?php _e('Order Table on Order Review Step', 'argMC'); ?>
            <span class="argmc-description"><?php _e(' Use this option to show/hide order review table on the order review step. This option works only if you decide to <strong>hide the order step</strong> from the first section using the "Show/Hide Step" options.', 'argMC'); ?></span>
        </th>
        <td>
            <div class="radio-buttons-wrapper">
                <input id="show-order-review-table" class="input-radio-button" type="radio" name="show_order_review_table" value="1" <?php checked($options['show_order_review_table'], 1); ?>>
                <label class="input-label-button label-button-left" for="show-order-review-table">
                    <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                </label>

                <input id="hide-order-review-table" class="input-radio-button" type="radio" name="show_order_review_table" value="0" <?php checked($options['show_order_review_table'], 0); ?>>
                <label class="input-label-button label-button-right" for="hide-order-review-table">
                    <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                </label>
            </div>
        </td>	
    </tr>
                                            
    </tbody>
</table>