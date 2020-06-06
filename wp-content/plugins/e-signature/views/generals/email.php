<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php
include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php');

// To default a var, add it to an array
$vars = array(
    'other_form_element', // will default $data['other_form_element']
    'pdf_options',
    'active_campaign_options'
);
$this->default_vals($data, $vars);
?>

<div class="esign-main-tab">

    <a class="mails_link " href="admin.php?page=esign-mails-general"><?php _e('General Option', 'esig'); ?></a> 

    | <a class="mails_link <?php echo $data['link_active']; ?>" href="admin.php?page=esign-email-general"><?php _e('E-mail Sending Options', 'esig'); ?></a>
    
   

</div>	


<div class="esig-mail wrap" id="esig-mail">

    <div class="esig-mail-left">


        <div class="esig-updated" <?php if (empty($data['message'])) echo "style=\"display:none\""; ?>>
            <p><strong><?php echo $data['message']; ?></strong></p>
        </div>

        <div class="error" <?php if (empty($data['error'])) echo "style=\"display:none\""; ?>>
            <p><strong><?php echo $data['error']; ?></strong></p>
        </div>

        <div class="esig-info-box">
<?php _e('This is an OPTIONAL settings page (and it is not required for your plugin to work).  The E-Signature Advanced Email settings will only affect the emails that are sent from WP E-Signature and will NOT affect your overall WordPress site email settings.  Sending from an SMTP is a secondary option for sending signer invite emails if you are currently experiencing issues sending emails.  <a href="https://wordpress.org/plugins/sendgrid-email-delivery-simplified/" target="_blank">SendGrid</a> is another (free) third-party option (which we recommend exploring). ', 'esig'); ?> <a href='https://www.approveme.com/wp-digital-signature-plugin-docs/article/wordpress-smtp-plugin-settings/' target='_blank'><?php _e('Here\'s a Helpful Troubleshooting Article', 'esig'); ?></a>
        </div>


        <div class="esig-settings-wrap">
            <h3><?php _e('E-signature Advanced E-mail Settings', 'esig') ?></h3>


<?php
$esig_options = (array_key_exists('esig_options', $data)) ? $data['esig_options'] : null;
$email_class = new WP_E_Email();
?>

            <form id="esig_settings_form" method="post" action="admin.php?page=esign-email-general">	

                <table class="form-table">

                    <tr valign="top">
                        <th scope="row"><?php _e("Advanced E-mail Settings", 'esig'); ?></th>
                        <td>
                            <input type="checkbox" name="esig_adv_mail_enable" value="yes" <?php if ('yes' == $esig_options['enable']) echo 'checked'; ?> />Enable<br />
                            <span class="esig_info"><?php _e("This checkbox will be used to enable E-signature mail settings", 'easy_wp_smtp'); ?></span>
                        </td>
                    </tr>


                  <!---  <tr valign="top">
                        <th scope="row"><?php _e("From Email Address", 'esig'); ?></th>
                        <td>
                            <input type="text" name="esig_from_email" class="regular-text" placeholder="e.g. john@gmail.com" value="<?php echo esc_attr($esig_options['from_email_field']); ?>"/><br />
                            <span class="esig_info"><?php _e("This email address will be used in the 'From' field.", 'easy_wp_smtp'); ?></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("From Name", 'esig'); ?></th>
                        <td>
                            <input type="text" placeholder="e.g. John Doe" name="esig_from_name" class="regular-text" value="<?php echo esc_attr($esig_options['from_name_field']); ?>"/><br />
                            <span  class="esig_info"><?php _e("This text will be used in the 'FROM' field for your eSignature emails", 'easy_wp_smtp'); ?></span>
                        </td>
                    </tr>--->			
                    <tr class="ad_opt esig_smtp_options">
                        <th><?php _e('SMTP Host', 'esig'); ?></th>
                        <td>
                            <input type='text' name='esig_smtp_host' class="regular-text" placeholder="smtp.gmail.com" value="<?php echo esc_attr($esig_options['smtp_settings']['host']); ?>" /><br />
                            <span class="esig_info"><?php _e("Your mail server", 'esig'); ?></span>
                        </td>
                    </tr>
                    <tr class="ad_opt esig_smtp_options">
                        <th><?php _e('Type of Encryption', 'esig'); ?></th>
                        <td>
                            <label for="esig_smtp_type_encryption_1"><input type="radio" id="esig_smtp_type_encryption_1" name="esig_smtp_type_encryption" value='none' <?php if ('none' == $esig_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('None', 'esig'); ?></label>
                            <label for="esig_smtp_type_encryption_2"><input type="radio" id="esig_smtp_type_encryption_2" name="esig_smtp_type_encryption" value='ssl' <?php if ('ssl' == $esig_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('SSL', 'esig'); ?></label>
                            <label for="esig_smtp_type_encryption_3"><input type="radio" id="esig_smtp_type_encryption_3" name="esig_smtp_type_encryption" value='tls' <?php if ('tls' == $esig_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('TLS', 'esig'); ?></label><br />
                            <span class="esig_info"><?php _e("For most servers SSL is the recommended option", 'easy_wp_smtp'); ?></span>
                        </td>
                    </tr>
                    <tr class="ad_opt esig_smtp_options">
                        <th><?php _e('SMTP Port', 'esig'); ?></th>
                        <td>
                            <input type='text' name='esig_smtp_port' class="regular-text" placeholder="e.g. 465" value="<?php echo esc_attr($esig_options['smtp_settings']['port']); ?>" /><br />
                            <span class="swpsmtp_info"><?php _e("The port to your mail server", 'esig'); ?></span>
                        </td>
                    </tr>
                    <tr class="ad_opt esig_smtp_options">
                        <th><?php _e('SMTP Authentication', 'esig_wp_smtp'); ?></th>
                        <td>
                            
                            <label for="esig_smtp_autentication"><input type="radio" id="esig_smtp_autentication" name="esig_smtp_autentication" value='no' <?php if ('no' == $esig_options['smtp_settings']['autentication']) echo 'checked="checked"'; ?> /> <?php _e('No', 'esig'); ?></label>
                            <label for="esig_smtp_autentication"><input type="radio" id="esig_smtp_autentication" name="esig_smtp_autentication" value='yes' <?php if ('yes' == $esig_options['smtp_settings']['autentication']) echo 'checked="checked"'; ?> /> <?php _e('Yes', 'esig'); ?></label><br />
                            <span class="esig_info"><?php _e("If you select No, the default WordPress php mail function will work and your smtp username & password is not required", 'esig'); ?></span>
                        </td>
                    </tr>
                    <tr class="ad_opt esig_smtp_options">
                        <th><?php _e('SMTP username', 'esig'); ?></th>
                        <td>
                            <input type='text' name='esig_smtp_username' class="regular-text" placeholder="e.g. john@gmail.com" value="<?php echo esc_attr($esig_options['smtp_settings']['username']); ?>" /><br />
                            <span class="esig_info"><?php _e("The username to login to your mail server", 'esig'); ?></span>
                        </td>
                    </tr>
                    <tr class="ad_opt esig_smtp_options">
                        <th><?php _e('SMTP Password', 'esig'); ?></th>
                        <td>
                            <input type='password' name='esig_smtp_password' class="regular-text" placeholder="e.g. Password" value='<?php echo $email_class->esig_mail_get_password(); ?>' /><br />
                            <span class="esig_info"><?php _e("The password to login to your mail server", 'esig'); ?></span>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" id="esig-mail-settings-form-submit" class="button-primary" value="<?php _e('Save Changes', 'esig') ?>" />
                    <input type="hidden" name="esig_mail_form_submit" value="submit" />
<?php wp_nonce_field("esig-mail-settings", 'esig_mail_nonce_name'); ?>
                </p>				
            </form>
        </div>

<?php
if (!empty($data['result'])) {
    wp_enqueue_style("wp-jquery-ui-dialog");
    wp_enqueue_script('jquery-ui-dialog');

    if ($data['result'] == "success") {
        $button_text = "CLOSE";
    } else {
        $button_text = "OK, I'LL TRY AGAIN";
    }
    ?>

            <script type="text/javascript">
                var j = jQuery.noConflict();
                j(document).ready(function () {
                    j("#esig-error-dialog").dialog({
                        dialogClass: 'esig-dialog',
                        height: 500,
                        width: 600,
                        modal: true,
                        buttons: [{
                                text: "<?php echo $button_text; ?>",
                                "ID": 'esig-primary-dgr-btn',
                                click: function () {
                                    j(this).dialog("close");
                                    return false;
                                }
                            }]
                    });
                });
            </script>
    <?php
}
?>

        <div id="esig-error-dialog"  style="display:none">

<?php
if ($data['result'] == "success") {
    echo "<div class='esig-dialog-header'><div class='esig-alert'><p align='center' class='s_logo'><span class='icon-success-check'></span></p></div><h3>" . __('High five... you nailed it!', 'esign') . "</h3></div><p>" . __('Congratulations, your <em>Advanced Emails Settings', 'esign') . "</em> " . __('has been succesfully setup for ', 'esign') . "<span style='color:#0073aa;'>" . esc_attr($esig_options['from_name_field']) . "</span>. </p><p>" . __("That means it's time to party, because anyone you send a document/contract to will now receive emails from: ", "esign") . "<em><span style='color:#0073aa;'>" . esc_attr($esig_options['from_email_field']) . "</span></em></p>";
} else {
    echo "<div class='esig-dialog-header'><div class='esig-alert'><span class='icon-esig-alert'></span></div><h3>" . __('Email Connection troubles...', 'esign') . "</h3></div><p>" . __("I apologize, but we're having trouble connecting to your <em>Email Server</em> (which is required to use this Advanced Email Sending feature).", "esign") . "</p>

<p><strong>" . __('For your site to magically send important signer invite emails from your email address, you will definitely need fix this issue.', 'esign') . "</strong></p>

<p>" . __('You can do 1 of 2 things...', 'esign') . "</p> 

<p>" . __('1. Double check that your email and password are entered correctly', 'esign') . "</p> 
<p>" . __('2. Or you can check out this helpful ', 'esign') . "<a href='https://www.approveme.com/wp-digital-signature-plugin-docs/article/wordpress-smtp-plugin-settings/' target='_blank'>" . __('SMTP Troubleshooting Article', 'esign') . "</a></p>";
}
?>




        </div>

        <div id="esig-test-email" class="esig-settings-wrap">
            <h3><?php _e('Important: Test your SMTP settings below:', 'esig'); ?></h3>
            <form id="esig_test_mail_form" method="post" action="admin.php?page=esign-email-general">					
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e("To", 'esig'); ?>:</th>
                        <td>
                            <input type="text" name="esig_to" class="regular-text"  placeholder="steve@gmail.com" value=""/><br />
                            <span class="esig_info"><?php _e("Enter the email address to recipient", 'esig'); ?></span>
                        </td>
                    </tr>
                    <tr valign="top">

                    <input type="text" name="esig_mail_subject" class="regular-text" hidden value="Re: Testing E-Signature SMTP"/>


                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Message", 'esig'); ?>:</th>
                        <td>
                            <textarea name="esig_mail_message" id="esig_mail_message" rows="5" class="regular-text"></textarea><br />
                            <span  class="esig_info"><?php _e("Write your message", 'esig'); ?></span>
                        </td>
                    </tr>				
                </table>
                <p class="submit">
                    <input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e('Send Test Email', 'esig') ?>" />
                    <input type="hidden" name="esig_test_mail_submit" value="submit" />
<?php wp_nonce_field('esig_test_mail', 'esig_mail_test_nonce_name'); ?>
<?php
if (!empty($data['result']) && $data['result'] != "success") { ?>  
                    
                    <span align="right"> <a href="admin.php?page=esign-systeminfo-about&tab=logs" class="button-primary"> View Error Logs </a> </span> <?php } ?>

    </p>
    </form>

    </div>
    </div>

    <div class = "esig-mail-right esig-smtp-alert esig-top-box">
   <?php _e("Some SMTP servers only allow up to 2, 000 emails to be sent per day. If you expect to exceed this traffic (for all senders) you should use a free transactional email plugin like","esig");?> <a href = "https://wordpress.org/plugins/wpmandrill/?approveme.me"><?php _e("Mandrill WP","esig");?></a>.
    </div>

    <div class = "esig-mail-right">
    <div class = "esig-mail-settings-wrap"><?php _e('
                    <h3> Gmail settings </h3>
                    SMTP Host: smtp.gmail.com<br>
                    Type of Encryption: SSL<br>
                    SMTP Port: 465<br>
                    SMTP Authentication: Yes', 'esign');
    ?>
                        </div>

                    <hr>

                    <div class="esig-mail-settings-wrap"><?php _e('
                    <h3> Yahoo settings </h3>
                    SMTP Host: smtp.mail.yahoo.com<br>
                    Type of Encryption: SSL<br>
                    SMTP Port: 465<br>
                    SMTP Authentication: Yes', 'esign'); ?>
                    </div>
                    <hr>

                    <div class="esig-mail-settings-wrap"><?php _e('
                    <h3> Office 365 settings </h3>
                    SMTP Host: smtp.office365.com<br>
                    Type of Encryption: TLS<br>
                    SMTP Port: 587<br>
                    SMTP Authentication: Yes', 'esign'); ?>
                    </div>
                    <hr>

                    <div class="esig-mail-settings-wrap"><?php _e('
                    <h3> Hotmail settings </h3>
                    SMTP Host: smtp.live.com<br>
                    Type of Encryption: SSL<br>
                    SMTP Port: 465<br>
                    SMTP Authentication: Yes', 'esign'); ?>
                    </div>
                    <hr>
                    <div class="esig-mail-settings-wrap"><?php _e('
                    <h3> Bluehost settings </h3>
                    SMTP Host: <span style="color:blue;">mail.yourdomain.com</span><br>
                    Type of Encryption: SSL<br>
                    SMTP Port: 465<br>
                    SMTP Authentication: Yes', 'esign'); ?>
                    </div>
                    <hr>
                    <div class="esig-mail-settings-wrap">
                        <h3> <?php _e('GoDaddy settings ', 'esign'); ?></h3>
                        <a href="https://support.godaddy.com/help/article/3552/managing-your-email-account-smtp-relays" target="_blank"><?php _e('Click here for instructions', 'esign'); ?></a>

                </div>
        </div>


    </div><!--  #esig-mail .esig-mail -->


