<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();

if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) {
    $overwriteData  = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);    
    $ovr_dbhost = $overwriteData['dbhost'];
    $ovr_dbname = $overwriteData['dbname'];
    $ovr_dbuser = $overwriteData['dbuser'];
    $ovr_dbpass = $overwriteData['dbpass'];
} else {
    $ovr_dbhost = '';
    $ovr_dbname = '';
    $ovr_dbuser = '';
    $ovr_dbpass = '';
}
?>
<script>
    var dbViewModeInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE)); ?>;

    var dbActionInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_ACTION)); ?>;
    var dbHostInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_HOST)); ?>;
    var dbNameInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_NAME)); ?>;
    var dbUserInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_USER)); ?>;
    var dbPassInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_PASS)); ?>;

    var dbCharsetDefaultID = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET)); ?>;
    var dbCollateDefaultID = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE)); ?>;

    var dbDbcharsetfbInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB)); ?>;
    var dbDbcharsetfbValWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL)); ?>;
    var dbDbcharsetfbValInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL)); ?>;

    var dbDbcollatefbInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB)); ?>;
    var dbDbcollatefbValWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL)); ?>;
    var dbDbcollatefbValInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL)); ?>;

    DUPX.basicDBActionChange = function ()
    {
        var action = $('#' + dbActionInputId).val();
        $('.s2-basic-pane .s2-warning-manualdb').hide();
        $('.s2-basic-pane .s2-warning-emptydb').hide();
        $('.s2-basic-pane .s2-warning-renamedb').hide();
        switch (action)
        {
            case 'create'  :
                break;
            case 'empty'   :
                $('.s2-basic-pane .s2-warning-emptydb').show(300);
                break;
            case 'rename'  :
                $('.s2-basic-pane .s2-warning-renamedb').show(300);
                break;
            case 'manual'  :
                $('.s2-basic-pane .s2-warning-manualdb').show(300);
                break;
        }
    };

    //HANDLEBAR HOOKS
    Handlebars.registerHelper('if_eq', function (a, b, opts) {
        return (a == b) ? opts.fn(this) : opts.inverse(this);
    });
    Handlebars.registerHelper('if_neq', function (a, b, opts) {
        return (a != b) ? opts.fn(this) : opts.inverse(this);
    });
    Handlebars.registerHelper('faqURL', function () {
        return "https://snapcreek.com/duplicator/docs/faqs-tech/";
    });
    Handlebars.registerHelper('reqText', function (req) {
        return '';
    });
    Handlebars.registerHelper('reqStyle', function (req) {
        switch (req) {
            case 0:
                return "status-badge fail";
                break;
            case 1:
                return "status-badge pass";
                break;
            case 2:
                return "status-badge warn";
                break;
            case - 1:
            default:
                return "";
        }
    });
    Handlebars.registerHelper('noticeStyle', function (req) {
        switch (req) {
            case 0:
                return "status-badge fail";
                break;
            case 1:
                return "status-badge good";
                break;
            case 2:
                return "status-badge warn";
                break;
            case - 1:
            default:
                return "";
        }
    });
    Handlebars.registerHelper('noticeText', function (warn) {
        return '';
    });
    Handlebars.registerHelper('getInfo', function (pass, info) {
        return (pass == 1)
                ? "<div class='success-msg'>" + info + "</div>"
                : "<div class='error-msg'>" + info + "</div>";
    });
    Handlebars.registerHelper('getTablePerms', function (perm) {
        if (perm == -1) {
            return "<span class='dupx-warn'>Requires Dependency</span>";
        } else if (perm == 0) {
            return "<span class='dupx-fail'>Fail</span>";
        } else {
            return "<span class='dupx-pass'>Pass</span>";
        }
    });

    DUPX.testDBConnect = function (dbResult, isValidCallback, showContentOnResult)
    {
        DUPX.pageComponents.resetTopMessages().showProgress({
            'title': 'Test database connection',
            'bottomText':
                    '<i>Keep this window open.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });

        let databaseCheckAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_DATABASE_CHECK); ?>;
        let databaseCheckToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_DATABASE_CHECK)); ?>;

        DUPX.StandarJsonAjaxWrapper(
                databaseCheckAction,
                databaseCheckToken,
                {},
                function (data) {
                    if (showContentOnResult) {
                        DUPX.pageComponents.showContent();
                    }

                    if (DUPX.intTestDBResults(data.actionData, dbResult)) {
                        if (typeof isValidCallback === "function") {
                            isValidCallback();
                        }
                    }
                },
                DUPX.ajaxErrorDisplayHideError,
                {
                    timeOut: 25000
                }
        );
    };

//Process Ajax Template
    DUPX.intTestDBResults = function (data, result)
    {
        if (data.hasOwnProperty('error') && data.error === true) {
            var msg = "<b>Error Processing Request</b> <br/> An error occurred while testing the database connection! Please Try Again...<br/> ";
            msg += "<small>If the error persists contact your host for database connection requirements.</small><br/> ";
            msg += "<small>Status details: " + data.errorMessage + "</small>";
            result.html("<div class='message dupx-fail'>" + msg + "</div>");
            return false;
        }

        var charsetDefaultObj = $('#'+dbCharsetDefaultID);
        var collateDefaultObj = $('#'+dbCollateDefaultID);

        charsetDefaultObj.val(data.payload.defaultCharset);
        collateDefaultObj.val(data.payload.defaultCollate);

        var charsetValInputObj = $('#' + dbDbcharsetfbValInputId);
        if (charsetValInputObj && typeof data.payload.extra.charset !== 'undefined') {
            charsetValInputObj.empty();

            for (i = 0; i < data.payload.extra.charset.list.length; i++) {
                item = data.payload.extra.charset.list[i];

                $('<option>')
                        .attr('value', item)
                        .text(item)
                        .prop('selected', (data.payload.extra.charset.selected == item))
                        .appendTo(charsetValInputObj);
            }
        }

        var collateValInputObj = $('#' + dbDbcollatefbValInputId);
        if (collateValInputObj && typeof data.payload.extra.collate !== 'undefined') {
            collateValInputObj.empty();

            for (i = 0; i < data.payload.extra.collate.list.length; i++) {
                item = data.payload.extra.collate.list[i];

                var option = $('<option>')
                        .attr({
                            'value': item.Collation,
                            'data-charset': item.Charset
                        })
                        .text(item.Collation)
                        .prop('selected', (data.payload.extra.collate.selected == item.Collation))
                        .appendTo(collateValInputObj);

                if (!data.payload.hasOwnProperty('selected') || typeof data.payload.extra.charset.selected == undefined || data.payload.extra.charset.selected == '') {
                } else if (data.payload.extra.charset.selected == item.Charset) {
                } else {
                    option.addClass('no-display');
                }
            }
        }

        var resultID = $(result).attr('id');
        var mode = '-' + data.payload.in.mode;
        var template = $('#s2-dbtest-hb-template').html();
        var templateScript = Handlebars.compile(template);
        var html = templateScript(data);
        result.html(html);

        //Make all id attributes unique to basic or cpanel areas
        //otherwise id will no longer be unique
        $("div#" + resultID + " div[id]").each(function () {
            var attr = this.id;
            $(this).attr('id', attr + mode);
        });

        $("div#" + resultID + " div[data-target]").each(function () {
            var attr = $(this).attr('data-target');
            $(this).attr('data-target', attr + mode);
        });

        $("div#" + resultID + " *[data-type='toggle']").on('click', DUPX.toggleClick);

        var $divReqsAll = $('#s2-reqs-all' + mode);
        var $divNoticeAll = $('#s2-notices-all' + mode);
        var $btnNext = $('#s2-next-btn' + mode);
        var $btnTestDB = $('#s2-dbtest-btn' + mode);
        var $divRetry = $('#s2-dbrefresh' + mode);

        $divRetry.show();
        $btnTestDB.removeAttr('disabled').removeClass('disabled');
        $btnNext.removeAttr('disabled').removeClass('disabled');

        if (data.payload.reqsPass == 1 || data.payload.reqsPass == 2) {
            $btnTestDB.addClass('disabled').attr('disabled', 'true');
            if (data.payload.reqsPass == 1) {
                $divReqsAll.hide();
            }
        } else {
            $btnNext.addClass('disabled').attr('disabled', 'true');
            $divReqsAll.show();
        }

        data.payload.noticesPass ? $divNoticeAll.hide() : $divNoticeAll.show();

        if ((data.payload.reqsPass == 1 || data.payload.reqsPass == 2) && data.payload.noticesPass == 1) {
            $btnTestDB.addClass('disabled').attr('disabled', 'true');
        }
        $('#s2-db-basic #' + dbActionInputId).on('change', {'mode': mode}, DUPX.resetDBTest);
        $('#s2-db-basic :input').on('keyup', {'mode': mode}, DUPX.resetDBTest);
        $('#s2-cpnl-db-opts :input').on('keyup', {'mode': mode}, DUPX.resetDBTest);
        $('#s2-cpnl-db-opts select#cpnl-dbaction').on('change', {'mode': mode}, DUPX.resetDBTest);
        $('#s2-cpnl-db-opts select#cpnl-dbuser-select').on('change', {'mode': mode}, DUPX.resetDBTest);
        $('#s2-cpnl-db-opts input#cpnl-dbuser-chk').on('click', {'mode': mode}, DUPX.resetDBTest);

        return true;
    }

    DUPX.resetDBTest = function (e)
    {
        var $btnNext = $('#s2-next-btn' + e.data.mode);
        var $btnTestDB = $('#s2-dbtest-btn' + e.data.mode);
        var $divTestArea = $('#s2-dbtest-hb' + e.data.mode);

        $btnTestDB.removeAttr('disabled').removeClass('disabled');
        $btnNext.addClass('disabled').attr('disabled', 'true');
        $divTestArea.html("<div class='sub-message'>To continue click the 'Test Database'<br/>button to retest the database setup.</div>");
    }


    //DOCUMENT INIT
    $(document).ready(function ()
    {
        $("#" + dbActionInputId).on("change", DUPX.basicDBActionChange);
        DUPX.basicDBActionChange();

        DUPX.checkOverwriteParameters = function (dbhost, dbname, dbuser, dbpass)
        {
            $("#" + dbHostInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbhost); ?>).prop('readonly', true);
            $("#" + dbNameInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbname); ?>).prop('readonly', true);
            $("#" + dbUserInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbuser); ?>).prop('readonly', true);
            $("#" + dbPassInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbpass); ?>).prop('readonly', true);
            $("#s2-db-basic-setup").show();
        };

        DUPX.fillInPlaceHolders = function ()
        {
            $("#" + dbHostInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbhost); ?>).prop('readonly', false);
            $("#" + dbNameInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbname); ?>).prop('readonly', false);
            $("#" + dbUserInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbuser); ?>).prop('readonly', false);
            $("#" + dbPassInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbpass); ?>).prop('readonly', false);
        };

        DUPX.resetParameters = function ()
        {
            $("#" + dbHostInputId).val('').attr('placeholder', '').prop('readonly', false);
            $("#" + dbNameInputId).val('').attr('placeholder', '').prop('readonly', false);
            $("#" + dbUserInputId).val('').attr('placeholder', '').prop('readonly', false);
            $("#" + dbPassInputId).val('').attr('placeholder', '').prop('readonly', false);
        };

<?php if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) : ?>
            DUPX.fillInPlaceHolders();
<?php endif; ?>
        DUPX.charsetfbCheckChanged = function () {
            var selectionBoxWrapper = $('#' + dbDbcharsetfbValWrapperId);
            if ($("#" + dbDbcharsetfbInputId).is(':checked')) {
                selectionBoxWrapper.slideDown('slow');
            } else {
                selectionBoxWrapper.slideUp('slow');
            }
        }

        DUPX.collatefbCheckChanged = function () {
            var selectionBoxWrapper = $('#' + dbDbcollatefbValWrapperId);
            if ($("#" + dbDbcollatefbInputId).is(':checked')) {
                selectionBoxWrapper.slideDown('slow');
            } else {
                selectionBoxWrapper.slideUp('slow');
            }
        }

        DUPX.charsetValChanged = function () {

            var collateObj = $('#' + dbDbcollatefbValInputId);
            var charsetObj = $('#' + dbDbcharsetfbValInputId);
            if (collateObj.is(":visible")) {
                collateObj.find('option').hide();
                collateObj.find('option[data-charset="' + charsetObj.val() + '"]').show().first().prop('selected', true);
            }
        }

        $("#" + dbDbcharsetfbInputId).change(DUPX.charsetfbCheckChanged).trigger('change');
        $("#" + dbDbcollatefbInputId).change(DUPX.collatefbCheckChanged).trigger('change');
        $("#" + dbDbcharsetfbValInputId).change(DUPX.charsetValChanged);
    });
</script>
<script id="s2-dbtest-hb-template" type="text/x-handlebars-template">
    <?php dupxTplRender('pages-parts/step2/dbtest-result-template'); ?>
</script>
