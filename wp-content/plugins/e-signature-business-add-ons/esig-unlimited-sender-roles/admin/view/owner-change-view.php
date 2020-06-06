<p>
    <a href="#" class="tooltip">
        <img src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/help.png" height="20px" width="20px" align="left" />
        <span>
            <?php _e('Document owner can hand over document owner to other e-signature sender/superadmin', 'esig'); ?>
        </span>
    </a>
    <?php _e('Document Owner', 'esig'); ?>
    
   <select id="esigOwnerId" class="esig-select2" style="width:200px;" data-placeholder=" __('Select a page...', 'esig-sad')" name="esigOwnerId">
       
        <?php 
        
        foreach($data['ownerList'] as $key => $value){
            $selected = ($key==$data['ownerId']) ? 'selected':NULL;
            echo '<option value="'. $key .'" ' . $selected .'>'. $value .'</option>';
        }
        
        ?>
       
   </select> 
    
    
</p>