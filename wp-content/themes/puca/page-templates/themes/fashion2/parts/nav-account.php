<?php if ( has_nav_menu( 'nav-account' ) ): ?>
    <div class="top-menu">
        <div class="dropdown menu">
            <span data-toggle="dropdown" class="dropdown-toggle"><i class="icon-menu icons"></i></span>
            <div class="dropdown-menu dropdown-menu-right">
                <nav class="tbay-nav-account">
                    <?php
                        $args = array(
                            'theme_location'  => 'nav-account',
                            'menu_class'      => 'tbay-menu-top list-inline',
                            'fallback_cb'     => '',
                            'menu_id'         => 'nav-account'
                        );
                        wp_nav_menu($args);
                    ?>
                </nav>
            </div>
        </div>
    </div>
<?php endif; ?>