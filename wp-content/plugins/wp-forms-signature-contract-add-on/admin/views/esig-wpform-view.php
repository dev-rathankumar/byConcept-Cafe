<?php

$wpform_settings = new WPForms_Builder_Panel_Settings();
echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-esignature">';
echo '<div class="wpforms-panel-content-section-title">';
_e('E-Signature Automation', 'wpforms');
echo '</div>';

wpforms_panel_field(
        'text', 'settings', 'signer_name', $wpform_settings->form_data, __('Signer Name', 'wpforms'), array('default' => '',
            'tooltip'=>'Select the name field from your Wpform. This field is what the signers full name will be on their WP E-Signature contract.',
            'placeholder'=>'Signer Name','smarttags'  => array('type'   => 'fields','fields' => 'name,email,text',))
);


wpforms_panel_field(
        'text', 'settings', 'signer_email', $wpform_settings->form_data, __('Signer Email', 'wpforms'), array('default' => '',
            'tooltip'=>'Select the email field from your Wpform. This field is what the signers email will be on their WP E-Signature contract.',
            'placeholder'=>'Signer Email','smarttags'  => array('type'   => 'fields','fields' => 'email',))
);

wpforms_panel_field(
    'select', 'settings', 'signing_logic', $wpform_settings->form_data, __('Signing Logic', 'wpforms'), array(
    'default' => '',  
    'options' => array(
        'redirect' => __('Redirect user to Contract/Agreement after Submission', 'wpforms'),
        'email' => __('Send User an Email Requesting their Signature after Submission', 'wpforms'),
    ),
    )
);



wpforms_panel_field(
    'select', 'settings', 'select_sad', $wpform_settings->form_data, __('Select stand alone document', 'wpforms'), array(
    'default' => '',
    'tooltip'=> 'It is a required field. Please select an agreement which will e-mail/redirect after submission of your Wpform',    
    'after'=>'<span> If you would like to you can <a href="edit.php?post_type=esign&amp;page=esign-add-document&amp;esig_type=sad"> Create new document</a></span>',
    'options' => $this->get_sad_documents(),
    )
        
);

wpforms_panel_field(
     'select', 'settings', 'underline_data', $wpform_settings->form_data, __('', 'wpforms'), array(
    'default' => '',   
    'options' => array(
        'underline' => __('Underline the data That was submitted from this Contact form', 'wpforms'),
        'not_under' => __('Do not underline the data that was submitted from the Contact Form', 'wpforms'),
    ),
    )
);



wpforms_panel_field(
        'checkbox', 'settings', 'enabling_signing_reminder', $wpform_settings->form_data, __('Enabling signing reminder email.', 'wpforms')
);

wpforms_panel_field(
    'select', 'settings', 'reminder_email', $wpform_settings->form_data, __('Send the reminder email to the signer in ', 'wpforms'), array(
    'default' => '',
    'options' => $this->get_days(),
    )
);
wpforms_panel_field(
    'select', 'settings', 'first_reminder_send', $wpform_settings->form_data, __('After the first Reminder send reminder every', 'wpforms'), array(
    'default' => '',
    'options' => $this->get_days(),
    )
);
wpforms_panel_field(
    'select', 'settings', 'expire_reminder', $wpform_settings->form_data, __('After the first Reminder send reminder every', 'wpforms'), array(
    'default' => '',
    'options' => $this->get_days(),
    )
);

echo '</div>';

?>

