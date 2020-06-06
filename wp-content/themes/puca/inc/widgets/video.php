<?php

class puca_Tbay_Featured_Video_Widget extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'tbay_featured_video_widget',
            // Widget name will appear in UI
            esc_html__('Tbay Featured Video Widget', 'puca'),
             // Widget description
            array( 'description' => esc_html__( 'Show Featured video', 'puca' ),)
        );
        $this->widgetName = 'video';
    }

    public function getTemplate() {
        $this->template = 'video.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }

    public function form( $instance ) {
        $defaults = array(
            'title' => 'Featured Video',
            'video_link' => 'https://www.youtube.com/watch?v=sd0grLQ4voU',
            'video_name' => 'video guide',
            'video_width' =>  300
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__('Title:', 'puca' ); ?></label>
            <br>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo  esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
            <label for="<?php echo  esc_attr($this->get_field_id( 'video_link' )); ?>"><?php echo esc_html__('Video link:', 'puca' ); ?></label>
            <br>
            <input class="widefat" id="<?php echo  esc_attr($this->get_field_id('video_link')); ?>" name="<?php echo  esc_attr($this->get_field_name('video_link')); ?>" type="text" value="<?php echo esc_attr( $instance['video_link'] ); ?>" />
            <br>
            <?php echo esc_html__('Support video from Youtube and Vimeo link. Ex: https://www.youtube.com/watch?v=sd0grLQ4voU', 'puca' ); ?>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('video_name') ); ?>"><?php echo esc_html__('Video name:', 'puca' ); ?></label>
            <br>
            <input class="widefat" id="<?php echo  esc_attr($this->get_field_id('video_name')); ?>" name="<?php echo  esc_attr($this->get_field_name('video_name')); ?>" type="text" value="<?php echo esc_attr( $instance['video_name'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('video_width')); ?>"><?php echo esc_html__('Video width:', 'puca'); ?></label>
            <br>
            <input class="widefat" id="<?php echo  esc_attr($this->get_field_id('video_width')); ?>" name="<?php echo esc_attr( $this->get_field_name('video_width') ); ?>" type="text" value="<?php echo esc_attr( $instance['video_width'] ); ?>" />
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['video_link'] = $new_instance['video_link'];
        $instance['video_name'] = $new_instance['video_name'];
        $instance['video_width'] = $new_instance['video_width'];
        return $instance;
    }
}