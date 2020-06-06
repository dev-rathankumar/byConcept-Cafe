<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$theme_path			= get_stylesheet_directory_uri().'/woocommerce/emails/images'; // Image Path
$logoWidth			= '140'; // Logo Width in Pixels Dont Add px, em, % Just Add Number


$visit_store_text	= 'Visit Our Store &rarr;'; // Top Msg Text


$menu_link_url_1	= 'https://byconcept.cafe/my-account/'; // Menu Url 1
$menu_link_text_1	= 'Account'; // Menu Text 1

$menu_link_url_2	= 'https://byconcept.cafe/my-account/orders/'; // Menu Url 2
$menu_link_text_2	= 'Order'; // Menu Text 2

$menu_link_url_3	= 'https://byconcept.cafe/contact-us/'; // Menu Url 3
$menu_link_text_3	= 'Contact'; // Menu Text 3

$menu_link_url_4	= ''; // Menu Url 4
$menu_link_text_4	= ''; // Menu Text 4

// $menu_link_url_5	= 'http://example.com/'; // Menu Url 5
// $menu_link_text_5	= 'New'; // Menu Text 5

// $menu_link_url_6	= 'http://example.com/'; // Menu Url 6
// $menu_link_text_6	= 'Sale'; // Menu Text 6

$menu_link_cart_url	= 'https://byconcept.cafe/cart/'; // Menu Link Cart Url
$menu_link_cart_imgurl	= $theme_path . '/icon-black-cart.png'; // Menu Link Cart Image Url

$menu_link_favorite_url	= 'https://byconcept.cafe/favourites/'; // Menu Link Favorite Url
$menu_link_favorite_imgurl	= $theme_path . '/icon-black-heart.png'; //Menu Link Favorite Image Url

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" <?php language_attributes(); ?>>
<head>
	<!--[if (gte mso 9)|(IE)]>
	<xml>
		<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
		</o:OfficeDocumentSettings>
	</xml>
	<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="x-apple-disable-message-reformatting" />
	<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>

	<!-- Google Fonts Link -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">


</head>
<body marginwidth="0" topmargin="0" marginheight="0" offset="0">
<center>

<!-- Top Msg : View in Browser Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">

			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-F9F9F9">
				<tr>
					<td class="spacer-20">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">

						<table cellpadding="0" cellspacing="0" align="center" class="table-640">
							<tr>
								<td align="center">

									<table cellpadding="0" cellspacing="0" align="center" width="100%">
										<tr>
											<td align="right" class="font-777777 font-primary font-12 font-weight-500 font-space-1 mo-text-center">
												<?php
													echo __('<a class="font-777777 font-underline" href="' . get_home_url() . '">' . $visit_store_text . '</a>');
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
					<td class="spacer-10">&nbsp;</td>
				</tr>
			</table>

		</td>
	</tr>
</table>
<!-- Top Msg : View in Browser Close -->

<!-- Left Logo : Center Links : Right Icon Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-FFFFFF">
				<tr>
					<td class="spacer-30">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25" style="">
						<table cellpadding="0" cellspacing="0" align="center" class="table-640">
							<tr>
								<td align="center" class="font-0">
									<!--[if (gte mso 9)|(IE)]><table cellpadding="0" cellspacing="0" align="center"  width="640"><tr><td width="150" valign="top"><![endif]-->
									<div class="row inline v-middle width-100pc mwidth-150">
										<table cellpadding="0" cellspacing="0" align="center" width="100%">
											<tr>
												<?php
													if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
														echo __('<td align="left" class="mo-center">
																	<!-- Brand Logo -->
																	<a href="' . get_home_url() . '">
																		<img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" width="'. $logoWidth .'" class="block" style="width:'. $logoWidth .';">
																	</a>
																</td>');

													} else {
														echo __('<td align="center" class="font-000000 font-primary font-32 font-weight-700 font-space-1 pb-30">
																<a class="font-000000" href="' . get_home_url() . '">' . get_bloginfo( 'name', 'display' ) . '
																</a>
														</td>');
													}
												?>
												
											</tr>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
	
									<div class="row inline mwidth-30 v-middle width-100pc">
										<table cellpadding="0" cellspacing="0" align="center" width="100%">
											<tr>
												<td class="spacer-30">&nbsp;</td>
											</tr>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="355" valign="middle"><![endif]-->
									
									<div class="row inline mwidth-355 v-middle width-100pc">
										<?php if( $menu_link_url_1 != "" || $menu_link_url_2 != "" || $menu_link_url_3 != "" || $menu_link_url_4 != "" ) :?>
											<table cellpadding="0" cellspacing="0" align="center" width="100%">
												<tr>
													<!-- Menu Links-->
													<td align="center" class="mo-link font-000000 font-primary font-16 font-weight-600 font-space-1">
														<?php
															if ( $menu_link_url_1 != "" ) {
																echo __('<a href="' . $menu_link_url_1 . '" class="font-000000">' . $menu_link_text_1  . '</a>' );
															}
															if ( $menu_link_url_2 != "" ) {
																echo __('&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $menu_link_url_2 . '" class="font-000000">' . $menu_link_text_2  . '</a>' );
															}
															if ( $menu_link_url_3 != "" ) {
																echo __('&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $menu_link_url_3 . '" class="font-000000">' . $menu_link_text_3  . '</a>' );
															}
															if ( $menu_link_url_4 != "" ) {
																echo __('&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $menu_link_url_4 . '" class="font-000000">' . $menu_link_text_4  . '</a>' );
															}
														?>
													</td>
												</tr>
											</table>
										<?php endif; ?>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
	
									<div class="row inline mwidth-30 v-middle width-100pc">
										<table cellpadding="0" cellspacing="0" align="center" width="100%">
											<tr>
												<td class="spacer-30">&nbsp;</td>
											</tr>
										</table>
									</div>
									<!--[if (gte mso 9)|(IE)]></td><td width="75" valign="middle"><![endif]-->
									<div class="row inline mwidth-75 v-middle width-100pc">
										<table cellpadding="0" cellspacing="0" align="center" width="100%">
											<?php if( $menu_link_cart_url != "") :?>
                                                <tr>
													<!-- Icons-->
                                                    <?php
                                                        if ( $menu_link_cart_url != "" ) {
                                                            echo __('<td align="center">
																		<a href="'.$menu_link_cart_url.'">
																			<img src="'.$menu_link_cart_imgurl.'" class="block width-20" alt="#" width="20">
																		</a>
																	</td>');
                                                        }
                                                        if ( $menu_link_favorite_url != "" ) {
                                                            echo __('<td align="center">
																		<a href="'.$menu_link_favorite_url.'">
																			<img src="'.$menu_link_favorite_imgurl.'" class="block width-20" alt="#" width="20">
																		</a>
																	</td>');
                                                        }
                                                    ?>                      
                                                </tr>
                                            <?php endif; ?>
											<tr>
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
					<td class="spacer-30">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- Left Logo : Center Links : Right Icon Close -->




