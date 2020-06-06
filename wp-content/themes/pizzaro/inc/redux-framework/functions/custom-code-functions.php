<?php
/**
 * Filter functions for Custom Code Section of Theme Options
 */

if ( ! function_exists( 'redux_apply_custom_css' ) ) {
	function redux_apply_custom_css() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['custom_css'] ) && trim( $pizzaro_options['custom_css'] ) != '' ) {
			?>
			<style type="text/css">
			<?php echo ( $pizzaro_options['custom_css'] ); ?>
			</style>
			<?php
		}
	}
}