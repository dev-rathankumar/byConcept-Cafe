/* =======================================
   LOCATIONS TREE FOR LIST TABLES
   ======================================= */

import Settings from '../../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import { Utils } from '../../../../../atum-stock-manager-for-woocommerce/assets/js/src/utils/_utils';

export default class HierarchyTree {
	
	locationsSet: string[];
	toSetLocations: string[];
	productId: number = null;
	easytreeInstance: any = null;
	swal: any = window['swal'];
	
	constructor(
		private settings: Settings
	) {
		
		$('body')
			
			// Show hierarchy popup.
			.on('click', '.show-hierarchy', (evt: JQueryEventObject) => {
				evt.preventDefault();
				this.showHierarchyPopup($(evt.currentTarget));
			})
		
			// Open all the tree nodes.
			.on('click', '.open-nodes', (evt: JQueryEventObject) => {
				evt.preventDefault();
				const nodes: any = this.easytreeInstance.getAllNodes();
				Utils.toggleNodes(nodes, 'open');
				this.easytreeInstance.rebuildTree(nodes);
			})
			
			// Close all the tree nodes.
			.on('click', '.close-nodes', (evt: JQueryEventObject) => {
				evt.preventDefault();
				const nodes: any = this.easytreeInstance.getAllNodes();
				Utils.toggleNodes(nodes, 'close');
				this.easytreeInstance.rebuildTree(nodes);
			});
		
	}
	
	/**
	 * Opens a popup with the locations' tree and allows to edit locations
	 *
	 * @param jQuery $button
	 */
	showHierarchyPopup($button: JQuery) {
		
		const isFullTree: string = $button.data('full-tree') || 'no';
		
		this.swal({
			title            : this.settings.get('bomTree'),
			html             : '<div id="atum-bom-tree" class="atum-tree"></div>',
			showCancelButton : false,
			showConfirmButton: false,
			showCloseButton  : true,
            background       : 'var(--atum-table-bg)',
			onOpen           : () => {
				
				let $bomTreeContainer: JQuery = $('#atum-bom-tree');
				
				$.ajax({
					url       : window['ajaxurl'],
					dataType  : 'json',
					method    : 'post',
					data      : {
						action    : 'atum_get_bom_tree',
						token     : this.settings.get('bomTreeNonce'),
						product_id: $button.closest('tr').data('id'),
						full_tree : isFullTree,
					},
					beforeSend:  () => $bomTreeContainer.append('<div class="atum-loading" />'),
					success   : (response: any) => {
						
						const $bomTree: any = $('#atum-bom-tree');
						
						this.easytreeInstance = $bomTree.easytree({
							data: response,
						});
						
						// Add the tree controls.
						$bomTreeContainer.before(`
							<div class="tree-controls">
								<a class="open-nodes" href="#">${ this.settings.get('openAllNodes') }</a>
								<a class="close-nodes" href="#">${ this.settings.get('closeAllNodes') }</a>
							</div>
						`);
						
					}
				});
				
			},
			onClose          : () => (<any>$button).blur().tooltip('hide')
		})
		.catch(this.swal.noop);
		
	}
	
}
