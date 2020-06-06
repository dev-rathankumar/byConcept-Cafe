<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package pizzaro
 */

?>

		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'pizzaro_before_footer_v3' ); ?>

	<footer id="colophon" class="site-footer footer-v3" role="contentinfo">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked in to pizzaro_footer action
			 *
			 * @hooked pizzaro_footer_widgets - 10
			 * @hooked pizzaro_credit         - 20
			 */
			do_action( 'pizzaro_footer_v3' ); ?>

		</div><!-- .col-full -->
	</footer><!-- #colophon -->

	<?php do_action( 'pizzaro_after_footer_v3' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>