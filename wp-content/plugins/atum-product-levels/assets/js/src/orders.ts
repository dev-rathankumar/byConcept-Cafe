/**
 * Atum Product Levels UI for orders
 *
 * @copyright Stock Management Labs ©2019
 *
 * @since 1.3.8
 */

import '../../../../atum-stock-manager-for-woocommerce/assets/js/vendor/jquery.easytree';

/**
 * Components
 */

import PLOrders from './components/orders/_pl-orders';
import Settings from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import Tooltip from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_tooltip';


// Modules that need to execute when the DOM is ready should go here.
jQuery( ($) => {
	
	window['$'] = $; // Avoid conflicts.
	
	// Get the settings from localized var.
	let settings = new Settings('atumPLOrdersVars');
	let tooltip = new Tooltip();
	
	new PLOrders(settings, tooltip);
	
});