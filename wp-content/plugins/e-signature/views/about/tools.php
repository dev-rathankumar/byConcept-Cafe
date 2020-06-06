<div class="wrap">
    <h2><?php _e('Export / Import Settings', 'esig'); ?></h2>
    
    <p><?php _e('This tool is NOT meant to be used to import/export E-Signature files into another site. This tool is to be used for same-site back up purposes only. If you are looking to import/export to a different site, take a look at <a href="https://www.approveme.com/wp-digital-signature-plugin-docs/article/import-export-tool/" target="_blank">this helpful article!</a>', 'esig'); ?></p>

    <?php echo WP_E_Notice::instance()->esig_print_notice(); ?>

    <div class="metabox-holder">
        <?php do_action('esig_export_import_top'); ?>
        <div class="postbox">
            <h3><span><?php _e('Export settings / documents', 'esig'); ?></span></h3>
            
            <div class="inside">
                <p><?php _e('Export the WP E-Signature settings for this site as a xml file.', 'esig'); ?></p>

                <form method="post" action="<?php echo admin_url('admin.php?page=esign-docs'); ?>">
                    <p>
                        <input type="hidden" name="esig_action" value="approveme_db_export" />
                    </p>
                    <p>
                        <?php wp_nonce_field('esig_export_nonce', 'esig_export_nonce'); ?>
                        <?php submit_button(__('Export', 'esig'), 'secondary', 'submit', false); ?>
                    </p>
                </form>
            </div><!-- .inside -->
        </div><!-- .postbox -->
        <div class="postbox">
            <h3><span><?php _e('Import settings / documents', 'esig'); ?></span></h3>
            <div class="inside">
                <p><?php _e('Import the WP E-Signature settings and documents from a xml file. This file can be obtained by exporting the settings on same site using the form above. This import will overwrite settings and documents.', 'edd'); ?></p>
                
                <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=esign-docs'); ?>">
                    <p>
                        <input type="file" id="approveme-import-file" name="aproveme_import"/>
                    </p>
                    <p>
                        <input type="hidden" name="esig_action" value="approveme_db_import" />
                        <?php wp_nonce_field('esig_import_nonce', 'esig_import_nonce'); ?>
                        <?php submit_button(__('Import', 'esig'), 'secondary', 'submit', false, array( 'id' => 'esig-import-submit' )); ?>
                    </p>
                </form>
                
            </div><!-- .inside -->
        </div><!-- .postbox -->

        <?php //if(!esigMigrate::instance()->is_db_updated()) :?>
        <div class="postbox">
            <h3><span><?php _e('Update E-Signature Database', 'esig'); ?></span></h3>
            <div class="inside">
                <p><?php _e('Update the WP E-Signature database. This should only be done if you have already exported your database.', 'esig'); ?></p>
                <form method="post" action="<?php echo admin_url('admin.php?page=esign-docs'); ?>">

                    <p>
                        <input type="hidden" name="esig_action" value="approveme_db_migrate" />
                        <?php wp_nonce_field('esig_migrate_nonce', 'esig_migrate_nonce'); ?>
                        <?php submit_button(__('Update', 'esig'), 'primary', 'submit', false); ?>
                    </p>
                </form>
            </div><!-- .inside -->
        </div><!-- .postbox -->
        <?php //endif ; ?> 
        
        <?php do_action('esig_export_import_bottom'); ?>
    </div><!-- .metabox-holder -->
</div><!-- .wrap -->

<?php
$migrateSuccess = esigget('msuccess');
$clean = esigget('clean');
if($clean){
    delete_option('esig_database_migrated');
    esig_unsetcookie('esig_db_exported');
    delete_option('esig_db_exported');
    delete_option('esig_database_migrated');
}

if ($migrateSuccess) {


    ?>

    <link rel='stylesheet' id='esig-dialog-notice-css'  href='<?php echo ESIGN_ASSETS_DIR_URI; ?>/css/esig-dialog.css' type='text/css' media='screen' />
    <link rel='stylesheet' id='esig-notices-css'  href='<?php echo ESIGN_DIRECTORY_URI; ?>lib/export/views/notices.css' type='text/css' media='screen' />
    
    <div id="esig-db-update-now-dialog" class="esig-db-update-dialog" style="display:none;">

        <div align="center" style="margin-bottom: 20px;"> <h1>Update E-Signature Database</h1> </div>

        <div id="esig-db-export-complete-window">

            <div class="migration-running">
                <img src="<?php echo ESIGN_ASSETS_DIR_URI . "/images/boss.svg"; ?>" width="150px" height="150px"/>
            </div> 

            <div class="esig-migrate-complete-message">
    <?php _e("Migration Complete!", "esig"); ?>  
            </div> 

        </div>

    </div>

    <script type="text/javascript">
        //window.onload=function(){TB_show("Google", "http://www.google.com", false);}
        jQuery(document).ready(function () {
            
            jQuery("#esig-db-update-now-dialog").dialog({
                dialogClass: 'esig-dialog',
                height: 600,
                width: 600,
                modal: true,
                
            });
            
        });



    </script>  

<?php }  // migrat success end here ..

  include_once  ESIGN_PLUGIN_PATH . '/lib/export/views/import-progress-bar.php';


?>

    



