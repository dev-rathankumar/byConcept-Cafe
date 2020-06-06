<?php
/**
 * Customer on-hold order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$theme_path		= get_stylesheet_directory_uri().'/woocommerce/emails/images'; // Image Path
$hero_bg_img 	= $theme_path . '/hero-customer-on-hold-order.png';


/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

?>

<!-- Hero BG : Left Text Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td align="center">
						<table cellpadding="0" cellspacing="0" align="center" width="100%">
							<tr>
								<td align="center" class="mo-px-25" bgcolor="#F5F5F5" style="background-image: url(<?php echo $hero_bg_img ?>);background-color:#F5F5F5;background-position:center top;background-size:cover;background-repeat: no-repeat;">
								
									<?php
										echo '<!--[if gte mso 9]>
										<v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:700px;height:430px;">
										<v:fill type="frame" src="'. $hero_bg_img .'" color="#F5F5F5"></v:fill>
										<v:textbox style="v-text-anchor:middle;" inset="0,0,0,0">
										<![endif]-->'
									?>
										<table cellspacing="0" cellpadding="0" align="center" class="table-640">
											<tr>
												<td class="h-120">&nbsp;</td>
											</tr>
											
											<tr>
												<td align="left" class="font-000000 font-primary font-42 font-weight-400 font-space-1 pb-40 mo-text-center">
													<?php echo $email_heading ?>
												</td>
											</tr>
											<tr>
												<td align="center">
													<table cellspacing="0" cellpadding="0" align="left" class="mo-btn-full mo-center">
														<tr>
															<td align="center" class="bg-000000 btn block">
																<a href="<?php echo $order->get_view_order_url(); ?>" class="font-btn font-primary font-14 font-weight-600 font-space-1 block">
																<?php printf(  __( 'Manage Order', 'woocommerce' ) ); ?>
																</a>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class="h-120">&nbsp;</td>
											</tr>
										</table>
										<!--[if (gte mso 9)|(IE)]></v:textbox></v:rect><![endif]-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- Hero BG : Left Text Close -->

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
?>

<!-- Transactional : Title Subtitle Btn Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">

			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-45">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">

						<table cellpadding="0" cellspacing="0" align="center" class="table-640">
							<tr>
								<td align="center">

									<table cellpadding="0" cellspacing="0" align="center" width="100%">
										<tr>
											<td align="center" class="font-000000 font-primary font-32 font-weight-600 font-space-2 pb-20">
												<?php printf(  __( 'Order On-Hold', 'woocommerce' ) ); ?>
											</td>
										</tr>
										<tr>
											<td align="center" class="font-777777 font-primary font-18 font-weight-400 font-space-2 pb-40">
												<?php 
													/* additional content */
													if ( $additional_content ) {
														echo __( wp_kses_post( wptexturize( $additional_content )));
													}
												?>
											</td>
										</tr>
										<tr>
											<td align="center">
												<table cellspacing="0" cellpadding="0" align="center" class="mo-btn-full">
													<tr>
														<td align="center" class="bg-000000 btn-l block">
															<a href="<?php echo $order->get_view_order_url(); ?>" class="font-btn font-primary font-14 font-weight-600 font-space-1 block">
																<?php printf(  __( 'Manage Order', 'woocommerce' ) ); ?>
															</a>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>

								</td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr>
					<td class="spacer-60">&nbsp;</td>
				</tr>
			</table>

		</td>
	</tr>
</table>
<!-- Transactional : Title Subtitle Btn Close -->

<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
