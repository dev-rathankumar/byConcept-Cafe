<?php
$current_user = wp_get_current_user();
$blogName = get_bloginfo('name');
?>

<link rel="stylesheet"
      href="<?php echo(plugins_url("/assets/css/customerly.css", __FILE__)); ?>">

<script src="<?php echo(plugins_url("/assets/js/main.js", __FILE__)); ?>"></script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5JC6WRF');</script>
<!-- End Google Tag Manager -->

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5JC6WRF"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


<!-- Customerly Integration Code -->
<script>
    window.customerlySettings = {
        app_id: "00c4ed07",
        email: "<?php  echo($current_user->user_email); ?>",
        name: "<?php  echo($current_user->user_firstname); ?>",
        widget_position: 'right',
        attributes: {
            source: "wordpress_plugin"
        }

    };
    !function () {
        function e() {
            var e = t.createElement("script");
            e.type = "text/javascript", e.async = !0,
                e.src = "https://widget.customerly.io/widget/00c4ed07";
            var r = t.getElementsByTagName("script")[0];
            r.parentNode.insertBefore(e, r)
        }

        var r = window, t = document, n = function () {
            n.c(arguments)
        };
        r.customerly_queue = [], n.c = function (e) {
            r.customerly_queue.push(e)
        },
            r.customerly = n, r.attachEvent ? r.attachEvent("onload", e) : r.addEventListener("load", e, !1)
    }();
</script>

