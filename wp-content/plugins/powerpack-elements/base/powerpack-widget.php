<?php
/**
 * PowerPack Elements Common Widget.
 *
 * @package PowerPack Elements
 */

namespace PowerpackElements\Base;

use Elementor\Widget_Base;
use PowerpackElements\Classes\PP_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Common Widget
 *
 * @since 0.0.1
 */
abstract class Powerpack_Widget extends Widget_Base {

	/**
	 * Get categories
	 *
	 * @since 0.0.1
	 */
	public function get_categories() {
		return [ 'powerpack-elements' ];
	}

	/**
	 * Get widget name
	 *
	 * @param string $slug Module class.
	 * @since 1.4.13.1
	 */
	public function get_widget_name( $slug = '' ) {
		return PP_Helper::get_widget_name( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 1.4.13.1
	 */
	public function get_widget_title( $slug = '' ) {
		return PP_Helper::get_widget_title( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 1.4.13.1
	 */
	public function get_widget_categories( $slug = '' ) {
		return PP_Helper::get_widget_categories( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 1.4.13.1
	 */
	public function get_widget_icon( $slug = '' ) {
		return PP_Helper::get_widget_icon( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 1.4.13.1
	 */
	public function get_widget_keywords( $slug = '' ) {
		return PP_Helper::get_widget_keywords( $slug );
	}

	/**
	 * Add a placeholder for the widget in the elementor editor
	 *
	 * @access public
	 * @since 1.3.11
	 *
	 * @return void
	 */
	public function render_editor_placeholder( $args = array() ) {

		if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			return;
		}

		$defaults = [
			'title' => $this->get_title(),
			'body' 	=> __( 'This is a placeholder for this widget and is visible only in the editor.', 'powerpack' ),
		];

		$args = wp_parse_args( $args, $defaults );

		$this->add_render_attribute([
			'placeholder' => [
				'class' => 'pp-editor-placeholder',
			],
			'placeholder-title' => [
				'class' => 'pp-editor-placeholder-title',
			],
			'placeholder-content' => [
				'class' => 'pp-editor-placeholder-content',
			],
		]);

		?><div <?php echo $this->get_render_attribute_string( 'placeholder' ); ?>>
			<h4 <?php echo $this->get_render_attribute_string( 'placeholder-title' ); ?>>
				<?php echo $args['title']; ?>
			</h4>
			<div <?php echo $this->get_render_attribute_string( 'placeholder-content' ); ?>>
				<?php echo $args['body']; ?>
			</div>
		</div><?php
	}

	/**
	 * Render Template Content
	 *
	 * @access public
	 *
	 * @param int 											$template_id 	The template post ID
	 * @param \PowerpackElements\Base\Powerpack_Widget		$widget 		The widget instance
	 * @since 1.4.13.2
	 */
	public function render_template_content( $template_id, \PowerpackElements\Base\Powerpack_Widget $widget ) {
		
		if ( 'publish' !== get_post_status( $template_id ) || ! method_exists( '\Elementor\Frontend', 'get_builder_content_for_display' ) ) {
			return;
		}

		if ( ! $template_id ) {
			if ( method_exists( $widget, 'render_editor_placeholder' ) ) {
				$placeholder = __( 'Choose a post template that you want to use as post skin in widget settings.', 'powerpack' );
				
				$widget->render_editor_placeholder([
					'title' => __( 'No template selected!', 'powerpack' ),
					'body' => $placeholder,
				]);
			} else {
				_e( 'No template selected!', 'powerpack' );
			}
		} else {

			global $wp_query;

			// Keep old global wp_query 
			$old_query = $wp_query;

			// Create a new query from the current post in loop
			$new_query = new \WP_Query( [
				'post_type' => 'any',
				'p' => get_the_ID(),
			] );

			// Set the global query to the new query
			$wp_query = $new_query;

    		// Fetch the template
			 $template = PP_Helper::elementor()->frontend->get_builder_content_for_display( $template_id, true );
			
			// Revert to the initial query
			$wp_query = $old_query;

			?><div class="elementor-template"><?php echo $template; ?></div><?php
		}
	}
	
	/**
	 * Get swiper slider settings
	 *
	 * @access public
	 * @since 1.4.13.1
	 */
	public function get_swiper_slider_settings( $settings, $new = true ) {
		$pagination = ( $new ) ? $settings['pagination'] : $settings['dots'];
		
		$effect = ( isset( $settings['carousel_effect'] ) && ( $settings['carousel_effect'] ) ) ? $settings['carousel_effect'] : 'slide';

        $slider_options = [
			'direction'			=> 'horizontal',
			'effect'			=> $effect,
			'speed'				=> ( $settings['slider_speed']['size'] !== '' ) ? $settings['slider_speed']['size'] : 400,
			'slidesPerView'		=> ( $settings['items']['size'] !== '' ) ? absint( $settings['items']['size'] ) : 3,
			'spaceBetween'       => ( $settings['margin']['size'] !== '' ) ? absint( $settings['margin']['size'] ) : 10,
			'grabCursor'		=> ( $settings['grab_cursor'] === 'yes' ),
			'autoHeight'		=> true,
			'loop'				=> ( $settings['infinite_loop'] === 'yes' ),
		];
		
		$autoplay_speed = 999999;
        
        if ( $settings['autoplay'] == 'yes' ) {
            if ( isset( $settings['autoplay_speed']['size'] ) ) {
				$autoplay_speed = $settings['autoplay_speed']['size'];
			} else if ( $settings['autoplay_speed'] ) {
				$autoplay_speed = $settings['autoplay_speed'];
			}
        }
        
        $slider_options['autoplay'] = [
            'delay'                  => $autoplay_speed,
            'disableOnInteraction'   => ( $settings['pause_on_interaction'] === 'yes' )
        ];
        
        if ( $pagination == 'yes' ) {
            $slider_options['pagination'] = [
                'el'                 => '.swiper-pagination-'.esc_attr( $this->get_id() ),
                'type'               => $settings['pagination_type'],
                'clickable'          => true,
            ];
        }
        
        if ( $settings['arrows'] == 'yes' ) {
            $slider_options['navigation'] = [
                'nextEl'             => '.swiper-button-next-'.esc_attr( $this->get_id() ),
                'prevEl'             => '.swiper-button-prev-'.esc_attr( $this->get_id() ),
            ];
        }
		
		$elementor_bp_lg		= get_option( 'elementor_viewport_lg' );
		$elementor_bp_md		= get_option( 'elementor_viewport_md' );
		$bp_desktop				= !empty($elementor_bp_lg) ? $elementor_bp_lg : 1025;
		$bp_tablet				= !empty($elementor_bp_md) ? $elementor_bp_md : 768;
		$bp_mobile				= 320;
        
        $slider_options['breakpoints'] = [
            $bp_desktop   => [
                'slidesPerView'      => ( $settings['items']['size'] !== '' ) ? absint( $settings['items']['size'] ) : 3,
                'spaceBetween'       => ( $settings['margin']['size'] !== '' ) ? absint( $settings['margin']['size'] ) : 10,
            ],
            $bp_tablet   => [
                'slidesPerView'      => ( $settings['items_tablet']['size'] !== '' ) ? absint( $settings['items_tablet']['size'] ) : 2,
                'spaceBetween'       => ( $settings['margin_tablet']['size'] !== '' ) ? absint( $settings['margin_tablet']['size'] ) : 10,
            ],
            $bp_mobile   => [
                'slidesPerView'      => ( $settings['items_mobile']['size'] !== '' ) ? absint( $settings['items_mobile']['size'] ) : 1,
                'spaceBetween'       => ( $settings['margin_mobile']['size'] !== '' ) ? absint( $settings['margin_mobile']['size'] ) : 10,
            ],
        ];
        
        return $slider_options;
    }
	
	
	
	/**
	 * Get swiper slider settings for _content_template function
	 *
	 * @access public
	 * @since 1.4.13.1
	 */
	public function get_swiper_slider_settings_js() {
		$elementor_bp_tablet	= get_option( 'elementor_viewport_lg' );
		$elementor_bp_mobile	= get_option( 'elementor_viewport_md' );
		$elementor_bp_lg		= get_option( 'elementor_viewport_lg' );
		$elementor_bp_md		= get_option( 'elementor_viewport_md' );
		$bp_desktop				= !empty($elementor_bp_lg) ? $elementor_bp_lg : 1025;
		$bp_tablet				= !empty($elementor_bp_md) ? $elementor_bp_md : 768;
		$bp_mobile				= 320;
        ?>
        <#
		   function get_slider_settings( settings ) {
		   
		   		if (typeof settings.effect !== 'undefined') {
		   			var $effect = settings.effect;
		   		} else {
		   			var $effect = 'slide';
		   		}

                var $items          = ( settings.items.size !== '' || settings.items.size !== undefined ) ? settings.items.size : 3,
                    $items_tablet   = ( settings.items_tablet.size !== '' || settings.items_tablet.size !== undefined ) ? settings.items_tablet.size : 2,
                    $items_mobile   = ( settings.items_mobile.size !== '' || settings.items_mobile.size !== undefined ) ? settings.items_mobile.size : 1,
                    $margin         = ( settings.margin.size !== '' || settings.margin.size !== undefined ) ? settings.margin.size : 10,
                    $margin_tablet  = ( settings.margin_tablet.size !== '' || settings.margin_tablet.size !== undefined ) ? settings.margin_tablet.size : 10,
                    $margin_mobile  = ( settings.margin_mobile.size !== '' || settings.margin_mobile.size !== undefined ) ? settings.margin_mobile.size : 10,
                    $autoplay       = ( settings.autoplay == 'yes' && settings.autoplay_speed.size != '' ) ? settings.autoplay_speed.size : 999999;

                return {
                    direction:              "horizontal",
                    speed:                  ( settings.slider_speed.size !== '' || settings.slider_speed.size !== undefined ) ? settings.slider_speed.size : 400,
                    effect:                 $effect,
                    slidesPerView:          $items,
                    spaceBetween:           $margin,
                    grabCursor:             ( settings.grab_cursor === 'yes' ) ? true : false,
                    autoHeight:             true,
                    loop:                   ( settings.infinite_loop === 'yes' ),
                    autoplay: {
                        delay: $autoplay,
		   				disableOnInteraction: ( settings.disableOnInteraction === 'yes' ),
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        type: settings.pagination_type,
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        <?php echo $bp_desktop; ?>: {
                            slidesPerView:  $items,
                            spaceBetween:   $margin
                        },
                        <?php echo $bp_tablet; ?>: {
                            slidesPerView:  $items_tablet,
                            spaceBetween:   $margin_tablet
                        },
                        <?php echo $bp_mobile; ?>: {
                            slidesPerView:  $items_mobile,
                            spaceBetween:   $margin_mobile
                        }
                    }
                };
           };
		#>
		<?php
	}
}
