<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>
	<?php include_once ESIGN_PLUGIN_PATH . "/views/about/install-checklist.php"; ?>
<?php if (array_key_exists('message', $data)) { echo $data['message']; } ?>

<div class="esign-about-wrap">

<h1><?php _e('Welcome to WP E-Signature', 'esig'); ?></h1>

<div class="esign-about-text"> <?php echo sprintf( __('Thanks for installing WP E-Signature %s &mdash; a Premium WordPress plugin.', 'esig'), $data['version_no']); ?>  
<br><i>"<?php _e('We help you and your clients sign legally binding documents. Easily.', 'esig'); ?>"</i>
</div>

<div class="esign-wp-badge">
	
	<img src='<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/e-icon-white.svg' alt='WP E-Signature' align='left' class='esig-wp-logo-badge'> 
	
	<span class='esig-wp-text-badge'><?php _e('Version', 'esig');  _e($data['version_no'],'esig'); ?></span>
	
	<a href='//www.approveme.com/wp-digital-e-signature/?ref=5' target='_blank'>
	
	<img src='<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/approveme-badge.svg' alt='WP E-Signature' align='left' class='esig-wp-powered-badge'>
	
	</a> 

</div>

			<p>
			
			<a class='wp-e-saveButton esign-blue-btn' href="admin.php?page=esign-settings"><?php _e('Settings', 'esig'); ?></a>
			
			<a class='wp-e-saveButton esign-blue-btn' href="admin.php?page=esign-docs"><?php _e('Docs','esig'); ?></a>
			
			<iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/tweet_button.1393899192.html#_=1394751928338&amp;count=horizontal&amp;hashtags=esignature&amp;id=twitter-widget-0&amp;lang=en&amp;size=l&amp;text=An%20open-source%20(free)%20%23WPeSignature%20plugin%20for%20%23WordPress%20that%20helps%20you%20sign%20documents.&amp;url=http%3A%2F%2Fgoo.gl%2F9CFLSv&amp;via=approveme" class="twitter-share-button twitter-tweet-button twitter-count-horizontal" title="Twitter Tweet Button" data-twttr-rendered="true" style="width: 138px; height: 35px;"></iframe>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</p>
	
<style type="text/css">
#TB_window
{
    width:auto !important;
    top:5% !important;
    
}

</style>
<?php add_thickbox();  ?>


<div class="esig-getting-started-video"><a href="https://www.youtube.com/embed/RWNKE1_qFwU?&autoplay=1&rel=0&theme=light&hd=1&autohide=1&showinfo=0&color=white&showinfo=0?TB_iframe=true&width=960&height=500" class="thickbox">


<img src="<?php echo plugins_url('assets/getting-started-video-thumb.png',__FILE__); ?>" align="center" width="70%">


</a>
</div>

<h2 class="nav-tab-wrapper">
	<a href="index.php?page=esign-about#" class="nav-tab nav-tab-active">
		<?php _e('What&#8217;s New', 'esig'); ?>	</a><a href="admin.php?page=esign-addons" class="nav-tab">
		<?php _e('Premium Support','esig'); ?>	</a><a href="admin.php?page=esign-addons" class="nav-tab">
		<?php _e('Add-Ons','esig'); ?>	</a>
</h2>

<div class="esign-changelog esign-about-features-margin">


	<h2 class="esign-about-headline-callout"><?php _e('Introducing electronic signatures for WordPress','esig'); ?></h2>
	<img class="esign-about-overview-img" src="<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/about/about-screenshot.png" />
	<div class="esign-feature-section esign-col esign-three-col about-updates">
		<div class="esign-col-1">
		<img src="<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/about/welcome-esign.svg" class="nice-to-meet"/>
		</div>
		<div class="esign-col-2">
			<img src='<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/about/audit-trail.png' class='esign-about-audit-img'/>
			<h3><?php _e('Legally Binding Plugin','esig'); ?></h3>
			<p><?php _e('The Uniform Electronic Transactions Act (UETA) and the passage of Electronic Signatures in Global and National Commerce Act (ESIGN) are strictly enforced using an audit trail, ip address collection, activity timestamp, terms of service agreement and more.', 'esig'); ?></p>
		</div>
		<div class="esign-col-3 esign-last-feature">
			<img src="<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/about/ssl-settings.png" class='esign-about-audit-img'/>
			<h3><?php _e('SSL and HTTPS','esig'); ?></h3>
			<p><?php _e('WP E-Signature comes SSL (Secure Sockets Layer) and HTTPS ready.
The Force SSL/HTTPS setting in WP E-Signature will ensure document-signing pages are only shown over a secure and encrypted HTTPS connection when the option is enabled (a third party ssl certificate must be previously installed).', 'esig'); ?></p>
		</div>
	</div>
</div>

<hr>


<div class="esign-changelog">
	<div class="esign-feature-section esign-col esign-two-col">
		<div>
			<p align="left" class="feature-text"><span class="esign-icon-secure"></span><span class="feature-list esign-about-features-a"><?php _e('Secure and encrypted.', 'esig'); ?></span>
            <?php _e('Security is our top priority. All of your signed documents and signatures are kept hidden and encrypted on your server at all times, using a GUID encryption sequence.', 'esig'); ?>
			</p>
			
			<p align="left" class="feature-text"><span class="esign-icon-audit"></span><span class="feature-list esign-about-features-a"><?php _e('Audit report.', 'esig'); ?></span>
			<?php _e('Documents move turbo speed from signer to signer. We include a detailed audit report with viewer details, ip addresses, document analytics, document id# and signer history.', 'esig'); ?>
			</p>
			
		<p align="left" class="feature-text"><span class="esign-icon-smarter"></span><span class="feature-list esign-about-features-a"><?php _e('No monthly fees.', 'esig'); ?></span>
		<?php _e('WP E-Signature is the easiest and most affordable way to sign documents. You have full control over your data - it never leaves your server. Built for WordPress.', 'esig'); ?>
		</p>
			
		</div>
		<div class="esign-last-feature esign-col esign-two-col">
			<p align="left" class="feature-text"><span class="esign-icon-legal"></span><span class="feature-list esign-about-features-a"><?php _e('Legally binding.', 'esig'); ?></span>
			<?php _e("WP E-Signature is recognized in court. We are (UETA) and (ESIGN) compliant and adhere to some of the strictest document signing policy's in the US and European unions.", "esig"); ?>
			</p>
			
			<p align="left" class="feature-text"><?php _e('<span class="esign-icon-integrated"></span><span class="feature-list esign-about-features-a">Automate signing documents.</span>Tired of having to manually send out your NDA contract, Terms of Service, Patient in take form, Insurance Documents etc. Now you can automate the process.', 'esig'); ?>
			</p>
			
			<p align="left" class="feature-text"><?php _e('<span class="esign-icon-wallet"></span><span class="feature-list esign-about-features-a">Tablet &amp; Cell Phone Friendly.</span>Upload documents via WordPress dashboard and collect real client signatures through your website. Users sign documents using their mouse, trackpad, tablet, or phone.', 'esig'); ?>
			</p>
		</div>
	</div>
</div>

<hr>


<div class="esign-changelog">
	<div class="esign-feature-section esign-col esign-two-col">
		<div>
			<h3><?php _e("No Monthly Fees (we're serious)","esign"); ?></h3>
			
			<p><?php _e('Our goal with WP E-Signature all along has been to create a FREE document signing alternative to the monthly service fees currently associated with the document signing industry. We make it ridiculously easy to sign documents from your very own WordPress website, send document signer invites, and save a ton of money by canceling your current monthly e-signature service (no monthly fees with us). ', 'esig'); ?></p>
	
			
			<h4><?php _e('You have full control over your document(s).</h4>
			<p>Each signature, document, and data related to your document is encrypted and stored in your very own website database.  Because documents are saved on your website server (and not a third party server), you can save lots of money by using WP E-Signature.', 'esig'); ?></p>
			
		</div>
		<div class="esign-last-feature esign-about-themes-img">
			<img src="<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/about/invite-emails.png" />
		</div>
	</div>
</div>

<hr>

<div class="esign-changelog about-twentyfourteen">
	<h2 class="esign-about-headline-callout"><?php _e('Customize E-Signature with ADD-ONS!', 'esig'); ?></h2>
	<p align='center'><?php _e('You can customize your Document Signing workflow to work with many of our E-Signature add-on extensions.', 'esig'); ?></p>
	
	<a href='//www.approveme.com/wp-digital-e-signature/wordpress-electronic-digital-signature-add-ons?ref=6' target='_blank'><img src='<?php echo $data['ESIGN_ASSETS_URL']; ?>/images/about/add-on-screenshots.png' class='esig-about-addon'/></a>

	<div class="esign-feature-section esign-col esign-one-col esign-center-col">
		<div>
			
			<p align='center'><a class='esign-red-btn-lrg wp-e-saveButton esign-blue-btn' href='admin.php?page=esign-settings'><?php _e("Let's Start Signing Documents!", "esig"); ?></a></p>
			
		</div>
	</div>
</div>

<hr>

<div class="esign-return-to-dashboard">
		<a href="index.php"><?php _e('Go to Dashboard &rarr; Home', 'esig'); ?></a>
</div>

</div>

