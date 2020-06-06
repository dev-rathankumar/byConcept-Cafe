

<div id="esig-log-viewer">
    
                <?php if(file_exists(ESIG_LOG_DIR . "esign-error-log.log")) : ?>
    
		<textarea width="100%" cols="100" rows="25"><?php echo esc_textarea( file_get_contents( ESIG_LOG_DIR . "esign-error-log.log" ) ); ?></textarea>
                
                <?php endif ; ?>
</div>

