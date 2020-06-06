/* =======================================
   PRODUCT LEVELS UI FOR ORDER ITEMS
   ======================================= */

import BOMMiManagement from './_bom-mi-management';
import Settings from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import Tooltip from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_tooltip';


export default class PLOrders {
	
	$bomTrees: JQuery = null;
	$itemsWrapper: JQuery;
	isAtumOrder: boolean;
	dataOrderItemName: string;
	
	constructor(
		private settings: Settings,
		private tooltip: Tooltip
	) {
		
		this.$itemsWrapper = $('#woocommerce-order-items, #atum_order_items');
		this.isAtumOrder = 'atum_order_items' === this.$itemsWrapper.attr('id');
		
		if (this.isAtumOrder) {
			this.dataOrderItemName = 'atum_order_item_id';
		}
		else {
			this.dataOrderItemName = 'order_item_id';
		}
		
		this.createBOMTrees();
		this.bindEvents();
	
	}
	
	/**
	 * Create the BOM trees for all the order items
	 */
	createBOMTrees() {
		
		this.$bomTrees = $('.atum-bom-tree');

		this.$bomTrees.each( ( index: number, elem: Element ) => {

			const $bomTree: JQuery = $( elem );

			if ( ! $bomTree.children('ul').hasClass('ui-easytree') ) {
				// Create one instance per each BOM tree.
				new BOMMiManagement( $( elem ), this.settings, this.tooltip );
			}

		} );
		
	}
	
	bindEvents() {
		
		// Rebuild the BOM MI management popups after saving the order items.
		// As WC doesn't have triggers once the Ajax request is completed, we must intercept the right Ajax call.
		$(document).ajaxComplete( (event: JQueryEventObject, xhr: JQueryXHR, settings: any) => {

			if (!settings || !settings.data) {
				return;
			}

			const data: string[] = settings.data.split('&');
			let action: string   = '';

			for (const value of data) {
				if (value.indexOf('action=') > -1) {
					action = value.replace('action=', '');
					break;
				}
			};

			if ( action && [
					'woocommerce_save_order_items',
					'woocommerce_load_order_items',
					'woocommerce_add_order_item',
					'atum_order_add_item'
				].indexOf(action) > -1
			) {
				setTimeout( () => this.createBOMTrees(), 1000 ); // We must wait until the order items are added.
			}

		});
		
		this.$itemsWrapper
		
			// Rebuild the BOM MI management popups for ATUM orders after reloading the order items.
			.on('atum-after-loading-order-items', () => this.createBOMTrees() )
			
			// After table sort. See stupidtable.js.
			.on('aftertablesort', '.woocommerce_order_items, .atum_order_items', (evt: Event, data: any) => {
				
				const $table: JQuery = $(evt.currentTarget);
				
				// Reposition the MI rows after sorting.
				$table.find('tr.order-item-bom-tree-panel').each( (index: number, elem: Element) => {
					$(elem).insertAfter($table.find('tr.item.with-bom-tree').filter(`[data-${ this.dataOrderItemName }="${ $(elem).data(this.dataOrderItemName) }"]`));
				});
				
			});
		
	}
	
}