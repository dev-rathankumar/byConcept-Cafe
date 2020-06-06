<?php if ( has_nav_menu( 'primary' ) ) : ?>
    <nav data-duration="400" class="hidden-xs hidden-sm tbay-megamenu slide animate navbar">
    <?php
        $args = array(
            'theme_location' => 'primary',
            'container_class' => 'collapse navbar-collapse',
            'menu_class' => 'nav navbar-nav',
            'fallback_cb' => '',
            'menu_id' => 'primary-menu-vertical',
			'walker' => new puca_Tbay_Nav_Menu()
        );
        wp_nav_menu($args);
    ?>
    </nav>
    <div class="bottom-canvas">
        <?php
            if( class_exists( 'WOOCS' ) ) {
                ?>
                <div class="tbay-currency">
                    <?php esc_html_e('Currency:  ','puca'); ?><?php echo do_shortcode( '[woocs]' ); ?>
                </div>
                <?php
            }
        ?>
        <?php puca_tbay_get_page_templates_parts('menu-account', 'offcanvas'); ?>
        <?php if(is_active_sidebar('top-copyright')) : ?>
        <div class="top-copyright hidden-md hidden-sm hidden-xs clearfix">
            <?php dynamic_sidebar('top-copyright'); ?>
            <!-- End Bottom Header Widget -->
        </div>
        <?php endif;?>
    </div>  
<?php endif; ?>