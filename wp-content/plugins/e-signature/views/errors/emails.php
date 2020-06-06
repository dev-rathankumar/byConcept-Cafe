 <script type="text/javascript">
                var j = jQuery.noConflict();
                j(document).ready(function () {
                    j("#esig-error-dialog").dialog({
                        dialogClass: 'esig-dialog',
                        height: 500,
                        width: 600,
                        modal: true,
                        buttons: [{
                                text: "OK,TAKE ME TO MY EMAIL SETTINGS PAGE",
                                "ID": 'esig-primary-dgr-btn',
                                click: function () {
                                    j(this).dialog("close");
                                    window.location ="admin.php?page=esign-email-general";
                                    return false;
                                }
                            }]
                    });
                });
            </script>
            
            
            <div id="esig-error-dialog"  style="display:none">

<?php


    echo "<div class='esig-dialog-header'><div class='esig-alert'><span class='icon-esig-alert'></span></div><h3>" . __('Email Connection troubles...', 'esign') . "</h3></div><p>" . __("I apologize, but we're having trouble connecting to your <em>Email Server</em> (which is required to use this Advanced Email Sending feature).", "esign") . "</p>

<p><strong>" . __('For your site to magically send important signer invite emails from your email address, you will definitely need fix this issue.', 'esign') . "</strong></p>

<p>" . __('You can do 1 of 2 things...', 'esign') . "</p> 

<p>" . __('1. Double check that your email and password are entered correctly', 'esign') . "</p> 
<p>" . __('2. Or you can check out this helpful ', 'esign') . "<a href='https://www.approveme.com/wp-digital-signature-plugin-docs/article/wordpress-smtp-plugin-settings/' target='_blank'>" . __('SMTP Troubleshooting Article', 'esign') . "</a></p>";

?>




        </div>

