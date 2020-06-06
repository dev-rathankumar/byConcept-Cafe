<?php
/**
 * functions preset for tbay framework
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Framework
 */


function tbay_framework_init_redux() {

    add_action( 'tbay_framework_preset', 'tbay_framework_redux_preset' );
    add_action( 'admin_enqueue_scripts', 'tbay_framework_redux_scripts' );

    add_action( 'wp_ajax_tbay_framework_new_preset', 'tbay_framework_redux_save_new_preset' );
    add_action( 'wp_ajax_nopriv_tbay_framework_new_preset', 'tbay_framework_redux_save_new_preset' );

    add_action( 'wp_ajax_tbay_framework_set_default_preset', 'tbay_framework_redux_set_default_preset' );
    add_action( 'wp_ajax_nopriv_tbay_framework_set_default_preset', 'tbay_framework_redux_set_default_preset' );

    add_action( 'wp_ajax_tbay_framework_delete_preset', 'tbay_framework_redux_delete_preset' );
    add_action( 'wp_ajax_nopriv_tbay_framework_delete_preset', 'tbay_framework_redux_delete_preset' );
    
    add_action( 'wp_ajax_tbay_framework_duplicate_preset', 'tbay_framework_redux_duplicate_preset' );
    add_action( 'wp_ajax_nopriv_tbay_framework_duplicate_preset', 'tbay_framework_redux_duplicate_preset' );
}

function tbay_framework_redux_scripts() {
    wp_enqueue_script( 'tbay-framework-admin', TBAY_FRAMEWORK_URL . 'assets/admin.js', array( 'jquery'  ), '20131022', true );
    wp_enqueue_style( 'tbay-framework-admin', TBAY_FRAMEWORK_URL . 'assets/backend.css' );
}

function tbay_framework_redux_duplicate_preset() {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $preset = isset($_POST['default_preset']) ? $_POST['default_preset'] : '';
    $opt_name = apply_filters( 'tbay_framework_get_opt_name' );
    $preset_option = get_option( $opt_name.$preset );
    
    $key = strtotime('now');
    if ( !empty($title) ) {
        $presets = get_option( 'tbay_framework_presets' );
        $key = strtotime('now');
        $presets[$key] = $title;
        update_option( 'tbay_framework_presets', $presets );
        update_option( $opt_name.$key, $preset_option );
        update_option( 'tbay_framework_preset_default', $key );
    }
}

function tbay_framework_redux_delete_preset() {
    $preset = isset($_POST['default_preset']) ? $_POST['default_preset'] : '';
    $default_preset = get_option( 'tbay_framework_preset_default' );

    if ( !empty($preset) ) {
        $presets = get_option( 'tbay_framework_presets' );
        if ( isset($presets[$preset]) ) {
            unset($presets[$preset]);
        }
        update_option( 'tbay_framework_presets', $presets );
        if ($preset == $default_preset) {
            update_option( 'tbay_framework_preset_default', '' );
        }
    }
}

function tbay_framework_redux_set_default_preset() {
    $default_preset = isset($_POST['default_preset']) ? $_POST['default_preset'] : '';
    update_option( 'tbay_framework_preset_default', $default_preset );
    die();
}

function tbay_framework_redux_save_new_preset() {
    $new_preset = isset($_POST['new_preset']) ? $_POST['new_preset'] : '';

    if ( !empty($new_preset) ) {
        $presets = get_option( 'tbay_framework_presets' );
        $key = strtotime('now');
        $presets[$key] = $new_preset;
        update_option( 'tbay_framework_presets', $presets );
        update_option( 'tbay_framework_preset_default', $key );
    }
    die();
}

function tbay_framework_redux_preset() {
    // preset
    $presets = get_option( 'tbay_framework_presets' );

    $default_preset = get_option( 'tbay_framework_preset_default' );
    if ( empty($presets) || !is_array($presets) ) {
        $presets = array();
    }
    ?>
    <section class="preset-section">
        <h3><?php esc_html_e( 'Preset Manager', 'tbay-framework' ); ?></h3>
        
        <div class="preset-content">
            <p class="note"><?php esc_html_e( 'Current preset default: ', 'tbay-framework' ); ?> <strong><?php echo (isset($presets[$default_preset]) ? $presets[$default_preset] : 'Default'); ?></strong></p>

            <label><?php esc_html_e( 'Create a new preset', 'tbay-framework' ); ?></label>
            <div><input type="text" name="new_preset" class="new_preset"> <button type="button" name="submit_new_preset" class="button submit-new-preset"><?php esc_html_e( 'Add new', 'tbay-framework' ); ?></button></div>
        
            
            <div class="set_default">
                <label><?php esc_html_e( 'Set default preset', 'tbay-framework' ); ?></label>
                <br>
                <select class="set_default_preset" name="default_preset">
                    <option value=""><?php esc_html_e( 'Default', 'tbay-framework' ); ?></option>
                    <?php foreach ($presets as $key => $preset) { ?>
                        <option value="<?php echo $key; ?>"<?php echo $key == $default_preset ? 'selected="selected"' : ''; ?>><?php echo $preset; ?></option>
                    <?php } ?>
                </select>
                <button type="button" name="submit_preset" class="button submit-preset"><?php esc_html_e( 'Set Default', 'tbay-framework' ); ?></button>
                <button type="button" name="submit_duplicate_preset" class="button submit-duplicate-preset"><?php esc_html_e( 'Duplicate', 'tbay-framework' ); ?></button>
                <button type="button" name="submit_delete_preset" class="button submit-delete-preset"><?php esc_html_e( 'Delete Preset', 'tbay-framework' ); ?></button>
                <div class="preset_des"><?php esc_html_e( 'Key:', 'tbay-framework' ); ?> <span class="key"><?php echo $default_preset; ?></span></div>
            </div>
            
        </div>
        <br>
        <br>
    </section>
    <?php
}