<?php
/**
 * Setting page for wp-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.1
 * @package woo-delivery-area-pro
 */

if ( isset( $_REQUEST['_wpnonce'] ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
		die( 'Cheating...' );
	} else {
		if(isset($response['success']))
		unset($_POST);
		else
		$data = $_POST;
	}
}

$form  = new WDAP_FORM();
$form->set_header( esc_html__( 'Reset Plugin Settings','woo-delivery-area-pro' ), $response );

$form->add_element(
	'message', 'reset_plugin_message', array(
		'value' => esc_html__( 'Type "YES" & click on "Reset" button to restore the plugin\'s default settings. All customizations made to plugin\'s settings page will be lost & plugin will be reset.', 'woo-delivery-area-pro' ),
		'class' => 'fc-msg fc-success',
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);

$form->add_element(
	'text', 'plugin_install_fresh_settings', array(
		'label'  => esc_html__( 'Verify Action', 'woo-delivery-area-pro' ),
		'value' => isset( $data['plugin_install_fresh_settings'] ) ? $data['plugin_install_fresh_settings'] : '',
		'class'  => 'form-control',
		'desc'   => esc_html__( 'Type "YES" and click on button below to install plugin\'s default settings again.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-5">',
		'after'  => '</div>',
	)
);


$form->add_element(
	'submit', 'plugin_fresh_install_submit', array(
		'value' => esc_html__( 'Reset','woo-delivery-area-pro' ),
	)
);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);


$form->render();
