<?php
defined("ABSPATH") or die("");

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/tests/class.u.tests.manager.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/tests/class.test.debug.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/tests/class.test.package.build.php');

$storage_list       = DUP_PRO_Storage_Entity::get_all();
$storage_list_count = count($storage_list);

$testsManager = DUP_PRO_U_Tests_manager::getInstance();
$debugTest    = new DUP_PRO_Test_debug();
$testsManager->register($debugTest, 'debug');

$debugTest = new DUP_PRO_Test_package_build();
$testsManager->register($debugTest, 'package');

$data = array(
    'test' => array(
        /* array(
          'title' => 'Debug test manager',
          'test' => $testsManager->test('debug')
          ), */
        array(
            'title' => 'Package creation test results',
            'test' => $testsManager->test('package')
        )
    )
);
?>
<style>

    .line_through {
        text-decoration: line-through;
    }
    #dup_test_content {
        display: flex;
    }

    #dup_test_content .col_left {
        position: relative;
        box-sizing: border-box;
        padding-right: 20px;
        flex: 1 1 70%;
    }

    #dup_test_content .col_right {
        position: relative;
        box-sizing: border-box;
        flex: 0 0 30%;
        max-width: 30%;
    }

    .dpro-opts-items {
        border:1px solid silver;
        background: #efefef;
        padding: 10px 5px;
        border-radius: 4px;
        margin:2px 0px 10px -2px;
        white-space: nowrap;
    }

    .dpro-opts-items .opt-wrapper {
        margin-bottom: 20px;
    }

    .dup-options-toggle {
        font-size: 13px;
        position: absolute;
        right: 20px;
    }
</style>
<div id="dup_test_content" class="dup-tests">
    <div class="col_left" >
        <h2><?php DUP_PRO_U::esc_html_e("Duplicator Build Test") ?> <a href="#" class="dup-options-toggle">Options <i class="fa fa-angle-double-right"></i></a></h2>
        <p>
            <?php DUP_PRO_U::esc_html_e("This page is a diagnostics feature that lets support troubleshoot a build process and check the various process states. When a package is created it goes through many stages and this tool can help faciliate any issues that might arise during the tests."); ?>
        </p>
        <button id="run_build_test" class="button button-primary" ><?php DUP_PRO_U::esc_html_e('Run package build test'); ?></button>

        <div id="package_build_test" class="tests_result no_display">
            <?php
            foreach ($data['test'] as $section) {
                ?>
                <h2 class="title" ><?php echo $section['title']; ?></h2>
                <div class="accordion-container">
                    <?php
                    foreach ($section['test'] as $testResult) {
                        foreach ($testResult as $test) {
                            ?>
                            <div class="control-section accordion-section <?php echo $test->htmlClass; ?>">
                                <h3 class="accordion-section-title" >
                                    <?php
                                    echo $test->getTestHtmlPassCheck().' '.esc_html($test->title);
                                    ?>
                                </h3>
                                <div class="accordion-section-content" >
                                    <?php echo $test->desc; ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="col_right" >
        <h2><?php DUP_PRO_U::esc_html_e("Options") ?></h2>
        <div class="dpro-opts-items">
            <div class="opt-wrapper" >
                <label for="dup-test-filesfilter" >
                    <input type="checkbox" id="dup-test-filesfilter" checked="checked" >
                    <strong><?php DUP_PRO_U::esc_html_e("Filter all files") ?></strong>
                </label>
                <i class="fas fa-question-circle fa-sm"
                   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e('Filter all file'); ?>"
                   data-tooltip="<?php DUP_PRO_U::esc_attr_e('Checking this checkbox will enable filter all files. It emulate package creation without create a full package size'); ?>" data-hasqtip="0" aria-describedby="qtip-0"></i>
            </div>
            <div class="opt-wrapper" >
                <label for="dup-test-dbfilter" >
                    <input type="checkbox" id="dup-test-dbfilter" checked="checked" >
                    <strong><?php DUP_PRO_U::esc_html_e("Filter all db table") ?></strong>
                </label>
                <i class="fas fa-question-circle fa-sm"
                   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e('Filter all db tables'); ?>"
                   data-tooltip="<?php DUP_PRO_U::esc_attr_e('Checking this checkbox will enable filter all db tables except wp_options'); ?>" data-hasqtip="0" aria-describedby="qtip-0"></i>
            </div>
            <div class="opt-wrapper" >
                <label for="dup-test-delpack" >
                    <input type="checkbox" id="dup-test-delpack" checked="checked"  >
                    <strong><?php DUP_PRO_U::esc_html_e("Delete package after test") ?></strong>
                </label>
                <i class="fas fa-question-circle fa-sm"
                   data-tooltip-title="<?php DUP_PRO_U::esc_attr_e('Delete package after test'); ?>"
                   data-tooltip="<?php DUP_PRO_U::esc_attr_e('Checking this checkbox will enable clean package test. if disabled pack created remain in pack list.'); ?>" data-hasqtip="0" aria-describedby="qtip-0"></i>
            </div>

            <hr>
            <h3><?php DUP_PRO_U::esc_html_e("Storages") ?></h3>
            <?php
            $i = 0;
            foreach ($storage_list as $store) {
                try {
                    $store_type = $store->get_storage_type_string();
                    $is_valid   = $store->is_valid() && $store->is_authorized();
                    $title      = $is_valid ? '' : 'title="'.DUP_PRO_U::esc_attr__('Storage isn\'t valid or autorized').'"';

                    $is_checked = in_array($store->id, $global->manual_mode_storage_ids) && $is_valid;
                    ?>
                    <div class="opt-wrapper <?php echo $is_valid ? '' : 'line_through' ?>" <?php echo $title; ?>>
                        <label for="dup-test-delpack" >
                            <input class="duppro-storage-input" <?php echo DUP_PRO_UI::echoDisabled($is_valid == false); ?>
                                   name="_storage_ids[]"
                                   type="checkbox"
                                   value="<?php echo intval($store->id); ?>" <?php DUP_PRO_UI::echoChecked($is_checked); ?> />

                            <?php
                            echo ($is_valid == false) ? '<i class="fa fa-exclamation-triangle fa-sm"></i>' : (($store_type == 'Local') ? '<i class="fa fa-server"></i>' : '<i class="fa fa-cloud"></i>');
                            echo ' <b>'.esc_html($store->name).'</b> (Type: '.esc_html($store_type).')';
                            ?>

                        </label>
                    </div>

                    <?php
                } catch (Exception $e) {
                    echo "<div class=\"opt-wrapper\" >"
                    .DUP_PRO_U::__('Unable to load storage type.  Please validate the setup.')
                    ."</div>";
                }
            }
            ?>
        </div>
    </div>
</div>



<?php
$descInfoStrings                = array(
    'test_check_pack_init' => array(
        'pass' => DUP_PRO_U::__('test_check_pack_init test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_pack_scan' => array(
        'pass' => DUP_PRO_U::__('test_check_archive test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_pack_start' => array(
        'pass' => DUP_PRO_U::__('test_check_pack_start test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_database' => array(
        'pass' => DUP_PRO_U::__('test_check_database test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_archive' => array(
        'pass' => DUP_PRO_U::__('test_check_archive test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_storage' => array(
        'pass' => DUP_PRO_U::__('test_check_storage test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_completed' => array(
        'pass' => DUP_PRO_U::__('test_check_completed test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    ),
    'test_check_clean' => array(
        'pass' => DUP_PRO_U::__('test_check_clean test ok'),
        'fail' => DUP_PRO_U::__('test failed<br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lectus ligula, placerat sed lorem rhoncus, finibus iaculis tellus. Proin ipsum libero, sagittis vel nibh ac, gravida tristique turpis. Aenean hendrerit. ')
    )
);
?>
<!-- ==========================================
THICK-BOX DIALOGS: -->
<?php
$dlg_pack_wait_running          = new DUP_PRO_UI_Dialog();
$dlg_pack_wait_running->title   = DUP_PRO_U::__('PACKAGE BUILD RUNNING');
$dlg_pack_wait_running->message = DUP_PRO_U::__('Try again later ...');
$dlg_pack_wait_running->initAlert();

$dlg_pack_storage_select          = new DUP_PRO_UI_Dialog();
$dlg_pack_storage_select->title   = DUP_PRO_U::__('Select at least one storage');
$dlg_pack_storage_select->message = '';
$dlg_pack_storage_select->initAlert();

$msg_pack_started                   = new DUP_PRO_UI_Messages(
    DUP_PRO_U::__('TESTING ...'));
$msg_pack_started->hide_on_init     = true;
$msg_pack_started->callback_on_show = 'DupPro.Tests.StartTestBuild()';
$msg_pack_started->auto_hide_delay  = 2000;
$msg_pack_started->initMessage();

$msg_ajax_error                   = new DUP_PRO_UI_Messages(
    DUP_PRO_U::__('AJAX ERROR!').'<br>'.
    DUP_PRO_U::__('Ajax request error')
    , DUP_PRO_UI_Messages::ERROR);
$msg_ajax_error->callback_on_show = 'DupPro.Tests.StopTestBuild()';
$msg_ajax_error->hide_on_init     = true;
$msg_ajax_error->initMessage();

$msg_response_error                   = new DUP_PRO_UI_Messages('', DUP_PRO_UI_Messages::ERROR);
$msg_response_error->hide_on_init     = true;
$msg_response_error->callback_on_show = 'DupPro.Tests.StopTestBuild()';
$msg_response_error->initMessage();

$msg_pack_error                   = new DUP_PRO_UI_Messages('', DUP_PRO_UI_Messages::ERROR);
$msg_pack_error->hide_on_init     = true;
$msg_pack_error->callback_on_show = 'DupPro.Tests.StopTestBuild()';
$msg_pack_error->initMessage();

$msg_pack_completed                   = new DUP_PRO_UI_Messages(
    DUP_PRO_U::__('Build package test completed'));
$msg_pack_completed->hide_on_init     = true;
$msg_pack_completed->callback_on_show = 'DupPro.Tests.StopTestBuild()';
$msg_pack_completed->auto_hide_delay  = 5000;
$msg_pack_completed->initMessage();
?>

<script>
<?php
$wp_test_nonce                        = wp_create_nonce('duplicator_pro_package_build_test');
?>
    jQuery(document).ready(function ($)
    {
        DupPro.Tests = {
            currPackId : false
        };

        DupPro.Tests.descInfo = <?php echo json_encode($descInfoStrings); ?>;

        DupPro.Tests.BuildStatus = {
            REQUIREMENTS_FAILED: -6,
            STORAGE_FAILED: -5,
            STORAGE_CANCELLED: -4,
            PENDING_CANCEL: -3,
            BUILD_CANCELLED: -2,
            ERROR: -1,
            PRE_PROCESS: 0,
            SCANNING: 3,
            AFTER_SCAN: 5,
            START: 10,
            DBSTART: 20,
            DBDONE: 39,
            ARCSTART: 40,
            ARCVALIDATION: 60,
            ARCDONE: 65,
            COPIEDPACKAGE: 70,
            STORAGE_PROCESSING: 75,
            COMPLETE: 100
        };

        DupPro.Tests.Init = function () {
            var opt_toggle = $('.dup-options-toggle');

            opt_toggle.toggle(
                    function () {
                        $("#dup_test_content .col_right").animate({
                            'flex-basis': "0%",
                            'max-width': '0%',
                            'opacity': 0
                        }, 500, function () {
                            opt_toggle.find('.fa').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
                        });
                    },
                    function () {
                        $("#dup_test_content .col_right").animate({
                            'flex-basis': "30%",
                            'max-width': '30%',
                            'opacity': 1
                        }, 500, function () {
                            opt_toggle.find('.fa').removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
                        });
                    });


            $.each(DupPro.Tests.descInfo, function (key, val) {
                $('.' + key).data('desc_info', val);
            });

            $('#run_build_test').click(function () {
                if (!$(this).hasClass('disabled')) {
                    $('#package_build_test').addClass('no_display');

<?php
$msg_pack_started->hideMessage();
$msg_ajax_error->hideMessage();
$msg_response_error->hideMessage();
$msg_pack_error->hideMessage();
$msg_pack_completed->hideMessage();
?>
                    if ($('input[name^="_storage_ids"]:checked').length) {
                        DupPro.Tests.checkPackRunning(
                                function () { // running_callback
<?php $dlg_pack_wait_running->showAlert(); ?>
                                },
                                function () { // no running_callback
<?php $msg_pack_started->showMessage(); ?>
                                });
                    } else {
<?php $dlg_pack_storage_select->showAlert(); ?>
                    }
                }
            });
        };


        DupPro.Tests.StartTestBuild = function () {
            DupPro.Tests.currPackId = false;

            $('#run_build_test').addClass('disabled');
            $('#package_build_test').removeClass('no_display');

            $('#package_build_test .accordion-container .accordion-section').addClass('no_display');
            var firstAccordion = $('#package_build_test .accordion-container .accordion-section:nth-child(1)');

            /** close all accordions */
            if (firstAccordion.hasClass('open')) {
                firstAccordion.find('.accordion-section-title').trigger('click');
            } else {
                firstAccordion.find('.accordion-section-title').trigger('click').trigger('click');
            }

            DupPro.Tests.checkUpdateWait($('#package_build_test'));

            firstAccordion.removeClass('no_display');

            DupPro.Tests.TestBuild();
            /** PREVENT TB_WINDOW OPEN BEFORE TB_REMOVE FADE OUT **/
            /*setTimeout(function () {
             DupPro.Tests.TestBuild();
             }, 1000);*/

        };

        DupPro.Tests.StopTestBuild = function () {
            $('#run_build_test').removeClass('disabled');
            DupPro.Tests.currPackId = false;
        };

        DupPro.Tests.checkAjaxResult = function (result) {
            var result = result || new Object();
            if (result.success !== true) {
<?php
$msg_response_error->updateMessage('"'.DUP_PRO_U::__('RESPONSE ERROR!').'<br>" + result.data.message');
$msg_response_error->showMessage();
?>
                return false;
            } else {
                return true;
            }
        };

        DupPro.Tests.currentTestAccordion = null;

        DupPro.Tests.checkUpdate = function (elem, pass, displayNext) {

            if (pass) {
                elem.find('.test-check').removeClass('wait').addClass('pass').text('Pass');
                elem.find('.accordion-section-content').html(elem.data('desc_info').pass);

                if (displayNext) {
                    DupPro.Tests.currentTestAccordion = elem.next().removeClass('no_display');
                }

            } else {
                elem.find('.test-check').removeClass('wait').addClass('fail').text('Fail');
                elem.find('.accordion-section-content').html(elem.data('desc_info').fail);
                DupPro.Tests.GetPackageLog(elem.find('.accordion-section-content'));
                /** open fail accordion */
                elem.find('.accordion-section-title').trigger('click');


            }
        };

        DupPro.Tests.checkUpdateWait = function (elem) {
            elem.find('.test-check').removeClass('pass fail').addClass('wait').html('<i class="fas fa-circle-notch fa-spin"></i> Wait');
            elem.find('.accordion-section-content').empty();
        };

        DupPro.Tests.UpdateStatus = function (status, displayNext) {
            if (status >= DupPro.Tests.BuildStatus.DBDONE) {
                DupPro.Tests.checkUpdate($('.test_check_database'), true, displayNext);
            }

            if (status >= DupPro.Tests.BuildStatus.ARCDONE) {
                DupPro.Tests.checkUpdate($('.test_check_archive'), true, displayNext);
            }

            if (status >= DupPro.Tests.BuildStatus.COPIEDPACKAGE) {
                DupPro.Tests.checkUpdate($('.test_check_storage'), true, displayNext);
            }

            if (status >= DupPro.Tests.BuildStatus.COMPLETE) {
                DupPro.Tests.checkUpdate($('.test_check_completed'), true, displayNext);
            }
        };


        DupPro.Tests.checkPackRunning = function (running_callback, no_running_callback)
        {
            var data = {
                action: 'duplicator_pro_is_pack_running',
                nonce: '<?php echo wp_create_nonce('duplicator_pro_is_pack_running'); ?>'
            };

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                timeout: 10000000,
                data: data,
                complete: function () { },
                success: function (result) {
                    if (DupPro.Tests.checkAjaxResult(result)) {
                        if (jQuery.isFunction(running_callback) && result.data.running) {
                            running_callback(result);
                        } else if (jQuery.isFunction(no_running_callback)) {
                            no_running_callback(result);
                        }
                    }
                },
                error: function (result) {
<?php
$msg_ajax_error->showMessage();
?>
                }
            });
        };

        DupPro.Tests.TestBuild = function () {
            var storages = [];
            $('input[name^="_storage_ids"]:checked').each(function () {
                storages.push($(this).val());
            });

            var input = {
                action: 'duplicator_pro_build_package_test',
                filesfilter: $('#dup-test-filesfilter').is(":checked"),
                dbfilter: $('#dup-test-dbfilter').is(":checked"),
                storages: storages,
                nonce: '<?php echo $wp_test_nonce; ?>'
            };

            $.ajax({
                type: "POST",
                cache: false,
                url: ajaxurl,
                dataType: "json",
                timeout: 10000000,
                data: input,
                complete: function () {},
                success: function (result) {
                    if (DupPro.Tests.checkAjaxResult(result)) {
                        DupPro.Tests.currPackId = result.data.data.package.ID;
                        /* console.log(result); */
                    }

                    DupPro.Tests.checkUpdate($('.test_check_pack_init'), result.data.data.pack_creation_1, true);

                    if (result.data.data.pack_creation_1) {
                        DupPro.Tests.checkUpdate($('.test_check_pack_scan'), result.data.data.pack_scan, true);
                    }

                    if (result.data.data.pack_scan) {
                        DupPro.Tests.checkUpdate($('.test_check_pack_start'), result.data.data.pack_start_build, true);
                    }

                    if (result.data.data.pack_start_build) {
                        DupPro.Tests.TestBuildStatus(result.data.data.package.ID);
                    }
                },
                error: function (result) {
                    var result = result || new Object();
<?php
$msg_ajax_error->showMessage();
?>
                }
            });
        };

        DupPro.Tests.TestBuildStatus = function (packId) {
            var input = {
                action: 'duplicator_pro_get_package_status',
                id: packId,
                nonce: '<?php echo $wp_test_nonce; ?>'
            };

            $.ajax({
                type: "POST",
                cache: false,
                url: ajaxurl,
                dataType: "json",
                timeout: 10000000,
                data: input,
                complete: function () {},
                success: function (result) {
                    if (DupPro.Tests.checkAjaxResult(result)) {
                        var status = result.data.data.status;
                        if (status < 0) {
                            DupPro.Tests.checkUpdate(DupPro.Tests.currentTestAccordion, false);
                            var dialogMessage = '<?php echo DUP_PRO_U::__('PACKGAGE STATUS ERROR!').'<br>'.DUP_PRO_U::__('Status: '); ?>' + status;
<?php
$msg_pack_error->updateMessage('dialogMessage');
$msg_pack_error->showMessage();
?>
                        } else if (status >= 100) {

                            if ($('#dup-test-delpack').is(":checked")) {
                                DupPro.Tests.UpdateStatus(status, true);
                                DupPro.Tests.TestDelete(packId);
                            } else {
                                DupPro.Tests.UpdateStatus(99, true);
                                DupPro.Tests.UpdateStatus(status, false);
<?php
$msg_pack_completed->showMessage();
?>
                            }
                        } else {
                            DupPro.Tests.UpdateStatus(status, true);
                            setTimeout(function () {
                                DupPro.Tests.TestBuildStatus(packId);
                            }, 1000);
                        }
                        /*console.log(result);*/
                    } else {
                        DupPro.Tests.checkUpdate(DupPro.Tests.currentTestAccordion, false);
                    }
                },
                error: function (result) {
                    var result = result || new Object();
<?php
$msg_ajax_error->showMessage();
?>
                }
            });
        };

        DupPro.Tests.TestDelete = function (packId) {
            var input = {
                action: 'duplicator_pro_get_package_delete',
                id: packId,
                nonce: '<?php echo $wp_test_nonce; ?>'
            };

            $.ajax({
                type: "POST",
                cache: false,
                url: ajaxurl,
                dataType: "json",
                timeout: 10000000,
                data: input,
                complete: function () {},
                success: function (result) {
                    if (DupPro.Tests.checkAjaxResult(result)) {
                        DupPro.Tests.checkUpdate($('.test_check_clean'), result.data.data.delete);
<?php
$msg_pack_completed->showMessage();
?>
                    } else {
                        DupPro.Tests.checkUpdate($('.test_check_clean'), false);
                    }
                },
                error: function (result) {
                    var result = result || new Object();
<?php
$msg_ajax_error->showMessage();
?>
                }
            });
        };

        DupPro.Tests.GetPackageLog = function (domElem) {
            if (DupPro.Tests.currPackId === false) {
                return;
            }
            
            var input = {
                action: 'duplicator_pro_get_package_log',
                id: DupPro.Tests.currPackId ,
                lines:  50,
                nonce: '<?php echo $wp_test_nonce; ?>'
            };

            $.ajax({
                type: "POST",
                cache: false,
                url: ajaxurl,
                dataType: "json",
                timeout: 10000000,
                data: input,
                complete: function () {},
                success: function (result) {
                    if (DupPro.Tests.checkAjaxResult(result)) {
                       console.log(result);
                       domElem.append('<br><b>Last log lines</b><br><pre>' + result.data.data.log + '</pre>');
                    } else {
                        
                    }
                },
                error: function (result) {
                    var result = result || new Object();
<?php
$msg_ajax_error->showMessage();
?>
                }
            });
        };

        DupPro.Tests.Init();
    });
</script>
