<div class="dokan-seller-listing">

    <?php
        global $post;
        $pagination_base = str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) );

        if ( $search == 'yes' ) {
        $search_query = isset( $_GET['dokan_seller_search'] ) ? sanitize_text_field( $_GET['dokan_seller_search'] ) : '';

        if ( ! empty( $search_query ) ) {
            printf( '<h2>' .__( 'Search Results for: %s', 'puca' ) . '</h2>', $search_query );
        }
    ?>
        <div class="wrapper-dokan">
            <span class="btn-showserach-dokan btn btn-theme btn-outline btn-sm"><i class="fa fa-search" aria-hidden="true"></i></span>
            <form role="search" method="get" class="dokan-seller-search-form" action="">
                <input type="search" id="search" class="search-field dokan-seller-search" placeholder="<?php esc_attr_e( 'Search Vendor &hellip;', 'puca' ); ?>" value="<?php echo esc_attr( $search_query ); ?>" name="dokan_seller_search" title="<?php esc_attr_e( 'Search seller &hellip;', 'puca' ); ?>" />
                <input type="hidden" id="pagination_base" name="pagination_base" value="<?php echo trim($pagination_base); ?>" />
                <input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( 'dokan-seller-listing-search' ); ?>" />
                <div class="dokan-overlay" style="display: none;"><span class="dokan-ajax-loader"></span></div>
            </form>
        </div>
        <?php
            /**
             *  Added extra search field after store listing search
             *
             * `dokan_after_seller_listing_serach_form` - action
             *
             *  @since 2.5.7
             *
             *  @param array|object $sellers
             */
            do_action( 'dokan_after_seller_listing_serach_form', $sellers );
        ?>

    <?php }
    else
    {
        $search_query = null;
    }
    $template_args = array(
        'sellers'         => $sellers,
        'limit'           => $limit,
        'offset'          => $offset,
        'paged'           => $paged,
        'search_query'    => $search_query,
        'pagination_base' => $pagination_base,
        'per_row'         => $per_row,
        'search_enabled'  => $search,
        'image_size'      => $image_size,
    );

    echo dokan_get_template_part( 'store-lists-loop', false, $template_args );
    ?>
</div>
