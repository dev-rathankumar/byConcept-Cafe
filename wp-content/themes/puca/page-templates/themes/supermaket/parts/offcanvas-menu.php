<?php 
    $tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type') );
    if ( empty($tbay_header) ) {
        $tbay_header = 'v1';
    }
    $location = 'mobile-menu';
    $tbay_location  = '';
    if ( has_nav_menu( $location ) ) {
        $tbay_location = $location;
    }

    $menu_option            = apply_filters( 'puca_menu_mobile_option', 10 );
    $menu_title             = puca_tbay_get_config('menu_mobile_title', 'Menu mobile');


    $menu_one_id    =  puca_tbay_get_config('menu_mobile_one_select');

?>
  

<?php if( $menu_option == 'treeview' ) : ?>

<div id="tbay-mobile-menu" class="tbay-offcanvas hidden-lg hidden-md <?php echo esc_attr($tbay_header);?>"> 
    <div class="tbay-offcanvas-body">


        <?php if( isset($menu_title) && !empty($menu_title) ) : ?>
            <div class="offcanvas-head">
                <?php echo trim($menu_title); ?>
                <button type="button" class="btn btn-toggle-canvas btn-danger" data-toggle="offcanvas">x</button>
            </div>
        <?php endif; ?>
        

        <nav id="tbay-mobile-menu-navbar-treeview" class="navbar navbar-offcanvas navbar-static">
            <?php


                $args = array(
                    'fallback_cb' => '',
                );

                if( empty($menu_one_id) ) {
                    $args['theme_location']     = $tbay_location;
                } else {
                    $args['menu']               = $menu_one_id;
                }

                $args['menu_class']         =   'menu treeview nav navbar-nav';
                $args['container_class']    =   'navbar-collapse navbar-offcanvas-collapse';
                $args['menu_id']            =   'main-mobile-menu';
                $args['walker']             =   new Puca_Tbay_Nav_Menu();

                wp_nav_menu($args);


            ?>
        </nav>


    </div>
</div>

<?php endif; ?>