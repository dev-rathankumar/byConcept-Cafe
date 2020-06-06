<?php
if (!defined('ABSPATH'))
  {
  exit;
  }
// The Plugin Settings
if (!isset($settings_args))
  {
  $settings_args = array(
    array(
      'name' => __('Settings', SZBD::TEXT_DOMAIN),
      'type' => 'title',
      'id' => 'SZbD_settings',

    ),
    array(
      'name' => __('Google Maps API Key', SZBD::TEXT_DOMAIN),
      'id' => 'szbd_google_api_key',
      'type' => 'text',
      'css' => 'min-width:300px;',

        'desc' => __(' <p><a href="https://cloud.google.com/maps-platform/#get-started" target="_blank">Visit Google to get your API Key &raquo;</a> <br>Include Maps JavaScript API, Places API, Geocoding API, Directions API</p>', SZBD::TEXT_DOMAIN)
    ),
     array(
      'name' => __('Show only lowest cost shipping method?', SZBD::TEXT_DOMAIN),
      'id' => 'szbd_exclude_shipping_methods',
      'type' => 'checkbox',
      'css' => 'min-width:300px;',
      'desc' => __('At checkout, show only the "Shipping Zones by Drawing" shipping method with the lowest cost.', SZBD::TEXT_DOMAIN)
    ),
     array(
      'name' => __('Hide shipping costs at cart page?', SZBD::TEXT_DOMAIN),
      'id' => 'szbd_hide_shipping_cart',
      'type' => 'checkbox',
      'css' => 'min-width:300px;',
      'default' => 'no',
      'desc' => __('At cart page, hide the shipping costs.', SZBD::TEXT_DOMAIN)
    ),
      array(
      'type' => 'sectionend',
      'id' => 'SZbD_settings'
    ),
      array(
      'name' => __('Advanced', SZBD::TEXT_DOMAIN),
      'type' => 'title',
      'id' => 'SZbD_settings_ad',

    ),
       array(
             'name' => __( 'Deactivate Post Code restriction', SZBD::TEXT_DOMAIN ),
            'id' => 'szbd_deactivate_postcode',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
             'desc' => __('Deactivate Post code restriction. For areas with unreliable post code matches.', SZBD::TEXT_DOMAIN ),

            'default' => 'no'
        ),

      array(
             'name' => __( 'De-activate Google Maps API?', SZBD::TEXT_DOMAIN ),
            'id' => 'szbd_deactivate_google',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',

            'default' => 'no'
        ),
       array(
             'name' => __( 'Debug Mode', SZBD::TEXT_DOMAIN ),
            'id' => 'szbd_debug',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
             'desc' => __('Show request and response data from Google calls.', SZBD::TEXT_DOMAIN ),

            'default' => 'no'
        ),
       array(
      'name' => __('Secondary Google Maps API Key', SZBD::TEXT_DOMAIN),
      'id' => 'szbd_google_api_key_2',
      'type' => 'text',
      'css' => 'min-width:300px;',

        'desc' => __('If your main API Key has restrictions by HTTP referrers (web sites) you will need to enter a secondary API Key for the server to server Directions API requests wich is used for calculation of dynamic shipping rates. This Key can not be restricted by HTTP referrers (web sites) and only need the Directions API activated.', SZBD::TEXT_DOMAIN)
    ),
    array(
      'type' => 'sectionend',
      'id' => 'SZbD_settings_ad'
    ),
     array(
      'name' => __('Test Store Address Geolocation', 'szbd'),
      'type' => 'title',
      'id' => 'SZbD_settings_test',
       'desc' => __('Press button below to test if Google can geolocate your WooCommerce store address', 'szbd' ),

    ),
     array(
        'type' => 'szbd_show_test',
        'id' => 'szbd_show_test'
    ),
     array(
      'type' => 'sectionend',
      'id' => 'SZbD_settings_test'
    ),
  );
  }
if (!isset($caps))
  {
  $x      = wp_count_posts(SZBD::POST_TITLE);
  $y      = intval($x->publish) + intval($x->draft);
  $cap_1  = $y < sin(deg2rad(90)) && isset($y) ? 'edit_' . SZBD::POST_TITLE : 'edit_' . SZBD::POST_TITLE;
  $labels = array(
    'name' => __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN),
    'menu_name' => __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN),
    'name_admin_bar' => __('Shipping Zone Maps', SZBD::TEXT_DOMAIN),
    'all_items' => __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN),
    'singular_name' => __('Zone List', SZBD::TEXT_DOMAIN),
    'add_new' => __('New Shipping Zone', SZBD::TEXT_DOMAIN),
    'add_new_item' => __('Add New Zone', SZBD::TEXT_DOMAIN),
    'edit_item' => __('Edit Zone', SZBD::TEXT_DOMAIN),
    'new_item' => __('New Zone', SZBD::TEXT_DOMAIN),
    'view_item' => __('View Zone', SZBD::TEXT_DOMAIN),
    'search_items' => __('Search Zone', SZBD::TEXT_DOMAIN),
    'not_found' => __('Nothing found', SZBD::TEXT_DOMAIN),
    'not_found_in_trash' => __('Nothing found in Trash', SZBD::TEXT_DOMAIN),
    'parent_item_colon' => ''
  );
  $caps   = array(
    'edit_post' => 'edit_szbdzone',
    'read_post' => 'read_szbdzone',
    'delete_post' => 'delete_szbdzone',
    'edit_posts' => 'edit_szbdzones',
    'edit_others_posts' => 'edit_others_szbdzones',
    'publish_posts' => 'publish_szbdzones',
    'read_private_posts' => 'read_private_szbdzones',
    'delete_posts' => 'delete_szbdzones',
    'delete_private_posts' => 'delete_private_szbdzones',
    'delete_published_posts' => 'delete_published_szbdzones',
    'delete_others_posts' => 'delete_others_szbdzones',
    'edit_private_posts' => 'edit_private_szbdzones',
    'edit_published_posts' => 'edit_published_szbdzones',
    'create_posts' => $cap_1
  );
  $args   = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => false,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => false,
    'hierarchical' => false,
    'supports' => array(
      'title',
      'author'
    ),
    'exclude_from_search' => true,
    'show_in_nav_menus' => false,
    'show_in_menu' => 'woocommerce',
    'can_export' => true,
    'map_meta_cap' => true,
    'capability_type' => 'szbdzone',
    'capabilities' => $caps
  );
  }
