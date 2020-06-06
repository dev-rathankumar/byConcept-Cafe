<?php

if ( !class_exists("Puca_Tbay_mmenu_menu") ) {
    class Puca_Tbay_mmenu_menu extends Walker_Nav_Menu {
        
       /**
         * __construct function.
         * 
         * @access public
         * @return void
         */
        public function __construct() {
            add_filter('nav_menu_css_class' , array($this, 'add_nav_class'), 10 , 2);
        }
        
        /**
         * special_nav_class function.
         * 
         * @access public
         * @param mixed $classes
         * @param mixed $item
         * @return void
         */
        public function add_nav_class($classes, $item){
            if(in_array('current-menu-item', $classes)){
                $classes[] = 'active ';
            }
            return $classes;
        }
        
        /**
         * start_lvl function.
         * 
         * @access public
         * @param mixed &$output
         * @param mixed $depth
         * @return void
         */
        public function start_lvl( &$output, $depth=0 ,$args = array() ) {
            $indent = str_repeat( "\t", $depth );
            $output    .= "\n$indent<ul class=\"sub-menu\">\n";

        }

        /**
         * Ends the list of after the elements are added.
         *
         * @see Walker::end_lvl()
         *
         * @since 3.0.0
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   An array of arguments. @see wp_nav_menu()
         */
        public function end_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        /**
         * start_el function.
         * 
         * @access public
         * @param mixed &$output
         * @param mixed $item
         * @param int $depth (default: 0)
         * @param array $args (default: array())
         * @param int $id (default: 0)
         * @return void
         */
       public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

            $li_attributes = '';

            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';
            $classes[] = 'menu-item-' . $item->ID;

            $class_names = join( ' ', $classes );

            $tbay_mega_profile = $this->getSubMegaMenuProfile($item, $depth);
            if ( $tbay_mega_profile ) {
                $args->has_children = true; 
                $class_names .= ' has-submenu';
            }

            $class_names = ' class="' . esc_attr( $class_names ) . '"';

            $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
            $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

            $output .= $indent . '<li' . $id . $class_names . $li_attributes . '>';

            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
            $attributes .= ! empty( $item->xfn ) ? ' rel="'    . esc_attr( $item->xfn ) .'"' : '';
            $attributes .= ! empty( $item->url ) ? ' href="'   . esc_url( $item->url ) .'"' : '';


            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . $this->display_icon($item) . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
            $item_output .= $this->generateSubMegaMenu( $item , $tbay_mega_profile );
            
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }

        /**
         * display_element function.
         * 
         * @access public
         * @param mixed $element
         * @param mixed &$children_elements
         * @param mixed $max_depth
         * @param int $depth (default: 0)
         * @param mixed $args
         * @param mixed &$output
         * @return void
         */
        public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
            if ( ! $element ) {
                return;
            }

            $id_field = $this->db_fields['id'];
            $id       = $element->$id_field;

            //display this element
            $this->has_children = ! empty( $children_elements[ $id ] );
            if ( isset( $args[0] ) && is_array( $args[0] ) ) {
                $args[0]['has_children'] = $this->has_children; // Back-compat.
            }

            $cb_args = array_merge( array(&$output, $element, $depth), $args);
            call_user_func_array(array($this, 'start_el'), $cb_args);

            // descend only when the depth is right and there are childrens for this element
            if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

                foreach ( $children_elements[ $id ] as $child ){

                    if ( !isset($newlevel) ) {
                        $newlevel = true;
                        //start the child delimiter
                        $cb_args = array_merge( array(&$output, $depth), $args);
                        call_user_func_array(array($this, 'start_lvl'), $cb_args);
                    }
                    $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
                }
                unset( $children_elements[ $id ] );
            }

            if ( isset($newlevel) && $newlevel ){
                //end the child delimiter
                $cb_args = array_merge( array(&$output, $depth), $args);
                call_user_func_array(array($this, 'end_lvl'), $cb_args);
            }

            //end this element
            $cb_args = array_merge( array(&$output, $element, $depth), $args);
            call_user_func_array(array($this, 'end_el'), $cb_args);
        }

        /**
         *
         */
        public function getSubMegaMenuProfile($item, $depth ) {
            return isset($item->tbay_mega_profile) && $item->tbay_mega_profile ? $item->tbay_mega_profile : false;
        }

        /**
         *
         */
        public function generateSubMegaMenu( $item, $tbay_mega_profile ) {
            global $tbaymegamenu; 
            if ( $tbay_mega_profile ) {

                $args = array(
                    'name'        => $tbay_mega_profile,
                    'post_type'   => 'tbay_megamenu',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $posts = get_posts($args);
                if ( $posts && isset($posts[0]) ) {
                    $post = $posts[0];
                    $tbaymegamenu = true;

                    if( puca_is_elementor_activated() && Elementor\Plugin::$instance->db->is_built_with_elementor( $post->ID ) ) {
                        $content = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post->ID );
                    } else {
                        $content = do_shortcode( $post->post_content );
                    } 

                    $tbaymegamenu  = false;
                    return '<div class="sub-menu"><div class="dropdown-menu-inner">'.$content.'</div></div>';
                }
            }
            return '';
        }

        public function display_icon($item) {
            if ( $item->tbay_icon_image ) {
                return '<img src="'.esc_url($item->tbay_icon_image).'" alt="'.esc_attr($item->title).'"/>';
            } elseif ( $item->tbay_icon_font ) {
                return '<i class="fa '.esc_attr($item->tbay_icon_font).'"></i>';
            }
        }
    }
}