<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_video') ) {
    exit; // Exit if accessed directly.
}
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

class Puca_Elementor_video extends Puca_Elementor_Widget_Base {
    
    public function get_name() {
        return 'tbay-video';
    }

    public function get_title() {
        return esc_html__('Puca Video', 'puca');
    }

    public function get_script_depends() {
        return [ 'slick' ];
    } 

    public function get_icon() {
        return 'eicon-youtube';
    }

    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('General', 'puca'),
            ]
        );

        $this->add_control(
            'video_image',
            [
                'label'     => esc_html__( 'Choose Image', 'puca' ),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url'   => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'video_url',
            [
                'label' => esc_html__( 'Video URL', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'Enter the video url at https://vimeo.com/ or https://www.youtube.com/', 'puca' ),
                'default' => 'https://youtu.be/Im2q_ri-7AM',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'video_btn_text',
            [
                'label'         => esc_html__( 'Button text', 'puca' ),
                'type'          => Controls_Manager::TEXT,
                'default'       => esc_html__('Play video', 'puca'),
                'label_block'   => true,
            ]
        );
        
        $this->end_controls_section(); 

        $this->remove_control('heading_subtitle');
        $this->update_responsive_control(
            'align',
            [  
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->update_responsive_control(
            'heading_style_margin',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );  

        $this->update_responsive_control(
            'heading_style_padding',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );          

        $this->update_responsive_control(
            'heading_style_bg',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'background: {{VALUE}};',
                ],
            ]
        );         

        $this->update_responsive_control(
            'heading_title_size',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );         

        $this->update_responsive_control(
            'heading_title_line_height',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );          

        $this->update_control(
            'heading_title_color',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'color: {{VALUE}};',
                ],
            ]
        );          

        $this->update_control(
            'heading_title_color_hover',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video:hover' => 'color: {{VALUE}};',
                ],
            ]
        );  

        $this->update_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_title_typography',
                'selector' => '{{WRAPPER}} .title-video',
            ]
        );

        $this->update_responsive_control(
            'heading_title_bottom_space',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->update_responsive_control(
            'heading_style_margin',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );    


        $this->update_responsive_control(
            'heading_style_padding',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        ); 

        $this->update_control(
            'heading_style_bg',
            [
                'selectors' => [
                    '{{WRAPPER}} .title-video' => 'background: {{VALUE}};',
                ],
            ]
        );
    }

    public function the_video_content() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        $video = puca_tbay_VideoUrlType($video_url);

        if( $video['video_type'] == 'youtube' ) {
            $url  = 'https://www.youtube.com/embed/'.$video['video_id'].'?autoplay=1';
        }elseif(( $video['video_type'] == 'vimeo' )) {
            $url = 'https://player.vimeo.com/video/'.$video['video_id'].'?autoplay=1';
        }

        $_id = puca_tbay_random_key(); 
        $image_id       = $video_image['id'];
        $img            = wp_get_attachment_image_src($image_id,'full');

        $icon = '<i class="icon-control-play icons"></i><span>' . $video_btn_text .'</span>';
        
        if( !empty($video_url) && ( !empty($img) && isset($img[0]) ) ) : ?>

        <div class="tbay-addon-video">

            <?php if ( !empty($img) && isset($img[0]) ): ?>
                <div class="video-image tbay-image-loaded">
                   <?php 
                        $image_alt  = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
                        puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
                    ?>
                </div>
            <?php endif; ?>

          <div class="modal fade tbay-video-modal" data-id="<?php echo esc_attr($_id); ?>" id="video-modal-<?php echo esc_attr($_id); ?>">
                <div class="modal-dialog">
                  <div class="modal-content tbay-modalContent">

                    <div class="modal-body">
                      
                      <div class="close-button">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item"></iframe>
                      </div>
                    </div>

                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->

          <button type="button" class="tbay-modalButton" data-toggle="modal" data-tbaySrc="<?php echo esc_attr($url); ?>" data-tbayWidth="640" data-tbayHeight="480" data-target="#video-modal-<?php echo esc_attr($_id); ?>"  data-tbayVideoFullscreen="true"><?php echo trim($icon); ?></button>
        </div>

        <?php endif;
    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_video());

