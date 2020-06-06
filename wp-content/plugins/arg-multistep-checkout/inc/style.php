<?php
/**
 * Table of Contents
 *
 *Wizard Styles
	*Wizard Color
	*Wizard Accent Color
	*Wizard Border Color
	*Wizard Max Width
	*Wizard Secondary Font Family
	*Wizard Secondary Font Weight
 *Tab Styles
	*Tab Text Color
	*Tab Background Color
	*Hovered/Completed Tab Text Color
	*Hovered/Completed Tab Background Color
 *Overwrite WooCommerce Styles
	*Woo Text Color
	*Woo Headings/Label Color
	*Woo Input Border/Background Color
	*Woo Button Background Color
	*Woo Button Background Color on Login And Coupon Forms
	*Woo Inherit Accent Color from Wizard  
 *Accent Color > 767px
 *Tab Number Text Color  > 767px
 *Tab Number Background Color > 767px
 *Tab Text Color > 767px
 *Tab Completed - Number Background Color  > 767px
 *
 */

 

if (!function_exists ('argMCStyles')) {
    function argMCStyles() {
        global $argOptions;
		
		$options = $argOptions;

        ?>			
        <style type="text/css" id="arg-custom-styles">

            /**********************************************************************************/
            /* Wizard Styles  ****************************************************************/
            /**********************************************************************************/

            /*Wizard Color*/
            .argmc-wrapper .argmc-nav-text {
				color: <?php echo $options['wizard_color']; ?>;	
            }



            /*Wizard Accent Color*/
            .argmc-wrapper .argmc-nav-text a 	{
                color: <?php echo $options['accent_color']; ?>;
            }



             /*Wizard Border Color*/
            .argmc-wrapper .argmc-nav-text,
            .argmc-wrapper .argmc-nav-buttons {
                border-color: <?php echo $options['border_color']; ?>;
            }
			
			/*Wizard Text Errors Color*/
			.woocommerce-checkout .woocommerce .argmc-wrapper form label.error {
                color: <?php echo $options['wizard_text_errors_color']; ?> !important;
            }
			
			
			/*Change Wizard Buttons Styles*/
			<?php
			if (!empty($options['overwrite_wizard_buttons'])) :
				?>
			
				/*Wizard Buttons Text Color*/
				.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-nav-buttons .button span {
					color: <?php echo $options['wizard_button_text_color']; ?>;
					transition: opacity 0.3s;
				}
				
				
				/*Wizard Buttons Text Color on Hover*/
				.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-nav-buttons .button:hover span {
					opacity: <?php echo $options['wizard_button_text_opacity']; ?>;
				}
				
				
				/*Wizard Next Button Bkg Color*/
				.woocommerce-checkout .woocommerce .argmc-wrapper #argmc-next,
				.woocommerce-checkout .woocommerce .argmc-wrapper #argmc-skip-login {
					background: <?php echo $options['next_button_bkg']; ?> !important;
					border-color: <?php echo $options['next_button_bkg']; ?> !important;
					outline-color: <?php echo $options['next_button_bkg']; ?> !important;
				}
				
				
				/*Wizard Previous Button Bkg Color*/
				.woocommerce-checkout .woocommerce .argmc-wrapper #argmc-prev {
					background: <?php echo $options['prev_button_bkg']; ?> !important;
					border-color: <?php echo $options['prev_button_bkg']; ?> !important;
					outline-color: <?php echo $options['prev_button_bkg']; ?> !important;
				}
				
				
				/*Wizard Place Order Button Bkg Color*/
				.woocommerce-checkout .woocommerce .argmc-wrapper #argmc-submit {
					background: <?php echo $options['place_order_button_bkg']; ?> !important;
					border-color: <?php echo $options['place_order_button_bkg']; ?> !important;
					outline-color: <?php echo $options['place_order_button_bkg']; ?> !important;
				}
				<?php
			endif;
			?>


            /*Wizard Max Widht*/
            .argmc-wrapper {
				max-width: <?php echo $options['wizard_max_width']; ?>;
            }


            /*Wizard Secondary Font Family*/
            <?php
            if (!empty($options['secondary_font'])) :
                ?>
                .argmc-wrapper .argmc-tabs-list {
                    font-family: <?php echo $options['secondary_font']; ?>;
                }
                <?php
            endif;
            ?>


            /*Wizard Secondary Font Weight*/
            .argmc-wrapper .argmc-tabs-list,
			.argmc-wrapper .login-headings {
                font-weight: <?php echo $options['secondary_font_weight']; ?>;
            }



            /**********************************************************************************/
            /* Tab Styles  ********************************************************************/
            /**********************************************************************************/
			
			
            <?php
			$tabsLayout = 'tabs-square';
			
            if (!empty($options['tabs_layout']) && in_array($options['tabs_layout'], array('tabs-square', 'tabs-arrow', 'tabs-arrow-alt', 'tabs-progress-bar', 'tabs-outline'))) {
				$tabsLayout = $options['tabs_layout'];
			}
			
            if ($tabsLayout == 'tabs-square') {
                ?>

				/*Tab Square Number Color*/
				
				.argmc-wrapper .argmc-tab-number {
					color: <?php echo $options['number_text_color']; ?>;
				}
				
                /*Tab Square Text Color*/

                .argmc-wrapper .argmc-tab-item {
                    color: <?php echo $options['tab_text_color']; ?>;
                }



                /*Tab Square Background Color*/

                .argmc-wrapper .argmc-tab-item {
                    background: <?php echo $options['tab_bkg_color']; ?>;
                }



                /*Tab Square Border Color*/

                .argmc-wrapper .argmc-tab-item {
                    border-bottom-color: <?php echo $options['tab_border_bottom_color']; ?>;
                    border-left-color: <?php echo $options['tab_border_left_color']; ?>;
                }

                .argmc-wrapper .argmc-tab-item.current::before,
                .argmc-wrapper .argmc-tab-item.completed::before {
                    border-bottom: 3px solid <?php echo $options['tab_border_bottom_color_hover']; ?>;
                }

                .argmc-wrapper .argmc-tab-item.current::after {
                    border-color: <?php echo $options['tab_border_bottom_color_hover']; ?> transparent transparent;
                }



                /*Tab Square Current/Completed Icon/Number Color*/
				.argmc-wrapper .argmc-tab-item.current .argmc-tab-number {
					color: <?php echo $options['tab_text_color_hover']; ?>;
				}
				
				.argmc-wrapper .argmc-tab-item.completed .argmc-tab-number {
					color: <?php echo $options['tab_number_color_hover']; ?>;
				}	


                /*Current/Hovered/Completed Tab Square Text Color*/

                .argmc-wrapper .argmc-tab-item.current,
                .argmc-wrapper .argmc-tab-item.selected,
                .argmc-wrapper .argmc-tab-item.completed {
                   color: <?php echo $options['tab_text_color_hover']; ?>;
                }


                /*Current/Hovered/Completed Tab Square Background Color*/

                .argmc-wrapper .argmc-tab-item.current,
                .argmc-wrapper .argmc-tab-item.completed {
                    background: <?php echo $options['tab_bkg_color_hover']; ?>;
                }

				
				<?php
				if (!empty($options['show_number_checkmark'])) :
					?>
					.argmc-wrapper .argmc-tab-item.current .number-text {
						font-size: 17px;
						line-height: 8px;
					}
					<?php
				else :
					?>
					.argmc-wrapper .argmc-tab-item.completed .number-text {
						display: none;
					}
					
					.argmc-wrapper .argmc-tab-item.completed .tab-completed-icon {
						display: inline-block;
					}
					
					<?php
				endif;
				?>
				
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-item .number-text {
					top: <?php echo $options['tab_adjust_number_position']; ?>;
				}
				
				/*Adjust Checkmark Position(vertical alignment)*/
				.argmc-wrapper .tab-completed-icon {
					top: <?php echo $options['tab_adjust_checkmark_position']; ?>;
				}
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-text span {
					top: <?php echo $options['tab_adjust_text_position']; ?>;
				}
				

                <?php
            } elseif ($tabsLayout == 'tabs-arrow') {
                ?>
				
				/*Tab Arrow Number Color*/
				
				.argmc-wrapper .argmc-tab-number {
					color: <?php echo $options['number_text_color']; ?>;
				}
				
				
				
                /*Tab Arrow Text Color*/

                .argmc-wrapper .argmc-tab-item {
                    color: <?php echo $options['tab_text_color']; ?>;
                }



                /*Tab Arrow Background Color*/

                .argmc-wrapper .argmc-tab-item {
                    background: <?php echo $options['tab_bkg_color']; ?>;
                }

                .argmc-wrapper .argmc-tab-item-outer:after {
                    border-color: transparent transparent transparent <?php echo $options['tab_bkg_color']; ?>;
                }



                /*Tab Arrow Border Color*/

                .argmc-wrapper .argmc-tab-item {
                    border-bottom-color: <?php echo $options['tab_border_bottom_color']; ?>;
                    border-left-color: <?php echo $options['tab_border_left_color']; ?>;
                }

                .argmc-wrapper .argmc-tab-item.current,
                .argmc-wrapper .argmc-tab-item.completed {
                    border-bottom-color: <?php echo $options['tab_border_bottom_color_hover']; ?>;
                }



                /*Tab Arrow Current/Completed Icon/Number Color*/
				
				.argmc-wrapper .argmc-tab-item.current .argmc-tab-number {
					color: <?php echo $options['tab_text_color_hover']; ?>;
				}
				
				.argmc-wrapper .argmc-tab-item.completed .argmc-tab-number {
					color: <?php echo $options['tab_number_color_hover']; ?>;
				}



                /*Current/Hovered/Completed Tab Arrow Text Color*/

                .argmc-wrapper .argmc-tab-item.current,
                .argmc-wrapper .argmc-tab-item.selected,
                .argmc-wrapper .argmc-tab-item.completed {
                    color: <?php echo $options['tab_text_color_hover']; ?>;
                }



                /*Current/Hovered/Completed Tab Arrow Background/Border Color*/

                .argmc-wrapper .argmc-tab-item.current,
                .argmc-wrapper .argmc-tab-item.completed {
                     background: <?php echo $options['tab_bkg_color_hover']; ?>;
                }

                .argmc-wrapper .argmc-tab-item.current .argmc-tab-item-outer:after,
                .argmc-wrapper .argmc-tab-item.completed .argmc-tab-item-outer:after {
                     border-color: transparent transparent transparent <?php echo $options['tab_bkg_color_hover']; ?>;
                }



                /*Before Tab Arrow Color*/
                .argmc-wrapper .argmc-tab-item-outer::before {
                     border-color: transparent transparent transparent <?php echo $options['tab_before_arrow_color']; ?>;
                }
				
				
				<?php
				if (empty($options['show_number_checkmark'])) :
				?>
					.argmc-wrapper .argmc-tab-item.completed .number-text {
						display: none;
					}
					
					.argmc-wrapper .argmc-tab-item.completed .tab-completed-icon {
						display: inline-block;
						margin: 0;
					}
					
					<?php
				endif;
				?>
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-item .number-text {
					top: <?php echo $options['tab_adjust_number_position']; ?>;
				}
				
				/*Adjust Checkmark Position(vertical alignment)*/
				.argmc-wrapper .tab-completed-icon {
					top: <?php echo $options['tab_adjust_checkmark_position']; ?>;
				}
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-text span {
					top: <?php echo $options['tab_adjust_text_position']; ?>;
				}
				

                <?php
            } elseif ($tabsLayout == 'tabs-arrow-alt') {
                ?>
				
				/*Tab Arrow Alt Text Color*/

                .argmc-wrapper .argmc-tab-item {
                    color: <?php echo $options['tab_arrow_alt_text_color']; ?>;
                }
				
				
				/*Tab Arrow Alt Number Color*/
				
				.argmc-wrapper .argmc-tab-number {
					color: <?php echo $options['tab_arrow_alt_text_color']; ?>;
				}


                /*Tab Arrow Alt Background Color*/

                .argmc-wrapper .argmc-tab-item {
                    background: <?php echo $options['tab_arrow_alt_bkg_color']; ?>;
                }

                .argmc-wrapper .argmc-tab-item-outer:after {
                    border-color: transparent transparent transparent <?php echo $options['tab_arrow_alt_bkg_color']; ?>;
                }



                /*Tab Arrow Alt Border Color*/

                .argmc-wrapper .argmc-tab-item {
                    border-bottom-color: <?php echo $options['tab_arrow_alt_border_bottom_color']; ?>;
                }
				


                /*Visited Tab Arrow Alt Icon/Number Color*/
				
				.argmc-wrapper .argmc-tab-item.visited .argmc-tab-number {
					color: <?php echo $options['tab_arrow_alt_text_color_hover']; ?>;
				}

				

                /*Visited / On Hover Tab Arrow Alt Text Color*/

                .argmc-wrapper .argmc-tab-item.visited {
                    color: <?php echo $options['tab_arrow_alt_text_color_hover']; ?>;
                }
				
				
				
				/*Visited Tab Arrow Alt Background/Border Color*/
				
				.argmc-wrapper .argmc-tab-item.visited {
					 background: <?php echo $options['tab_arrow_alt_completed_bkg_color']; ?>;
				}

				.argmc-wrapper .argmc-tab-item.visited .argmc-tab-item-outer:after {
					 border-color: transparent transparent transparent <?php echo $options['tab_arrow_alt_completed_bkg_color']; ?>;
				}

				
				
				/*Visited Tab Arrow Alt Background/Border Color*/

				.argmc-wrapper .argmc-tab-item.current.visited {
					 background: <?php echo $options['tab_arrow_alt_bkg_color_hover']; ?>;
				}

				.argmc-wrapper .argmc-tab-item.current.visited .argmc-tab-item-outer:after {
					 border-color: transparent transparent transparent <?php echo $options['tab_arrow_alt_bkg_color_hover']; ?>;
				}
				
				
				
				/*Visited Tab Arrow Alt Border Bottom Color*/
				
                .argmc-wrapper .argmc-tab-item.visited {
                    border-bottom-color: <?php echo $options['tab_arrow_alt_border_bottom_color_hover']; ?>;
                }
				
				
				
                /*Before Tab Arrow Alt Color*/
				
                .argmc-wrapper .argmc-tab-item-outer::before {
                     border-color: transparent transparent transparent <?php echo $options['tab_arrow_alt_before_arrow_color']; ?>;
                }
				
				<?php
				if (empty($options['tab_arrow_alt_show_number_checkmark'])) :
				?>
					.argmc-wrapper .argmc-tab-item.completed .number-text {
						display: none;
					}
					
					.argmc-wrapper .argmc-tab-item.completed .tab-completed-icon {
						display: inline-block;
						margin: 0;
					}
					
					<?php
				endif;
				?>
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-item .number-text {
					top: <?php echo $options['tab_arrow_alt_adjust_number_position']; ?>;
				}
				
				/*Adjust Checkmark Position(vertical alignment)*/
				.argmc-wrapper .tab-completed-icon {
					top: <?php echo $options['tab_arrow_alt_adjust_checkmark_position']; ?>;
				}
				
				/*Adjust Text Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-text span {
					top: <?php echo $options['tab_arrow_alt_adjust_text_position']; ?>;
				}

                <?php			
			} elseif ($tabsLayout == 'tabs-progress-bar') {
			    ?>
	
				/*Tab Progress Bar Text Color*/

                .argmc-wrapper .argmc-tab-item {
                    color: <?php echo $options['tab_progress_bar_text_color']; ?>;
                }
	
	
				/*Tab Progress Bar Number Color*/
				
				.argmc-wrapper .argmc-tab-item .argmc-tab-number {
					color: <?php echo $options['tab_progress_bar_number_text_color']; ?>;
				}

				
				/*Tab Progress Bar Number Background Color*/
				
				.argmc-wrapper .argmc-tab-item .argmc-tab-number {
					background: <?php echo $options['tab_progress_bar_number_bkg_color']; ?>;
				}
				
				
				/*Tab Progress Bar Background Color*/
					
				.argmc-wrapper .argmc-tab-item {
                    background: <?php echo $options['tab_progress_bar_bkg_color']; ?>;
				}
				
				
				/*Tab Progress Bar Border Color*/
					
				.argmc-wrapper .argmc-tab-item:before {
                    border-bottom-color: <?php echo $options['tab_progress_bar_border_bottom_color']; ?>;
				}
				
                
                /*Completed Tab Progress Bar*/
                
                .argmc-wrapper .argmc-tab-item.completed {
                    color: <?php echo $options['tab_progress_bar_completed_text_color']; ?>;
                }
                
                .argmc-wrapper .argmc-tab-item.completed .argmc-tab-number {
                    color: <?php echo $options['tab_progress_bar_completed_number_color']; ?>;
                }
                
                .argmc-wrapper .argmc-tab-item.completed .argmc-tab-number {
                    background: <?php echo $options['tab_progress_bar_completed_number_bkg_color']; ?>;
                }
				
                
				/*Current / Completed Tab Progress Bar Border Color*/
					
				.argmc-wrapper .argmc-tab-item.visited:before {
                    border-bottom-color: <?php echo $options['tab_progress_bar_border_bottom_color_hover']; ?>;
				}
				
				
				/*Current/Hovered/Completed Tab Progress Bar Text Color*/

				.argmc-wrapper .argmc-tab-item.current,
				.argmc-wrapper .argmc-tab-item.visited:hover,
				.argmc-wrapper .argmc-tab-item.last.current + .argmc-tab-item:hover {
                    color: <?php echo $options['tab_progress_bar_text_color_hover']; ?>;
                }
				
				
				/*Current Tab Progress Bar Number Color*/

				.argmc-wrapper .argmc-tab-item.current .argmc-tab-number,
				.argmc-tab-item.visited:hover .argmc-tab-number,
				.argmc-wrapper .argmc-tab-item.last.current + .argmc-tab-item:hover .argmc-tab-number {
                    color: <?php echo $options['tab_progress_bar_number_color_hover']; ?>;
                }
				
				
				/*Current Tab Progress Bar Background Color*/

				.argmc-wrapper .argmc-tab-item.current .argmc-tab-number,
				.argmc-tab-item.visited:hover .argmc-tab-number,
				.argmc-wrapper .argmc-tab-item.last.current + .argmc-tab-item:hover .argmc-tab-number {
                    background:  <?php echo $options['tab_progress_bar_number_bkg_color_hover']; ?>;
                }				
      
				
				<?php
				if (empty($options['tab_progress_bar_show_number_checkmark'])) :
				?>
					.argmc-wrapper .argmc-tab-item.completed .number-text {
						display: none;
					}
					
					.argmc-wrapper .argmc-tab-item.completed .tab-completed-icon {
						display: inline-block;
						margin: 0;
					}
					
					<?php
				endif;
				?>
				
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-item .number-text {
					top: <?php echo $options['tab_progress_bar_adjust_number_position']; ?>;
				}
				
				/*Adjust Checkmark Position(vertical alignment)*/
				.argmc-wrapper .tab-completed-icon {
					top: <?php echo $options['tab_progress_bar_adjust_checkmark_position']; ?>;
				}
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-text span {
					top: <?php echo $options['tab_progress_bar_adjust_text_position']; ?>;
				}
				
			    <?php			
			} elseif ($tabsLayout == 'tabs-outline') {
			    ?>	
				
				/*Tab Vertical Text Color*/
                .argmc-wrapper .argmc-tab-item,
				.argmc-wrapper .argmc-tab-item.visited {
                    color: <?php echo $options['tab_outline_text_color']; ?>;
                }
			
				
				/*Tab Vertical Border Color*/
				.argmc-wrapper.orientation-vertical .argmc-tab-item:first-child .argmc-tab-item-outer {
					border-top-color: <?php echo $options['tab_outline_border_color']; ?>;
				}
				
				.argmc-wrapper.orientation-vertical .argmc-tab-item .argmc-tab-item-outer {
					border-bottom-color: <?php echo $options['tab_outline_border_color']; ?>;
					border-right-color: <?php echo $options['tab_outline_border_color']; ?>;
				}
                
                .argmc-wrapper.orientation-horizontal::after {
                    border-bottom-color: <?php echo $options['tab_outline_border_color']; ?>;
                }
				
				.argmc-wrapper .argmc-tab-item .argmc-tab-number {
					border-color: <?php echo $options['tab_outline_border_color']; ?>;
				}
				
				
				/*Tab Vertical Number Color*/
				.argmc-wrapper .argmc-tab-item .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_text_color']; ?>;
				}
				
				
				/*Last Tab Vertical Text Color*/
				.argmc-wrapper.orientation-vertical .argmc-tab-item.last.visited  {
					color: <?php echo $options['tab_outline_text_color_hover']; ?>;
				}
				
				
				/*Current Tab Vertical Background Color*/
				.argmc-wrapper.orientation-vertical .argmc-tab-item.current.visited .argmc-tab-item-outer,
				.argmc-wrapper.orientation-vertical .argmc-tab-item.visited:hover .argmc-tab-item-outer,
				.argmc-wrapper.orientation-vertical .argmc-tab-item.current.last + .argmc-tab-item:hover .argmc-tab-item-outer {
					background: <?php echo $options['tab_outline_bkg_color_hover']; ?>;
				}
				
				
				/*Current Tab Vertical Left Border Color*/
				.argmc-wrapper .argmc-tab-item.current.visited::after {
					background: <?php echo $options['tab_outline_current_border_left']; ?>;
				}
				
				
				/*Current Tab Vertical Number Color/Bkg Color*/
				.argmc-wrapper.orientation-vertical .argmc-tab-item.last .argmc-tab-number,
				.argmc-wrapper.orientation-vertical .argmc-tab-item.current.last + .argmc-tab-item:hover .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_bkg_color_hover']; ?>;
				}
				
				
				/*Visited Tab Vertical Number Color/Bkg Color*/
				.argmc-wrapper .argmc-tab-item.completed .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_bkg_color_completed']; ?>;
				}

                
                /*Current Tab Vertical Number Color/Bkg Color*/
                .argmc-wrapper.orientation-horizontal .argmc-tab-item.current.completed .argmc-tab-number,
				.argmc-wrapper.orientation-horizontal .argmc-tab-item.current .argmc-tab-number,
                .argmc-wrapper.orientation-horizontal .argmc-tab-item.visited:hover .argmc-tab-number,
				.argmc-wrapper.orientation-horizontal .argmc-tab-item.current.last + .argmc-tab-item:hover .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_color_hover']; ?>;
					background-color: <?php echo $options['tab_outline_number_bkg_color_hover']; ?>;
				}
				
                .argmc-wrapper.orientation-horizontal .argmc-tab-item.current,
                .argmc-wrapper.orientation-horizontal .argmc-tab-item.visited:hover,
                .argmc-wrapper.orientation-horizontal .argmc-tab-item.current.last + .argmc-tab-item:hover {
					color: <?php echo $options['tab_outline_text_color_hover']; ?>;
				}
				
                /*Completed Tab*/
                .argmc-wrapper .argmc-tab-item.completed  {
					color: <?php echo $options['tab_outline_completed_text_color']; ?>;
				}
                
				.argmc-wrapper.orientation-horizontal .argmc-tab-item.completed .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_color_completed']; ?>;
					background: <?php echo $options['tab_outline_number_bkg_color_completed']; ?>;
				}
				
				<?php
				if (empty($options['tab_outline_show_number_checkmark'])) :
				?>
					.argmc-wrapper .argmc-tab-item.completed .number-text {
						display: none;
					}
					
					.argmc-wrapper .argmc-tab-item.completed .tab-completed-icon {
						display: inline-block;
					}
					
					<?php
				endif;
				?>
				
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-item .number-text {
					top: <?php echo $options['tab_outline_adjust_number_position']; ?>;
				}
				
				/*Adjust Checkmark Position(vertical alignment)*/
				.argmc-wrapper .tab-completed-icon {
					top: <?php echo $options['tab_outline_adjust_checkmark_position']; ?>;
				}
				
				/*Adjust Number Position(vertical alignment)*/
				.argmc-wrapper .argmc-tab-text span {
					top: <?php echo $options['tab_outline_adjust_text_position']; ?>;
				}
			
			
			    <?php
			}
            ?>

            /*Woocommerce Checkout Review Order Table - Show Product Image*/
            <?php
            if (!empty($options['show_product_image'])) :
                ?>
				
				.woocommerce-checkout .woocommerce .argmc-wrapper .cart_item .product-name,
				.woocommerce-checkout .woocommerce .argmc-wrapper .cart_item .product-total {
					vertical-align: middle;
				}
				
				
				.woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-checkout-review-order-table thead td:nth-child(2n+1),
				.woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-checkout-review-order-table thead th:nth-child(2n+1),
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table thead td:nth-child(2n+1),
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table thead th:nth-child(2n+1) {
					padding-top: 11px;
					padding-bottom: 11px;
				}
				
				.argmc-wrapper .arg-product-image,
				.argmc-wrapper .arg-product-desc {
					display: block;
				}
				
                .argmc-wrapper .woocommerce-checkout-review-order-table .product-name img,
				.argmc-wrapper table.review-table .product-name img  {
                    max-width: 80px;
                    margin: 0 14px 0 0;
					padding: 0;
					
					position: relative;
					top: 0;
					left: 0;
					right: auto;
					bottom: auto;
                }
				
				.argmc-wrapper .arg-product-desc {
					line-height: 1.4;
					margin-top: 15px;
					padding-right: 30px;
					
				}
				
                .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-checkout-review-order-table tbody .product-name,
                .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-checkout-review-order-table tbody .product-total,
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table tbody .product-name,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.review-table tbody .product-total {
                    padding-top: 18px;
					padding-bottom: 18px;
                }
                
                .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-checkout-review-order-table tfoot tr,
                .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-checkout-review-order-table tfoot td,
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table tfoot tr,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.review-table tfoot td {
                    padding-top: 14px;
					padding-bottom: 14px;
                }
				
				.arg-product-qwt {
					display: inline-block;
				}
				
                <?php
            endif;

            if (!empty($options['overwrite_woo_styles'])) :
                ?>

                /**********************************************************************************/
                /* Overwrite Woocommerce Styles  **************************************************/
                /**********************************************************************************/

                /*Woo Text Color*/

                .woocommerce-checkout .woocommerce .argmc-wrapper .argmc-form-steps,
                .woocommerce-checkout .woocommerce .argmc-wrapper #payment div.payment_box,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.shop_table th,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.shop_table td,
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table th,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.review-table td {
                    color: <?php echo $options['woo_text_color']; ?>;
                }


                /*Woo Headings/Label Color*/

                .woocommerce-checkout .woocommerce .argmc-wrapper h2,
                .woocommerce-checkout .woocommerce .argmc-wrapper h3,
                .woocommerce-checkout .woocommerce .argmc-wrapper label,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-invalid label,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.shop_table thead th,
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table thead th,
                .woocommerce-checkout .woocommerce .argmc-wrapper input.input-radio[type="radio"] + label {
                    color: <?php echo $options['woo_label_color']; ?>;
                }


                /*Woo Input Border/Background Color*/

                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="text"],
				.woocommerce-checkout .woocommerce .argmc-wrapper input[type="number"],
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="password"],
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="search"],
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="email"],
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="url"],
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="tel"],
                .woocommerce-checkout .woocommerce .argmc-wrapper  select,
                .woocommerce-checkout .woocommerce .argmc-wrapper textarea,
                .woocommerce-checkout .select2-container .select2-choice,
				.woocommerce-checkout .woocommerce .argmc-wrapper .select2-container .select2-selection	{
                    border-color: <?php echo $options['woo_input_border_color']; ?>;
                    background: <?php echo $options['woo_input_bkg_color']; ?>;
                    border-radius: <?php echo $options['woo_field_border_radius']; ?>;
                }

				.woocommerce-checkout .woocommerce .argmc-wrapper .select2-container .select2-choice,
				.woocommerce-checkout .woocommerce .argmc-wrapper .select2-container .select2-selection {
					border-color: <?php echo $options['woo_input_border_color']; ?>;
                    background: <?php echo $options['woo_input_bkg_color']; ?>;
					border-radius: <?php echo $options['woo_field_border_radius']; ?>;
				}

                /*Woo Input Background Color on Hover*/
                <?php
                /*
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="text"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="number"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="password"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="search"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="email"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="url"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="tel"]:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper select:focus,
                .woocommerce-checkout .woocommerce .argmc-wrapper textarea:focus,
                .woocommerce-checkout .select2-container .select2-choice:focus {
                    background: <?php echo $options['woo_input_bkg_color_hover']; ?>;
                }
                */
                ?>


                /*Woo Button Background Color*/

                .woocommerce-checkout .woocommerce .argmc-wrapper #respond input#submit,
                .woocommerce-checkout .woocommerce .argmc-wrapper a.button,
                .woocommerce-checkout .woocommerce .argmc-wrapper button.button,
                .woocommerce-checkout .woocommerce .argmc-wrapper input.button,
                .woocommerce-checkout .woocommerce .argmc-wrapper #respond input#submit:hover,
                .woocommerce-checkout .woocommerce .argmc-wrapper a.button:hover,
                .woocommerce-checkout .woocommerce .argmc-wrapper button.button:hover,
                .woocommerce-checkout .woocommerce .argmc-wrapper input.button:hover {
                    background: <?php echo $options['woo_button_bkg_color']; ?>;
                    border-radius: <?php echo $options['woo_field_border_radius']; ?>;
                }


                /*Woo Button Background Color on Login And Coupon Forms*/

                .woocommerce-checkout .woocommerce .argmc-wrapper .login .button,
                .woocommerce-checkout .woocommerce .argmc-wrapper form.checkout_coupon .button,
				.woocommerce-checkout .woocommerce .argmc-wrapper .register .button {
                    background: <?php echo $options['woo_button_bkg_color_login']; ?> !important;
                }


				/*Woo Inherit Accent Color from Wizard*/

                .woocommerce-checkout .woocommerce .argmc-wrapper #payment .payment_method_paypal .about_paypal,
                .woocommerce-checkout .woocommerce .terms.wc-terms-and-conditions a,
                .woocommerce-checkout .woocommerce .argmc-wrapper .login .lost_password a,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row .required,
                .woocommerce-checkout .woocommerce .argmc-wrapper form.login label[for="rememberme"]:after,
                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="checkbox"] + label:after {
                    color:  <?php echo $options['accent_color']; ?>;
                }


                .woocommerce-checkout .woocommerce .argmc-wrapper input[type="radio"].input-radio + label:after,
                .woocommerce-checkout .woocommerce .argmc-wrapper ul#shipping_method li input[type="radio"].shipping_method + label:after,
                .woocommerce-checkout .select2-results .select2-highlighted,
				.woocommerce-checkout .select2-results .select2-results__option--highlighted {
                    background: <?php echo $options['accent_color']; ?>;
                }

                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-invalid .select2-container,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-invalid select,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-invalid input,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .has-error input,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .has-error .select2-choice,
				.woocommerce-checkout .woocommerce .argmc-wrapper form .has-error .select2-selection {
                    border-color:  <?php echo $options['woo_invalid_required_field_border']; ?> !important;
                    background: <?php echo $options['woo_invalid_required_field_bkg']; ?>;
                }

                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-validated .select2-container,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-validated input.input-text,
                .woocommerce-checkout .woocommerce .argmc-wrapper form .form-row.woocommerce-validated select {
                    border-color:  <?php echo $options['woo_validated_field_border']; ?>;
                }

                <?php
                if (!empty($options['secondary_font'])) :
                    ?>
                    .woocommerce-checkout .woocommerce .argmc-wrapper label,
                    .woocommerce-checkout .woocommerce .argmc-wrapper .login .button,
					.woocommerce-checkout .woocommerce .argmc-wrapper .register .button,
                    .woocommerce-checkout .woocommerce .argmc-wrapper form.checkout_coupon .button,
                    .woocommerce-checkout .woocommerce .argmc-wrapper .argmc-nav-buttons .button,
                    .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-billing-fields h3,
                    .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-shipping-fields h3,
					.argmc-login-tabs h3,
                    .woocommerce-checkout .woocommerce .argmc-wrapper table.shop_table thead th,
					.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table thead th {
                        font-family: <?php echo $options['secondary_font']; ?>;
                    }
                    <?php
                endif;
                ?>

                .woocommerce-checkout .woocommerce .argmc-wrapper .wc_payment_method input.input-radio[type="radio"] + label,
                .woocommerce-checkout .woocommerce .argmc-wrapper .login .button,
				.woocommerce-checkout .woocommerce .argmc-wrapper .register .button,
                .woocommerce-checkout .woocommerce .argmc-wrapper form.checkout_coupon .button,
				.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-login-step .tab-item, 
                .woocommerce-checkout .woocommerce .argmc-wrapper .argmc-nav-buttons .button,
                .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-billing-fields h3,
                .woocommerce-checkout .woocommerce .argmc-wrapper .woocommerce-shipping-fields h3,
                .woocommerce-checkout .woocommerce .argmc-wrapper table.shop_table thead th,
				.woocommerce-checkout .woocommerce .argmc-wrapper table.review-table thead th,
                .woocommerce-checkout .woocommerce .argmc-wrapper #ship-to-different-address label {
                    font-weight: <?php echo $options['secondary_font_weight']; ?>;
                }				

                <?php
            endif;
            ?>
			
			
			/*customer order review*/
			.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-customer-review h2,
			.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-customer-review h3 {
				font-family: <?php echo $options['secondary_font']; ?>;
			}
		
			.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-customer-review h2,
			.woocommerce-checkout .woocommerce .argmc-wrapper .argmc-customer-review h3,
			.argmc-customer-detail {
				font-weight: <?php echo $options['secondary_font_weight']; ?>;
			}


            @media screen and (min-width: 767px) {


                /**********************************************************************************/
                /* Tab Styles  ********************************************************************/
                /**********************************************************************************/

                <?php
                if ($tabsLayout == 'tabs-square') {
                ?>

					
                    /*Current/Hovered Tab Square Number Color > 767px*/

                    .argmc-wrapper .argmc-tab-item.current .argmc-tab-number,
                    .argmc-wrapper .argmc-tab-item.current .number-text,
                    .argmc-wrapper .argmc-tab-item.visited:hover .argmc-tab-number {
                        color: <?php echo $options['tab_number_color_hover']; ?>;
                    }



                    /*Tab Square Icon/Number Color > 767px*/

                    .argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover .argmc-tab-number {
                        color: <?php echo $options['tab_number_color_hover']; ?>;
                    }



                    /*Tab Square Border Color > 767px*/

                    .argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover::before,
                    .argmc-wrapper .argmc-tab-item.visited:hover::before {
                        border-bottom: 3px solid <?php echo $options['tab_border_bottom_color_hover']; ?>;
                    }



                    /*Hovered/Completed Tab Square Text Color > 767px*/

                    .argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover,
                    .argmc-wrapper .argmc-tab-item.current:hover,
                    .argmc-wrapper .argmc-tab-item.completed:hover,
                    .argmc-wrapper .argmc-tab-item.visited:hover  {
                        color: <?php echo $options['tab_text_color_hover']; ?>;
                    }



                    /*Hovered/Completed Tab Square Background Color > 767px*/

                    .argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover,
                    .argmc-wrapper .argmc-tab-item.current:hover,
                    .argmc-wrapper .argmc-tab-item.completed:hover,
                    .argmc-wrapper .argmc-tab-item.visited:hover {
                        background: <?php echo $options['tab_bkg_color_hover']; ?>;
                    }

                <?php } elseif ($tabsLayout == 'tabs-arrow') { ?>
				
					
					/*Current/Hovered/Completed Tab Arrow Color >767px*/
	
					.argmc-wrapper .argmc-tab-item.current .number-text,
					.argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover .number-text {
						color: <?php echo $options['tab_number_color_hover']; ?>;
					}	
	
	
	
					/*Current/Hovered/Completed Tab Arrow Border Color >767px*/
	
					.argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover,
					.argmc-wrapper .argmc-tab-item.visited:hover {
						 border-bottom-color: <?php echo $options['tab_border_bottom_color_hover']; ?>;
					}
	
	
	
					/*Current/Hovered/Completed Tab Arrow Text Color >767px*/
	
					.argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover,
					.argmc-wrapper .argmc-tab-item.current:hover,
					.argmc-wrapper .argmc-tab-item.completed:hover,
					.argmc-wrapper .argmc-tab-item.visited:hover {
						color: <?php echo $options['tab_text_color_hover']; ?>;
					}
	
	
	
					/*Current/Hovered/Completed Tab Arrow Background/Border Color >767px*/
	
					.argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover, 
					.argmc-wrapper .argmc-tab-item.current:hover,
					.argmc-wrapper .argmc-tab-item.completed:hover,
					.argmc-wrapper .argmc-tab-item.visited:hover {
						background: <?php echo $options['tab_bkg_color_hover']; ?>;
					}
	
					.argmc-wrapper .argmc-tab-item.current + .argmc-tab-item:hover .argmc-tab-item-outer:after,
					.argmc-wrapper .argmc-tab-item.current:hover .argmc-tab-item-outer:after,
					.argmc-wrapper .argmc-tab-item.completed:hover .argmc-tab-item-outer:after,
					.argmc-wrapper .argmc-tab-item.visited:hover .argmc-tab-item-outer:after {
						border-color: transparent transparent transparent <?php echo $options['tab_bkg_color_hover']; ?>;
					}

                <?php
            } elseif ($tabsLayout == 'tabs-arrow-alt') {  
                ?>
				
				    .argmc-wrapper .argmc-tab-item.visited .argmc-tab-number,
					.argmc-wrapper .argmc-tab-item .argmc-tab-number {
						color: <?php echo $options['tab_arrow_alt_number_text_color']; ?>;
					}
					
					<?php
					if ($options['tab_arrow_alt_hide_number_bkg']) {
					?>
					.argmc-wrapper .argmc-tab-item.visited .argmc-tab-number {
						color: <?php echo $options['tab_arrow_alt_text_color_hover']; ?>;
					}
					<?php } ?>
					
					/*Tab Arrow Alt Number Background Color > 767px*/
					
					.argmc-wrapper .argmc-tab-item.visited .argmc-tab-number,
					.argmc-wrapper .argmc-tab-item .argmc-tab-number {
						background:  <?php echo $options['tab_arrow_alt_number_bkg_color']; ?>;
					}
					
					
					/*Visited / On Hover Tab Arrow Alt Text Color > 767px*/
	
					.argmc-wrapper .argmc-tab-item.visited:hover,
					.argmc-wrapper .argmc-tab-item.current.last + .argmc-tab-item:hover {
						color: <?php echo $options['tab_arrow_alt_text_color_hover']; ?>;
					}
					
					
					/*Visited / On Hover Tab Arrow Alt Number Color > 767px*/
	
					.argmc-wrapper .argmc-tab-item.current .argmc-tab-number,
					.argmc-wrapper .argmc-tab-item.visited:hover .argmc-tab-number,
					.argmc-wrapper .argmc-tab-item.current.last + .argmc-tab-item:hover .argmc-tab-number {
						color: <?php echo $options['tab_arrow_alt_number_color_hover']; ?>;
					}
					
					
					/*Visited / On Hover Tab Arrow Alt Number Background Color > 767px*/
	
					.argmc-wrapper .argmc-tab-item.current .argmc-tab-number,
					.argmc-wrapper .argmc-tab-item.visited:hover .argmc-tab-number,
					.argmc-wrapper .argmc-tab-item.current.last + .argmc-tab-item:hover .argmc-tab-number {
						background: <?php echo $options['tab_arrow_alt_number_bkg_color_hover']; ?>;
					}
					
					
					/*Visited / On Hover Tab Arrow Alt Border Color > 767px*/
	
					.argmc-wrapper .argmc-tab-item.visited,
					.argmc-wrapper .argmc-tab-item.visited:hover,
					.argmc-wrapper .argmc-tab-item.current.last + .argmc-tab-item:hover {
						 border-bottom-color: <?php echo $options['tab_arrow_alt_border_bottom_color_hover']; ?>;
					}
					
					
					/*On Hover Tab Arrow Alt Background/Border Color > 767px*/
	
					.argmc-wrapper .argmc-tab-item.visited:hover,
					.argmc-wrapper .argmc-tab-item.current.last + .argmc-tab-item:hover	 {
						 background: <?php echo $options['tab_arrow_alt_bkg_color_hover']; ?>;
					}
	
					.argmc-wrapper .argmc-tab-item.visited:hover .argmc-tab-item-outer:after,
					.argmc-wrapper .argmc-tab-item.current.last  + .argmc-tab-item:hover .argmc-tab-item-outer:after {
						 border-color: transparent transparent transparent <?php echo $options['tab_arrow_alt_bkg_color_hover']; ?>;
					}
					
				
				     <?php			
			} elseif ($tabsLayout == 'tabs-progress-bar') {
			    
					
				/*Tab Progress Bar Styles*/
					
			   
            } elseif ($tabsLayout == 'tabs-outline') {  
                ?>
				
                /*Tab Outline Styles*/
				
				/*Current Tab Vertical Number Color/Bkg Color*/
				.argmc-wrapper.orientation-vertical .argmc-tab-item.last .argmc-tab-number,
				.argmc-wrapper.orientation-vertical .argmc-tab-item.current.last + .argmc-tab-item:hover .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_color_hover']; ?>;
					background-color: <?php echo $options['tab_outline_number_bkg_color_hover']; ?>;
				}
				
				/*Visited Tab Vertical Number Color/Bkg Color*/
				.argmc-wrapper .argmc-tab-item.completed .argmc-tab-number {
					color: <?php echo $options['tab_outline_number_color_completed']; ?>;
					background: <?php echo $options['tab_outline_number_bkg_color_completed']; ?>;
				}
			
			<?php } ?>
				
				
				/*Woocommerce Checkout Review Order Table - Show Product Image*/
				<?php
				if (!empty($options['show_product_image'])) :
				?>
					.argmc-wrapper .arg-product-image,
					.argmc-wrapper .arg-product-desc {
						display: table-cell;
						vertical-align: middle;
					}
					
					.argmc-wrapper .arg-product-desc {
						margin: 0;
						padding-right: 30px;
					}
				
                <?php
				endif;
				?>

            }

        </style>
        <?php						
    }
}

add_action('wp_head', 'argMCStyles', 100);    