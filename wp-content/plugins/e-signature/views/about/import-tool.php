

<?php


$super_admin_wp_data = get_userdata(WP_E_Sig()->user->esig_get_super_admin_id());

$plugins = get_plugins();
$active_plugins = get_option('active_plugins', array());

  global $wpdb;   
  $firstSignedDate = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT last_modified FROM " . Esign_Query::table_name(Esign_Query::$table_documents) . " WHERE document_status=%s order by document_id ASC LIMIT 1 ","signed"
                )
        );

 $lastSignedDate = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT last_modified FROM " . Esign_Query::table_name(Esign_Query::$table_documents) . " WHERE document_status=%s order by document_id DESC LIMIT 1 ","signed"
                )
        );

?>


<img src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/logo.svg">

<p><?php _e('import information', 'esig'); ?> </p>
<form action="" method="POST">
    <p class="submit">
        <a href="#" onclick="copyToClipboard()" id="esig-copy-clipboard" class="button-primary esig-debug-report"><?php _e('Copy To Clipboard', 'esig'); ?></a>
       
    </p>
    
    
    <?php 
      $active_integration  = "";
      $numberOfIntregation=0;
     foreach ($plugins as $plugin_path => $plugin) {
            if (strpos($plugin_path,"signature")){
                $numberOfIntregation++;
                $active_integration .= "\t\t\t" . $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
            }
           
        }
    
    ?>

    <textarea readonly id="esig-import-info-textarea" name="esig_import_info">
            
### Begin Import Report ###

        <?php do_action('edd_system_info_before'); ?>

===== Site Info =====

Number of SIGNED Documents:                <?php echo WP_E_Sig()->document->getDocumentsTotal('signed') . "\n"; ?>
Number of UNIQUE Signer:                   <?php echo WP_E_Sig()->user->getUserTotal() . "\n"; ?>
Date of the first SIGNED document:         <?php echo $firstSignedDate . "\n"; ?>
Date of the last SIGNED document:          <?php echo $lastSignedDate . "\n"; ?>
Number of Stand Alone Documents:           <?php echo WP_E_Sig()->document->getDocumentsTotal('stand_alone') . "\n"; ?>
Number of Integrations:                    <?php echo $numberOfIntregation . "\n"; ?>



===== E-Signature  Active Integration =====
                      
    <?php
                echo $active_integration;
        ?>

    


### End Import Report ###

    </textarea>


</form>


<script type="text/javascript">
    function copyToClipboard() {

        //var text = document.getElementById('#esig-system-info-textarea').innerHTML;
        var copyTextarea = document.querySelector('#esig-import-info-textarea');
        copyTextarea.select();

        try {
            var successful = document.execCommand('copy');
           // var msg = successful ? 'successful' : 'unsuccessful';
            //console.log('Copying text command was ' + msg);
            document.getElementById("esig-copy-clipboard").innerHTML = 'Copied Successfully';
            //alert();
        } catch (err) {
            alert("Unable to copy");
        }
    }
</script>

