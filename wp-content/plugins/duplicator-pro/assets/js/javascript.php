<?php defined("ABSPATH") or die(""); ?>
<?php ?>
<script>
    (function ($) {
        /* ============================================================================
         * DESCRIPTION: Methods and Objects in this file are global and common in
         * nature use this file to place all shared methods and varibles */

        //UNIQUE NAMESPACE
        DupPro = new Object();
        DupPro.UI = new Object();
        DupPro.Pack = new Object();
        DupPro.Tools = new Object();
        DupPro.Settings = new Object();
        DupPro.Storage = new Object();
        DupPro.Storage.Dropbox = new Object();
        DupPro.Storage.OneDrive = new Object();
        DupPro.Storage.FTP = new Object();
        DupPro.Storage.SFTP = new Object();
        DupPro.Storage.GDrive = new Object();
        DupPro.Storage.S3 = new Object();
        DupPro.Schedule = new Object();
        DupPro.Template = new Object();
        DupPro.Support = new Object();
        DupPro.Debug = new Object();

        //GLOBAL CONSTANTS
        DupPro.DEBUG_AJAX_RESPONSE = false;
        DupPro.AJAX_TIMER = null;

        DupPro._WordPressInitDateTime = '<?php echo current_time("D M d Y H:i:s O") ?>';
        DupPro._WordPressInitTime = '<?php echo current_time("H:i:s") ?>';
        DupPro._ServerInitDateTime = '<?php echo date("D M d Y H:i:s O") ?>';
        DupPro._ClientInitDateTime = new Date();

        DupPro.parseJSON = function (mixData) {
            try {
                var parsed = JSON.parse(mixData);
                return parsed;
            } catch (e) {
                console.log("JSON parse failed - 1");
                console.log(mixData);
            }

            if (mixData.indexOf('[') > -1 && mixData.indexOf('{') > -1) {
                if (mixData.indexOf('{') < mixData.indexOf('[')) {
                    var startBracket = '{';
                    var endBracket = '}';
                } else {
                    var startBracket = '[';
                    var endBracket = ']';
                }
            } else if (mixData.indexOf('[') > -1 && mixData.indexOf('{') === -1) {
                var startBracket = '[';
                var endBracket = ']';
            } else {
                var startBracket = '{';
                var endBracket = '}';
            }

            var jsonStartPos = mixData.indexOf(startBracket);
            var jsonLastPos = mixData.lastIndexOf(endBracket);
            if (jsonStartPos > -1 && jsonLastPos > -1) {
                var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
                try {
                    var parsed = JSON.parse(expectedJsonStr);
                    return parsed;
                } catch (e) {
                    console.log("JSON parse failed - 2");
                    console.log(mixData);
                    throw e;
                    // errorCallback(xHr, textstatus, 'extract');
                    return false;
                }
            }
            // errorCallback(xHr, textstatus, 'extract');
            throw "could not parse the JSON";
            return false;
        }

        /* ============================================================================
         *  BASE NAMESPACE: All methods at the top of the Duplicator Namespace
         * ============================================================================ */

        /* Starts a timer for Ajax calls */
        DupPro.StartAjaxTimer = function ()
        {
            DupPro.AJAX_TIMER = new Date();
        };

        /*	Ends a timer for Ajax calls */
        DupPro.EndAjaxTimer = function ()
        {
            var endTime = new Date();
            DupPro.AJAX_TIMER = (endTime.getTime() - DupPro.AJAX_TIMER) / 1000;
        };

        /**
         *
         * @param string message // html message conent
         * @param string errLevel // notice warning error
         * @returns void
         */
        DupPro.addAdminMessage = function (message, errLevel) {
            var classErrLevel = 'notice';
            switch (errLevel) {
                case 'error':
                    classErrLevel = 'error';
                    break;
                case 'warning':
                    classErrLevel = 'update-nag';
                    break;
                case 'notice':
                default:
                    classErrLevel = 'updated';
                    break;

            }

            var msgNode = $('<div class="notice ' + classErrLevel + ' no_display is-dismissible"><p class="msg-content">' + message + '</p>' +
                    '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');

            var anchor = $("#wpcontent");
            if (anchor.find('.wrap').length) {
                anchor = anchor.find('.wrap').first();
            }

            if (anchor.find('h1').length) {
                anchor = anchor.find('h1').first();
                var dismissButton = msgNode.insertAfter(anchor).find('.notice-dismiss');
                //anchor.after(html).find('');
            } else {
                var dismissButton = msgNode.prependTo(anchor).find('.notice-dismiss');
                //anchor.prepend(html);
            }

            dismissButton.click(function () {
                $(this).closest('.is-dismissible').fadeOut("slow", function () {
                    $(this).remove();
                });
            });

            $("body, html").animate({scrollTop: 0}, 500);
            jQuery(msgNode).fadeIn("slow", function () {
                $(this).removeClass("no_display");
            });
        };

        DupPro.StandarJsonAjaxWrapper = function (ajaxData, callbackSuccess, callbackFail) {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: ajaxData,
                success: function (result, textStatus, jqXHR) {
                    var message = '';
                    if (result.success) {
                        if (typeof callbackSuccess === "function") {
                            message = callbackSuccess(result.data.funcData, result.data, textStatus, jqXHR);
                        }
                        if (message.length) {
                            DupPro.addAdminMessage(message, 'notice');
                        }
                    } else {
                        if (typeof callbackFail === "function") {
                            message = callbackFail(result.data.funcData, result.data, textStatus, jqXHR);
                        } else {
                            message = '<?php DUP_PRO_U::_e('RESPONSE ERROR!'); ?>' + '<br><br>' + result.data.message;
                        }
                        if (message.length) {
                            DupPro.addAdminMessage(message, 'error');
                        }
                    }
                },
                error: function (result) {
                    DupPro.addAdminMessage(<?php echo DupProSnapJsonU::wp_json_encode(DUP_PRO_U::__('AJAX ERROR!').'<br>'.DUP_PRO_U::__('Ajax request error')); ?>, 'error');
                }
            });
        };

        /*	Reloads the current window
         *	@param data		An xhr object  */
        DupPro.ReloadWindow = function (data, queryString)
        {
            if (DupPro.DEBUG_AJAX_RESPONSE) {
                DupPro.Pack.ShowError('debug on', data);
            } else {
                var url = window.location.href;
                if (typeof queryString !== 'undefined') {
                    var character = '?';
                    if (url.indexOf('?') > -1) {
                        character = '&';
                    }
                    url += character + queryString;
                }
                window.location = url;
            }
        };

        /* Basic Util Methods here */
        DupPro.OpenLogWindow = function (log)
        {
            var logFile = log || null;
            if (logFile == null) {
                window.open('?page=duplicator-pro-tools', 'Log Window');
            } else {
                window.open('<?php echo DUPLICATOR_PRO_SSDIR_URL; ?>' + '/' + log)
            }
        };

        DupPro.humanFileSize = function (size)
        {
            var i = Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        };


        /* ============================================================================
         *  UI NAMESPACE: All methods at the top of the Duplicator Namespace
         *  =========================================================================== */

        /*  Stores the state of a view into the database  */
        DupPro.UI.SaveViewStateByPost = function (key, value)
        {
            if (key != undefined && value != undefined) {
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    dataType: "json",
                    data: {action: 'DUP_PRO_UI_ViewState_SaveByPost', key: key, value: value, nonce: '<?php echo wp_create_nonce('DUP_PRO_UI_ViewState_SaveByPost'); ?>'},
                    success: function (data) {},
                    error: function (data) {}
                });
            }
        }

        DupPro.UI.SaveMulViewStatesByPost = function (states)
        {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {action: 'DUP_PRO_UI_ViewState_SaveByPost', states: states, nonce: '<?php echo wp_create_nonce('DUP_PRO_UI_ViewState_SaveByPost'); ?>'},
                success: function (data) {},
                error: function (data) {}
            });
        }

        DupPro.UI.SetScanMode = function ()
        {
            var scanMode = jQuery('#scan-mode').val();

            if (scanMode == <?php echo DUP_PRO_PHPDump_Mode::Multithreaded ?>) {
                jQuery('#scan-multithread-size').show();
                jQuery('#scan-chunk-size-label').show();
            } else {
                jQuery('#scan-multithread-size').hide();
                jQuery('#scan-chunk-size-label').hide();
            }

        }

        /*  Animate the progress bar  */
        DupPro.UI.AnimateProgressBar = function (id)
        {
            //Create Progress Bar
            var $mainbar = jQuery("#" + id);
            $mainbar.progressbar({value: 100});
            $mainbar.height(25);
            $mainbar.width(20);
            runAnimation($mainbar);

            function runAnimation($pb) {
                $pb.css({"padding-left": "0%", "padding-right": "90%"});
                $pb.progressbar("option", "value", 100);
                $pb.animate({paddingLeft: "90%", paddingRight: "0%"}, 2500, "linear", function () {
                    runAnimation($pb);
                });
            }
        }

        DupPro.UI.IsSaveViewState = true;
        /*	Toggle MetaBoxes */
        DupPro.UI.ToggleMetaBox = function ()
        {
            var $title = jQuery(this);
            var $panel = $title.parent().find('.dup-box-panel');
            var $arrow = $title.parent().find('.dup-box-arrow i');
            var key = $panel.attr('id');
            var value = $panel.is(":visible") ? 0 : 1;
            $panel.toggle();
            if (DupPro.UI.IsSaveViewState)
                DupPro.UI.SaveViewStateByPost(key, value);
            (value)
                    ? $arrow.removeClass().addClass('fa fa-caret-up')
                    : $arrow.removeClass().addClass('fa fa-caret-down');

        }

        DupPro.UI.ClearTraceLog = function (reload)
        {
            var reload = reload || 0;
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: 'duplicator_pro_delete_trace_log',
                    nonce: '<?php echo wp_create_nonce('duplicator_pro_delete_trace_log'); ?>'
                },
                success: function (respData) {
                    try {
                        var data = DupPro.parseJSON(respData);
                    } catch (err) {
                        console.error(err);
                        console.error('JSON parse failed for response data: ' + respData);
                        return false;
                    }
                    if (reload) {
                        window.location.reload();
                    }
                },
                error: function (data) {}
            });
            return false;
        }

        /*	Toggle Password input */
        DupPro.UI.TogglePasswordDisplay = function (display, inputID)
        {
            if (display) {
                document.getElementById(inputID).type = "text";
            } else {
                document.getElementById(inputID).type = "password";
            }
        }

        /* Clock generator, used to show an active clock.
         * Intended use is to be called once per page load
         * such as:
         *		<div id="dpro-clock-container"></div>
         *		DupPro.UI.Clock(DupPro._WordPressInitTime); */
        DupPro.UI.Clock = function ()
        {
            var timeDiff;
            var timeout;

            function addZ(n) {
                return (n < 10 ? '0' : '') + n;
            }

            function formatTime(d) {
                return addZ(d.getHours()) + ':' + addZ(d.getMinutes()) + ':' + addZ(d.getSeconds());
            }

            return function (s) {

                var now = new Date();
                var then;
                // Set lag to just after next full second
                var lag = 1015 - now.getMilliseconds();

                // Get the time difference when first run
                if (s) {
                    s = s.split(':');
                    then = new Date(now);
                    then.setHours(+s[0], +s[1], +s[2], 0);
                    timeDiff = now - then;
                }

                now = new Date(now - timeDiff);
                jQuery('#dpro-clock-container').html(formatTime(now));
                timeout = setTimeout(DupPro.UI.Clock, lag);
            };
        }();

        /* ============================================================================
         *  Util functions
         *  =========================================================================== */
        DupPro.Util = {};

        DupPro.Util.isEmpty = function (val) {
            return (val === undefined || val == null || val.length <= 0) ? true : false;
        };

        jQuery(document).ready(function ($)
        {

            DupPro.UI.loadQtip = function ()
            {
                //Look for tooltip data
                $('[data-tooltip!=""]').qtip({
                    content: {
                        attr: 'data-tooltip',
                        title: function () {
                            if ($(this)[0].hasAttribute("data-tooltip-title")) {
                                return  $(this).data('tooltip-title');
                            } else {
                                return false;
                            }
                        }
                    },
                    style: {
                        classes: 'qtip-light qtip-rounded qtip-shadow',
                        width: 500
                    },
                    position: {
                        my: 'top left',
                        at: 'bottom center'
                    }
                });
            }

            DupPro.UI.loadSimpeQtip = function ()
            {
                //Look for tooltip data
                $('[data-simpletip!=""]').qtip({
                    content: {
                        attr: 'data-simpletip'
                    },
                    style: {
                        classes: 'qtip-light qtip-rounded qtip-shadow'
                    },
                    position: {
                        my: 'top left',
                        at: 'bottom center'
                    }
                });
            }

            DupPro.UI.Copytext = function () {
                $('[data-dup-copy-text]').each(function () {
                    $(this).click(function () {
                        var elem = $(this);
                        var message = '';
                        var textToCopy = elem.data('dup-copy-text');
                        var tmpArea = jQuery("<textarea></textarea>").css({
                            position: 'absolute',
                            top: '-10000px'
                        }).text(textToCopy).appendTo("body");
                        tmpArea.select();
                        try {
                            var successful = document.execCommand('copy');
                            message = successful ? 'Copied: ' + textToCopy : 'unable to copy';
                        } catch (err) {
                            message = 'unable to copy';
                        }
                        elem.qtip('option', 'content.text', message).qtip('show');
                        setTimeout(function () {
                            elem.qtip('option', 'content.text', 'Copy');
                        }, 2000);
                    }).qtip({
                        content: {
                            text: 'copy'
                        },
                        style: {
                            classes: 'qtip-light qtip-rounded qtip-shadow'
                        },
                        position: {
                            my: 'top left',
                            at: 'bottom center'
                        }
                    });
                });
            };

            //INIT: DupPro Tabs
            $("div[data-dpro-tabs='true']").each(function ()
            {
                //Load Tab Setup
                var $root = $(this);
                var $lblRoot = $root.find('ul:first-child')
                var $lblKids = $lblRoot.children('li');
                var $lblKidsA = $lblRoot.children('li a');
                var $pnls = $root.children('div');

                //Apply Styles
                $root.addClass('categorydiv');
                $lblRoot.addClass('category-tabs');
                $pnls.addClass('tabs-panel').css('display', 'none');
                $lblKids.eq(0).addClass('tabs').css('font-weight', 'bold');
                $pnls.eq(0).show();

                var _clickEvt = function (evt)
                {
                    var $target = $(evt.target);
                    if (evt.target.nodeName == 'A') {
                        var $target = $(evt.target).parent();
                    }
                    var $lbls = $target.parent().children('li');
                    var $pnls = $target.parent().parent().children('div');
                    var index = $target.index();

                    $lbls.removeClass('tabs').css('font-weight', 'normal');
                    $lbls.eq(index).addClass('tabs').css('font-weight', 'bold');
                    $pnls.hide();
                    $pnls.eq(index).show();
                }

                //Attach Events
                $lblKids.click(_clickEvt);
                $lblKids.click(_clickEvt);
            });

            //INIT: Toggle MetaBoxes
            $('div.dup-box div.dup-box-title').each(function () {
                var $title = $(this);
                var $panel = $title.parent().find('.dup-box-panel');
                var $arrow = $title.find('.dup-box-arrow');
                $title.click(DupPro.UI.ToggleMetaBox);
                ($panel.is(":visible"))
                        ? $arrow.html('<i class="fa fa-caret-up"></i>')
                        : $arrow.html('<i class="fa fa-caret-down"></i>');
            });

            DupPro.UI.loadQtip();
            DupPro.UI.loadSimpeQtip();
            DupPro.UI.Copytext();

            //HANDLEBARS HELPERS
            if (typeof (Handlebars) != "undefined") {

                function _handleBarscheckCondition(v1, operator, v2) {
                    switch (operator) {
                        case '==':
                            return (v1 == v2);
                        case '===':
                            return (v1 === v2);
                        case '!==':
                            return (v1 !== v2);
                        case '<':
                            return (v1 < v2);
                        case '<=':
                            return (v1 <= v2);
                        case '>':
                            return (v1 > v2);
                        case '>=':
                            return (v1 >= v2);
                        case '&&':
                            return (v1 && v2);
                        case '||':
                            return (v1 || v2);
                        case 'obj||':
                            v1 = typeof (v1) == 'object' ? v1.length : v1;
                            v2 = typeof (v2) == 'object' ? v2.length : v2;
                            return (v1 != 0 || v2 != 0);
                        default:
                            return false;
                    }
                }

                Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
                    return _handleBarscheckCondition(v1, operator, v2)
                            ? options.fn(this)
                            : options.inverse(this);
                });

                Handlebars.registerHelper('if_eq', function (a, b, opts) {
                    return (a == b) ? opts.fn(this) : opts.inverse(this);
                });
                Handlebars.registerHelper('if_neq', function (a, b, opts) {
                    return (a != b) ? opts.fn(this) : opts.inverse(this);
                });
            }

            //Prevent notice boxes from flashing as its re-positioned in DOM
            $('div.dpro-wpnotice-box').show(300);

        });
    })(jQuery);
</script>
