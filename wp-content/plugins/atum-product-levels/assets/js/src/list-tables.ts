/**
 * Atum Manufacturing Central
 *
 * @copyright Stock Management Labs ©2019
 *
 * @since 1.1.0
 */

/**
 * Third Party Plugins
 */

import '../../../../atum-stock-manager-for-woocommerce/assets/js/vendor/jquery.easytree';

/**
 * Components
 */

import Settings from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import HierarchyTree from './components/_hierarchy-tree';
import MCListTable from './components/list-table/_list-table';


// Modules that need to execute when the DOM is ready should go here.
jQuery( ($) => {
	
	window['$'] = $; // Avoid conflicts.
	
	// Get the settings from localized var.
	let settings = new Settings('atumManCentral');
	
	// Initialize components.
	new HierarchyTree(settings);
	new MCListTable(settings);
	
});