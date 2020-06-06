<?php 
/**
 * Templates Name: Social Icons
 * Widget: Social Icons
 */
$styles = '';
extract($settings);

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->add_render_attribute('wrapper', 'class', ['widget', 'widget-social '] );

$this->settings_layout(); 

$settings = $this->get_settings_for_display();
$fallback_defaults = [
    'fa fa-facebook',
    'fa fa-twitter',
    'fa fa-google-plus',
];

$migration_allowed = \Elementor\Icons_Manager::is_migration_allowed();

?>
<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>

    <?php $this->render_element_heading(); ?>

    <div class="widget-content">
        <ul class="social list-inline <?php echo esc_attr($styles);?>">
        <?php
            foreach ( $settings['social_icon_list'] as $index => $item ) {
                $migrated = isset( $item['__fa4_migrated']['social_icon'] );
                $is_new = empty( $item['social'] ) && $migration_allowed;
                $social = '';

                // add old default
                if ( empty( $item['social'] ) && ! $migration_allowed ) {
                    $item['social'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
                }

                if ( ! empty( $item['social'] ) ) {
                    $social = str_replace( 'fa fa-', '', $item['social'] );
                }

                if ( ( $is_new || $migrated ) && 'svg' !== $item['social_icon']['library'] ) {
                    $social = explode( ' ', $item['social_icon']['value'], 2 );
                    if ( empty( $social[1] ) ) {
                        $social = '';
                    } else {
                        $social = str_replace( 'fa-', '', $social[1] );
                    }
                }

                $social = str_replace( 'icon-social-', '', $social );

                if ( 'svg' === $item['social_icon']['library'] ) {
                    $social = '';
                }

                $link_key = 'link_' . $index;

                $this->add_render_attribute( $link_key, 'href', $item['link']['url'] );

                $this->add_render_attribute( $link_key, 'class', $social );

                if ( $item['link']['is_external'] ) {
                    $this->add_render_attribute( $link_key, 'target', '_blank' );
                }

                if ( $item['link']['nofollow'] ) {
                    $this->add_render_attribute( $link_key, 'rel', 'nofollow' );
                }

                ?>
                <li><a <?php echo trim($this->get_render_attribute_string( $link_key )); ?>>
                    <?php
                    if ( $is_new || $migrated ) {
                        \Elementor\Icons_Manager::render_icon( $item['social_icon'] );
                    } else { ?>
                        <i class="<?php echo esc_attr( $item['social'] ); ?>"></i>
                    <?php } ?>
                </a></li>
            <?php } ?>
        </ul>
    </div>
</div>