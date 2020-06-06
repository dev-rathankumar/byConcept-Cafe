<?php $tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type', 'v1') ); ?>

<div id="tbay-mobile-menu" class="tbay-offcanvas hidden-lg hidden-md <?php echo esc_attr($tbay_header);?>"> 
    <div class="tbay-offcanvas-body">
        <div class="offcanvas-head bg-primary">
            <button type="button" class="btn btn-toggle-canvas btn-danger" data-toggle="offcanvas">x</button>
        </div>

        <nav class="navbar navbar-offcanvas navbar-static">
            <?php
                $args = array(
                    'theme_location' => 'primary',
                    'container_class' => 'navbar-collapse navbar-offcanvas-collapse',
                    'menu_class' => 'nav navbar-nav',
                    'fallback_cb' => '',
                    'menu_id' => 'main-mobile-menu',
                    'walker' => new puca_Mobile_Menu()
                );
                wp_nav_menu($args);
            ?>
        </nav>

    </div>
</div>