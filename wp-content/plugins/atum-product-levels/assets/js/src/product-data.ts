/**
 * Atum Product Levels
 *
 * @copyright Stock Management Labs ©2019
 *
 * @since 1.1.2
 */

/**
 * Third Party Plugins
 */

import '../../../../atum-stock-manager-for-woocommerce/assets/js/vendor/bootstrap3-custom.min';    // TODO: USE BOOTSTRAP 4
import '../../../../atum-stock-manager-for-woocommerce/assets/js/vendor/jquery.easytree';

/**
 * Components
 */

import BillOfMaterials from './components/product-data/_bill-of-materials';
import BomAssociates from './components/product-data/_bom-associates';
import EnhancedSelect from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_enhanced-select';
import HierarchyTree from './components/_hierarchy-tree';
import Popover from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_popover';
import Settings from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import Tooltip from '../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_tooltip';


// Modules that need to execute when the DOM is ready should go here.
jQuery( ($) => {
	
	window['$'] = $; // Avoid conflicts.
	
	// Get the settings from localized var.
	let settings = new Settings('atumProdLevels');
	let tooltip = new Tooltip();
	let enhancedSelect = new EnhancedSelect();
	let popover = new Popover(settings);
	
	// Initialize components.
	new HierarchyTree(settings);
	new BillOfMaterials(settings, tooltip, enhancedSelect);
	new BomAssociates(tooltip, popover, enhancedSelect);
	
});