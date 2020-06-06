<?php
use PowerpackElements\Classes\PP_Admin_Settings;

$current_tab  = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'general';
$settings     = PP_Admin_Settings::get_settings();

?>

<div class="wrap">

    <h2>
        <?php
            $admin_label = $settings['admin_label'];
            $admin_label = trim( $admin_label ) !== '' ? trim( $admin_label ) : 'PowerPack';
            echo sprintf( esc_html__( '%s Settings', 'powerpack' ), $admin_label );
        ?>
    </h2>

    <?php \PowerpackElements\Classes\PP_Admin_Settings::render_update_message(); ?>

    <form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">

        <div class="icon32 icon32-powerpack-settings" id="icon-pp"><br /></div>

        <h2 class="nav-tab-wrapper pp-nav-tab-wrapper">
			<?php self::render_tabs( $current_tab ); ?>
        </h2>

        <?php self::render_setting_page(); ?>

    </form>

    <?php if ( 'on' != $settings['hide_support'] ) { ?>
    <hr />

    <h2><?php esc_html_e('Support', 'powerpack'); ?></h2>
    <p>
        <?php
            $support_link = $settings['support_link'];
            $support_link = !empty( $support_link ) ? $support_link : 'https://powerpackelements.com/contact/';
            esc_html_e('For submitting any support queries, feedback, bug reports or feature requests, please visit', 'powerpack'); ?> <a href="<?php echo $support_link; ?>" target="_blank"><?php esc_html_e('this link', 'powerpack'); ?></a>
    </p>
    <?php } ?>

</div>
