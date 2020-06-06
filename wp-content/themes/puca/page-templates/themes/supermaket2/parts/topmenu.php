<?php if ( has_nav_menu( 'topmenu' ) ): ?>
    <?php
        $args = array(
            'theme_location'  => 'topmenu',
            'menu_class'      => 'tbay-menu-top list-inline',
            'fallback_cb'     => '',
            'menu_id'         => 'topmenu'
        );
        wp_nav_menu($args);
    ?>
<?php endif; ?>