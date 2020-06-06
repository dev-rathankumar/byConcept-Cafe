<?php
/**
 * Email Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<!-- Transactional : Billing Adress Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-30">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">

						<table cellpadding="0" cellspacing="0" align="center" class="table-640">
							<tr>
								<td align="center">
									<table cellpadding="0" cellspacing="0" align="center" width="100%">
										<tr>
											<td align="left" class="font-000000 font-primary font-22 font-weight-600 font-space-2 pb-10 mo-text-center">
												<?php printf(  __( 'Billing Address :', 'woocommerce' ) ); ?>
											</td>
										</tr>
										<tr>
											<td align="left" class="font-777777 font-primary font-16 font-weight-400 font-space-1 mo-text-center">
												<?php echo '<strong style="text-transform: capitalize;">'. $order->get_billing_first_name() .'&nbsp;'.$order->get_billing_last_name() .'</strong>';?>
												<?php if ($order->get_billing_company()) : ?>
													<strong style="text-transform: capitalize;">&nbsp;(&nbsp;<?php echo $order->get_billing_company(); ?>&nbsp;)</strong>
												<?php endif; ?>
												<?php if ($order->get_billing_address_1()) {
														echo '<br>'. $order->get_billing_address_1() .',&nbsp;';
													}	
												?>
												<?php if ($order->get_billing_address_2()) {
														echo $order->get_billing_address_2() .',&nbsp;';
													}	
												?>
												<?php if ($order->get_billing_city()) {
														echo $order->get_billing_city() .',&nbsp;'. WC()->countries->states[$order->billing_country][$order->billing_state].',&nbsp;'. $order->get_billing_postcode() .',&nbsp;'. WC()->countries->countries[ $order->get_billing_country() ] .'&nbsp;';
													}	
												?>
												<?php if ($order->get_billing_phone()) : ?>
												<br><strong>Phone:</strong>&nbsp;<a href="#" class="font-777777"><?php echo $order->get_billing_phone(); ?></a>&nbsp;
												<?php endif; ?>
												<?php if ($order->get_billing_email()) : ?>
												<strong>Email:</strong>&nbsp;<a href="#" class="font-777777"><?php echo $order->get_billing_email(); ?></a>&nbsp;
												<?php endif; ?>
											</td>
										</tr>
									</table>
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
<!-- Transactional : Billing Adress Close -->

<!-- Transactional : Shipping Adress Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-30">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">
						<table cellpadding="0" cellspacing="0" align="center" class="table-640">
							<tr>
								<td align="center">
									<table cellpadding="0" cellspacing="0" align="center" width="100%">
										<tr>
											<td align="left" class="font-000000 font-primary font-22 font-weight-600 font-space-2 pb-10 mo-text-center">
												<?php printf(  __( 'Shipping Address :', 'woocommerce' ) ); ?>
											</td>
										</tr>
										<tr>
											<td align="left" class="font-777777 font-primary font-16 font-weight-400 font-space-1 mo-text-center">
												<?php echo '<strong style="text-transform: capitalize;">'. $order->get_shipping_first_name() .'&nbsp;'.$order->get_shipping_last_name() .'</strong>';?>
												<?php if ($order->get_shipping_company()) : ?>
													<strong style="text-transform: capitalize;">&nbsp;(&nbsp;<?php echo $order->get_shipping_company(); ?>&nbsp;)</strong>
												<?php endif; ?>
												<?php if ($order->get_shipping_address_1()) {
														echo '<br>'. $order->get_shipping_address_1() .',&nbsp;';
													}	
												?>
												<?php if ($order->get_shipping_address_2()) {
														echo $order->get_shipping_address_2() .',&nbsp;';
													}	
												?>
												<?php if ($order->get_shipping_city()) {
														echo $order->get_shipping_city() .',&nbsp;'. WC()->countries->states[$order->billing_country][$order->billing_state].',&nbsp;'. $order->get_shipping_postcode() .',&nbsp;'. WC()->countries->countries[ $order->get_shipping_country() ] .'&nbsp;';
													}	
												?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr>
					<td class="spacer-30">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- Transactional : Shipping Adress Close -->

<!-- Divider : Open -->
<table cellspacing="0" cellpadding="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">

			<table cellspacing="0" cellpadding="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td align="center" class="mo-px-25">

						<table cellspacing="0" cellpadding="0" align="center" class="table-640">
							<tr>
								<td class="h-1 bg-F1F1F1">&nbsp;</td>
							</tr>
						</table>

					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>
<!-- Divider : Close -->