<?php $tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type', 'v1') ); ?>

<div id="tbay-offcanvas-main" class="tbay-offcanvas-main right verticle-menu hidden-lg hidden-md <?php echo esc_attr($tbay_header);?>"> 
    <div class="tbay-offcanvas-body">
        <div class="offcanvas-head bg-primary">
            <button type="button" class="btn btn-toggle-canvas btn-danger" data-toggle="offcanvas">x</button>
        </div>

        <?php puca_tbay_get_page_templates_parts('nav-vertical'); ?>

    </div>
</div>