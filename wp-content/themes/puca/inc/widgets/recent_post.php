<?php

class puca_Tbay_Recent_Post extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'tbay_recent_post',
            esc_html__('Tbay Recent Posts Widget', 'puca'),
            array( 'description' => esc_html__( 'Show list of recent post', 'puca' ), )
        );
        $this->widgetName = 'recent_post';
    }
 
    public function getTemplate() {
        $this->template = 'recent-post.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Latest Post',
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
            'grid' => esc_html__('Grid','puca'),
        );

        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'puca' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>">
                <?php echo esc_html__('Type:', 'puca' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>">
                <?php foreach (get_post_types(array('public' => true)) as $key => $value) { ?>
                    <?php if($key!='attachment' && $key!='product'){ ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['post_type'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
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

                <?php echo esc_html__('No choose columns product found ', 'puca'); ?>

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