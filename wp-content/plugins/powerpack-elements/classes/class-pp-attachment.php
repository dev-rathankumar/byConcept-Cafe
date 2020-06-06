<?php
/**
 * Attachment Data Extra fields
 *
 * @package PowerPack
 */

if ( ! class_exists( 'PP_Attachment' ) ) {

	/**
	 * Class PP_Attachment.
	 */
	class PP_Attachment {

		/**
		 * Constructor function that initializes required actions and hooks
		 *
		 * @since 1.0
		 */
		function __construct() {

			add_filter( 'attachment_fields_to_edit', array( $this, 'custom_attachment_field_link' ), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'custom_attachment_field_link_save' ), 10, 2 );
		}

		/**
		 * Add Custom Link field to media uploader
		 *
		 * @param array  $form_fields fields to include in attachment form.
		 * @param object $post attachment record in database.
		 * @return aaray $form_fields modified form fields
		 */
		function custom_attachment_field_link( $form_fields, $post ) {

			$form_fields['pp-custom-link'] = array(
				'label' => __( 'PP - Custom Link', 'powerpack' ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, 'pp-custom-link', true ),
			);

			return $form_fields;
		}


		/**
		 * Save values of Custom Link field in media uploader
		 *
		 * @param array $post the post data for database.
		 * @param array $attachment attachment fields from $_POST form.
		 * @return array $post modified post data.
		 */
		function custom_attachment_field_link_save( $post, $attachment ) {

			if ( isset( $attachment['pp-custom-link'] ) ) {
				update_post_meta( $post['ID'], 'pp-custom-link', $attachment['pp-custom-link'] );
			}

			return $post;
		}
	}

	new PP_Attachment();
}
