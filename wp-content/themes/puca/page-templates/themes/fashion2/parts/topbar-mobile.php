<?php   
    global $woocommerce; 
    $_id = puca_tbay_random_key();
?>
<div class="topbar-mobile  hidden-lg hidden-md  hidden-xxs clearfix">
	<div class="logo-mobile-theme logo-tablet col-xs-6 text-left">
        <?php
            $mobilelogo = puca_tbay_get_config('mobile-logo');
            $active_theme 	= puca_tbay_get_theme(); 
        ?>
        <?php if( isset($mobilelogo['url']) && !empty($mobilelogo['url']) ): ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"> 
                <img src="<?php echo esc_url( $mobilelogo['url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>">
            </a>
        <?php else: ?>
            <div class="logo-theme">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <img src="<?php echo esc_url_raw( get_template_directory_uri().'/images/'.$active_theme.'/logo.png'); ?>" alt="<?php bloginfo( 'name' ); ?>">
                </a>
            </div>
        <?php endif; ?>
	</div>
     <div class="topbar-mobile-right col-xs-6 text-right">
        <div class="active-mobile">
            <?php echo apply_filters( 'puca_get_menu_mobile_icon', 10,2 ); ?>
        </div>
        <div class="topbar-inner text-left">
            <div class="search-popup">
                <span class="show-search"><i class="icon-magnifier icons"></i></span>
                <?php puca_tbay_get_page_templates_parts('productsearchform-mobile'); ?>
            </div>
            
            <div class="setting-popup">

                <div class="dropdown">
                    <button class="account-button btn btn-sm btn-primary btn-outline dropdown-toggle" type="button" data-toggle="dropdown"><i class="icon-user icons"></i></button>
                    <div class="account-menu">
                        <?php if ( has_nav_menu( 'nav-account' ) ) { ?>
                            <?php
                                $args = array(
                                    'theme_location'  => 'nav-account',
                                    'container_class' => '',
                                    'menu_class'      => 'menu-topbar'
                                );
                                wp_nav_menu($args);
                            ?>
                        <?php } ?>
                    </div>
                </div>

            </div>

            <?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
                <div class="tbay-topcart">
                    <div id="cart-<?php echo esc_attr($_id); ?>" class="cart-dropdown dropdown version-1">
                        <a class="dropdown-toggle mini-cart v2" data-offcanvas="offcanvas-right" data-toggle="dropdown" aria-expanded="true" role="button" aria-haspopup="true" data-delay="0" href="#" title="<?php esc_html_e('View your shopping cart', 'puca'); ?>">
                            
                            <span class="text-skin cart-icon">
                                <i class="icon-bag"></i>
                                <span class="mini-cart-items">
                                <?php echo sprintf( '%d', $woocommerce->cart->cart_contents_count );?>
                                </span>
                            </span>
                            
                        </a>             
                    </div>
                </div>
                <?php puca_tbay_get_page_templates_parts('offcanvas-cart','right'); ?>
            <?php endif; ?>

        </div>
    </div>       
</div>
