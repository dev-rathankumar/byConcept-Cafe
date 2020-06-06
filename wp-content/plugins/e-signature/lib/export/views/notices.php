

<link rel='stylesheet' id='esig-test-notice-css'  href='<?php echo ESIGN_ASSETS_DIR_URI; ?>/css/esig-updater.css' type='text/css' media='screen' />
<link rel='stylesheet' id='esig-dialog-notice-css'  href='<?php echo ESIGN_ASSETS_DIR_URI; ?>/css/esig-dialog.css' type='text/css' media='screen' />
<link rel='stylesheet' id='esig-notices-css'  href='<?php echo ESIGN_DIRECTORY_URI; ?>lib/export/views/notices.css' type='text/css' media='screen' />


<div id="esig-db-migration-alert" class="notice notice-warning esig-notice" style="border-top: 1px solid #f1f1f1;padding:14px;">

    <div style="width:80%;display:inline-block;font-size: 16px;font-weight: 500;">WP E-Signature needs to upgrade your document database. <a href="https://www.approveme.com/wordpress-contract-plugin/mcrypt-php-replaced-open-ssl/" class="learn-more" style="text-decoration:underline;" target="_blank">learn more</a></div>
    <div style="width:18%;display:inline-block;text-align:right;"><a  href="#" id="esig-update-now-clicked" class="esig-update-btn"> Update Now </a></div> 

</div>

<?php
$display = $displayExport = '';
if (esig_export::instance()->isRecentExport()) {
    $displayExport = 'style="display:none"';
} else {
    $display = 'style="display:none"';
}
?>


<div id="esig-db-tools-dialog" class="esig-db-update-dialog" style="display:none;">

    <div class="esig-dialog-header" style="margin-bottom: 20px;"> <h3>Update E-Signature Database</h3> </div>

    <div id="esig-db-update-now-window" class="update-now-window" <?php echo $display; ?>>

        <div class="update-main-container">
            
            <div class="search-logo"> 
                <img src="<?php echo ESIGN_ASSETS_DIR_URI . "/images/search.svg"; ?>" width="150px" height="150px"/>
            </div>

            <div class="parent-description">
                <div class="esig-migrate-now-description">

                    <?php _e("Your documents and settings have been exported and are safe and sound. You are ready to update.", "esig"); ?>  
                </div> 
            </div>

            <div class="esig-db-update-now-btn" style="text-align: center">
                <a href="<?php echo esigMigrate::instance()->migrateLink(); ?>" id="esig-db-btn-update-now" class="button-primary"> Update Now</a>
            </div>
        </div>

    </div>

    <div id="esig-db-export-now-window" <?php echo $displayExport; ?>>

        <div class="esig-export-alert-text">
            <?php _e("BEFORE UPDATING: Please export your settings and documents. Exporting your documents protects them during database updates. If you do not export your settings and documents, they may be lost permanently.", "esig"); ?>  
        </div> 

        <div class="esig-db-update-now-btn" style="text-align: center;margin-top:30px;">
            <div style="width:48%;display:inline-block;text-align: center;"> 
                <a href="<?php echo esig_export::instance()->exportLink(); ?>" id="esig-db-btn-export-now" class="button-primary"> Export Now</a>
            </div>
            <div style="width:50%;display:inline-block;text-align:center;vertical-align: middle;font-size: 16px;font-family: vardana;font-weight:700;">
                <a href="<?php echo esigMigrate::instance()->migrateLink(); ?>" class="esig-migrate-anyway" id="esig-db-btn-update-now"> Update without exporting</a>
            </div>
        </div>

    </div>


    <div id="esig-db-update-running-window" style="display:none;">

        <div class="migration-running">
            <img src="<?php echo ESIGN_ASSETS_DIR_URI . "/images/ajax-loader.gif"; ?>"/>
        </div> 

        <div class="donot-close">
            <?php _e("Updating database please do not close or refresh this page.", "esig"); ?>  
        </div> 

    </div>

    <div id="esig-db-export-running-window" style="display:none;">

        <div class="migration-running">
            <img src="<?php echo ESIGN_ASSETS_DIR_URI . "/images/ajax-loader.gif"; ?>"/>
        </div> 

        <div class="donot-close">
            <?php _e("Exporting database please do not close or refresh this page.", "esig"); ?>  
        </div> 

    </div>

</div>

<?php $ajaxUrl = admin_url("admin-ajax.php?action=esig_check_export"); ?>
<script type="text/javascript">


    jQuery(document).ready(function () {

        var ajaxHandle;

        var approvemeAjaxCall = function () {
            jQuery.post("<?php echo $ajaxUrl; ?>", function (data) {
                if (data == 'success') {
                    if (jQuery("#esig-db-update-now-window").hasClass('activeit')) {
                        clearInterval(ajaxHandle);
                    } else {
                        jQuery("#esig-db-export-running-window").hide();
                        jQuery("#esig-db-update-now-window").show();
                        jQuery("#esig-db-update-now-window").addClass('activeit');
                    }
                }
            });
        }

        jQuery("#esig-update-now-clicked").click(function (e) {

            e.preventDefault();
            tb_show("", '#TB_inline?inlineId=esig-db-tools-dialog&width=500&height=300');
            jQuery("#TB_window").removeClass("thickbox-loading");
        });

        jQuery("#esig-db-btn-export-now").click(function () {

            jQuery("#esig-db-export-now-window").hide();
            jQuery("#esig-db-export-running-window").show();
            ajaxHandle = setInterval(approvemeAjaxCall, 500);

        });

        jQuery("#esig-db-btn-update-now").click(function () {
            jQuery("#esig-db-update-now-window").hide();
            jQuery("#esig-db-update-running-window").show();
        });

    });

</script>    