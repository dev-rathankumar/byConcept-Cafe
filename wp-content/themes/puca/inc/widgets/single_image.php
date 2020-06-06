<?php

class puca_Tbay_Single_Image extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'tbay_single_image',
            esc_html__('Tbay Single Image Widget', 'puca'),
            array( 'description' => esc_html__( 'Show single image', 'puca' ), )
        );
        $this->widgetName = 'single_image';

        add_action('admin_enqueue_scripts', array($this, 'scripts'));
    }

    public function scripts() {
        wp_enqueue_script( 'tbay-upload-image', TBAY_FRAMEWORK_URL . 'assets/upload.js', array( 'jquery', 'wp-pointer' ), TBAY_FRAMEWORK_VERSION, true );
    }

    public function getTemplate() {
        $this->template = 'single-image.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Single Image',
            'alt' => '',
            'single_image' => '',
            'description' => ''
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'puca' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e( 'Description:', 'puca' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>" rows="3" cols="30"><?php echo esc_attr($instance['description']); ?></textarea>
        </p>
        
        <label for="<?php echo esc_attr($this->get_field_id( 'single_image' )); ?>"><?php esc_html_e( 'Image:', 'puca' ); ?></label>
        <div class="screenshot">
            <?php if ( isset($instance['single_image']) && $instance['single_image'] ) { ?>
                <img src="<?php echo esc_url($instance['single_image']); ?>">
            <?php } ?>
        </div>
        <input class="widefat upload_image" id="<?php echo esc_attr($this->get_field_id( 'single_image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'single_image' )); ?>" type="hidden" value="<?php echo esc_attr($instance['single_image']); ?>" />
        <div class="upload_image_action">
            <input type="button" class="button add-image" value="Add">
            <input type="button" class="button remove-image" value="Remove">
        </div>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'alt' )); ?>"><?php esc_html_e( 'Alt:', 'puca' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'alt' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'alt' )); ?>" type="text" value="<?php echo esc_attr($instance['alt']); ?>" />
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['alt'] = ( ! empty( $new_instance['alt'] ) ) ? strip_tags( $new_instance['alt'] ) : '';
        $instance['description'] = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['single_image'] = ( ! empty( $new_instance['single_image'] ) ) ? strip_tags( $new_instance['single_image'] ) : '';
        return $instance;

    }
}