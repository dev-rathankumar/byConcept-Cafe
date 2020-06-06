<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('elementor/theme/before_do_header', function () {
    echo '<div class="tbay-wrapper"><div id="page" class="site">';
});

add_action('elementor/theme/after_do_header', function () {
    echo '<div class="site-content-contain"><div id="content" class="site-content">';
});

add_action('elementor/theme/before_do_footer', function () {
    echo '</div></div>';
});

add_action('elementor/theme/after_do_footer', function () {
    echo '</div>' . do_action('tbay_end_wrapper') . '</div>';
});