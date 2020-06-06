<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php
// To default a var, add it to an array
$vars = array(
    'settings_tab_class', // will default $data['settings_tab_class']
    'support_tab_class',
    'documents_tab_class',
    'mails_tab_class',
    'misc_tab_class',
    'addons_tab_class',
    'esig_more_tab',
    'alerts',
    'loop_head',
    'all_class',
    'draft_class',
    'signed_class',
    'trash_class',
    'Licenses',
    'esig-red-btn'
);
$this->default_vals($data, $vars);

$this->setting = new WP_E_Setting();
?>

<div id='esig-settings-container' class='wrap approveme_main wpd-sign' >

    <div id='esig-headlink-col1'>

        <div class="esig-masthead">
            <a href='http://www.approveme.com/wp-digital-e-signature' target='_blank' style='text-decoration:none;'>
                <img src='<?php echo ESIGN_ASSETS_DIR_URI ; ?>/images/logo.png' alt='WP E-Signature'>
            </a>
            <br>

            <img src='<?php echo ESIGN_ASSETS_DIR_URI ; ?>/images/pen_icon_gray.svg' altSigning documents just got a lot easiera='Signing documents just got easier'>
            <span class='settings-title'><?php _e('Signing documents just got a lot easier.', 'esig'); ?></span>
        </div>

        <!-- The following action allows the messaging component to inject the notices -->
        <?php do_action( 'esig_after_settings_banner' ); ?>

    </div><!--/esig-headlink-col1-->

    <div id='esig-headlink-col2'>

        <ul>
            <li class='esig-extension-headimg'>
                <span class='esig-extension-headtext'>
<?php _e('To enable more features and signature functions you should visit', 'esig'); ?>
                </span>
                <br>
                <a href='admin.php?page=esign-addons' class='esig-extension-headlink'><?php _e('E-Sign Add-On Extensions.', 'esig'); ?></a>
                <br>

                <!-- upgrade link code start here -->
<?php
$settings = new WP_E_Setting();
$esig_license_type = $settings->get_generic('esig_wp_esignature_license_type');

if ($esig_license_type == 'Individual License' || $esig_license_type == 'Professional License') {
    ?>

                    <a href="http://www.approveme.com/e-signature-upgrade-license/" target="_blank" class="esig-mini-red-btn" >Upgrade License</a>
<?php }
?>

            </li>
        </ul>
        <!-- upgrade link code end here -->
    </div><!--/esig-headlink-col2-->
</div><!--/wrap approveme_main wpd-sig-->
<div class="esign-main-tab">
    <h1 class="nav-tab-wrapper">

        <a class="nav-tab <?php echo $data['documents_tab_class']; ?>" href="?page=esign-docs"><?php _e('My Documents', 'esig'); ?></a>

        <a class="nav-tab <?php echo $data['settings_tab_class']; ?>" href="?page=esign-settings"><?php _e('Settings', 'esig'); ?></a>
        <a class="nav-tab <?php echo $data['mails_tab_class']; ?>" href="?page=esign-mails-general"><?php _e('E-Mails', 'esig'); ?></a>
<?php
if ($this->setting->esign_super_admin()) {
    ?>
            <?php
            echo $data['Licenses'];
            ?>



    <!-- <a class="nav-tab <?php echo $data['support_tab_class']; ?>" href="?page=esign-support-general"><?php _e('Premium Support', 'esig'); ?></a> -->


            <a class="nav-tab <?php echo $data['misc_tab_class']; ?>" href="?page=esign-misc-general"><?php _e('Customization', 'esig'); ?></a>


            <a class="nav-tab <?php echo $data['addons_tab_class']; ?>" href="?page=esign-addons"><?php _e('Add-Ons', 'esig'); ?></a>


    <?php echo $data['esig_more_tab']; ?>

<?php } // super admin checking finished here  ?>
    </h1>
    <br />

        <?php echo $data['alerts']; ?>


</div>

<?php
    //$notices ='' ;
     $notices=apply_filters('esig_notices_display','');



     if($notices):
?>

<div  class="esig-notices-wrap" >

    <div style="margin: 10px auto;padding: auto auto;width:90%;">

   <h2 style="margin:0 !important;padding: 0px;font-size:0;"></h2>
<?php echo $notices; ?>
    </div>

</div>

<?php endif;  ?>
