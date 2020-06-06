<?php
/**
 * Flippercode Product Overview Setup Class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 2.0.0
 * @package Core
 */

if ( ! class_exists( 'Flippercode_Product_Overview' ) ) {

	/**
	 * FlipperCode Overview Setup Class.
	 *
	 * @author Flipper Code<hello@flippercode.com>
	 *
	 * @version 2.0.0
	 *
	 * @package Core
	 */


	class Flippercode_Product_Overview {


		public $PO;


		public $productOverview;

		/**
		 * Store object type
		 *
		 * @var  String
		 */


		public $productName;


		/**
		 * Store object type
		 *
		 * @var  String
		 */


		public $productSlug;


		/**
		 * Store object type
		 *
		 * @var  String
		 */


		public $productTagLine;


		/**
		 * Store object type
		 *
		 * @var  String
		 */


		public $productTextDomain;


		/**
		 * Store object type
		 *
		 * @var  String
		 */


		public $productIconImage;




		/**
		 * Store product current running version number
		 *
		 * @var  String
		 */


		public $productVersion;

		/**
		 * Store object type
		 *
		 * @var  String
		 */


		private $commonBlocks;




		/**


		 * Store object type
		 *
		 * @var  String
		 */


		private $productSpecificBlocks;


		/**
		 * Store object type
		 *
		 * @var  String
		 */


		private $is_common_block;




		/**
		 * Store Product Overview Markup
		 *
		 * @var  String
		 */


		private $productBlocksRendered = 0;




		/**
		 * Store Product Overview Markup
		 *
		 * @var  String
		 */


		private $blockHeading;


		/**
		 * Store Product Overview Markup
		 *
		 * @var  String
		 */


		private $blockContent;


		/**
		 * Store Current Block Indication Class
		 *
		 * @var  String
		 */


		private $blockClass = '';


		/**
		 * Store Product Overview Markup
		 *
		 * @var  String
		 */


		private $commonBlockMarkup = '';


		/**
		 * Store Product Overview Markup
		 *
		 * @var  String
		 */


		private $pluginSpecificBlockMarkup = '';


		/**
		 * Final Product Overview Markup
		 *
		 * @var  String
		 */


		private $finalproductOverviewMarkup = '';


		/**
		 * Assign all products their i-cards :)
		 *
		 * @var  Array
		 */


		private $allProductsInfo = array();


		/**
		 * Store current message
		 *
		 * @var  Boolean
		 */


		private $message = '';

		/**
		 * Store current error = '';
		 *
		 * @var  Boolean
		 */


		private $error;
		/**
		 * Store product online doc url;
		 *
		 * @var  Boolean
		 */


		private $docURL;
		/**
		 * Store product demo url;
		 *
		 * @var  Boolean
		 */


		private $demoURL;
		/**
		 * Product Image Path;
		 *
		 * @var  Boolean
		 */


		private $productImagePath;
		/**
		 * Is Update Available ?;
		 *
		 * @var  Boolean
		 */


		private $isUpdateAvailable;


		private $multisiteLicence;


		private $productSaleURL;


		function __construct( $pluginInfo ) {

			$this->commonBlocks = array( 'product-activation', 'newsletter', 'refund-block', 'extended-support' );
			if ( isset( $pluginInfo['excludeBlocks'] ) ) {
				$this->commonBlocks = array_diff( $this->commonBlocks, $pluginInfo['excludeBlocks'] );
			}
			$this->init( $pluginInfo );
			$this->renderOverviewPage();

		}


		function renderOverviewPage() { ?>


			<div class="flippercode-ui fcdoc-product-info" data-current-product=<?php echo esc_attr( $this->productTextDomain ); ?> data-current-product-slug=<?php echo esc_attr( $this->productSlug ); ?> data-product-version = <?php echo esc_attr( $this->productVersion ); ?> data-product-name = "<?php echo esc_attr( $this->productName ); ?>" >
			<div class="fc-main">	
			<div class="fc-container">

				 <div class="fc-divider"><div class="fc-12"><div class="fc-divider">
					  <div class="fcdoc-flexrow">
					 <?php $this->renderBlocks(); ?> 
					  </div>
				 </div></div></div>
			 </div>    
			</div>
			<?php

		}


		function setup_plugin_info( $pluginInfo ) {

			foreach ( $pluginInfo as $pluginProperty => $value ) {
				$this->$pluginProperty = $value;
			}

		}


		function get_mailchimp_integration_form() {

			$form = '';
			$form .= '<!-- Begin MailChimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/slim-10_7.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
</style>


<div id="mc_embed_signup">
<form action="//flippercode.us10.list-manage.com/subscribe/post?u=eb646b3b0ffcb4c371ea0de1a&amp;id=3ee1d0075d" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>


    <div id="mc_embed_signup_scroll">
	<label for="mce-EMAIL">' . $this->PO['subscribe_mailing_list'] . '</label>
	<input type="email"  name="EMAIL" value="' . get_bloginfo( 'admin_email' ) . '" class="email" id="mce-EMAIL" placeholder="email address" required>
    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_eb646b3b0ffcb4c371ea0de1a_3ee1d0075d" tabindex="-1" value=""></div>
    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="fc-btn fc-btn-default"></div>


    </div>
</form>
</div>


<!--End mc_embed_signup-->';

			 return $form;

		}



		function init( $pluginInfo ) {

			$this->setup_plugin_info( $pluginInfo );

			$this->PO = $this->productOverview;

			foreach ( $this->commonBlocks as $block ) {

				switch ( $block ) {

					case 'product-activation':
						$this->blockHeading = '<h1>' . $this->PO['product_info_heading'] . '</h1>';
						$this->blockContent .= '<div class="fc-divider fcdoc-brow">
	                       	<div class="fc-2"><i class="fa fa-file-video-o" aria-hidden="true"></i></div>
	                       	<div class="fc-10">' . $this->PO['product_info_desc'] . '<br><br><strong><a href="' . $this->demoURL . '" target="_blank" class="fc-btn fc-btn-default get_started_link">' . $this->PO['live_demo_caption'] . '</a></strong>
                            </div>
                        </div>';
						$this->blockContent .= '<div class="fc-divider fcdoc-brow">
	                       	<div class="fc-2"><i class="fa fa-arrow-right" aria-hidden="true"></i></div>
	                       	<div class="fc-10">' . $this->PO['installed_version'] . '<br><strong>' . $this->productVersion . '</strong>
							<div class="action">';
						$this->blockContent .= '</div></div>';

						$this->blockContent .= '</div>';

						break;

					case 'newsletter':
						$this->blockHeading = '<h1>' . $this->PO['subscribe_now']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow"> 
	                       	<div class="fc-2"><i class="fa fa-bullhorn" aria-hidden="true"></i></div>
	                       	<div class="fc-10">' . $this->PO['subscribe_now']['desc1'] . '		
	                         </div>
                        </div>
                        <div class="fc-divider fcdoc-brow"> 
	                       	<div class="fc-2"><i class="fa fa-thumbs-up" aria-hidden="true"></i></div>
	                       	<div class="fc-10">' . $this->PO['subscribe_now']['desc2'] . '		
	                        </div>
                        </div>';

						$this->blockContent .= $this->get_mailchimp_integration_form();
						break;

					case 'refund-block':
						$this->blockHeading = '<h1>' . $this->PO['refund']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow"> 
	                       	<div class="fc-2"><i class="fa fa-smile-o" aria-hidden="true"></i></div>
	                       	<div class="fc-10">' . $this->PO['refund']['desc'] . '<br><br><a target="_blank" class="fc-btn fc-btn-default refundbtn" href="http://codecanyon.net/refund_requests/new">' . $this->PO['refund']['request'] . '</a></div></div>';

						break;

					case 'extended-support':
						$this->blockHeading = '<h1>' . $this->PO['support']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow"> 
	                       	<div class="fc-2"><i class="fa fa-life-ring" aria-hidden="true"></i><br></div>
	                       	<div class="fc-10">' . $this->PO['support']['desc1'] . '<br><br>
	                         	<div class="support_btns"><a target="_blank" href="' . esc_url( $this->productSaleURL ) . '" name="one_year_support" id="one_year_support" value="" class="fc-btn fc-btn-default support">' . $this->PO['support']['link'] . '</a>
	                       	    <a target="_blank" href="' . esc_url( $this->multisiteLicence ) . '" name="multi_site_licence" id="multi_site_licence" class="fc-btn fc-btn-default supportbutton">' . $this->PO['support']['link2'] . '</a></div>
	                         </div>
                    </div>';
						break;

				}

				$info = array( $this->blockHeading, $this->blockContent, $block );
				$this->commonBlockMarkup .= $this->get_block_markup( $info );

			}

		}


		function get_block_markup( $blockinfo ) {

			$markup = '<div class="fc-6 fcdoc-blocks ' . $blockinfo[2] . '">
			                <div class="fcdoc-block-content">
			                    <div class="fcdoc-header">' . $blockinfo[0] . '</div>
			                    <div class="fcdoc-body">' . $blockinfo[1] . '</div>
			                </div>
            		   </div>';

			$this->productBlocksRendered++;

			if ( $this->productBlocksRendered % 2 == 0 ) {

				$markup .= '</div></div><div class="fc-divider"><div class="fcdoc-flexrow">'; }

			return $markup;

		}


		function renderBlocks() {

			$this->finalproductOverviewMarkup = $this->commonBlockMarkup . $this->pluginSpecificBlockMarkup;
			echo $this->finalproductOverviewMarkup;

		}


	}

}


