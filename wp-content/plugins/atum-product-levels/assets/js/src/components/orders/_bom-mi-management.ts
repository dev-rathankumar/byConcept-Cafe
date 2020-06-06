/* =======================================
   ORDER ITEM's BOM MI MANAGEMENT
   ======================================= */

import Settings from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import Tooltip from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_tooltip';
import { Utils } from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/utils/_utils';

interface Inventory {
	id: number,
	bomId: number,
	name: string,
	used: number
}

export default class BOMMiManagement {
	
	easytree: any;
	lastLevelNodes: any[] = [];
	swal: any = window['swal'];
	$bomMiManagementModal: JQuery = null;
	
	lastLevelBomIds: number[] = [];
	preSelectedInventories: Inventory[] = [];
	selectedInventories: Inventory[] = [];
	orderItemId: number = null;
	$orderItemQtyInput: JQuery = null;
	newQty: number = null;
	
	constructor(
		private $bomTree: JQuery,
		private settings: Settings,
		private tooltip: Tooltip
	) {
		
		if (!this.$bomTree.length) {
			return;
		}
		
		const $miPanel: JQuery = this.$bomTree.closest('.order-item-mi-panel, .order-item-bom-tree-panel');
		
		const $orderItemWrapper: JQuery = this.$bomTree.closest('.order-item-inventory').length ?
			this.$bomTree.closest('.order-item-inventory') : $miPanel.prev('tr');
		
		this.orderItemId = $miPanel.data('order_item_id') || $miPanel.data('atum_order_item_id');
		this.$orderItemQtyInput = $orderItemWrapper.find('input.quantity');
		
		// Build and set up the tree.
		this.easytree = (<any>this.$bomTree).easytree();
		this.setupTree();
		
		this.bindEvents();
		
	}
	
	/**
	 * Set up the BOM tree with easytree
	 *
	 * @param {boolean} findLastLevelNodes
	 */
	setupTree(findLastLevelNodes: boolean = true) {
		
		const allNodes: any = this.easytree.getAllNodes();
		
		if (allNodes) {
			
			if (findLastLevelNodes) {
				this.lastLevelNodes = []; // Clear any possible old values.
				this.findLastLevelNodes(allNodes);
			}
			
			// Add the has-child classes to last child elements.
			this.lastLevelNodes.forEach((node: any) => {
				
				const $lastChild: JQuery = $(`#${ node.id }`),
				      $lastLevel: JQuery = !$lastChild.hasClass('mi-node') ? $lastChild : $lastChild.closest('ul').siblings('span');
				
				$lastLevel.addClass('has-child');
				
				// Bind the BOM management popup.
				if (!this.$bomTree.hasClass('read-only')) {
					
					const bomId: number = $lastLevel.closest('[data-bom_id]').data('bom_id');
					
					if (bomId && this.lastLevelBomIds.indexOf(bomId) === -1) {
						this.lastLevelBomIds.push(bomId);
					}
					
					$lastLevel.unbind('click'); // Avoid running the click event multiple times for BOMs with multiple inventories.
					
					$lastLevel.click( (evt: JQueryEventObject) => {
						
						const $elem: JQuery = $(evt.target);
						
						// Do not open the popup when expanding/collapsing the tree item.
						if ($elem.hasClass('easytree-expander')) {
							return false;
						}
						
						const $liNode: JQuery  = $elem.closest('li'),
						      $miPanel: JQuery = $liNode.closest('.order-item-mi-panel, .order-item-bom-tree-panel');
						
						if ('yes' === $liNode.data('has_mi') && $miPanel.hasClass('editing')) {
							this.loadBOMManagementUI();
						}
						
					});
					
				}
				
			});
			
		}
		
	}
	
	/**
	 * Bind events
	 */
	bindEvents() {

		console.log('binding BOM MI envents');
		
		this.$bomTree.closest('.bom-tree-wrapper')
		
			// Open all the tree nodes.
			.on('click', '.open-nodes', (evt: JQueryEventObject) => {
				evt.preventDefault();
				const nodes: any = this.easytree.getAllNodes();
				Utils.toggleNodes(nodes, 'open');
				this.easytree.rebuildTree(nodes);
			})
			
			// Close all the tree nodes.
			.on('click', '.close-nodes', (evt: JQueryEventObject) => {
				evt.preventDefault();
				const nodes: any = this.easytree.getAllNodes();
				Utils.toggleNodes(nodes, 'close');
				this.easytree.rebuildTree(nodes);
			})
		
			// Expand/Collapse the BOM tree field.
			.on('click', '.collapse-tree', (evt: JQueryEventObject) => {
			
				const $collapseTreeBtn: JQuery = $(evt.currentTarget),
				      $bomTreeField: JQuery    = $collapseTreeBtn.closest('.bom-tree-wrapper').find('.bom-tree-field');
				
				if (!$collapseTreeBtn.hasClass('collapsed')) {
					$bomTreeField.slideUp('fast');
					$collapseTreeBtn.addClass('collapsed');
				}
				else {
					$bomTreeField.slideDown('fast');
					$collapseTreeBtn.removeClass('collapsed');
				}
				
			});
		
		const $orderItemRow: JQuery = this.$bomTree.closest('tr.order-item-bom-tree-panel').prev('tr');
		
		if ($orderItemRow.length) {
			// Add the 'with-bom-tree' class to all the rows containing BOM trees.
			$orderItemRow.addClass('with-bom-tree');
		}
		
		// Do actions when editing an order item.
		$('#order_line_items').on('click', '.edit-atum-order-item, .edit-order-item', (evt: JQueryEventObject) => {
			
			// Add the editing class when going to edit an order item.
			$(evt.currentTarget).closest('tr').next('.order-item-bom-tree-panel').addClass('editing');
			
			// Disable the quantity input field, so the user is forced to use the BOM management popup.
			if (this.$bomTree.find('[data-has_mi="yes"]').length) {
				this.$orderItemQtyInput.prop({
					readonly: true,
					title   : this.settings.get('editQuantityFromPopup'),
				});
			}
			
		});
		
	}
	
	/**
	 * Bind popup specific events
	 */
	bindModalEvents() {
		
		this.$bomMiManagementModal
			
			// Check/uncheck all inventories.
			.on('click', 'th :checkbox', (evt: JQueryEventObject) => {
				
				const $checkbox: JQuery = $(evt.currentTarget),
				      $miTable: JQuery  = $checkbox.closest('table'),
				      $rows: JQuery     = $miTable.find('tbody tr');
				
				if ($checkbox.is(':checked')) {
					$rows.addClass('active').find('td :checkbox').prop('checked', true).change();
				}
				else {
					$rows.removeClass('active').find('td :checkbox').prop('checked', false).change();
				}
				
				// Focus on the first selected row's input.
				$miTable.find('tbody tr.active').first().find('.stock-used input').select().focus();
				
			})
			
			// Check/uncheck single inventory.
			.on('change', 'td :checkbox', (evt: JQueryEventObject) => {
				
				const $checkbox: JQuery    = $(evt.currentTarget),
				      $miTable: JQuery     = $checkbox.closest('table'),
				      $row: JQuery         = $checkbox.closest('tr'),
				      countChecked: number = $miTable.find('td :checkbox:checked').length;
				
				if ($checkbox.is(':checked')) {
					$row.addClass('active');
				}
				else {
					$row.removeClass('active');
				}
				
				// Add the focus to the stock used input.
				$row.find('.stock-used input').select().focus();
				
				if ($miTable.find('td :checkbox').length === countChecked) {
					$miTable.find('thead tr').find(':checkbox').prop('checked', true);
				}
				else {
					$miTable.find('thead tr').find(':checkbox').prop('checked', false);
				}
				
				this.checkRequired( $checkbox.closest('.bom-item-mi') );
				this.maybeSelectInventory($row);
				
			})
			
			// Stock used input changes.
			.on('keyup change paste', '.stock-used input', (evt: JQueryEventObject) => {
				
				const $input: JQuery = $(evt.currentTarget);
				this.checkRequired( $input.closest('.bom-item-mi') );
				this.maybeSelectInventory($input.closest('tr'));
				
			} )
			
			// Order item quantity input changes.
			.on('keyup change paste', '.order-item-qty input', (evt: JQueryEventObject) => {
				
				const $qtyInput: JQuery = $(evt.currentTarget);
				
				this.$bomMiManagementModal.find('.bom-item-mi').each( (index: number, elem: Element) => {
					
					const $bomMiManagementItem: JQuery = $(elem),
					      $bomTotalRequired: JQuery = $bomMiManagementItem.find('.bom-total-required');
					
					this.newQty = parseFloat( $qtyInput.val() );
					const newRequired: number = ( parseFloat( $bomTotalRequired.data('required') ) / ( parseFloat( $qtyInput.data('qty') ) || 1 ) ) * this.newQty;
					
					$bomTotalRequired.data('required', newRequired );
					$bomMiManagementItem.find('.required-amt').text(newRequired);
					
				});
				
				$qtyInput.data('qty', this.newQty);
				this.validateModal();
				
			} )
		
			// Expand/Collapse BOM MI items.
			.on('click', '.toggle-item', (evt: JQueryEventObject) => {
				
				const $button: JQuery  = $(evt.currentTarget),
				      $content: JQuery = $button.closest('.bom-item-mi').children().not('.table-legend');
				
				if ($button.hasClass('collapsed')) {
					$button.removeClass('collapsed');
					$content.slideDown('fast');
				}
				else {
					$button.addClass('collapsed');
					$content.slideUp('fast');
				}
				
			});
		
	}
	
	/**
	 * Find the last level elements within the tree
	 *
	 * @param {any[]}   nodes
	 * @param {boolean} updateQtys
	 *
	 * @return {any | void}
	 */
	findLastLevelNodes(nodes: any[], updateQtys: boolean = false): any|null {
		
		nodes.forEach( (value: any, index: number) => {
			
			const n: any = nodes[index];
			
			// MI's last level item found.
			if (n.isFolder === false) {
				this.lastLevelNodes.push(n);
				return n;
			}
			
			const hasChildren: boolean = n.children && n.children.length > 0;
			
			if (hasChildren) {
				
				// Maybe update qtys.
				if (updateQtys === true && this.newQty) {
					this.updateNodeQty(n);
				}
				
				const node: any = this.findLastLevelNodes(n.children, updateQtys);
				if (node) {
					return node;
				}
				
			}
			// Last level (without MI children) found.
			else {
				this.lastLevelNodes.push(n);
			}
			
		});
		
		return null;
		
	}
	
	/**
	 * Update the Qty shown on any node
	 *
	 * @param {any}    node
	 * @param {number} newQty
	 */
	updateNodeQty(node: any, newQty: number = null) {
		
		const $title: JQuery      = $(`<span>${ node.text }</span>`),
		      $qtyWrapper: JQuery = $title.find('span');
		
		if ( null === newQty ) {
			const qty: number = parseFloat( $qtyWrapper.text().replace( /\(|\)/g, '' ) );
			newQty = ( qty / ( parseFloat( this.$orderItemQtyInput.data( 'qty' ) ) || 1 ) ) * parseFloat( this.$orderItemQtyInput.val() );
		}
		
		if ( ! isNaN( newQty ) && null !== newQty ) {
			$qtyWrapper.text( `(${ newQty })` );
			node.text = $title.html();
		}
		
	}
	
	/**
	 * Load the BOM Inventory Selector
	 */
	loadBOMManagementUI() {
		
		const $template: JQuery = $(`#bom-mi-management-popup`);
		
		if ( ! $template.length ) {
			this.swal( {
				title: this.settings.get( 'error' ),
				text : this.settings.get( 'unableToLoadPopup' ),
				type : 'error',
			} );
			return;
		}
		
		this.swal({
			title              : this.settings.get('managementPopupTitle'),
			html               : $template.html(),
			showCancelButton   : false,
			showCloseButton    : true,
			confirmButtonText  : `<i class="atum-icon atmi-plus-circle"></i>${ this.settings.get('saveButton') }`,
			confirmButtonColor : '#00B8DB',
			customClass        : 'bom-mi-management',
			showLoaderOnConfirm: true,
			allowOutsideClick  : false,
			onOpen             : () => {
				
				this.selectedInventories = []; // Ensure there are no inventories selected when displaying the popup.
				this.$bomMiManagementModal = $('.bom-mi-management');
				this.prepareModalUI();
				
			},
			preConfirm         : (): Promise<any|boolean> => {
				
				return new Promise( (resolve: Function, reject: Function) => {
					
					if (Object.keys(this.selectedInventories).length) {
						
						// If there were no changes, just resolve the promise.
						if (JSON.stringify(this.preSelectedInventories) === JSON.stringify(this.selectedInventories)) {
							resolve();
						}
						
						// Update the inventories on the db.
						$.ajax({
							url     : window['ajaxurl'],
							data: {
								action       : 'atum_set_bom_order_item_inventories',
								token        : this.settings.get('nonce'),
								inventories  : this.selectedInventories,
								order_id     : $('#post_ID').val(),
								order_item_id: this.orderItemId,
								bom_ids      : this.lastLevelBomIds,
							},
							type    : 'POST',
							dataType: 'json',
							success : (response: any) => {
								
								if (response.success === false) {
									this.showModalError( response.data ? response.data : this.settings.get('errorSaving'), 'error', true );
									reject();
								}
								else {
									resolve();
								}
								
							},
							error: () => {
								this.showModalError( this.settings.get('errorSaving'), 'error', true );
								reject();
							}
						});
						
					}
					else {
						this.showModalError( this.settings.get('noSelectedInventories') );
						reject();
					}
					
				});
				
			}
		})
		.then( (result: any) => {
			
			if (result) {
				this.setOrderItemBOMInventories();
				this.updateOrderItemQuantity();
			}
			
		})
		.catch(this.swal.noop);
		
	}
	
	/**
	 * Prepare the BOM management modal UI
	 */
	prepareModalUI() {
		
		this.bindModalEvents(); // Needed to be called at top.
		
		// Add all the BOM MI management item's templates to the modal.
		$(`[data-id="bom-inventories-${ this.orderItemId }"]`).each( ( index: number, elem: Element ) => {
			this.$bomMiManagementModal.find('.bom-mi-items').append( $(elem).html() );
		} );
		
		// Add the right qty.
		const orderQty: number = this.$orderItemQtyInput.val();
		this.$bomMiManagementModal.find('.order-item-qty input').data( 'qty', orderQty ).val( orderQty );
		
		this.$bomMiManagementModal.find('.bom-item-mi').each( (index: number, elem: Element ) => {
			
			const $bomMiManagementItem: JQuery = $(elem),
			      $bomTotalRequired: JQuery    = $bomMiManagementItem.find('.bom-total-required'),
			      $requiredAmt: JQuery         = $bomMiManagementItem.find('.required-amt'),
			      bomId: number                = $bomMiManagementItem.data('bom_id'),
			      requiredQty: number          = this.$bomTree.find(`[data-bom_id="${ bomId }"]`).data('qty');
			
			// Set the required items.
			$bomTotalRequired.data('required', requiredQty);
			$requiredAmt.text(requiredQty);
			
			// If there are pre-selected inventories, adjust them.
			// TODO: WHAT HAPPENS IF THE SAME BOM IS DISPLAYED MULTIPLE TIMES WITHIN THE SAME TREE? WON'T WORK?
			const $miNodes: JQuery = this.$bomTree.find(`[data-bom_id="${ bomId }"] li`);
			
			if ($miNodes.length) {
				
				$miNodes.each( (index: number, elem: Element) => {
					
					const $miNode: JQuery     = $(elem),
					      inventoryId: number = $miNode.data('inventory_id'),
					      used: number        = $miNode.data('qty'),
					      $inventory: JQuery  = $bomMiManagementItem.find(`[data-inventory_id="${ inventoryId }"]`);
					
					$inventory.find('.stock-used-value').text(used);
					$inventory.find('.stock-used input').val(used);
					$inventory.find(':checkbox').prop('checked', true).change();
					
				} );
				
				this.preSelectedInventories = [...this.selectedInventories];
				
			}
			
		} );
		
		this.validateModal();
		
		// Disable the button until a change is made.
		this.$bomMiManagementModal.find('.swal2-confirm').prop('disabled', true);
		
		// Add the tooltips to modal (if any).
		this.tooltip.addTooltips(this.$bomMiManagementModal);
		
	}
	
	/**
	 * Check whether the required item amount is met
	 *
	 * @param {JQuery} $bomMiManagementItem
	 */
	checkRequired($bomMiManagementItem: JQuery) {
		
		let totalAdded: number = 0;
		
		$bomMiManagementItem.find('table tbody tr.active').each( (index: number, elem: Element) => {
			
			const $row: JQuery   = $(elem),
			      $input: JQuery = $row.find('.stock-used input');
			
			let newValue: number = parseFloat( $input.val() );
			
			// The value must be a number greater than 0.
			if (isNaN(newValue) || newValue < 0) {
				this.showModalError( this.settings.get('wrongStockAmount') );
				$input.val(0);
				return false;
			}
			
			// The value must be equal or lower than the available stock (if BOM stock control is enabled).
			if ('yes' === this.settings.get('bomStockControl')) {
				const availableStock: number = parseFloat($row.find('.stock-available').data('available'));
				if (newValue > availableStock) {
					this.showModalError(this.settings.get('notEnoughStock'), 'warning');
					$input.val(availableStock);
					newValue = availableStock;
				}
			}
			
			totalAdded += newValue;
			$row.siblings('.stock-used-value').text( newValue );
			
		});
		
		$bomMiManagementItem.find('.bom-total-added').data('added', totalAdded);
		$bomMiManagementItem.find('.added-amt').text(totalAdded);
		this.validateModal();
		
	}
	
	/**
	 * Validates the modal and activates the button if necessary
	 */
	validateModal() {
		
		let canSave: boolean = null;
		
		// All the BOM items must be correct.
		this.$bomMiManagementModal.find('.bom-item-mi').each( (index: number, elem: Element ) => {
			
			const $bomMiManagementItem: JQuery = $(elem),
			      $requiredAmt: JQuery         = $bomMiManagementItem.find('.required-amt');
			
			if ( parseFloat( $bomMiManagementItem.find('.bom-total-required').data('required') ) !== parseFloat( $bomMiManagementItem.find('.bom-total-added').data('added') ) ) {
				$requiredAmt.removeClass('valid').addClass('invalid');
				canSave = false; // Just 1 being not valid is enough to disable saving.
			}
			else {
				$requiredAmt.removeClass('invalid').addClass('valid');
			}
			
		} );
		
		this.$bomMiManagementModal.find('.swal2-confirm').prop('disabled', canSave === false);
		
	}
	
	/**
	 * Show an error within the BOM management popup
	 *
	 * @param {string}  errorMsg
	 * @param {string}  type
	 * @param {boolean} persistent
	 */
	showModalError(errorMsg: string, type: string = 'error', persistent: boolean = false) {
		
		const errorId: string = 'bom-mi-management-alert';
		
		if ($(`#${ errorId }`).length) {
			$(`#${ errorId }`).remove();
		}
		
		let typeClass: string = '';
		
		switch (type) {
			
			case 'info':
				typeClass = 'alert-primary';
				break;
			
			case 'warning':
				typeClass = 'alert-warning';
				break;
			
			default:
				typeClass = 'alert-danger';
				break;
			
		}
		
		const $error: JQuery = $('<div />', {
			class: `alert ${ typeClass }`,
			id   : errorId,
			text : errorMsg,
		}).hide();
		
		this.$bomMiManagementModal.find('.bom-mi-items').prepend($error.slideDown('fast'));
		
		if (!persistent) {
			setTimeout(() => $(`#${ errorId }`).slideUp('fast', () => $(`#${ errorId }`).remove()), 5000);
		}
		
	}
	
	/**
	 * Select/Unselect an inventory
	 *
	 * @param {JQuery} $row
	 */
	maybeSelectInventory($row: JQuery) {
		
		const inventoryId: number = parseInt($row.data('inventory_id')),
		      bomId: number       = parseInt($row.closest('.bom-item-mi').data('bom_id'));
		
		if ($row.find('input[type=checkbox]').is(':checked')) {
			
			const stockUsed: number = parseFloat($row.find('.stock-used input').val());
			
			if (stockUsed > 0) {
				
				this.addInventory({
					id   : inventoryId,
					bomId: bomId,
					name : $.trim( $row.find('.name').text() ),
					used : stockUsed,
				});
			}
			else {
				this.removeInventory(inventoryId, bomId);
			}
			
		}
		else {
			this.removeInventory(inventoryId, bomId)
		}
		
	}
	
	/**
	 * Add an inventory to the selectedInventories array
	 *
	 * @param {Inventory} inventory
	 */
	addInventory(inventory: Inventory) {
		
		const currentIndex: number = this.selectedInventories.findIndex( (inv: Inventory) => inv.id === inventory.id && inv.bomId === inventory.bomId );
		
		if (currentIndex === -1) {
			this.selectedInventories.push(inventory);
		}
		else {
			this.selectedInventories[currentIndex] = inventory;
		}
		
	}
	
	/**
	 * Remove an inventory from the selectedInventories array
	 *
	 * @param {number} inventoryId
	 * @param {number} bomId
	 */
	removeInventory(inventoryId: number, bomId: number) {
		
		const index: number = this.selectedInventories.findIndex( (inv: Inventory) => inv.id === inventoryId && inv.bomId === bomId );
		
		if (index > -1) {
			this.selectedInventories.splice(index, 1);
		}
		
	}
	
	/**
	 * Set the inventories for the order item's BOM
	 */
	setOrderItemBOMInventories() {
		
		this.lastLevelBomIds.forEach( (bomId: number) => {
			
			const lastLevelNodeId: string = this.$bomTree.find(`[data-bom_id="${ bomId }"]`).children('span').attr('id'),
			      lastLevelNode: any      = this.easytree.getNode(lastLevelNodeId);
			
			// Remove all the children nodes before start addind them.
			if (lastLevelNode.children && lastLevelNode.children.length) {
				
				// Clone the object to avoid issues when removing multiple nodes.
				const miNodes: any[] = [...lastLevelNode.children];
				
				miNodes.forEach( (node: any) => this.easytree.removeNode(node.id) );
			}
			
			if (this.selectedInventories.length) {
				
				this.selectedInventories
					.filter( (inv: Inventory) => inv.bomId === bomId )
					.forEach( (inventory: Inventory) => {
					
						const node: any = {
							text    : `${ inventory.name } (${ inventory.used })`,
							isFolder: false,
							uiIcon  : 'atum-icon atmi-multi-inventory',
							spanCss : 'mi-node',
							dataAtts: {
								qty         : inventory.used,
								inventory_id: inventory.id,
							},
						};
						
						// Add the new node to the tree.
						this.easytree.addNode(node, lastLevelNode.id);
						
					} );
				
			}
			
		} );
		
		// Rebuild the tree.
		this.easytree.rebuildTree();
		this.setupTree();
		
	}
	
	/**
	 * Update the Order item quantity (if modified)
	 */
	updateOrderItemQuantity() {
		
		if ( this.newQty && parseFloat( this.$orderItemQtyInput.val() ) !== this.newQty ) {
			
			this.$orderItemQtyInput.val(this.newQty).change(); // Triggering the change is needed so MI can update the order item quantity.
			
			const allNodes: any[] = this.easytree.getAllNodes();
			this.lastLevelNodes = []; // Clear any possible old values.
			this.findLastLevelNodes(allNodes, true);
			
			// Update the qty on the root level node.
			const rootNodeId: string = this.$bomTree.find('> ul > li > span').attr('id'),
			      rootNode: any      = this.easytree.getNode(rootNodeId);
			
			this.updateNodeQty(rootNode, this.newQty);
			
			this.easytree.rebuildTree();
			this.setupTree(false);
			
		}
		
	}
	
}