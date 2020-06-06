jQuery(document).ready(function () {
    var searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('appid') == true) {
        var appid = searchParams.get('appid');
        save_main_options_ajax();
    }
    replaceFooter();

});
function send_event(event_name, event_value){

    try {
        customerly("event", "WP "+ event_name);
    }
    catch (err) {
        console.log("We have got an error", err);
    }

    try {
        ga('send', 'WordPress plugin', event_name, '', event_value);

    }
    catch (err) {
        console.log("We have got an error", err);
    }
    try {
         fbq('trackCustom', event_name, {email: jQuery('#email').val()});
    }
    catch (err) {
        console.log("We have got an error", err);
    }
}
function reset(){
    send_event('Reset Configuration', 0);
    jQuery('#appID').val("");
    jQuery('#sessionToken').val("");
    jQuery('#appkey').val("");
    save_main_options_ajax();
}
function show_login(){
    send_event('Show Login', 0);
    jQuery('.customerly_login').slideDown();
    jQuery('.customerly_register').slideUp();
}

function show_register(){
    send_event('Show Register', 0);
    jQuery('.customerly_register').slideDown();
    jQuery('.customerly_login').slideUp();
}
function login() {

    if (jQuery('#loginpassword').val().length < 6){
        show_error("login","Please insert your Password");
        return;
    }

    jQuery('#login-button').hide();
    jQuery('#login-loader').show();


    var data = JSON.stringify({
        email : jQuery('#loginemail').val(),
        password : jQuery('#loginpassword').val(),
    });

    jQuery.post({
        url: 'https://app.customerly.io/backend_api/v1/security/wordpress-login',
        type: 'POST',
        data: data,
        success: function (data) {

            send_event('Login', 0);

            if (data.error != undefined){
                show_error("login",data.error.message);
                jQuery('#login-button').show();
                jQuery('#login-loader').hide();
                return;
            }


            var token = data.data.token;
            jQuery('#sessionToken').val(token);
            elaborate_available_apps(data.data.apps);

            jQuery('.customerly_app_select').slideDown();
            jQuery('.customerly_login').slideUp();


        }
    });

}

function select_app(appid, token){
    jQuery('#appID').val(appid);
    jQuery('#appkey').val(token);
    save_main_options_ajax();
}
function elaborate_available_apps(apps){

    //IF the account has just one app, I'll select it automatically
    if (Object.entries(apps).length == 1){
        var key = Object.keys(apps)[0];
        var app = apps[key];
        select_app(app.app_id, app.access_token);
        return;
    }else{
        for (const [key, value] of Object.entries(apps)) {
            var app = value;
            jQuery('#app_container').append('<div class="app-container" onclick="select_app(\''+key+'\',\''+app.access_token+'\');">\n' +
                '<h4 class="app-name">'+ app.app_name+' <span class="app-id">'+app.app_id+'</span></h4>\n' +
                '</div>');
        }
    }

}
function show_error(position, message){

    if (position == 'login'){
        jQuery('#error_message_login').html(message);
        jQuery('#error_message_login').slideDown();

        setTimeout(function () {
            jQuery('#error_message_login').html("").slideUp();
        },10000);
    }else{
        jQuery('#error_message').html(message);
        jQuery('#error_message').slideDown();

        setTimeout(function () {
            jQuery('#error_message').html("").slideUp();
        },10000);
    }

}
function register_account() {


    if (jQuery('#app_name').val().length < 3){
        show_error("register","Please add a Project Name");
        return;
    }
    if (jQuery('#password').val().length < 6){
        show_error("register", "Please add at least 6 char to the Password");
        return;
    }

    jQuery('#register-button').hide();
    jQuery('#register-loader').show();

    var data = JSON.stringify({
        email : jQuery('#email').val(),
        submission : {
            extra : {
                utm_source : 'wordpress',
                utm_campaign: 'plugin',
                ref: 'lucamicheli'
            }
        },
        app : {
            name : jQuery('#app_name').val(),
            installed_domain : jQuery('#domain').val(),
            widget_position : 1,
            extra : {
                utm_source : 'wordpress',
                utm_campaign: 'plugin',
                ref: 'lucamicheli'
            }},
        account : {
            name : jQuery('#name').val(),
            password : jQuery('#password').val(),
            marketing : jQuery('#marketing:checkbox:checked').length > 0,
            extra : {
                utm_source : 'wordpress',
                utm_campaign: 'plugin',
                ref: 'lucamicheli'
            }
        }
    });


    jQuery.post({
        url: 'https://app.customerly.io/backend_api/v1/security/wordpress-register',
        type: 'POST',
        data: data,
        success: function (data) {

            if (data.error != undefined){
                show_error("register",data.error.message);
                jQuery('#register-button').show();
                jQuery('#register-loader').hide();
                return;
            }
            var token = data.data.token;
            jQuery('#sessionToken').val(token);
            elaborate_available_apps(data.data.apps);

            jQuery('.customerly_app_select').slideDown();
            jQuery('.customerly_register').slideUp();
        }
    });

}

function save_main_options_ajax() {
     jQuery('#customerlySettings').submit();
}
function replaceFooter() {
    jQuery('#footer-upgrade').hide();
    jQuery("#footer-left").html('Do you like <strong>Customerly</strong>? Please leave us a <a href="https://go.customerly.io/wpreview" target="_blank">★★★★★ review</a>. We appreciate your support!');
}
