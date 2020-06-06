<?php

//portfolio scripts
if ( !function_exists('puca_tbay_portfolio_scripts') ) {
    function puca_tbay_portfolio_scripts() {

        global $post; 

        if( isset($post->post_type) && $post->post_type == 'project' ) { 

            $suffix = (puca_tbay_get_config('minified_js', false)) ? '.min' : PUCA_MIN_JS;
            
            wp_register_script( 'jquery-magnific-popup', PUCA_SCRIPTS . '/jquery.magnific-popup' . $suffix . '.js', array( ), '1.0.0', true );             
            wp_register_script( 'jquery-shuffle', PUCA_SCRIPTS . '/jquery.shuffle' . $suffix . '.js', array( ), '1.0.0', true );             
            wp_register_style( 'magnific-popup', PUCA_STYLES . '/magnific-popup.css', array(), '1.0.0' );

            wp_enqueue_script('jquery-shuffle');
            wp_enqueue_script('jquery-magnific-popup');
            wp_enqueue_style('magnific-popup');

        } 
    }

    add_action('wp_enqueue_scripts', 'puca_tbay_portfolio_scripts');
}

//Project organize
remove_action( 'projects_before_single_project_summary', 'projects_template_single_title', 10 );
add_action( 'projects_single_project_summary', 'projects_template_single_title', 5 );

add_action( 'projects_before_main_project', 'projects_template_single_title', 5 );

remove_action( 'projects_before_single_project_summary', 'projects_template_single_short_description', 20 );
remove_action( 'projects_before_single_project_summary', 'projects_template_single_gallery', 40 );
add_action( 'projects_single_project_gallery', 'puca_projects_template_single_gallery', 40 );
//projects list
remove_action( 'projects_loop_item', 'projects_template_loop_project_title', 20 ); 

if ( ! function_exists( 'puca_portfolio_loop_columns' ) ) {
    function puca_portfolio_loop_columns( $columns ) {

        if( isset($_GET['columns']) && is_numeric($_GET['columns']) ) {
            $columns = $_GET['columns'];
        } else {
            $columns = puca_tbay_get_config('portfolio_columns', 4);
        }
        
        return $columns;
    }
    add_filter( 'projects_loop_columns', 'puca_portfolio_loop_columns' );
}

if ( ! function_exists( 'puca_portfolio_per_page' ) ) {
    function puca_portfolio_per_page( $per_page ) {

        if( isset($_GET['per_page']) && is_numeric($_GET['per_page']) ) {
            $per_page = $_GET['per_page'];
        } else {
            $per_page = puca_tbay_get_config('portfolio_per_page', 12);
        }

        return $per_page;
    }
    add_filter( 'projects_per_page', 'puca_portfolio_per_page' );
}

if ( ! function_exists( 'puca_projects_archive_random_size_image' ) ) {
    function puca_projects_archive_random_size_image( $random ) {

        if( isset($_GET['random'])) {
            $random = $_GET['random'];
        } else {
            $random = puca_tbay_get_config('portfolio_random_size_image', false);
        }

        return $random;
    }
    add_filter( 'projects_random_size_image', 'puca_projects_archive_random_size_image' );
}

if ( ! function_exists( 'puca_projects_archive_full_wide' ) ) {
    function puca_projects_archive_full_wide( $class ) {

        if( isset($_GET['full_wide'])) {
            $full_wide = $_GET['full_wide'];
        } else {
            $full_wide = puca_tbay_get_config('portfolio_full_wide', false);
        }

        if( $full_wide ) {
            $class = 'container-fluid';
        }else {
            $class = 'container';
        }

        return $class;
    }
    add_filter( 'projects_container_class', 'puca_projects_archive_full_wide' );
}

if ( ! function_exists( 'puca_portfolio_single_layout' ) ) {
    function puca_portfolio_single_layout( $layout ) {

        if( isset($_GET['single_layout'])) {
            $layout = $_GET['single_layout'];
        } else {
            $layout = puca_tbay_get_config('portfolio_single_layout', 'carousel');
        }

        return $layout;
    }
    add_filter( 'projects_single_layout', 'puca_portfolio_single_layout' );
}

if ( ! function_exists( 'puca_projects_template_single_gallery' ) ) {

    /**
     * Output the project gallery before the single project summary.
     *
     * Hooked into projects_before_single_project_summary
     *
     * @access public
     * @subpackage  Project
     * @return void
     */
    function puca_projects_template_single_gallery() {

        $layout =   apply_filters('projects_single_layout', 'carousel');

        if( isset($layout) ) {
            switch ($layout) {
                case 'full':
                    projects_get_template( 'single-project/project-gallery-full.php' );
                    break;                

                case 'stick':
                    projects_get_template( 'single-project/project-gallery-stick.php' );
                    break;       

                case 'carousel':
                    projects_get_template( 'single-project/project-gallery-carousel.php' );
                    break;
                
                default:
                    projects_get_template( 'single-project/project-gallery-full.php' );
                    break;
            }
        }
    }
    add_action( 'projects_single_gallery', 'puca_projects_template_single_gallery', 5 );
}

if ( ! function_exists( 'puca_projects_template_loop_project_thumbnail' ) ) {
    remove_action( 'projects_loop_item', 'projects_template_loop_project_thumbnail', 10 );
    add_action( 'projects_loop_item', 'puca_projects_template_loop_project_thumbnail', 10 );
    /**
     * Get the puca project thumbnail for the loop.
     *
     * Hooked into projects_loop_item
     *
     * @access public
     * @subpackage  Loop
     * @return void
     */
    function puca_projects_template_loop_project_thumbnail() {

        $full_image = apply_filters('projects_random_size_image', false);

        if($full_image) {

            $a =   array('project-thumbnail', 'project-single', 'project-archive');
            $random = $a[rand(0, count($a) - 1)];

            echo '<figure class="project-thumbnail">' . projects_get_project_thumbnail($random) . '</figure>';
        } else {
            echo '<figure class="project-thumbnail">' . projects_get_project_thumbnail() . '</figure>';
        }
    }
}