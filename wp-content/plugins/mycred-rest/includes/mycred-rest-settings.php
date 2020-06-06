<?php

add_action('mycred_after_core_prefs','mycred_rest_api_settings', 99, 1);

function mycred_rest_api_settings( $object ) {

    $rest_api_settings = get_option('mycred_rest_api');

    ?>
    <h4><span class="dashicons dashicons-dashboard static"></span><label><?php _e( 'Rest API Settings', 'mycred' ); ?></label></h4>
    <div class="body ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="display: block;" id="ui-id-6" aria-labelledby="ui-id-5" role="tabpanel" aria-hidden="false">
	<label class="subheader">API Key</label>
	<ol id="myCRED-remote-api-key" class="inline">
		<li>
			<label>Key</label>
			<div class="h2"><input type="text" name="<?php echo $object->field_name( array( 'mycred_rest_api' => 'api_key' ) ); ?>" id="<?php echo $object->field_id( array( 'mycred_rest_api' => 'api_key' ) ); ?>" value="<?php echo $rest_api_settings['api_key']?>" style="width:90%;" placeholder="16, 24 or 32 characters"></div>
			<span class="description">Required for this feature to work!<br>Minimum 12 characters.</span>
		</li>
		<li>
			<label>Key Length</label>
			<div class="h2" style="line-height: 30px; color:green">(<span id="mycred-length-counter">16</span>)</div>
		</li>
		<li>
			<label>&nbsp;</label><br>
			<input type="button" id="mycred-generate-api-key" value="Generate New Key" class="button button-large button-primary">
		</li>
		<li class="block"><p><strong>Warning!</strong> Keep this key safe! Those you share this key with will be able to remotely deduct / add / transfer Points!</p></li>
	</ol>
	<label class="subheader">Incoming URI</label>
	<ol id="myCRED-remote-api-uri">
		<li>
			<div class="h2"><?= site_url(); ?> / wp-json / <input type="text" name="<?php echo $object->field_name( array( 'mycred_rest_api' => 'api_url' ) ); ?>" id="<?php echo $object->field_id( array( 'mycred_rest_api' => 'api_url' ) ); ?>" value="<?php echo $rest_api_settings['api_url']?>"> /</div>
			<span class="description">The incoming call address. Remote calls made to any other URL will be ignored.</span>
		</li>
	</ol>
</div>
    <?php
}

add_filter( 'mycred_save_core_prefs', 'mycred_rest_api_save_settings',99, 3 );

function mycred_rest_api_save_settings( $new_data, $post, $object ) {
    
    update_option('mycred_rest_api',$post['mycred_rest_api']);

    return $new_data;
}