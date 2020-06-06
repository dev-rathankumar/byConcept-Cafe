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

    $menu_attribute         = '';
    $menu_title             = puca_tbay_get_config('menu_mobile_title', 'Menu mobile');

    if( $menu_option == 'smart_menu' ) {
        $search_items           = puca_tbay_get_config('menu_mobile_search_items', 'Search menu item');
        $search_no_results      = puca_tbay_get_config('menu_mobile_no_esults', 'No results found.');
        $search_splash          = puca_tbay_get_config('menu_mobile_search_splash', 'What are you looking for? Start typing to search the menu.');
        $search_enable          = puca_tbay_get_config('enable_menu_mobile_search', false);
        $menu_counters          = puca_tbay_get_config('enable_menu_mobile_counters', false);  

        $menu_second            = puca_tbay_get_config('enable_menu_second', false);  
 




        $menu_mobile_themes            = puca_tbay_get_config('menu_mobile_themes', 'light');
        $menu_attribute                .= 'data-themes="' . $menu_mobile_themes . '" ';  

        /*Socials*/
        $enable_menu_social           = puca_tbay_get_config('enable_menu_social', false); 
        if( $enable_menu_social ) {
            $social_slides            = puca_tbay_get_config('menu_social_slides');  


            $social_array = array();

            if(count($social_slides) == 1 && empty($social_slides['0']['title']) && empty($social_slides['0']['url']) ) {
                 $menu_attribute           .= 'data-enablesocial="false" '; 
            } else {
                $menu_attribute           .= 'data-enablesocial="' . $enable_menu_social . '" '; 
                foreach ($social_slides as $index => $val) {

                    $social_array[$index]['icon']     =   $val['title'];
                    $social_array[$index]['url']      =   $val['url'];
                }

                $social_json = str_replace('"', "'", json_encode($social_array));

                $menu_attribute         .= 'data-socialjsons="' . $social_json . '" ';
            }

        }

        /*tabs icon*/
        if( $menu_second ) {

            $menu_second_id         =  puca_tbay_get_config('menu_mobile_second_select');

            $menu_tab_one           = puca_tbay_get_config('menu_mobile_tab_one', 'Menu');
            $menu_tab_one_icon      = puca_tbay_get_config('menu_mobile_tab_one_icon', 'fa fa-bars');            

            $menu_tab_second        = puca_tbay_get_config('menu_mobile_tab_scond', 'Categories');
            $menu_tab_second_icon   = puca_tbay_get_config('menu_mobile_tab_second_icon', 'fa fa-th');


            $menu_attribute         .= 'data-enabletabs="' . $menu_second . '" ';


            $menu_attribute         .= 'data-tabone="' . $menu_tab_one . '" ';
            $menu_attribute         .= 'data-taboneicon="' . $menu_tab_one_icon . '" ';            

            $menu_attribute         .= 'data-tabsecond="' . $menu_tab_second . '" ';
            $menu_attribute         .= 'data-tabsecondicon="' . $menu_tab_second_icon . '" ';

        }

        /*Effect */
        $enable_effects            = puca_tbay_get_config('enable_menu_mobile_effects', false);  
        $menu_attribute           .= 'data-enableeffects="' . $enable_effects . '" '; 

        if($enable_effects) {
            $effects_panels        =  puca_tbay_get_config('menu_mobile_effects_panels', '');
            $effects_listitems     =  puca_tbay_get_config('menu_mobile_effects_listitems', '');

            $menu_attribute         .= 'data-effectspanels="' . $effects_panels . '" ';
            $menu_attribute         .= 'data-effectslistitems="' . $effects_listitems . '" ';
        }


        $menu_attribute         .= 'data-counters="' . $menu_counters . '" ';
        $menu_attribute         .= 'data-title="' . $menu_title . '" ';
        $menu_attribute         .= 'data-enablesearch="' . $search_enable . '" ';
        $menu_attribute         .= 'data-textsearch="' . $search_items . '" ';
        $menu_attribute         .= 'data-searchnoresults="' . $search_no_results . '" ';
        $menu_attribute         .= 'data-searchsplash="' . $search_splash . '" ';
    }


    $menu_one_id    =  puca_tbay_get_config('menu_mobile_one_select');

?>
  

<?php if( $menu_option == 'smart_menu' ) : ?>
<div id="tbay-mobile-smartmenu" <?php echo trim($menu_attribute); ?> class="tbay-mmenu hidden-lg hidden-md <?php echo esc_attr($tbay_header);?>"> 
    <div class="tbay-offcanvas-body">

        <nav id="tbay-mobile-menu-navbar" class="navbar navbar-offcanvas navbar-static">
            <?php


                $args = array(
                    'fallback_cb' => '',
                );

                if( empty($menu_one_id) ) {
                    $args['theme_location']     = $tbay_location;
                } else {
                    $args['menu']               = $menu_one_id;
                }

                $args['container_id']       =   'main-mobile-menu-mmenu';
                $args['menu_id']            =   'main-mobile-menu-mmenu-wrapper';
                $args['walker']             =   new Puca_Tbay_mmenu_menu();

                wp_nav_menu($args);


                if( isset($menu_second) && $menu_second ) {

                    $args_second = array(
                        'menu'    => $menu_second_id,
                        'fallback_cb' => '',
                    );

                    $args_second['container_id']       =   'mobile-menu-second-mmenu';
                    $args_second['menu_id']            =   'main-mobile-second-mmenu-wrapper';
                    $args_second['walker']             =   new Puca_Tbay_mmenu_menu();
               

                    wp_nav_menu($args_second);

                }


            ?>
        </nav>


    </div>
</div>

<?php endif; ?>