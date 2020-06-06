<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooCommerce')) {
    exit;
}

//Define tabs class
$tabsClass = array(
    '2'	=> 'two-tabs',
    '3'	=> 'three-tabs',
    '4'	=> 'four-tabs',
    '5'	=> 'five-tabs',
    '6'	=> 'six-tabs'
);

//Get admin options
$options = get_option('arg-mc-options');

if (empty($options)) :
    $options = array();
endif;

//Show / hide form steps
$showLogin 	        = !empty($options['show_login']) ? true : false;
$showRegister       = !empty($options['show_register']) ? true : false;
$showCoupon         = !empty($options['show_coupon']) ? true : false;
$showOrder 	        = !empty($options['show_order']) ? true : false;
$showShipping 	    = false;
$showOrderReview    = !empty($options['show_order_review']) ? true : false;

if (!empty($options['show_additional_information'])) :
    $showShipping = true;
else :
    add_filter('woocommerce_enable_order_notes_field', '__return_false');
endif;

if (true === WC()->cart->needs_shipping_address()) :
    $showShipping = true;
endif;


if ($showLogin === false) :
    unset($options['steps']['login']);
endif;

if ($showCoupon === false) :
    unset($options['steps']['coupon']);
endif;

if ($showOrder === false) :
    unset($options['steps']['order']);
endif;

if ($showShipping === false) :
    unset($options['steps']['shipping']);
endif;

if ($showOrderReview === false) :
    unset($options['steps']['order_review']);
endif;


//Merge Billing and Shipping
if (!empty($options['merge_billing_shipping'])) :
    unset($options['steps']['billing']);
    unset($options['steps']['shipping']);
else :
    unset($options['steps']['billing_shipping']);
endif;


//Merge Order and Payment
if (!empty($options['merge_order_payment'])) :
    unset($options['steps']['order']);
    unset($options['steps']['payment']);
else :
    unset($options['steps']['order_payment']);
endif;

$options = apply_filters('arg-mc-init-options', $options);


$isLogged = false;
if (is_user_logged_in()) :
    $isLogged   = true; 
    $showLogin  = false;
    
    unset($options['steps']['login']);
endif;


//Set the first step
$firstStep  = '';
$class      = $options['tabs_template'];

if (!empty($options['steps'])) :
    
    $firstStep = key($options['steps']);
    
    if (!empty($options['steps'][$firstStep]['data'])) :
        $class .= ' ' . $options['steps'][$firstStep]['data'];
    endif;
endif;



$classTabsLayout = '';

if (!empty($options['tabs_layout'])) :
    if ($options['tabs_layout'] == 'tabs-arrow-alt') :
    
        //Tabs Arrow Alt Layout Classes      
        $classTabsLayout = ' tabs-arrow-alt';
        
        if (!empty($options['tab_arrow_alt_hide_number_bkg'])) :
            $classTabsLayout .= ' hide-numbers-bkg';
        endif;
        
        if (!empty($options['tab_arrow_alt_hide_tails'])) :
            $classTabsLayout .= ' hide-arrows';
            
            if ($options['tab_arrow_alt_orientation'] == 'horizontal') :
                $classTabsLayout .= ' orientation-horizontal';   
            else:
                $classTabsLayout .= ' orientation-vertical'; 
            endif;
        endif;
    
    
    elseif ($options['tabs_layout'] == 'tabs-outline') :
    
        //Tabs Outline Layout Classes
        $classTabsLayout = ' tabs-outline';

        if ($options['tab_outline_orientation'] == 'horizontal-text-under') :
            $classTabsLayout .= ' orientation-horizontal';   
        elseif ($options['tab_outline_orientation'] == 'horizontal-text-inline') :
            $classTabsLayout .= ' orientation-horizontal inline';
        else:
            $classTabsLayout .= ' orientation-vertical';
        endif;   

    elseif ($options['tabs_layout'] == 'tabs-progress-bar') :
        $classTabsLayout = ' tabs-progress-bar';
        
        if ($options['tab_progress_template'] == 'progress-not-boxed') :
            $classTabsLayout .= ' progress-alt';
        endif;
        
    endif;   
    
endif;
?>

<div data-scrolltopdesktops="<?php echo $options['scrollTopDesktops']; ?>" data-scrolltopmobiles="<?php echo $options['scrollTopMobiles']; ?>" class="argmc-wrapper wrapper-no-bkg <?php echo $class . $classTabsLayout; ?>" data-coupon-position="<?php echo $showCoupon === false ? $options['coupon_position'] : 'default'; ?>">

	<?php
	
	wc_print_notices();
	
	//If checkout registration is disabled and not logged in, the user cannot checkout
	if (!$checkout->enable_signup && !$checkout->enable_guest_checkout && !$isLogged) :
            echo apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce'));
            return;
	endif;

	//Hide coupon form for moving it to another step
	if ($showCoupon === false && $options['coupon_position'] != 'default') :
		?>
		<div class="coupon-placeholder">
			<div class="coupon-wrapper">
				<?php do_action('woocommerce_checkout_coupon_form', $checkout); ?>
			</div>
		</div>	
		<?php
	endif;
	
	remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
	remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

	//do_action('woocommerce_before_checkout_form', $checkout);	

	if (!empty($options['steps'])) :
		$class          = $options['tabs_width'];
		$countSteps     = count($options['steps']);
		$class         .= $countSteps >= 2 && $countSteps <= 6 ? ' '. $tabsClass[$countSteps] : '';
        
		?>
		<ul class="argmc-tabs-list <?php echo $class; ?>">
			<?php
			$i = 1;
			//Display tabs
			foreach ($options['steps'] as $template => $step) :
				?>		
				<li class="argmc-tab-item<?php echo $i == 1 ? ' current visited last' : ''; ?>">

					<div class="argmc-tab-item-outer">
						<div class="argmc-tab-item-inner">
							<div class="argmc-tab-number-wrapper">
								<div class="argmc-tab-number">
									<span class="number-text"><?php echo $i.'<span>.</span>'; ?></span>
									<span class="tab-completed-icon"></span>
								</div>
							</div>

							<div class="argmc-tab-text"><span><?php echo $step['text']; ?></span></div>
						</div>
					</div>

				</li>
				<?php
				$i++;
			endforeach;
			?>
		</ul><!--argmc-tabs-list-->

		<div class="argmc-form-steps-wrapper">
			<?php 
			$i                              = 1; 
			
			$displayCheckoutForm            = true;
			$checkoutOpenFormHtml           = '<form name="checkout" method="post" class="checkout woocommerce-checkout argmc-form" action="' . esc_url(wc_get_checkout_url()) .'" enctype="multipart/form-data">';
			
			$displayOrderReview             = true;
			$checkoutOpenOrderReviewHtml    = '<div id="order_review" class="woocommerce-checkout-review-order">';
			$checkoutCloseOrderReview		= false;
            $forceUserLogin                 = '';

			foreach ($options['steps'] as $template => $step) : 
				switch ($template) :
					//Login step
					case 'login':
						$class = !empty($step['class']) ? ' ' . $step['class'] : '';
						
						if ($showRegister) : 
                            
                            if (!empty($options['force_login_message'])) :
                                $forceUserLogin = 'data-forcelogin="yes" data-forceloginmessage="' . $options['force_login_message'] . '"';
                            endif;
                        
                            if ($options['login_layout'] == 'register-on-right-side') :
                                $class .= ' has-register register-visible';
                            else :
                                $class .= ' has-register register-hidden';
                            endif;
						endif;

						?>
						<div data-step="<?php echo $step['data']; ?>" <?php echo $forceUserLogin; ?> class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo $class; ?>">
						    
                            <h3 class="step-name"><?php echo $step['text']; ?></h3>
                            
                            <?php
                            //Show Login/Register Top Message
                            if ($showRegister) :
                            ?>
                                <div class="login-register-message"><?php echo $options['login_register_top_message']; ?></div>  
                            <?php
                            endif;
                            ?>   
                         
						    <div class="argmc-login-inner">
							
                            <?php
                            if (!$showRegister) :
                                //No Register Form on Login Step
                                ?>
                                <div class="argmc-login">
                                    <?php do_action('woocommerce_checkout_login_form', $checkout); ?>
                                </div><!--argmc-login-->
                                <?php
                            else :
                                //Show Register Form on Login Step

                                //Register Form is Visible on Login Step
                                if ($options['login_layout'] == 'register-on-right-side') :
                                    ?>
                                    <div class="argmc-login">
                                        <h3 class="login-headings"><?php echo $options['login_heading']; ?></h3>
                                        <?php do_action('woocommerce_checkout_login_form', $checkout); ?>
                                    </div><!--argmc-login-->
                                
                                    <div class="argmc-register">
                                        <h3 class="login-headings"><?php echo $options['register_heading']; ?></h3>
                                        <?php wc_get_template('checkout/form-register.php', array('checkout' => $checkout)); ?>	
                                    </div><!--argmc-register-->                              
                                    <?php
                               
                                else :
                                    //Register Form is Hidden on Login Step (tabs switch style)
                                    ?>                                 
                                    <div class="argmc-login-tabs">
                                        <h3 class="tab-item current" data-target="argmc-login"><?php echo $options['login_heading']; ?></h3>
                                        <span class="tab-sep"><?php _e('/', 'argMC'); ?></span>
                                        <h3 class="tab-item" data-target="argmc-register"><?php echo $options['register_heading']; ?></h3>
                                    </div>
                                
                                    <div class="argmc-login">
                                        <?php do_action('woocommerce_checkout_login_form', $checkout); ?>
                                    </div><!--argmc-login-->
                                
                                    <div class="argmc-register" style="display: none;">
                                        <?php wc_get_template('checkout/form-register.php', array('checkout' => $checkout)); ?>																	
                                    </div><!--argmc-register-->
                                    
                                <?php
                                endif;
                                
                            endif;
                            
                           ?>
							</div><!--argmc-login-inner-->
						</div>
						<?php

						break;

					//Coupon step
					case 'coupon':
						?>
                        <div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							<h3 class="step-name"><?php echo $step['text']; ?></h3>
                            <?php do_action('woocommerce_checkout_coupon_form', $checkout); ?>
						</div>                                            
						<?php

						break;

					//Billing step
					case 'billing':
						if ($displayCheckoutForm === true) :
							echo $checkoutOpenFormHtml;
							$displayCheckoutForm = false;
						endif;
						?>
                        <div id="customer_details">
                            <div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
                                <h3 class="step-name"><?php echo $step['text']; ?></h3>
                                <?php
                                if ($checkout->get_checkout_fields()) :
                                    //do_action('woocommerce_checkout_before_customer_details'); 
                                    do_action('woocommerce_checkout_billing'); 
                                endif;
                                ?>
                            </div>    
                            <?php
                        
                        if ($showShipping === false) :
                        ?>
                        </div><!--#customer_details-->
                        <?php
                        endif;

						break;

					//Shipping step	
					case 'shipping':
						if ($displayCheckoutForm === true) :
							echo $checkoutOpenFormHtml;
							$displayCheckoutForm = false;
						endif;  
						?>
						<div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							<h3 class="step-name"><?php echo $step['text']; ?></h3>
                            <?php
							if ($checkout->get_checkout_fields()) :
								do_action('woocommerce_checkout_shipping');
								//do_action('woocommerce_checkout_after_customer_details');
							endif;
							?>
						</div>
                        </div><!--#customer_details-->
						<?php

						break;

					//Order step
					case 'order' :
						if ($displayCheckoutForm === true) :
							echo $checkoutOpenFormHtml;
							$displayCheckoutForm = false;
						endif; 
						
						if ($displayOrderReview == true) :
							echo $checkoutOpenOrderReviewHtml;
							
							$displayOrderReview 		= false;
							$checkoutCloseOrderReview 	= true;
						endif;
						?>
						<div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							<h3 class="step-name"><?php echo $step['text']; ?></h3>
                            
                            <?php
                            if ($displayOrderReview == true) :
                                //do_action('woocommerce_checkout_before_order_review');
                            endif;
                            
                            do_action('woocommerce_order_review'); ?>
						</div>    
						<?php

						break;

					//Billing & shipping step
					case 'billing_shipping' :
						if ($displayCheckoutForm === true) :
							echo $checkoutOpenFormHtml;
							$displayCheckoutForm = false;
						endif;  
						?>
                        <div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
                            <h3 class="step-name"><?php echo $step['text']; ?></h3>
                            <?php
                            if ($checkout->get_checkout_fields()) :
                                //do_action('woocommerce_checkout_before_customer_details');
                                ?>
                                <div id="customer_details">
                                    <?php    
                                    do_action('woocommerce_checkout_billing');   
                                    do_action('woocommerce_checkout_shipping');
                                    ?>
                                </div><!--#customer_details-->
                                <?php
                                //do_action('woocommerce_checkout_after_customer_details');
                            endif;						
                            ?>
                        </div><!--.argmc-form-steps-->
						<?php

						break;

					//Order & payment step
					case 'order_payment' :  
						                     
						echo $checkoutOpenOrderReviewHtml;
						
						$checkoutCloseOrderReview = true;
						?>
						<div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							  
                            <h3 class="step-name"><?php echo $step['text']; ?></h3>
                            
                            <?php
                            
                            //do_action('woocommerce_checkout_before_order_review');
							
							if ($showOrder) :
								do_action('woocommerce_order_review');
							endif;

							do_action('woocommerce_checkout_payment');
							
							if (!empty($options['show_customer_details_review'])) :
								do_action('arg_checkout_customer_details');
							endif;									
							?>
						 
						</div>    
						<?php

						break;

					//Payment step	
					case 'payment' :
						
						if ($displayOrderReview == true) :							
							echo $checkoutOpenOrderReviewHtml;
							
							$displayOrderReview 		= false;
							$checkoutCloseOrderReview 	= true;
						endif;                                
						?>
						<div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							<h3 class="step-name"><?php echo $step['text']; ?></h3>
                            <?php
                            
                            if ($displayOrderReview == true) :
                                //do_action('woocommerce_checkout_before_order_review');
                            endif;
                            
							do_action('woocommerce_checkout_payment');
							
							if (!empty($options['show_customer_details_review'])) :
								do_action('arg_checkout_customer_details');
							endif;
							?>
						</div>       
						<?php
						
						break;
					
					//Order review step
					case 'order_review' :  
						?>
						<div data-step="<?php echo $step['data']; ?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							<h3 class="step-name"><?php echo $step['text']; ?></h3>
                            <?php 
											 
							if ($showOrder === false && !empty($options['show_order_review_table'])) :
								do_action('woocommerce_order_review');
							endif;
							?>
							
							<h2><?php _e('Order Review', 'argMC'); ?></h2>

							<div class="argmc-review-order-wrapper"></div>
							
							<?php do_action('arg_checkout_customer_details'); ?>
													
						</div>    
						<?php
						
						if ($checkoutCloseOrderReview == true) :
							$checkoutCloseOrderReview = false;
							?>
							</div><!--order_review-->
							<?php
							//do_action('woocommerce_checkout_after_order_review'); 								
						endif;

						break;
						
					//Custom step    
					default:
						if ($checkoutCloseOrderReview == true) :
							$checkoutCloseOrderReview = false;
                            
                            //do_action('woocommerce_checkout_after_order_review');
							?>
							</div><!--order_review-->
							<?php							 							
						endif;
						
						?>
						<div data-step="<?php echo !empty($step['data']) ? $step['data'] : ''?>" class="argmc-form-steps argmc-form-step-<?php echo $i; ?><?php echo $i == 1 ? ' first current' : ''; ?><?php echo !empty($step['class']) ? ' ' . $step['class'] : ''?>">
							<?php
                            if (!empty($step['text'])) :
                                ?>
                                <h3 class="step-name"><?php echo $step['text']; ?></h3>
                                <?php
                            endif;
                            
                            do_action('arg-mc-checkout-step', $template);
                            ?>
						</div>    
						<?php
				endswitch;
				$i++;
			endforeach;
			
			if ($checkoutCloseOrderReview == true) :
				?>
				</div><!--order_review-->
				<?php
				//do_action('woocommerce_checkout_after_order_review'); 								
			endif;					
			?>
			</form><!--checkout argmc-form-->
		</div><!--argmc-form-steps-wrapper-->
		<?php
	endif;

	?>
	<div class="argmc-nav">
		<div class="argmc-nav-text"><?php echo $options['footer_text']; ?></div>
		<div class="argmc-nav-buttons">
			<button id="argmc-prev" class="button argmc-previous hide-button" type="button"><span><?php echo $options['btn_prev_text']; ?></span></button>
			<button id="argmc-next" class="button argmc-next<?php echo $showLogin && $firstStep == 'login' ? ' hide-button' : ' show-button'; ?>" type="button"><span><?php echo $options['btn_next_text']; ?></span></button>
			<?php
			if ($showLogin) :
				?>
				<button id="argmc-skip-login" class="button argmc-next<?php echo $firstStep != 'login' ? ' hide-button' : ' show-button'; ?>" type="button"><span><?php echo $options['btn_skip_login_text']; ?></span></button>				
				<?php
			endif;
			?>
			<button id="argmc-submit" class="button argmc-submit hide-button" type="submit"><span><?php echo $options['btn_submit_text']; ?></span></button>
		</div>
	</div><!--argmc-nav-->
	<?php //do_action('woocommerce_after_checkout_form', $checkout); ?>
	<span class="tab-completed-icon preload-icon"></span>
</div><!--argmc-wrapper-->