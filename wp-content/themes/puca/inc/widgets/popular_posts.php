<?php

class puca_Tbay_Popular_Post extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'tbay_popular_post',
            esc_html__('Tbay Popular Posts Widget', 'puca'),
            array( 'description' => esc_html__( 'Show list of popular posts', 'puca' ), )
        );
        $this->widgetName = 'popular_post';
    }
 
    public function getTemplate() {
        $this->template = 'popular-post.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Popular Posts',
            'layout' => 'default' ,
            'number_post' => '4',
            'post_type' => 'post'
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        // Widget admin form

        if(isset($instance[ 'styles' ])){
            $styles = $instance[ 'styles' ];
        } else {
            $styles = 1;
        }

        $allstyles = array(
            'list' => esc_html__('List','puca'),
            'grid2' => esc_html__('Grid 2','puca'),
            'grid4' => esc_html__('Grid 4','puca'),
            'feature' => esc_html__('Feature','puca'),
        );

        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'puca' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'number_post' )); ?>"><?php esc_html_e( 'Num Posts:', 'puca' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number_post' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_post' )); ?>" type="text" value="<?php echo esc_attr($instance['number_post']); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'styles' )); ?>"><?php esc_html_e( 'Styles:', 'puca' ); ?></label>

            <br>
            <?php if(!empty($allstyles)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('styles')); ?>" name="<?php echo esc_attr($this->get_field_name('styles')); ?>">
                <?php 

                foreach ($allstyles as $key => $style) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($key),

                        ( $key == $styles ) ? 'selected="selected"' : '',

                        esc_html($style)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose style post found ', 'puca'); ?>

            <?php endif; ?>

        </p>    

<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['post_type'] = $new_instance['post_type'];
        $instance['number_post'] = ( ! empty( $new_instance['number_post'] ) ) ? strip_tags( $new_instance['number_post'] ) : ''; 

        $instance['styles']    = ( ! empty( $new_instance['styles'] ) ) ? strip_tags( $new_instance['styles'] ) : '';
        return $instance;

    }
}
