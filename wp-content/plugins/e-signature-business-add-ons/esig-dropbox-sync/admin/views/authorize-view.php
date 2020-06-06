
<?php

if (Esig_Dropbox_Settings::is_dbox_default_enabled()) {
    $checked = "checked";
} else {
    $checked = "";
}
 
    $spaceUsed= esigDsSetting::instance()->spaceUsed();
    $allocation =esigget('allocation',$spaceUsed);// return a array
    $allocated =esigget('allocated',$allocation);
    $used= esigget('used',$spaceUsed);
    $used_quota = round($used / 1073741824, 1);
    $quota = round($allocated / 1073741824, 1);
    $remainingQuata= $allocated-$used;
   
?>
 <div style="padding:0 10px;"><div class="esig-settings-wrap">
         
     <p>
              You have used <?php echo $used_quota ?>
              <acronym title="Gigabyte">GB</acronym> of <?php echo $quota; ?> GB. You now have <?php echo round(($remainingQuata/$allocated  ) * 100, 0); ?> % free space in your Dropbox account.

              <a href="admin.php?page=esign-misc-general&unlink">Unlink</a>
              your <?php echo $account->getDisplayName(); ?> Dropbox account.
              </p>    
         
<p id="esig_dropbox_option">
    <a href="#" class="tooltip">
        <img src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/help.png" height="20px" width="20px" align="left" />
        <span>
            <?php _e('You can set your default Dropbox PDF Sync settings here but override them on each document.  Everytime a document is signed by ALL parties a PDF is generated and synced in your Dropbox apps folder.', 'esig'); ?>
        </span>
    </a>
    <input type="checkbox" <?php echo $checked; ?> id="esig_dropbox_default" name="esig_dropbox_default" value="1"><?php _e('Sync PDF to Dropbox once document is signed by everyone', 'esig'); ?>
</p>
     
 </div></div>
