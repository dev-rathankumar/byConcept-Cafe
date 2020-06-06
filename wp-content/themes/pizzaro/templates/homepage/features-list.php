<?php
/**
 * Features Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( empty( $features ) ) {
	return;
}

$section_class = empty( $section_class ) ? 'features-list' : 'features-list ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$features_count = count( $features );
$column_class 	= 'col-xs-12';

switch( $features_count ) {
	case 1:
		$column_class = $column_class;
	break;
	case 2:
		$column_class .= ' col-sm-6';
	break;
	case 3:
		$column_class .= ' col-sm-4';
	break;
	case 4:
		$column_class .= ' col-sm-3';
	break;
	case 5:
		$column_class .= ' col-sm-20p';
	break;
	default:
		$column_class .= ' col-sm-2';
}

?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
	<div class="row">
	<?php foreach( $features as $feature ) : ?>
		<div class="feature <?php echo esc_attr( $column_class ); ?>">
			<div class="feature-icon"><i class="<?php echo esc_attr( $feature['icon'] ); ?>"></i></div>
			<div class="feature-label"><?php echo wp_kses_post( $feature['label'] ); ?></div>
		</div>
	<?php endforeach; ?>
	</div>
</div>