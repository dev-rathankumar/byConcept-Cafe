<?php

class TbayFramework_Facebook_Like_Box extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'tbay_facebook_like',
            esc_html__('Tbay Facebook Like Box', 'tbay-framework'),
            array( 'description' => esc_html__( 'Adds support for Facebook Like Box. ', 'tbay-framework' ), )
        );
        $this->widgetName = 'facebook_like';
    }

    public function getTemplate() {
        $this->template = 'facebook-like.php';
    }

    public function widget( $args, $instance ) {
        extract( $args );
        extract( $instance );
        
        $instance['show_faces'] = isset($instance['show_faces']) ? 'true' : 'false';
        $instance['show_header'] = isset($instance['show_header']) ? 'true' : 'false';
        $height = '65';
        if($instance['show_faces'] == 'true') {
            $height = '240';
        }
        if($instance['show_faces'] == 'true') {
            $height = '540';
        }
        if($instance['show_header'] == 'true') {
            $height = $height + 30;
        }
        $instance['height'] = $height;

        $this->display($args, $instance);
    }

    public function form( $instance ) {
        $defaults = array('title' => 'Find us on Facebook', 'layout' => 'default' , 'page_url' => '', 'width' => '268', 'show_faces' => 'on', 'show_header' => false);
        $instance = wp_parse_args((array) $instance, $defaults); 

        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('page_url')); ?>">Facebook Page URL:</label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('page_url')); ?>" name="<?php echo esc_attr($this->get_field_name('page_url')); ?>" value="<?php echo esc_url( $instance['page_url'] ); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('width')); ?>">Width:</label>
            <input class="widefat" type="text" style="width: 50px;" id="<?php echo esc_attr($this->get_field_id('width')); ?>" name="<?php echo esc_attr($this->get_field_name('width')); ?>" value="<?php echo esc_attr( $instance['width'] ); ?>" />
        </p>


        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_faces'], 'on'); ?> id="<?php echo esc_attr($this->get_field_id('show_faces')); ?>" name="<?php echo esc_attr($this->get_field_name('show_faces')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_faces')); ?>">Show faces</label>
        </p>


        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_header'], 'on'); ?> id="<?php echo esc_attr($this->get_field_id('show_header')); ?>" name="<?php echo esc_attr($this->get_field_name('show_header')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_header')); ?>">Show facebook header</label>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['page_url'] = $new_instance['page_url'];
        $instance['width'] = $new_instance['width'];
        $instance['color_scheme'] = $new_instance['color_scheme'];
        $instance['show_faces'] = $new_instance['show_faces'];
        $instance['show_stream'] = $new_instance['show_stream'];
        $instance['show_header'] = $new_instance['show_header'];
        $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : 'default';
        return $instance;

    }
}

register_widget( 'TbayFramework_Facebook_Like_Box' );