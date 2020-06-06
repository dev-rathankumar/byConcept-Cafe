<?php
include_once("headers.php");

$options = get_option('customerly_settings');
if (isset($options['customerly_text_field_session_token'])) {
    $login =  "https://app.customerly.io/secure/".$options['customerly_text_field_session_token'];
}



?>
<script>
    jQuery(document).ready(function () {
        send_event('Configured');
    });
</script>
<div class="floating-header-section" id="customerly_configured">

    <div class="section-content">
        <div class="section-item">

            <img class="icon-customerly" height="90"
                 src="<?php echo(plugins_url("/assets/img/blue_fill_notification.svg", __FILE__)); ?>">
            <h1>Connected with Customerly</h1>
            <p class="margin-bottom">Your live chat is up and running. Yay 😎 <br> To check your incoming conversation visit your inbox. </p>

            <div class="button-container">
                <a class="button button-start"
                   href="<?php echo $login; ?>"
                   target="_blank"> Go to my inbox</a>

                <button class="button button-hero"
                   onclick="reset()"
                   target="_blank"> Reconfigure</button>
            </div>

        </div>
        <p>Loving Customerly ❤️?  <a
                    href="https://go.customerly.io/wpreview">Rate us ★★★★★</a></p>
    </div>

</div>