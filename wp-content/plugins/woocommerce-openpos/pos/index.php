<?php
    global $op_in_pos_screen;

    $op_in_pos_screen = true;

    $base_dir = dirname(dirname(dirname(dirname(__DIR__))));

    # $base_dir = dirname(__DIR__);  // uncommnent this line if you want change pos address to https://yoursite-address.com/pos/


    require_once ($base_dir.'/wp-load.php');
    global $OPENPOS_SETTING;
    global $OPENPOS_CORE;

    $lang = $OPENPOS_SETTING->get_option('pos_language','openpos_pos');

    if(!$lang || $lang == '_auto')
    {

        $lang = false;
    }
    $pos_url =  OPENPOS_URL.'/pos/';

    # $pos_url =  'https://yoursite-address.com/pos/';  // uncomment and change this to https://yoursite-address.com/pos/ if you want post to webroot
    $plugin_info = $OPENPOS_CORE->getPluginInfo();

?>
<!doctype html>
<html lang="<?php echo $lang ? $lang : 'en'?>" style="height: calc(100% - 0px);">
<head>
    <meta charset="utf-8">
    <title>POS</title>
    <meta NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <base href="<?php echo $pos_url ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="manifest" href="<?php echo $pos_url; ?>manifest.json" />
    <link rel="preload" href="<?php echo $pos_url; ?>assets/i18n/en.json" as="fetch" />
    <?php
        $handes = array(
            'openpos.material.icon',
            'openpos.styles',
            'openpos.front'
        );
        wp_print_styles(apply_filters('openpos_pos_header_style',$handes));
    ?>
    <script type="text/javascript">
        
        var action_url = '<?php echo admin_url('admin-ajax.php'); ?>';
        <?php if($lang): ?>
        var pos_lang = '<?php echo $lang; ?>';
        <?php endif; ?>
        var pos_receipt_css = <?php echo json_encode($OPENPOS_CORE->getReceiptFontCss()); ?>;
        var global = global || window;
        global.action_url = action_url;
        global.version = '<?php echo  esc_attr($plugin_info['Version']); ?>';
        var Buffer = Buffer || [];
        var process = process || {
            env: { DEBUG: undefined },
            version: []
        };
    </script>
    <script type="text/javascript">
        window.addEventListener("beforeunload", function (e) {
            var confirmationMessage = 'It looks like you have been editing something. '
                + 'If you leave before saving, your changes will be lost.';

            (e || window.event).returnValue = confirmationMessage; //Gecko + IE
            return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
        });
    </script>
</head>
<?php
$handes = array(
    'openpos.pos.ga'
);
wp_print_scripts($handes);
?>
<body style="width: 100%; height: 100%; overflow: hidden;">

<app-root>
    <style>

        body {
            background: #152B3B;
            margin: 0;
            padding: 0;
        }

        .sk-circle {
            margin: 25% auto;
            width: 40px;
            height: 40px;
            position: relative;
        }
        .sk-circle .sk-child {
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
        }
        .sk-circle .sk-child:before {
            content: '';
            display: block;
            margin: 0 auto;
            width: 15%;
            height: 15%;
            background-color: #FEC608;
            border-radius: 100%;
            -webkit-animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
            animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
        }
        .sk-circle .sk-circle2 {
            -webkit-transform: rotate(30deg);
            -ms-transform: rotate(30deg);
            transform: rotate(30deg); }
        .sk-circle .sk-circle3 {
            -webkit-transform: rotate(60deg);
            -ms-transform: rotate(60deg);
            transform: rotate(60deg); }
        .sk-circle .sk-circle4 {
            -webkit-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            transform: rotate(90deg); }
        .sk-circle .sk-circle5 {
            -webkit-transform: rotate(120deg);
            -ms-transform: rotate(120deg);
            transform: rotate(120deg); }
        .sk-circle .sk-circle6 {
            -webkit-transform: rotate(150deg);
            -ms-transform: rotate(150deg);
            transform: rotate(150deg); }
        .sk-circle .sk-circle7 {
            -webkit-transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            transform: rotate(180deg); }
        .sk-circle .sk-circle8 {
            -webkit-transform: rotate(210deg);
            -ms-transform: rotate(210deg);
            transform: rotate(210deg); }
        .sk-circle .sk-circle9 {
            -webkit-transform: rotate(240deg);
            -ms-transform: rotate(240deg);
            transform: rotate(240deg); }
        .sk-circle .sk-circle10 {
            -webkit-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            transform: rotate(270deg); }
        .sk-circle .sk-circle11 {
            -webkit-transform: rotate(300deg);
            -ms-transform: rotate(300deg);
            transform: rotate(300deg); }
        .sk-circle .sk-circle12 {
            -webkit-transform: rotate(330deg);
            -ms-transform: rotate(330deg);
            transform: rotate(330deg); }
        .sk-circle .sk-circle2:before {
            -webkit-animation-delay: -1.1s;
            animation-delay: -1.1s; }
        .sk-circle .sk-circle3:before {
            -webkit-animation-delay: -1s;
            animation-delay: -1s; }
        .sk-circle .sk-circle4:before {
            -webkit-animation-delay: -0.9s;
            animation-delay: -0.9s; }
        .sk-circle .sk-circle5:before {
            -webkit-animation-delay: -0.8s;
            animation-delay: -0.8s; }
        .sk-circle .sk-circle6:before {
            -webkit-animation-delay: -0.7s;
            animation-delay: -0.7s; }
        .sk-circle .sk-circle7:before {
            -webkit-animation-delay: -0.6s;
            animation-delay: -0.6s; }
        .sk-circle .sk-circle8:before {
            -webkit-animation-delay: -0.5s;
            animation-delay: -0.5s; }
        .sk-circle .sk-circle9:before {
            -webkit-animation-delay: -0.4s;
            animation-delay: -0.4s; }
        .sk-circle .sk-circle10:before {
            -webkit-animation-delay: -0.3s;
            animation-delay: -0.3s; }
        .sk-circle .sk-circle11:before {
            -webkit-animation-delay: -0.2s;
            animation-delay: -0.2s; }
        .sk-circle .sk-circle12:before {
            -webkit-animation-delay: -0.1s;
            animation-delay: -0.1s; }

        @-webkit-keyframes sk-circleBounceDelay {
            0%, 80%, 100% {
                -webkit-transform: scale(0);
                transform: scale(0);
            } 40% {
                  -webkit-transform: scale(1);
                  transform: scale(1);
              }
        }

        @keyframes sk-circleBounceDelay {
            0%, 80%, 100% {
                -webkit-transform: scale(0);
                transform: scale(0);
            } 40% {
                  -webkit-transform: scale(1);
                  transform: scale(1);
              }
        }
    </style>

    <div class="sk-circle">
        <div class="sk-circle1 sk-child"></div>
        <div class="sk-circle2 sk-child"></div>
        <div class="sk-circle3 sk-child"></div>
        <div class="sk-circle4 sk-child"></div>
        <div class="sk-circle5 sk-child"></div>
        <div class="sk-circle6 sk-child"></div>
        <div class="sk-circle7 sk-child"></div>
        <div class="sk-circle8 sk-child"></div>
        <div class="sk-circle9 sk-child"></div>
        <div class="sk-circle10 sk-child"></div>
        <div class="sk-circle11 sk-child"></div>
        <div class="sk-circle12 sk-child"></div>
    </div>
</app-root>
<script type='text/javascript' src='<?php echo trim($pos_url,'/') ; ?>/runtime.js?ver=<?php echo  esc_attr($plugin_info['Version']); ?>'></script>
<script type='text/javascript' src='<?php echo trim($pos_url,'/') ; ?>/polyfills.js?ver=<?php echo  esc_attr($plugin_info['Version']); ?>'></script>
<script type='text/javascript' src='<?php echo trim($pos_url,'/') ; ?>/main.js?ver=<?php echo  esc_attr($plugin_info['Version']); ?>'></script>
<script type='text/javascript'>
        
</script>

<?php
    $handes = array(
        'openpos.pos.main'
    );
    wp_print_scripts(apply_filters('openpos_pos_footer_js',$handes));
?>
</body>
</html>
