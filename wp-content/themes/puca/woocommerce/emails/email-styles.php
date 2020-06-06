<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-styles.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load colors.
$bg        = get_option( 'woocommerce_email_background_color' );
$body      = get_option( 'woocommerce_email_body_background_color' );
$base      = get_option( 'woocommerce_email_base_color' );
$base_text = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text      = get_option( 'woocommerce_email_text_color' );

// Pick a contrasting color for links.
$link_color = wc_hex_is_light( $base ) ? $base : $base_text;

// Pick a contrasting color for Btn.
$btn_color = wc_hex_is_light( $base ) ? $base : $base_text;

if ( wc_hex_is_light( $body ) ) {
	$link_color = wc_hex_is_light( $base ) ? $base_text : $base;
}

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );
$text_lighter_45 = wc_hex_lighter( $text, 45 );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
// body{padding: 0;} ensures proper scale/positioning of the email in the iOS native email app.

?>

body {
	margin: 0 !important;
	padding: 0 !important;
	width: 100% !important;
	word-break: break-word;
	mso-line-height-rule: exactly;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust: 100%;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

table {
	border: 0;
	border-spacing: 0;
	border-collapse: collapse;
}

img {
	height: auto;
	outline: none;
	text-decoration: none;
	-ms-interpolation-mode: bicubic;
}

img:hover {
	opacity: 0.9 !important;
}

a {
	text-decoration: none;
}

p {
	margin: 0;
}

ul, ol {
	margin: 0 0 1em;
}

strong {
	font-weight: 700;
}

a[x-apple-data-detectors] {
	color: inherit !important;
	text-decoration: none !important;
	font-size: inherit !important;
	font-family: inherit !important;
	font-weight: inherit !important;
	line-height: inherit !important;
}


.font-primary {
	font-family: 'Poppins', DejaVu Sans, Verdana, sans-serif;
}

.font-secondary {
	font-family: 'Lora', Palatino, Book Antiqua, Georgia, serif;
}

.width-100pc{
	width: 100%;
}

.width-36{
	width: 36px;
}

.width-30{
	width: 30px;
}

.width-20{
	width: 20px;
}

.width-110{
	width: 110px;
}

.mwidth-275{
	max-width: 275px;
}

.mwidth-305{
	max-width: 305px;
}

.mwidth-355{
	max-width: 355px;
}

.mwidth-150{
	max-width: 150px;
}

.mwidth-10{
	max-width: 10px;
}

.mwidth-30{
	max-width: 30px;
}

.mwidth-75{
	max-width: 75px;
}

.table-100pc {
	width: 100%;
	max-width: 100%;
	margin: 0 auto;
}

.table-700 {
	width: 700px;
	max-width: 700px;
	margin: 0 auto;
}

.table-640 {
	width: 640px;
	max-width: 640px;
	margin: 0 auto;
}

.inline {
	display: inline-block;
}

.block {
	display: block;
}

.v-top {
	vertical-align: top;
}

.v-middle {
	vertical-align: middle;
}


.bg-000000 {
	background-color: <?php echo esc_attr( $base ); ?>;
}

.bg-FFFFFF {
	background-color: <?php echo esc_attr( $body ); ?>;
}

.bg-F9F9F9 {
	background-color: <?php echo esc_attr( $bg ); ?>;
}

.bg-F1F1F1 {
	background-color: #F1F1F1;
}



.font-000000 {
	color: <?php echo esc_attr( $text ); ?>;
}

.font-777777 {
	color: <?php echo esc_attr( $text_lighter_45 ); ?>;
}

.font-btn {
	color: <?php echo esc_attr( $btn_color ); ?>;
}

.font-FF0A0A {
	color: #FF0A0A;
}

.font-0 {
	font-size: 0px;
}

.font-12 {
	font-size: 12px;
	line-height: 22px
}

.font-14 {
	font-size: 14px;
	line-height: 24px
}

.font-16 {
	font-size: 16px;
	line-height: 26px
}

.font-18 {
	font-size: 18px;
	line-height: 28px
}

.font-20 {
	font-size: 20px;
	line-height: 30px
}

.font-22 {
	font-size: 22px;
	line-height: 32px
}

.font-24 {
	font-size: 24px;
	line-height: 34px
}

.font-32 {
	font-size: 32px;
	line-height: 42px
}

.font-42 {
	font-size: 42px;
	line-height: 52px
}

.font-weight-400 {
	font-weight: 400;
}

.font-weight-500 {
	font-weight: 500;
}

.font-weight-600 {
	font-weight: 600;
}

.font-weight-700 {
	font-weight: 700;
}

.font-italic {
	font-style: italic;
}

.font-space-1 {
	letter-spacing: 1px;
}

.font-space-2 {
	letter-spacing: 2px;
}

/*--- Text Decoration --*/
.font-underline {
	text-decoration: underline;
}


/* Spacer Start With 5px - 60px with 5px Gap */

.spacer-5 {
	height: 5px;
	font-size: 5px;
	line-height: 5px;
}

.spacer-10 {
	height: 10px;
	font-size: 10px;
	line-height: 10px;
}

.spacer-15 {
	height: 15px;
	font-size: 15px;
	line-height: 15px;
}

.spacer-30 {
	height: 30px;
	font-size: 30px;
	line-height: 30px;
}

.spacer-45 {
	height: 45px;
	font-size: 45px;
	line-height: 45px;
}

.spacer-60 {
	height: 60px;
	font-size: 60px;
	line-height: 60px;
}


.pb-5 {
	padding: 0;
	padding-bottom: 5px;
}

.pb-10 {
	padding: 0;
	padding-bottom: 10px;
}

.pb-15 {
	padding: 0;
	padding-bottom: 15px;
}

.pb-20 {
	padding: 0;
	padding-bottom: 20px;
}

.pb-30 {
	padding: 0;
	padding-bottom: 30px;
}

.ptb-30 {
	padding: 0;
	padding-top: 30px;
	padding-bottom: 30px;
}

.pb-40 {
	padding: 0;
	padding-bottom: 40px;
}

.pb-60 {
	padding: 0;
	padding-bottom: 60px;
}

.h-1 {
	height: 1px;
	font-size: 1px;
	line-height: 1px;
}

.h-2 {
	height: 2px;
	font-size: 2px;
	line-height: 2px;
}

.h-35 {
	height: 35px;
	font-size: 35px;
	line-height: 35px;
}

.h-100 {
	height: 100px;
	font-size: 100px;
	line-height: 100px;
}

.h-120 {
	height: 120px;
	font-size: 120px;
	line-height: 120px;
}

.btn {
	mso-padding-alt: 14px 40px 14px 40px;
}

.btn a {
	padding: 14px 40px 14px 40px;
	white-space: nowrap;
}

.btn-l {
	mso-padding-alt: 18px 100px 18px 100px;
}

.btn-l a {
	padding: 18px 100px 18px 100px;
	white-space: nowrap;
}



@media only screen and (max-width:699px) {

	table.table-700, table.table-640, table.table-540, div.row {
		width: 100% !important;
		max-width: 100% !important;
	}

	table.halfRow, div.halfRow {
		width: 46% !important;
		max-width: 46% !important;
	}

	.mo-bg-reset {
		background-position: center center !important;
		background-size: cover !important;
		background-repeat: no-repeat !important;
	}

	.mo-none {
		mso-hide: all !important;
		display: none !important;
	}

	.mo-block {
		display: block !important;
		text-align: center !important;
		margin: 0 auto !important;
	}

	.mo-p-0 {
		padding: 0 !important;
	}

	.mo-pb-30 {
		padding-bottom: 30px !important;
	}

	.mo-pb-60 {
		padding-bottom: 60px !important;
	}

	.mo-pt-30 {
		padding-top: 30px !important;
	}

	.mo-pt-60 {
		padding-top: 60px !important;
	}

	.mo-h-0 {
		height: 0 !important;
		font-size: 0px !important;
		line-height: 0px !important;
	}

	.mo-h-30 {
		height: 30px !important;
		font-size: 30px !important;
		line-height: 30px !important;
	}

	.mo-h-60 {
		height: 60px !important;
		font-size: 60px !important;
		line-height: 60px !important;
	}

	.mo-border-none {
		border: none ! important;
	}

	.vertical-divider {
		height: 2px !important;
		width: 100% !important;
	}

	img.mo-img-full {
		width: 100% !important;
		max-width: 100% !important;
	}

	table.mo-center, td.mo-center, td.mo-center img {
		float: none !important;
		margin: 0 auto !important;
	}

	td.mo-text-center {
		text-align: center !important;
	}

	td.mo-px-25 {
		padding-left: 25px !important;
		padding-right: 25px !important;
	}

	table.mo-link td {
		width: 100% !important;
		display: block !important;
		text-align: center !important;
	}

	table.mo-link td a {
		display: block !important;
		text-align: center !important;
		padding-bottom: 15px !important;
		border-bottom: 2px solid #F1F1F1 !important;
	}

	td.mo-link {
		line-height: 20px !important;
	}

	td.mo-link a {
		display: block !important;
		text-align: center !important;
		padding-bottom: 15px !important;
		border-bottom: 2px solid #F1F1F1 !important;
	}

}

@media only screen and (max-width:480px) {

	table.table-380, div.row {
		width: 100% !important;
		max-width: 100% !important;
	}

	table.halfRow, div.halfRow {
		width: 100% !important;
		max-width: 100% !important;
	}

	.btn-full a {
		padding-right: 60px !important;
		padding-left: 60px !important;
	}

	.btn-l a {
		padding-right: 60px !important;
		padding-left: 60px !important;
	}

	.mo-row {
		width: 100% !important;
		max-width: 100% !important;
	}

	.mo-btn-full {
		width: 100% !important;
		float: none !important;
		margin: 0 auto !important;
		text-align: center !important;
		min-width: 100% !important;
	}

	.mo-font-50 {
		font-size: 50px !important;
		line-height: 60px !important;
	}

	.mo-font-100 {
		font-size: 100px !important;
		line-height: 110px !important;
	}

}
<?php
