<?php

/**
 * Display any messages admin notices on the E-Signature settings screens.
 *
 * Includes JS to allow for dismissible notices.
 */
function esig_show_messages() {
    $license_info = Esign_licenses::check_license();

    if ( ! empty( $license_info->esig_message ) ) {
        $class = $license_info->license === 'expired' ? 'notice-error' : 'notice-success';
        if ( ! isset( $_COOKIE['dismissed-esig_message'] ) ) {
            ?>
            <script>
                jQuery(function($) {
                    $( document ).on( 'click', '.esig-message-notice .notice-dismiss', function () {
                        var type = $( this ).closest( '.esig-message-notice' ).data( 'notice' );
                        $.ajax( ajaxurl,
                            {
                                type: 'POST',
                                data: {
                                    action: 'esig_dismissed_notice_handler',
                                    type: type,
                                }
                            } );
                    } );
                });
            </script>
            <h2></h2><!-- Necessary empty h2 so the admin notices don't get repositioned -->
            <div class="notice esig-message-notice <?php echo $class; ?> is-dismissible" data-notice="esig_message">
                <p><?php echo $license_info->esig_message; ?></p>
            </div>
            <?php
        }
    }
}
add_action( 'esig_after_settings_banner', 'esig_show_messages' );

/**
 * AJAX handler to store the state of dismissible notices as a cookie that expires in 48 hours.
 */
function esig_ajax_notice_handler() {
    $type = ! empty( $_POST['type'] ) ? 'dismissed-' . sanitize_text_field( $_POST['type'] ) : false;
    if ( false !== $type ) {
        setcookie( $type, 1, current_time( 'timestamp' ) + DAY_IN_SECONDS * 2 );
    }
    exit;
}
add_action( 'wp_ajax_esig_dismissed_notice_handler', 'esig_ajax_notice_handler' );
