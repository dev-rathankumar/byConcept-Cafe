<?php

class TbayFramework_Twitter_Widget extends Tbay_Widget{

    public function __construct() {
         parent::__construct(
            'tbay_twitter_widget',
            esc_html__('Tbay Latest Twitter', 'tbay-framework'),
            array( 'description' => esc_html__( 'Latest Twitter widget.', 'tbay-framework' ), )
        );
        $this->widgetName = 'twitter';
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
    }

    public function getTemplate() {
        $this->template = 'twitter.php';
    }

    public function admin_enqueue_scripts ( $hook_suffix )
    {
        if ( $hook_suffix != 'widgets.php' )
            return;
        wp_enqueue_style( 'wp-color-picker' );          
        wp_enqueue_script( 'wp-color-picker' ); 
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }

    public function form( $instance ) {
        //Set up some default widget settings.
        $defaults = array(
            'title' => esc_html__('Latest tweets.', 'tbay-framework'),
            'user' => 'envato',
            'twitter_id' => '681414676190605312',
            'limit' => 2,
            'width' => 180,
            'height' => 200,
            'border_color' => ('#000'),
            'link_color' => ('#000'),
            'text_color' => ('#000'),
            'name_color' => ('#000'),
            'show_header' => 0,
            'show_footer' => 0,
            'show_border' => 0,
            'show_scrollbar' => 0,
            'transparent' => 0,
            'show_replies' => 0,
        ); 
        $values = array(
            1 => esc_html__('Yes', 'tbay-framework'),
            0 => esc_html__('No', 'tbay-framework'),
        );              
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'tbay-framework'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'user' )); ?>"><?php esc_html_e('Twitter Username:', 'tbay-framework'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'user' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'user' )); ?>" value="<?php echo esc_attr( $instance['user'] ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'limit' )); ?>"><?php esc_html_e('Limit:', 'tbay-framework'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'limit' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'limit' )); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" style="width:100%;" />
        </p>        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'twitter_id' )); ?>"><?php esc_html_e('Twitter Id:', 'tbay-framework'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'twitter_id' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_id' )); ?>" value="<?php echo esc_attr( $instance['twitter_id'] ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'border_color' )); ?>"><?php esc_html_e('Border Color:', 'tbay-framework'); ?></label>
             <br>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'border_color' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'border_color' )); ?>" value="<?php echo esc_attr( $instance['border_color'] ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'link_color' )); ?>"><?php esc_html_e('Link Color:', 'tbay-framework'); ?></label>
             <br>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'link_color' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'link_color' )); ?>" value="<?php echo esc_attr( $instance['link_color'] ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'text_color' )); ?>"><?php esc_html_e('Text Color:', 'tbay-framework'); ?></label>
             <br>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'text_color' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'text_color' )); ?>" value="<?php echo esc_attr( $instance['text_color'] ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'name_color' )); ?>"><?php esc_html_e('Name Color:', 'tbay-framework'); ?></label>
             <br>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'name_color' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'name_color' )); ?>" value="<?php echo esc_attr( $instance['name_color'] ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'transparent' )); ?>"><?php esc_html_e('Show background', 'tbay-framework'); ?></label>
            <br>
            <select name="<?php echo esc_attr($this->get_field_name( 'transparent' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'transparent' )); ?>">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['transparent'], $key ); ?>><?php echo esc_html( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'show_replies' )); ?>"><?php esc_html_e('Show Replies', 'tbay-framework'); ?></label>
            <br>
            <select name="<?php echo esc_attr($this->get_field_name( 'show_replies' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'show_replies' )); ?>">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_replies'], $key ); ?>><?php echo esc_html( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'show_header' )); ?>"><?php esc_html_e('Show Header', 'tbay-framework'); ?></label>
            <br>
            <select name="<?php echo esc_attr($this->get_field_name( 'show_header' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'show_header' )); ?>">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_header'], $key ); ?>><?php echo esc_html( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'show_footer' )); ?>"><?php esc_html_e('Show Footer', 'tbay-framework'); ?></label>
            <br>
            <select name="<?php echo esc_attr($this->get_field_name( 'show_footer' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'show_footer' )); ?>">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_footer'], $key ); ?>><?php echo esc_html( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'show_border' )); ?>"><?php esc_html_e('Show Border', 'tbay-framework'); ?></label>
            <br>
            <select name="<?php echo esc_attr($this->get_field_name( 'show_border' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'show_border' )); ?>">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_border'], $key ); ?>><?php echo esc_html( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>    
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'show_scrollbar' )); ?>"><?php esc_html_e('Show Scrollbar', 'tbay-framework'); ?></label>
            <br>
            <select name="<?php echo esc_attr($this->get_field_name( 'show_scrollbar' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'show_scrollbar' )); ?>">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_scrollbar'], $key ); ?>><?php echo esc_html( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>            
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#<?php echo esc_js( $this->get_field_id( 'border_color' ) ); ?>').wpColorPicker();
                $('#<?php echo esc_js( $this->get_field_id( 'link_color' ) ); ?>').wpColorPicker();
                $('#<?php echo esc_js( $this->get_field_id( 'text_color' ) ); ?>').wpColorPicker();
                $('#<?php echo esc_js( $this->get_field_id( 'name_color' ) ); ?>').wpColorPicker();
                $('#<?php echo esc_js( $this->get_field_id( 'mail_color' ) ); ?>').wpColorPicker();
            });
        </script>   
    <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['user']       = strip_tags( $new_instance['user'] );
        $instance['twitter_id'] = strip_tags( $new_instance['twitter_id'] );
        $instance['limit']      = $new_instance['limit'];
        $instance['width']      = $new_instance['width'];
        $instance['height']     = $new_instance['height'];
        $instance['border_color']       = strip_tags( $new_instance['border_color'] );
        $instance['link_color']         = strip_tags( $new_instance['link_color'] );
        $instance['text_color']         = strip_tags( $new_instance['text_color'] );
        $instance['name_color']         = strip_tags( $new_instance['name_color'] );
        $instance['show_header']    = $new_instance['show_header'];
        $instance['show_footer']    = $new_instance['show_footer'];
        $instance['show_border']    = $new_instance['show_border'];
        $instance['show_scrollbar'] = $new_instance['show_scrollbar'];
        $instance['transparent']    = $new_instance['transparent'];
        $instance['show_replies']   = $new_instance['show_replies'];
        return $instance;
    }

}

register_widget( 'TbayFramework_Twitter_Widget' );
