<?php
/*-----------------------------------------------------------------------------------*/
/*	About Widget Class
/*-----------------------------------------------------------------------------------*/
class Pizzaro_About_Widget extends WP_Widget {

	public $defaults;

	public function __construct() {

		$widget_ops = array(
			'classname' 	=> 'pizzaro_about_widget',
			'description' 	=> esc_html__( 'Your site&#8217;s about widget.', 'pizzaro' )
		);

		parent::__construct( 'pizzaro_about_widget', esc_html__('Pizzaro About', 'pizzaro'), $widget_ops );

		$defaults = apply_filters( 'pizzaro_about_widget_default_args', array(
			'title'		=> '',
			'desc'		=> '',
			'image'		=> '',
			'logo'		=> '',
			'show_social_links'	=> true
		) );
		$this->defaults = $defaults;
	}

	public function widget( $args, $instance ) {

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		pizzaro_get_template( 'widgets/about-widget.php', array( 'args' => $args,'instance' => $instance ) );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']				= strip_tags( $new_instance['title'] );
		$instance['desc']				= strip_tags( $new_instance['desc'] );
		$instance['image']				= strip_tags( $new_instance['image'] );
		$instance['logo']				= strip_tags( $new_instance['logo'] );
		$instance['show_social_links']	= strip_tags( $new_instance['show_social_links'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$show_social_links = isset( $instance['show_social_links'] ) ? (bool) $instance['show_social_links'] : false;
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title', 'pizzaro'); ?>:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php esc_html_e('Description', 'pizzaro'); ?>:</label>
			<textarea rows="1" cols="28" id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc' ) ); ?>" class="widefat"><?php echo wp_kses_post( $instance['desc'] ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e('Featured Image', 'pizzaro'); ?>:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" value="<?php echo esc_attr( $instance['image'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'logo' ) ); ?>"><?php esc_html_e('Logo', 'pizzaro'); ?>:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'logo' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'logo' ) ); ?>" value="<?php echo esc_attr( $instance['logo'] ); ?>" class="widefat" />
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_social_links ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_social_links' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_social_links' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_social_links' ) ); ?>"><?php esc_html_e( 'Display social links?', 'pizzaro' ); ?></label>
		</p>

		<?php
		do_action( 'pizzaro_about_widget_add_opts', $this, $instance);
	}

}
