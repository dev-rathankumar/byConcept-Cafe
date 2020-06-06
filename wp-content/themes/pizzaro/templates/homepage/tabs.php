<?php
/**
 * Tabs
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( empty( $args['tabs'] ) ) {
	return;
}

$section_class = empty( $section_class ) ? 'section-tabs' : $section_class . ' section-tabs';

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$default_active_tab = isset( $default_active_tab ) && ( $default_active_tab >= 1 ) ? ( $default_active_tab - 1 ) : 1;

$tab_uniqid = 'tab-' . uniqid();
?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( ! empty( $animation ) ): ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>

	<ul class="nav nav-inline">

		<?php foreach( $args['tabs'] as $key => $tab ) :

			$tab_id = $tab_uniqid . $key; ?>

			<li class="nav-item <?php if ( $key == $default_active_tab ) echo esc_attr( 'active' ); ?>">
				<a class="nav-link" href="#<?php echo esc_attr( $tab_id ); ?>" data-toggle="tab">
					<?php echo wp_kses_post ( $tab['title'] ); ?>
				</a>
			</li>

		<?php endforeach; ?>

	</ul>

	<div class="tab-content">

		<?php foreach( $args['tabs'] as $key => $tab ) :

			$tab_id = $tab_uniqid . $key; ?>

			<div class="tab-pane <?php if ( $key == $default_active_tab ) echo esc_attr( 'active' ); ?>" id="<?php echo esc_attr( $tab_id ); ?>" role="tabpanel">
				<?php echo sprintf( '%s', $tab['content'] ); ?>
			</div>

		<?php endforeach; ?>

	</div>
</div>
