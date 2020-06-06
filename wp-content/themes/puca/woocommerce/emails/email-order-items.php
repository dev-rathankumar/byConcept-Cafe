<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<!-- Transactional : Order Id Order Date Open -->
<table cellspacing="0" cellpadding="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">

			<table cellspacing="0" cellpadding="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-30">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">

						<table cellspacing="0" cellpadding="0" align="center" class="table-640">
							<tr>
								<td align="center" class="font-0">

									<!--[if (gte mso 9)|(IE)]><table cellpadding="0" cellspacing="0" align="center"  width="640"><tr><td width="305" valign="middle"><![endif]-->

									<div class="row inline v-middle" style="width:100%;max-width:305px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td align="left" class="font-00000 font-primary font-16 font-weight-400 font-space-1 mo-text-center">
													<?php
														echo wp_kses_post( $before . sprintf( __( 'Order Number : ', 'woocommerce' ) . $after . '<span class="mo-block"> #%s</span>', $order->get_order_number(), $order->get_date_created()->format( 'c' )));
													?>
												</td>
											</tr>
										</table>
									</div>

									<!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
	
									<div class="row inline v-middle" style="width:100%;max-width:30px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td class="spacer-10">&nbsp;</td>
											</tr>
										</table>
									</div>

									<!--[if (gte mso 9)|(IE)]></td><td width="305" valign="middle"><![endif]-->

									<div class="row inline v-middle" style="width:100%;max-width:305px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td align="right" class="font-000000 font-primary font-16 font-weight-400 font-space-1 mo-text-center">
													<?php
														echo wp_kses_post( $before . sprintf( __( 'Order Date : ', 'woocommerce' ) . $after . '<span class="mo-block"><time  datetime="%s">%s</time></span>', $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created())));
													?>
												</td>
											</tr>
										</table>
									</div>

									<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->

								</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
			</table>

		</td>
	</tr>
</table>
<!-- Transactional : Order Id Order Date Close -->

<!-- Divider : Open -->
<table cellspacing="0" cellpadding="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">

			<table cellspacing="0" cellpadding="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">

						<table cellspacing="0" cellpadding="0" align="center" class="table-640">
							<tr>
								<td class="h-1 bg-F1F1F1">&nbsp;</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
			</table>

		</td>
	</tr>
</table>
<!-- Divider : Close -->

<?php
foreach ( $items as $item_id => $item ) :
	$product       = $item->get_product();
	$sku           = '';
	$purchase_note = '';
	$image         = '';

	if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		continue;
	}

	if ( is_object( $product ) ) {
		$sku           = $product->get_sku();
		$purchase_note = $product->get_purchase_note();
		$image         = $product->get_image( $image_size );
	}

?>
<!-- Transactional : Product Order Info 1 Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">
						<table cellpadding="0" cellspacing="0" align="center" class="table-640">
							<tr>
								<td align="center" class="font-0">

									<!--[if (gte mso 9)|(IE)]><table cellpadding="0" cellspacing="0" align="center" width="640"><tr><td width="160" valign="middle"><![endif]-->
									<div class="row inline v-top" style="width:100%;max-width:160px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td align="left">
													
													<a href="<?php echo $url = get_permalink( $item['product_id'] ); ?>">
													<?php
														echo apply_filters( 'woocommerce_order_item_thumbnail', '<img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'medium' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" class="mo-img-full block" width="150" style="max-width:150px"', $item);
													?>
													</a>

												</td>
											</tr>
										</table>
									</div>

									<!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
									<div class="row inline v-top" style="width:100%;max-width:30px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td class="spacer-30">&nbsp;</td>
											</tr>
										</table>
									</div>

									<!--[if (gte mso 9)|(IE)]></td><td width="280" valign="middle"><![endif]-->
									<div class="row inline v-top" style="width:100%;max-width:280px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td class="h-35 mo-h-0">&nbsp;</td>
											</tr>
											<tr>
												<td align="left" class="font-000000 font-primary font-22 font-weight-400 pb-5 mo-text-center">

													<a href="<?php echo $url = get_permalink( $item['product_id'] ); ?>" class="font-000000">
														<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); ?>
													</a>
												</td>
											</tr>
											<tr>
												<td align="left" class="font-000000 font-primary font-20 font-weight-400 font-space-2 pb-5 mo-text-center">
													<?php 
														echo apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item ) . ' x ';
														if ($product->get_sale_price() != '') {
															echo wc_price($product->get_sale_price());
														} else {
															echo wc_price($product->get_regular_price());
														}
													?>

												</td>
											</tr>

											<?php if ( $show_purchase_note && $purchase_note ) :?>
											<tr>
												<td align="left" class="font-777777 font-primary font-12 font-weight-400 mo-text-center">
													<?php printf(  __( 'Note :', 'woocommerce' ) ); ?>
													<?php echo wp_kses_post(do_shortcode( $purchase_note ));?>
												</td>
											</tr>
											<?php endif; ?>


											<?php if($sku != '' ) :?>
											<tr>
												<td align="left" class="font-777777 font-primary font-12 font-weight-400 pb-5 mo-text-center">
													<?php echo wp_kses_post('Sku : #' . $sku . ''); ?>
													<?php
														// allow other plugins to add additional product information here
														do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

														wc_display_item_meta( $item );

														if ( $show_download_links ) {
															wc_display_item_downloads( $item );
														}

														// allow other plugins to add additional product information here
														do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
													?>
												</td>
											</tr>
											<?php endif; ?>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
									<div class="row inline v-top" style="width:100%;max-width:30px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td class="spacer-30">&nbsp;</td>
											</tr>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="120" valign="middle"><![endif]-->
									<div class="row inline v-top" style="width:100%;max-width:120px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td class="h-35 mo-h-0">&nbsp;</td>
											</tr>
											<tr>
												<td align="right" class="font-000000 font-primary font-24 font-weight-700 font-space-1 mo-text-center">
													<?php echo $order->get_formatted_line_subtotal( $item ); ?>
												</td>
											</tr>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- Transactional : Product Order Info 1 Close -->

<!-- Divider : Open -->
<table cellspacing="0" cellpadding="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">
						<table cellspacing="0" cellpadding="0" align="center" class="table-640">
							<tr>
								<td class="h-1 bg-F1F1F1">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="spacer-15">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- Divider : Close -->


		

<?php endforeach; ?>

