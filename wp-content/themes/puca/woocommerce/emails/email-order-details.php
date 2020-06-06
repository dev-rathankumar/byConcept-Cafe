<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
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
	exit; // Exit if accessed directly
}

$text_align = is_rtl() ? 'right' : 'left';

if(isset($order_type) == false ) {
	$order_type = 'order';
}

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); 

?>

<?php
	echo wc_get_email_order_items( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$order,
		array(
			'show_sku'      => $sent_to_admin,
			'show_image'    => true,
			'image_size'    => array( 150, 150 ),
			'plain_text'    => $plain_text,
			'sent_to_admin' => $sent_to_admin,
		)
	);
?>

<!-- Transactional : Order Summary Open -->
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
							<?php 
							$item_totals = $order->get_order_item_totals();
							$numItems = count($item_totals);
							if ( $item_totals ) {
								$i = 0;
								foreach ( $item_totals as $total ) {
									$i++;
							?>
							<tr>
								<td align="center" class="font-0">
									<!--[if (gte mso 9)|(IE)]><table cellpadding="0" cellspacing="0" align="center"  width="640"><tr><td width="305" valign="middle"><![endif]-->
									<div class="row inline v-middle" style="width:100%;max-width:305px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<?php if ( $i === $numItems ) { ?>
											<tr>
												<td align="left" class="font-000000 font-primary font-24 font-weight-700 font-space-1 mo-text-center">
													<?php echo wp_kses_post( $total['label'] ); ?>
												</td>
											</tr>
											<?php } else { ?>
											<tr>
												<td align="left" class="font-000000 font-primary font-20 font-weight-400 font-space-1 mo-text-center">
													<?php echo wp_kses_post( $total['label'] ); ?>
												</td>
											</tr>
											<?php } ?>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
	
									<div class="row inline v-middle" style="width:100%;max-width:30px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<tr>
												<td class="spacer-5">&nbsp;</td>
											</tr>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="305" valign="middle"><![endif]-->
									<div class="row inline v-middle" style="width:100%;max-width:305px;">
										<table cellspacing="0" cellpadding="0" align="center" width="100%">
											<?php if ( $i === $numItems ) { ?>
											<tr>
												<td align="right" class="font-000000 font-primary font-24 font-weight-700 font-space-1 mo-text-center">
													<?php echo wp_kses_post( $total['value'] ); ?>
												</td>
											</tr>
											<?php } else { ?>
											<tr>
												<td align="right" class="font-000000 font-primary font-20 font-weight-400 font-space-1 mo-text-center">
													<?php echo wp_kses_post( $total['value'] ); ?>
												</td>
											</tr>
											<?php } ?>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
								</td>
							</tr>
							<tr>
								<td class="spacer-30">&nbsp;</td>
							</tr>
							<?php } } ?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

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

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
