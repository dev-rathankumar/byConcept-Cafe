<?php

class puca_Tbay_Posts extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'tbay_posts',
            // Widget name will appear in UI
            esc_html__('Tbay Posts Widget', 'puca'),
            // Widget description
            array( 'description' => esc_html__( 'Grid post widget', 'puca' ), )
        );
        $this->widgetName = 'posts';
    }

    public function getTemplate() {
        $this->template = 'posts.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }

    public function form( $instance ) {
        $defaults = array(
            'title' => 'List Posts',
            'layout' => 'default' ,
            'ids' => array(),
            'class'=>''
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $posts = get_posts( array('orderby'=>'title','posts_per_page'=>-1) );

        if(isset($instance[ 'styles' ])){
            $styles = $instance[ 'styles' ];
        } else {
            $styles = 1;
        }

        $allstyles = array(
            'vertical'      => esc_html__('Vertical','puca'),
            'horizontal'    => esc_html__('Horizontal','puca'),
        );


        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo esc_html__( 'Title:', 'puca' ); ?></label>
            <br>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'ids' )); ?>"><?php echo esc_html__( 'Posts:', 'puca' ); ?></label>
            <br>
            <select multiple name="<?php echo esc_attr($this->get_field_name( 'ids' )); ?>[]" id="<?php echo esc_attr($this->get_field_id( 'ids' )); ?>" style="width:100%;height:250px;">
               <?php foreach( $posts as $value ){ ?>
                <?php
                    $selected = ( in_array($value->ID, $instance['ids'] ) )?' selected="selected"':'';
                ?>
                <option value="<?php echo esc_attr( $value->ID ); ?>" <?php echo trim($selected); ?>>
                    <?php echo esc_html( $value->post_title ); ?>
                </option>
               <?php } ?>
            </select>
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
        $instance['ids'] = $new_instance['ids'];
        $instance['styles']    = ( ! empty( $new_instance['styles'] ) ) ? strip_tags( $new_instance['styles'] ) : '';
        return $instance;
    }
}
