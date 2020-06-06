<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-footer.php.
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

$theme_path				= get_stylesheet_directory_uri() . '/woocommerce/emails/images'; // Image Path

$banner_show 			= "NO"; // Footer Banner Show 'YES' or 'NO', Case Sensitive
$banner_link			= 'http://example.com'; // Offer Banner URL
$banner_img				= $theme_path . '/offers.png'; // Offer Banner Img URL

$social_1_link 			= 'https://www.facebook.com/byconcept.cafe/'; // Footer Social Link 
$social_1_img			= $theme_path . '/social-grey-facebook.png'; // Footer Social Img Url

$social_2_link 			= 'https://www.instagram.com/byconcept.cafe/'; // Footer Social Link
$social_2_img			= $theme_path . '/social-grey-twitter.png'; // Footer Social Img Url

$social_3_link 			= 'https://twitter.com/byconceptcafe'; // Footer Social Link
$social_3_img			= $theme_path . '/social-grey-instagram.png'; // Footer Social Img Url

$social_4_link 			= 'https://www.messenger.com/t/byconcept.cafe'; // Footer Social Link
$social_4_img			= $theme_path . '/social-grey-messenger.png'; // Footer Social Img Url 

$social_5_link 			= 'http://example.com'; // Footer Social Link
$social_5_img			= $theme_path . '/social-grey-whatsapp.png'; // Footer Social Img Url

$social_6_link 			= ''; // Footer Social Link
$social_6_img			= $theme_path . '/social-grey-youtube.png'; // Footer Social Img Url


$footer_link_1			= 'http://example.com'; // Footer Link 1
$footer_link_name_1		= 'My Account'; // Footer Link 1 Text

$footer_link_2			= 'http://example.com'; // Footer Link 2
$footer_link_name_2		= 'Customer Care'; // Footer Link 2 Text

$footer_link_3			= 'http://example.com'; // Footer Link 3
$footer_link_name_3		= 'Privacy Policy'; // Footer Link 3 Text

$footer_left_text		= 'The Restaurant Reinvented'; // Footer Left Text

$footer_feature1_imgicon= $theme_path . '/icon-grey-shop.png'; // Footer Feature1 Img Icon Url
$footer_feature2_imgicon= $theme_path . '/icon-grey-box.png'; // Footer Feature2 Img Icon Url
$footer_feature3_imgicon= $theme_path . '/icon-grey-support.png'; // Footer Feature3 Img Icon Url

$footer_feature1_title  = 'Check Our Delivery Area'; // Footer Feature1 Title
$footer_feature2_title  = 'Collect Concept Coins'; // Footer Feature2 Title
$footer_feature3_title  = 'Customer Suppor'; // Footer Feature3 Title

$footer_unsub_link		= 'http://example.com'; // Footer Unsubscribe Link
$footer_unsub_link_name = ''; // Footer Unsubscribe Text


$footer_info 			= "©2020 Concept Café. The brand names, slogans, logos, service marks and other trademarks of Concept Café's goods, services and promotions belong exclusively to By Concept (Pty) Ltd and/or its subsidiary companies, licensees and partners, and are protected from copying and simulation under national and international trademark and copyright laws and treaties throughout the world."; //Footer Info


$footer_app_title 		= "Download Our Concept App To Experience the Ultimate Convenience"; //Footer Download App Title

$footer_app_1_link 		= 'http://example.com'; //Fotter App link
$footer_app_1_img 		= $theme_path . '/app-grey-apple-store.png'; //Footer App Url

$footer_app_2_link 		= 'http://example.com'; //Fotter App link
$footer_app_2_img 		= $theme_path . '/app-grey-google-play.png'; //Footer App Url

?>

<!-- Transactional : Ad Banner Open -->
<?php if( $banner_show == "YES") :?>
<table cellspacing="0" cellpadding="0" align="center" width="100%" class="table-100pc bg-F9F9F9">
	<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" align="center" class="table-700 bg-F9F9F9">
				<tr>
					<td class="spacer-30">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" class="mo-px-25">
						<table cellspacing="0" cellpadding="0" align="center" width="100%" style="border:10px solid #FFFFFF;">
							<tr>
								<td align="center">
									<?php

										if ( $banner_link != "" ){
											echo __('<a href="' . $banner_link . '">
													<img src="' . $banner_img . '" alt="Banner" width="680" class="mo-img-full" style="width:680px;">
													</a>');
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
</table>
<?php endif; ?>
<!-- Transactional : Ad Banner Close -->

<!-- Footer 3 : Open -->
<table cellpadding="0" cellspacing="0" align="center" width="100%" class="table table-100pc bg-F9F9F9">
    <tr>
        <td align="center" style="">
            <table cellpadding="0" cellspacing="0" align="center" class="table-700 bg-F9F9F9">
                <tr>
                    <td class="spacer-60">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" style="">
                        <table cellpadding="0" cellspacing="0" align="center" class="table-640">
                            <tr>
                                <td align="center" class="mo-px-25" style="">
                                    <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                        <tr>
                                            <td align="center" class="pb-30">
                                                <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                                    <tr>
                                                        <td align="center" class="bg-F1F1F1 h-2">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" class="font-0">
                                                <!--[if (gte mso 9)|(IE)]><table cellpadding="0" cellspacing="0" align="center"  width="640"><tr><td width="305" valign="top"><![endif]-->
                                                <div class="row inline v-top width-100pc mwidth-355">
                                                    <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                                        <?php if( $footer_left_text != "") :?>
                                                        <tr>
                                                            <?php

                                                                if ( $footer_left_text != "" ) {
                                                                    echo __('<td align="center" class="font-777777 font-primary font-24 font-weight-600 font-space-1">'.$footer_left_text.'
                                                                    </td>');
                                                                }
                                                            ?>                      
                                                        </tr>
                                                        <?php endif; ?>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><td width="10" valign="top"><![endif]-->
                                                <div class="row inline v-top width-100pc mwidth-10">
                                                    <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                                        <tr>
                                                            <td class="spacer-30">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><td width="305" valign="top"><![endif]-->
                                                <div class="row inline v-top width-100pc mwidth-275">
                                                    <?php if( $social_1_link != "" || $social_2_link != "" || $social_3_link != "" || $social_4_link != "" || $social_5_link != "" || $social_6_link != "" ) :?>
                                                        <table cellpadding="0" cellspacing="0" align="center">
                                                            <tr>
                                                                <?php

                                                                    if ( $social_1_link != "" ){
                                                                        echo __('<td align="center" width="45">
                                                                            <a href="' . $social_1_link . '"><img src="' . $social_1_img . '" alt="social-url-1" width="36" class="block width-36"></a>
                                                                        </td>');
                                                                    }

                                                                    if ( $social_2_link != "" ){
                                                                        echo __('<td align="center" width="45">
                                                                        <a href="' . $social_2_link . '">
                                                                            <img src="' . $social_2_img . '" alt="social-url-1" width="36" class="block width-36">
                                                                        </a>
                                                                    </td>');
                                                                    }

                                                                    if ( $social_3_link != "" ){
                                                                        echo __('<td align="center" width="45">
                                                                        <a href="' . $social_3_link . '">
                                                                            <img src="' . $social_3_img . '" alt="social-url-1" width="36" class="block width-36">
                                                                        </a>
                                                                    </td>');
                                                                    }

                                                                    if ( $social_4_link != "" ){
                                                                        echo __('<td align="center" width="45">
                                                                        <a href="' . $social_4_link . '">
                                                                            <img src="' . $social_4_img . '" alt="social-url-1" width="36" class="block width-36">
                                                                        </a>
                                                                    </td>');
                                                                    }

                                                                    if ( $social_5_link != "" ){
                                                                        echo __('<td align="center" width="45">
                                                                        <a href="' . $social_5_link . '">
                                                                            <img src="' . $social_5_img . '" alt="social-url-1" width="36" class="block width-36">
                                                                        </a>
                                                                    </td>');
                                                                    }

                                                                    if ( $social_6_link != "" ){
                                                                        echo __('<td align="center" width="45">
                                                                        <a href="' . $social_6_link . '">
                                                                            <img src="' . $social_6_img . '" alt="social-url-1" width="36" class="block width-36">
                                                                        </a>
                                                                    </td>');
                                                                    }

                                                                ?>
                                                            </tr>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" class="ptb-30">
                                                <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                                    <tr>
                                                        <td align="center" class="bg-F1F1F1 h-2">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" class="pb-30 font-0">
                                                <!--[if (gte mso 9)|(IE)]><table cellpadding="0" cellspacing="0" align="center"  width="640"><tr><td width="305" valign="top"><![endif]-->
                                                <div class="row inline v-middle width-100pc mwidth-305">
                                                    <table cellpadding="0" cellspacing="0" align="center">
                                                        <?php if( $footer_feature1_title != "") :?>
                                                        <tr>
                                                            <?php
                                                                if ( $footer_feature1_imgicon != "" ) {
                                                                    echo __('<td align="center" width="50">
                                                                                <img src="'.$footer_feature1_imgicon.'" alt="#" width="30" class="block width-30">
                                                                            </td>');
                                                                }
                                                                if ( $footer_feature1_title != "" ) {
                                                                    echo __('<td align="left" class="font-777777 font-primary font-16 font-weight-600 font-space-1">
                                                                                <a href="http://example.com/" class="font-777777">'.$footer_feature1_title.'</a>
                                                                            </td>');
                                                                }
                                                            ?>                      
                                                        </tr>
                                                        <?php endif; ?>
                                                        <tr>
                                                            <td class="spacer-30">&nbsp;</td>
                                                        </tr>
                                                        <?php if( $footer_feature2_imgicon != "") :?>
                                                        <tr>
                                                            <?php
                                                                if ( $footer_feature2_imgicon != "" ) {
                                                                    echo __('<td align="center" width="50">
                                                                                <img src="'.$footer_feature2_imgicon.'" alt="#" width="30" class="block width-30">
                                                                            </td>');
                                                                }
                                                                if ( $footer_feature2_title != "" ) {
                                                                    echo __('<td align="left" class="font-777777 font-primary font-16 font-weight-600 font-space-1">
                                                                                <a href="http://example.com/" class="font-777777">'.$footer_feature2_title.'</a>
                                                                            </td>');
                                                                }
                                                            ?>                      
                                                        </tr>
                                                        <?php endif; ?>
                                                        <tr>
                                                            <td class="spacer-30">&nbsp;</td>
                                                        </tr>
                                                        <?php if( $footer_feature3_imgicon != "") :?>
                                                        <tr>
                                                            <?php
                                                                if ( $footer_feature3_imgicon != "" ) {
                                                                    echo __('<td align="center" width="50">
                                                                                <img src="'.$footer_feature3_imgicon.'" alt="#" width="30" class="block width-30">
                                                                            </td>');
                                                                }
                                                                if ( $footer_feature3_title != "" ) {
                                                                    echo __('<td align="left" class="font-777777 font-primary font-16 font-weight-600 font-space-1">
                                                                                <a href="http://example.com/" class="font-777777">'.$footer_feature3_title.'</a>
                                                                            </td>');
                                                                }
                                                            ?>                      
                                                        </tr>
                                                        <?php endif; ?>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><td width="30" valign="middle"><![endif]-->
                                                <div class="row inline v-middle width-100pc mwidth-30">
                                                    <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                                        <tr>
                                                            <td class="spacer-30">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><td width="305" valign="middle"><![endif]-->
                                                <div class="row inline v-middle width-100pc mwidth-305">
                                                    <table cellpadding="0" cellspacing="0" align="center">
                                                        <?php if( $footer_app_title != "") :?>
                                                        <tr>
                                                            <?php
                                                                if ( $footer_app_title != "" ) {
                                                                    echo __('<td align="center" class="font-777777 font-primary font-18 font-weight-600 font-space-1 pb-30">'.$footer_app_title.'
                                                                    </td>');
                                                                }
                                                            ?>                      
                                                        </tr>
                                                        <?php endif; ?>
                                                        <tr>
                                                            <td align="center">
                                                                <table cellpadding="0" cellspacing="0" align="center">
                                                                    <tr>
                                                                        <?php
                                                                            if ( $footer_app_1_link != "" ){
                                                                                    echo __('<td align="center" width="140">
                                                                                                <a href="' . $footer_app_1_link . '">
                                                                                                    <img src="' . $footer_app_1_img . '" alt="#" width="110" class="block width-110">
                                                                                                </a>
                                                                                            </td>');
                                                                            }
                                                                            if ( $footer_app_2_link != "" ){
                                                                                    echo __('<td align="center" width="140" style="">
                                                                                                <a href="' . $footer_app_2_link . '">
                                                                                                    <img src="' . $footer_app_2_img . '" alt="google-store" width="110" class="block width-110">
                                                                                                </a>
                                                                                            </td>');
                                                                            }
                                                                        ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" class="pb-30">
                                                <table cellpadding="0" cellspacing="0" align="center" width="100%">
                                                    <tr>
                                                        <td align="center" class="bg-F1F1F1 h-2">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <?php if( $footer_info != "") :?>
                                        <tr>
                                            <?php

                                                if ( $footer_info != "" ) {
                                                    echo __('<td align="left" class="mo-text-center font-777777 font-primary font-12 font-weight-400 font-space-1 pb-10">'.$footer_info.'</td>');
                                                }
                                            ?>                      
                                        </tr>
                                        <?php endif; ?>
                                        <?php if( $footer_unsub_link_name != "") :?>
                                        <tr>
                                            <?php

                                                if ( $footer_unsub_link_name != "" ) {
                                                    echo __(' <td align="left" class="mo-text-center font-primary font-weight-400 font-12 font-space-1">
                                                                <a href="'.$footer_unsub_link.'" class="font-777777">'.$footer_unsub_link_name.'</a>
                                                            </td>');
                                                }
                                            ?>                      
                                        </tr>
                                        <?php endif; ?>
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
<!-- Footer 3 : Close -->