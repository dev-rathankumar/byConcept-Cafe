




    
    <?php echo apply_filters("esig-branding-before-content",""); ?>
    
    <div class="esig-settings-wrap">
        <table class="form-table esig-settings-form">

            <tbody>
            <h3><?php _e('Upload Custom Branding to Email Invites', 'esig'); ?></h3>
            <tr> 
                <th> <label for="header_image" id="header_image_label"><?php _e('Header Image', 'esig') ?></label></th>
                <td> <a href="#" id="esig_logo_upload" class="button insert-media add_media"><?php _e('Upload Your Logo', 'esig') ?></a><br />

                    <p>or</p>
                    <span class="description"><?php _e('Enter a URL to an image you want to use instead', 'esig') ?></span>
                    <input type="text" name="esig_branding_header_image" id="esig_branding_header_image" value="<?php echo $data['esig_branding_header_image']; ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><label for="">
                        <input name="esig_document_head_img" id="esig_document_head_img" type="checkbox" value="1" <?php echo $data['esig_document_head_img']; ?>> 
                        <?php _e('Display header image on document signing page', 'esig') ?> </label>

                </td>
            </tr>
           
            <tr>
                <th>&nbsp;</th>
                <td>
                    <?php
                    $alignment = $data['esig_head_img_alignment'];
                    ?>
                    <input type="radio" name="esig_document_head_img_alignment" value="left" <?php
                    if ($alignment == 'left') {
                        echo 'checked';
                    }
                    ?>> Align Left
                    <input type="radio" name="esig_document_head_img_alignment" value="center" <?php
                    if ($alignment == 'center') {
                        echo 'checked';
                    }
                    ?>> Align Center
                    <input type="radio" name="esig_document_head_img_alignment" value="right" <?php
                           if ($alignment == 'right') {
                               echo 'checked';
                           }
                    ?>> Align Right
                </td>
            </tr>
            
            <tr>
                <th> <label for="document_title" > <?php _e("Document Title Alignment", "esig"); ?> </label></th>
                <td>
                    <?php
                           $sender_type = $data['esig_document_title_alignment'];                          ?>
                    <input type="radio" name="esig_document_title_alignment" value="left" <?php
                           if ($sender_type == 'left') {
                               echo 'checked';
                           } if (empty($sender_type)) {
                               echo 'checked';
                           }
                           ?>> Left
                    <input type="radio" name="esig_document_title_alignment" value="center" <?php
                           if ($sender_type == 'center') {
                               echo 'checked';
                           }
                           ?>>Center
                    
                    <input type="radio" name="esig_document_title_alignment" value="right" <?php
                           if ($sender_type == 'right') {
                               echo 'checked';
                           }
                           ?>>Right

                </td>
            </tr>

            <?php
            if (!isset($data['esig_branding_logo_tagline']) && $data['esig_branding_logo_tagline'] == false) {
                $esig_header_tagline = __('Sign Legally Binding Documents using a WordPress website', 'esig');
            } else {
                $esig_header_tagline = $data['esig_branding_logo_tagline'];
            }
            ?>

            <tr>
                <th><label for="logo_tagline" id="logo_tagline_label"><?php _e('Logo Tagline', 'esig') ?></label></th>
                <td><input type="text" name="esig_branding_logo_tagline" id="esig_branding_logo_tagline" value="<?php echo $esig_header_tagline; ?>" class="regular-text" />
                    <span class="description"><?php _e('Enter the tagline text that will appear beneath your logo in the signer invite emails', 'esig') ?></span></td>
            </tr>

            <?php
            if (!isset($data['esig_branding_footer_text_headline']) && $data['esig_branding_footer_text_headline'] == false) {
                $esig_footer_head = __('What is WP E-Signature?', 'esig');
            } else {
                $esig_footer_head = $data['esig_branding_footer_text_headline'];
            }
            ?>

            <tr>
                <th><label for="footer_text_headline" id="footer_text_headline_label"><?php _e('Footer Text Headline', 'esig') ?></label></th>
                <td><input type="text" <?php echo $data['esig_extra_attr']; ?> name="esig_branding_footer_text_headline" id="esig_branding_footer_text_headline" size="30" value="<?php echo $esig_footer_head; ?>"  class="regular-text" />

                    <span class="description"><?php _e('Enter the headline text that will appear above the footer text in the signer invite emails.', 'esig') ?></span></td>
            </tr>

            <?php
            if (!isset($data['esig_branding_email_footer_text']) && $data['esig_branding_email_footer_text'] == false) {
                $esig_footer_text = __('WP E-Signature by Approve Me is the
                                fastest way to sign and send documents
                                using WordPress. Save a tree (and a
                                stamp).  Instead of printing, signing
                                and uploading your contract, the
                                document signing process is completed
                                using your WordPress website. You have
                                full control over your data - it never
                                leaves your server. <br>
                                <b>No monthly fees</b> - <b>Easy to use
                                  WordPress plugin.</b><a style="color:#368bc6;text-decoration:none" href="https://www.approveme.com/wp-digital-e-signature/?ref=1" target="_blank"> Learn more</a> ', 'esig');
            } else {
                $esig_footer_text = $data['esig_branding_email_footer_text'];
            }
            ?>

            <tr>
                <th><label for="email_footer_text"><?php _e('E-mail Footer Text', 'esig') ?></label></th>
                <td><span class="esig-description"> <?php _e('The text to appear in the footer of signer invite emails.', 'esig') ?></span>
                    <textarea <?php echo $data['esig_extra_attr']; ?> id="esig_branding_footer_text" name="esig_branding_email_footer_text"  rows="5" cols="100%"><?php echo $esig_footer_text; ?></textarea></td>
            </tr>

            <tr>
                <th>&nbsp;</th>
                <td><label for="">
                        <input name="esig_brandhing_disable" id="esig_brandhing_disable" type="checkbox" value="1" <?php echo $data['esig_brandhing_disable']; ?>> <?php _e('Disable footer text', 'esig') ?></label>
                    <br>
                    <span class="description"><?php _e('If the box is checked, the footer text and header will not displayed.', 'esig') ?></span>
                </td>
            </tr>



            <tr>
                <th> <label for="email_sender" > <?php _e("E-mail Sender", "esig"); ?> </label></th>
                <td>
                    <?php
                           $sender_type = $data['esig_email_invitation_sender_checked'];
                           ?>
                    <input type="radio" name="esig_email_invitation_sender_checked" value="owner" <?php
                           if ($sender_type == 'owner') {
                               echo 'checked';
                           } if (empty($sender_type)) {
                               echo 'checked';
                           }
                           ?>> Super admin first name last name
                    <input type="radio" name="esig_email_invitation_sender_checked" value="company" <?php
                           if ($sender_type == 'company') {
                               echo 'checked';
                           }
                           ?>>Organization's Name

                </td>
            </tr>

            <tr>

                <th><label for="email_button"><?php _e("Button Color", "esig"); ?></label></th>
                <td>

                    <input name="esig_button_background" id="esig_button_background" type="textbox" value="<?php
                           if ($data['esig_branding_back_color']) {

                               echo $data['esig_branding_back_color'];
                           } else {
                               echo '#0083C5';
                           }
                           ?>" class="esig-color-picker" /> </label>

                </td>
            </tr>

            </tbody>
        </table>
    </div>
<?php
if (array_key_exists("esig_branding_more_content", $data)) {
    echo $data['esig_branding_more_content'];
}
?>



    <table class="form-table esig-settings-form esig-settings-wrap" id="esig-cover-page-section" <?php if ($data['esig_branding_header_image'] == "") {
    echo 'style="display:none;"';
} ?>>
        <tbody>
            <tr>
                <th><label for="esig_cover_text"><?php _e('Document Cover Page', 'esig') ?></label></th>
                <td><label for="">
                        <input name="esig_cover_page" id="esig_cover_page" type="checkbox" value="1" <?php echo $data['esig_cover_page']; ?>> <?php _e('Create a cover page with my logo and document info', 'esig') ?></label>

                </td>
            </tr>
        </tbody>
    </table>

