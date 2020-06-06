<?php
include_once("headers.php");
$current_user = wp_get_current_user();
$blogName = get_bloginfo('name');
$email = $current_user->user_email;
$domain = get_site_url();
?>
<div class="floating-header-section" >
    <div class="section-content">
        <div class="section-item">

            <img class="icon-customerly" height="90"
                 src="<?php echo(plugins_url("/assets/img/blue_fill_notification.svg", __FILE__)); ?>">
            <h1>Connect with Customerly</h1>
            <p>To configure your Live Chat you need to create a Free Customerly account.</p>
            <div class="customerly_register">

                <div style="margin: 10px 0">
                    <input class="input-field" type="text" placeholder="Your Name..." name="name" id="name" required="required"
                           value="<?php echo($current_user->first_name);?>"/>

                    <input class="input-field" placeholder="Project Name..." type="text" name="app_name" id="app_name" required="required"
                           value="<?php echo($blogName);?>"/>

                    <input class="input-field" placeholder="Email..." type="text" name="email" id="email" required="required"
                           value="<?php echo($current_user->user_email);?>"/>

                    <input value="<?php echo($domain); ?>" type="hidden" id="domain"/>
                    <input class="input-field" placeholder="Password..." type="password" name="password" id="password" required="required"
                           value=""/>

                    <label style="color: red; display: none" id="error_message"></label>
                </div>


                <label style="margin: 10px"><input type="checkbox" name="marketing" id="marketing"/> Receive once a month useful tutorials to improve your marketing results.</label>

                <div class="cta-container">
                    <div id="register-loader" class="lds-ring" style="display: none"><div></div><div></div><div></div><div></div></div>
                    <input type="submit" name="submit" id="register-button" class="button button-start" onclick="register_account();"
                           value="Register and Install Live Chat"/>
                </div>


                <div style="margin-top: 10px;font-size: 10px;color: gray;" class="row text-center margin-top-5 text-sm-center">By continuing, you agree to
                    the
                    <a href="https://www.customerly.io/terms-of-use" target="_blank">Terms of Service
                    </a>
                    and
                    <a href="https://www.customerly.io/privacy" target="_blank">Privacy Policy
                    </a>
                </div>

            </div>
            <div class="customerly_login" style="display: none">

                <div style="margin: 10px 0">

                    <input class="input-field" placeholder="Email..." type="text" name="loginemail" id="loginemail" required="required"
                    />

                    <input value="<?php echo($domain); ?>" type="hidden" id="domain"/>
                    <input class="input-field" placeholder="Password..." type="password" name="loginpassword" id="loginpassword" required="required"
                           value=""/>
                    <label style="color: red; display: none" id="error_message_login"></label>
                </div>

                <div class="cta-container">
                    <div id="login-loader" class="lds-ring" style="display: none"><div></div><div></div><div></div><div></div></div>
                    <input type="submit" name="submit" id="login-button" class="button button-start" onclick="login();"
                           value="Login and Install Live Chat"/>
                </div>


            </div>
            <div class="customerly_app_select" style="display: none;">

                <div id="app_container">

                </div>

            </div>
        </div>
        <div class="customerly_register" style="margin: 20px"> Already have an account?  <a onclick="show_login();" style="cursor: pointer"> Login</a></div>
        <div class="customerly_login" style="margin: 20px; display: none;"> Need an account?  <a onclick="show_register();" style="cursor: pointer"> Register</a></div>
    </div>
</div>