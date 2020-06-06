/* =======================================
   BILL OF MATERIALS TAB
   ======================================= */

import EnhancedSelect from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_enhanced-select';
import Settings from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';
import Tooltip from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_tooltip';

export default class BillOfMaterials {
	
	isPurchasable: string = '';
	forceEdit: boolean = false;
	$wcMetaBox: JQuery;
	$itemTemplate: JQuery;
	productType: string;
	$atumDataPanel: JQuery;
	$bomDataPanel: JQuery;
	$manageStockCheckboxes: JQuery;
	$manageStockCheckboxesFiltered: JQuery;
	isBOM: boolean;
	swal: any = window['swal'];
	hasLinkedBOMs: boolean;
	
	constructor(
		private settings: Settings,
		private tooltip: Tooltip,
		private enhancedSelect: EnhancedSelect
	) {
		
		// Initialize props.
		this.$wcMetaBox = $('#woocommerce-product-data');
		this.$itemTemplate = $($('#bom-template').html());
		this.productType = $('#product-type').val();
		this.$atumDataPanel = this.$wcMetaBox.find('#atum_product_data');
		this.$bomDataPanel = this.$wcMetaBox.find('#bom_product_data');
		this.isBOM = this.settings.get('levels').indexOf(this.productType) >= 0;
		
		const $soldIndividually: JQuery = this.$wcMetaBox.find('.form-field').filter('[class*="_sold_individually_"]');
		
		// Set isPurchasable prop.
		this.setPurchasable($('input[name="_is_purchasable"]').filter(':checked'));
		
		// Set the fields' visibility.
		$.each( this.settings.get('levels'), (index: number, value: string) => {
			
			if (value.indexOf('variable') === -1) {
				// Show the pricing and stock fields for BOM products.
				this.$wcMetaBox.find('.pricing').addClass(`show_if_${ value }`);
			}
			else {
				
				// Show the "used for variations" checkbox on variable product levels.
				this.$wcMetaBox.find('.enable_variation').addClass(`show_if_${ value }`);
				$('body').on( 'woocommerce_added_attribute', () => {
					
					const $enableVariation: JQuery = this.$wcMetaBox.find('.enable_variation');
					$enableVariation.addClass(`show_if_${ value }`);
					
					if (this.productType.indexOf('variable') > -1) {
						$enableVariation.show();
					}
					
				} );
				
				// Hide the stock status for variables.
				this.$wcMetaBox.find('.stock_status_field').addClass(`hide_if_${ value }`);
				
			}
			
			// Show the "manage stock" checkbox for all.
			this.$wcMetaBox.find('.form-field').filter('[class*="_manage_stock_"]').addClass(`show_if_${ value }`);
			this.$wcMetaBox.find('.show_if_variable-subscription').addClass(`hide_if_${ value }`);
			
			$soldIndividually.parent().addClass(`show_if_${ value }`);
			
		});
		
		// If is a PL type.
		if (this.isBOM) {
			
			$soldIndividually.removeClass('show_if_simple show_if_variable');
			
			// Hide non needed fields for non sellable BOMs.
			if (this.isPurchasable === 'no') {
				
				// When the BOM stock control is enabled, the backorders field is needed if the user wants to set it on the parent product.
				if (this.settings.get('bomStockControl') === 'no') {
					this.$wcMetaBox.find('._backorders_field').hide();
				}
				
				if ( this.productType !== 'variable-product-part' && this.productType !== 'variable-raw-material') {
					$soldIndividually.hide();
				}
				
			}
			
		}

		this.setManageStockStatus();
		this.bindEvents();
		
		this.maybeDisableAllowBackorders( this.$wcMetaBox );
	
	}

	/**
	 * Change the manage stock checkbox status depending on the current product's properties.
	 */
	setManageStockStatus() {

		const rawMaterialVal: any = this.$bomDataPanel.find('#raw_material').val(),
		      productPartVal: any = this.$bomDataPanel.find('#product_part').val();

		this.hasLinkedBOMs = ! ( ( rawMaterialVal === '' || ( Array.isArray( rawMaterialVal ) && rawMaterialVal.length === 0 ) )
			&& ( productPartVal === '' || ( Array.isArray( productPartVal ) && productPartVal.length === 0 ) ) );
		this.setManageStockSelector();

		this.$manageStockCheckboxes.prop('disabled', false);

		// If is a PL type.
		if (this.isBOM) {

			if (this.productType.indexOf('variable') === -1) {

				// Disable manage stock option for simple BOMs if the BOM Stock Control is enabled.
				if (this.settings.get('bomStockControl') === 'yes') {

					this.$manageStockCheckboxesFiltered.each( ( index: number, elem: Element ) => {

						const $manageStock = $(elem);

						if ( !$manageStock.prop('checked')) {
							$manageStock.prop('checked', true).change();
						}

						$manageStock.prop({
							disabled: true,
							title   : this.settings.get('manageStockMsg'),
						});

					});

				}
			}
			else {

				// Disable manage stock option for simple BOMs if the BOM Stock Control is enabled.
				if (this.settings.get('bomStockControl') === 'yes') {

					this.$manageStockCheckboxesFiltered.each( ( index: number, elem: Element ) => {

						this.SetVariationManageStockStatus( elem );
					});

				}

			}

		}
		// If has BOMs and the BOM Stock Control is enabled, disable the manage_stock.
		else if (this.settings.get('bomStockControl') ) {

			if (this.productType.indexOf('variable') === -1) {

				if ( this.hasLinkedBOMs ) {

					this.$manageStockCheckboxesFiltered.prop({
						disabled: true,
						checked : true,
						title   : this.settings.get('manageStockMsg'),
					});
				}
			}
			else {
				this.$manageStockCheckboxesFiltered.each( ( index: number, elem: Element ) => {

					this.SetVariationManageStockStatus( elem );
				});
			}

		}

	}

	/**
	 * Change a variation manage stock checkbox status depending on the variation's properties.
	 */
	SetVariationManageStockStatus( elem: Element ) {

		let $elem: JQuery      = $( elem ),
		    doDisable: boolean = true;

		const $data: JQuery = $elem.closest( '.data' ),
		      hasBOM        = ! ( 'no' === $data.find( '.raw_materials' ).data( 'has-bom' ) && 'no' === $data.find( '.product_parts' ).data( 'has-bom' ) );

		if ( ! this.isBOM && ! hasBOM ) {
			doDisable = false;
		}
		// Multi-Inventory compatibility (if has BOM, only the  main Inventory should be disabled).
		else if ( $elem.attr( 'name' ).indexOf( 'atum_mi' ) > -1
			&& hasBOM && ! $elem.closest( '.inventory-group' ).hasClass( 'main' ) ) {

			doDisable = false;
		}


		if ( doDisable ) {

			if ( ! $elem.is( ':checked' ) ) {
				$elem.prop( 'checked', true ).change();
			}

			$elem.prop( {
				disabled: true,
				title   : this.settings.get( 'manageStockMsg' ),
			} );

		}

	}
	
	/**
	 * Bind events.
	 */
	bindEvents() {
		
		let $postForm: JQuery   = $('form#post'),
		    $stockField: JQuery = $postForm.find('#_stock');
		
		// Show/Hide extra fields on product type select's changes.
		$('#product-type').change( (evt: JQueryEventObject) => {
			
			const $syncPurchaseWrapper: JQuery = this.$bomDataPanel.find('.sync_purchase_price');
			
			this.productType = $(evt.currentTarget).val();
			this.isBOM = this.settings.get('levels').indexOf(this.productType) >= 0;
			
			setTimeout( () => {
				
				if (this.isBOM) {
					this.toggleExtraFields(this.$atumDataPanel.find('.purchasable_buttons'));
				}
				
				this.toggleVariationFields();
				this.maybeToggleStockfield();
				
			}, 300);
			
			// WC Bookings compatibility.
			$syncPurchaseWrapper.find('._sync_purchase_price_field').css('visibility', this.productType === 'booking' ? 'hidden' : 'visible');
			
		}).change();
		
		
		this.$wcMetaBox
		
			// Listen for quantity field changes.
			.on('change', '#bom_product_data .item-quantity input, .atum-data-panel .item-quantity input', (evt: JQueryEventObject) => {
				
				// Check if there is enough BOMs in stock to cover this change.
				let $input: JQuery            = $(evt.currentTarget),
				    $bomItem: JQuery          = $input.closest('.linked-bom'),
				    $bomData: JQuery          = $bomItem.next('.bom-data'),
				    $list: JQuery             = $input.closest('.linked-boms'),
				    newQty: number            = parseFloat($input.val()),
				    needConfirmation: boolean = false;
				
				// If the BOM Stock Control is enabled, we don't have to check the availability
				if (this.settings.get('bomStockControl') === 'yes') {
					
					if (newQty && newQty > parseFloat($bomData.find('.total_in_warehouse').text()) ) {
						needConfirmation = true;
					}
					
				}
				else {
					
					this.recalculateBomData($bomItem);
					
					if (newQty && parseFloat($bomData.find('.shortage').text()) < 0) {
						needConfirmation = true;
					}
					
				}
				
				if (needConfirmation) {
					
					if (!$input.hasClass('confirmed')) {
						
						this.confirmChange( () => {
							
							this.updateBomLine($bomItem, true);
							
							// Do not show the alert again for this item.
							$input.addClass('confirmed');
							
						},
						(dismiss: string) => {
							
							// Dismiss can be 'cancel', 'overlay', 'close' or 'timer'.
							if (dismiss === 'cancel') {
								
								let savedBom = $list.siblings('input[type=hidden]').val(),
								    oldValue = 0;
								
								if (savedBom) {
									savedBom = $.parseJSON(savedBom);
									
									$.each(savedBom, (index: number, value: any) => {
										if (typeof value === 'object' && value.hasOwnProperty('bom_id') && parseInt(value.bom_id) === $input.data('id')) {
											oldValue = value.qty;
										}
									});
								}
								
								// Restore the previous value.
								$input.removeClass('confirmed').val(oldValue);
								$input.focus();
								this.recalculateBomData($bomItem);
								
							}
							
						});
						
					}
					else {
						this.updateBomLine($bomItem, true);
					}
					
				}
				else {
					this.updateBomLine($bomItem, false);
				}
				
			})
			
			// Will trigger after the product variations are loaded.
			.on('woocommerce_variations_added woocommerce_variations_loaded', () => {
				
				this.bindSelects();
				this.doSortable();
				this.tooltip.addTooltips(this.$wcMetaBox);
				this.setManageStockSelector();
				
				this.toggleVariationFields();
				this.maybeToggleStockfield();
				
				// Disable manage stock option for variation BOMs if BOM Stock Control is enabled.
				if ( this.settings.get( 'bomStockControl' ) === 'yes' ) {

					this.$manageStockCheckboxesFiltered.each( ( index: number, elem: Element ) => {

						this.SetVariationManageStockStatus( elem );

					} );

					// Check whether to disable the allow backorders select
					const $variationsPanel: JQuery = this.$wcMetaBox.find( '.woocommerce_variations' );

					if ( $variationsPanel.length && $variationsPanel.children( '.woocommerce_variation' ).length ) {

						$variationsPanel.find( '.woocommerce_variation' ).each( ( index: number, elem: Element ) => {
							this.maybeDisableAllowBackorders( $( elem ) );
						} );
					}

				}
				
				
			})
			
			// Enable manage stock checkboxes if disabled.
			.on('woocommerce_variations_save_variations_button', '#variable_product_options', () => this.$manageStockCheckboxesFiltered.prop( 'disabled', false ) )
			
			// Purchasable button group.
			.on('change', '.purchasable_buttons input:radio', (evt: JQueryEventObject) => {
				
				let $button: JQuery           = $(evt.currentTarget),
				    $variationWrapper: JQuery = $button.closest('.woocommerce_variation');
				
				this.setPurchasable($button);
				
				// Enable the save button for variations.
				if ($variationWrapper.length) {
					$variationWrapper.addClass('variation-needs-update');
					$('.save-variation-changes, .cancel-variation-changes').prop('disabled', false);
				}
				
				this.toggleExtraFields($button);
				
			})
			
			// Expand/collapse the BOM data table.
			.on('click', '.bom-data-toggler .toggle-indicator', (evt: JQueryEventObject) => {
				evt.preventDefault();
				$(evt.currentTarget).closest('.linked-bom').toggleClass('expanded').next('.bom-data').toggle();
			})
			
			// Update Purchase price when enabling Sync.
			.on( 'change', '#_sync_purchase_price', (evt: JQueryEventObject) => this.calculateBomCost( $(evt.currentTarget).closest('.sync_purchase_price').siblings().find('.linked-boms').first() ) )
			
			// Bulk actions.
			.on( 'click', '.apply-bom-bulk', (evt: JQueryEventObject) => {
				evt.preventDefault();
				this.applyBulkAction( $(evt.currentTarget) );
			})
			
			// "Select All" checkbox.
			.on( 'change', '.linked-boms .select-all', (evt: JQueryEventObject) => {
				const $selectAll = $(evt.currentTarget);
				$selectAll.closest('.linked-boms').find('.select-row').prop('checked', $selectAll.is(':checked'));
			});

		this.$atumDataPanel.on( 'atum-mi-added-inventory', (evt: JQueryEventObject, $Inventory: JQuery ) => {

			console.log( $Inventory );
			this.setManageStockStatus();

		});
		
		
		// Bind selects.
		this.bindSelects();
		
		// Sortable rows.
		this.doSortable();
		
		// Add tooltips.
		this.tooltip.addTooltips( this.$bomDataPanel );
		
		// If it's a BOM product, check whether the stock amount is enough to cover all the committed BOMs.
		if (this.productType === 'product-part' || this.productType === 'raw-material') {
			$stockField.change( (evt: JQueryEventObject) => $(evt.currentTarget).addClass('dirty') );
		}
		
		// Trigger before submitting the product post form.
		$postForm.submit( (evt: JQueryEventObject) => {
			
			if (this.settings.get('bomStockControl') !== 'yes' && !this.forceEdit && (this.productType === 'product-part' || this.productType === 'raw-material')) {
				
				const committed: number = parseFloat($postForm.find('#_committed').val()),
				      stock: number     = parseFloat($stockField.val());
				
				if (committed > 0 && stock < committed && $stockField.hasClass('dirty')) {
					
					evt.preventDefault();
					
					this.confirmChange( () => {
						// Change approved, submit the form.
						this.forceEdit = true;
						$postForm.submit();
					},
					(dismiss: string) => {
						// Dismiss can be 'cancel', 'overlay', 'close' or 'timer'.
						if (dismiss === 'cancel') {
							// Set focus on the stock quantity field.
							this.$wcMetaBox.find('.inventory_tab a').click();
							this.$wcMetaBox.find('#_stock').focus();
						}
					});
					
				}
				
			}
			
		});
		
		$postForm.find('input[type=submit]').click( () => {
		
			if (this.settings.get('bomStockControl') === 'yes' && !this.forceEdit) {
				this.$manageStockCheckboxesFiltered.prop('disabled', false);
				this.forceEdit = true;
			}
			
		});
		
	}
	
	/**
	 * Set the isPurchasable property
	 *
	 * @param {JQuery} $isPurchasableInput
	 */
	setPurchasable($isPurchasableInput: JQuery) {
		
		if(!this.isBOM) {
			return;
		}
		
		this.isPurchasable = $isPurchasableInput.val();
		
		if (this.isPurchasable === '') {
			this.isPurchasable = this.settings.get('defaultBomSelling');
		}
		
		// Toggle the BOM selling fields.
		if ('yes' === this.settings.get('bomStockControl')) {
			
			const $wrapper: JQuery = $isPurchasableInput.closest('.woocommerce_variation').length ? $isPurchasableInput.closest('.woocommerce_variation') : this.$wcMetaBox;
			
			$wrapper.find('._selling_priority_field, ._minimum_threshold_field, ._available_to_purchase_field')
				.css('display', ('no' === this.isPurchasable || ('' === this.isPurchasable && 'no' === this.settings.get('defaultBomSelling'))) ? 'none' : 'block')
				.find('input').val('');
			
		}
		
	}
	
	/**
	 * Bind events for product search fields
	 */
	bindSelects() {
		
		// Listen for changes on the Product Parts and Raw Materials fields.
		this.$wcMetaBox.find('.raw_materials_search, .product_parts_search').change( (evt: JQueryEventObject) => {
			
			const $select: JQuery = $(evt.currentTarget);
			
			if ($select.val()) {
				this.addBom($select);
			}
			
		});
		
		this.enhancedSelect.doSelect2( this.$wcMetaBox.find('.atum-select2'), {
			minimumResultsForSearch: 20,
		} );
		
	}
	
	/**
	 * Allow to reorder the items
	 */
	doSortable() {
		
		// BOM list sorting.
		(<any>this.$wcMetaBox).find('.linked-boms').sortable({
			handle              : '.drag-item',
			items               : '.linked-bom',
			forcePlaceholderSize: true,
			// Update the input to fit the new order.
			stop                : (evnt: any, ui: any) => this.updateInput( $(ui.item).closest('.linked-boms') ),
		});
		
	}
	
	/**
	 * Add the selected BOM to the list
	 *
	 * @param {JQuery} $select
	 */
	addBom($select: JQuery) {
		
		let itemName: string = $select.find('option:selected').text(),
		    itemId: number   = $select.val(),
		    $item: JQuery    = this.$itemTemplate.clone(),
		    $list: JQuery    = $select.closest('.bom-builder').find('.linked-boms');
		
		// If there is an empty table's row placeholder, remove it first.
		if ($list.find('.no-items').length) {
			$list.find('.no-items').remove();
		}
		
		$item.find('.item-name').text(itemName);
		$item.find('.item-quantity input').data('id', itemId);
		$list.children('tbody').append($item);
		$list.show();
		
		// Load the BOM data via Ajax.
		this.loadBomData($list.find('.linked-bom').last());
		
		// Empty the field and remove the button after adding the new item to the list.
		$select.val('').change();
		this.doSortable();
		this.tooltip.addTooltips($list);
		
		// Update the total cost.
		this.calculateBomCost($list);
		
		// Update the input to add the new item.
		this.updateInput($list);

		this.setManageStockStatus();
		
	}
	
	/**
	 * Do an ajax call to get the BOM data after adding one to the list
	 *
	 * @param {JQuery} $bomItem
	 */
	loadBomData($bomItem: JQuery) {
		
		let $itemQty: JQuery          = $bomItem.find('.item-quantity input'),
		    $variationWrapper: JQuery = $itemQty.closest('.woocommerce_variation'),
		    productId: number         = $variationWrapper.length ? $variationWrapper.find('.remove_variation').attr('rel') : $('#post_ID').val();
		
		$.ajax({
			url       : window['ajaxurl'],
			data      : {
				action    : 'atum_get_bom_data',
				bom_id    : $itemQty.data('id'),
				product_id: productId,
				token     : this.$bomDataPanel.data('nonce'),
			},
			method    : 'POST',
			beforeSend: () => $itemQty.prop('disabled', true),
			success   : (response: any) => {
				
				if (response) {
					$bomItem.replaceWith(response);
				}
				
				$itemQty.prop('disabled', false);
				
			},
		});
		
	}
	
	/**
	 * Recalculate the BOM data after BOM quantity changes
	 *
 	 * @param {JQuery} $bomItem
	 */
	recalculateBomData($bomItem: JQuery) {
		
		// Only needed when the BOM Stock Control is disabled.
		if (this.settings.get('bomStockControl') !== 'yes') {
			
			const $input: JQuery              = $bomItem.find('.item-quantity input'),
			      $bomData: JQuery            = $bomItem.next('.bom-data'),
			      $committed: JQuery          = $bomData.find('.committed span'),
			      $shortage: JQuery           = $bomData.find('.shortage span'),
			      $freeToUse: JQuery          = $bomData.find('.free_to_use span'),
			      newQty: number              = parseFloat($input.val()),
			      newCommittedAmt: number     = parseFloat($committed.text()) - parseFloat($input.data('original-value')) + newQty,
			      totalInWarehouseAmt: number = parseFloat($bomData.find('.total_in_warehouse span').text());
			
			$committed.text( newCommittedAmt );
			$input.data('original-value', newQty);
			
			$freeToUse.text((totalInWarehouseAmt - newCommittedAmt) > 0 ? totalInWarehouseAmt - newCommittedAmt : 0);
			
			let newShortageAmt: number;
			
			if (totalInWarehouseAmt < 0) {
				newShortageAmt = newCommittedAmt * -1;
			}
			else if (newCommittedAmt > totalInWarehouseAmt) {
				newShortageAmt = totalInWarehouseAmt - newCommittedAmt;
			}
			else {
				newShortageAmt = 0;
			}
			
			$shortage.text(newShortageAmt);
			
			this.updateBomItemStatus($bomItem, newShortageAmt < 0);
			
		}
		
	}
	
	/**
	 * Update the meta key input value
	 *
	 * @param {JQuery} $bomList
	 */
	updateInput($bomList: JQuery) {
		
		let $input: JQuery    = $bomList.siblings('input[type=hidden]'),
		    inputValue: any[] = [];
		
		$bomList.find('.linked-bom').each( (index: number, elem: Element) => {
			
			let $input: JQuery = $(elem).find('.item-quantity input');
			
			inputValue.push({
				bom_id: $input.data('id'),
				qty   : parseFloat($input.val()),
			});
			
		});

		$input.val( inputValue.length ? JSON.stringify( inputValue ) : '' );
		
		// Update the excluded items to avoid retrieving them again on next searches.
		const $searchSelect: JQuery = $bomList.parent().find('.wc-product-search');
		
		// The current product should never show
		$searchSelect.data('exclude', $('#post_ID').val());
		
		if (inputValue.length) {
			
			let excluded: string = $searchSelect.data('exclude').toString();
			
			if (excluded) {
				
				let excludedArr: string[] = excluded.split(',');
				
				$.each(inputValue, (index: number, value: any) => {
					
					const id: string = value.bom_id.toString();
					
					if ( $.inArray(id, excludedArr) === -1 ) {
						excludedArr.push(id);
					}
					
				});
				
				$searchSelect.data('exclude', excludedArr.join(','));
				
			}
			
		}
		
		// Check if we should update the "hasLinkedBom" data.
		const $nonEmptyimput: JQuery = $input.filter( (index: number, elem: Element) => {
			return $(elem).val() !== '' && $(elem).val() !== '[]';
		});
		
		$bomList.closest('.bom-builder').data('has-bom', $nonEmptyimput.length ? 'yes' : 'no');
		
		const $wrapper: JQuery = $bomList.closest('.woocommerce_variation').length ? $bomList.closest('.woocommerce_variation') : this.$bomDataPanel;
		this.maybeToggleBomStockControlFields( $wrapper );
		
	}
	
	/**
	 * Remove a BOM from the list
	 *
	 * @param {JQuery} $bomItem
	 */
	removeBom($bomItem: JQuery) {
		
		$bomItem = $bomItem.add($bomItem.next('.bom-data'));
		
		$bomItem.hide( 400,() => {
			
			let $list: JQuery       = $bomItem.closest('.linked-boms'),
			    $bomBuilder: JQuery = $list.closest('.bom-builder');
			
			$bomItem.remove();
			
			// Update the total cost.
			this.calculateBomCost($list);
			
			// Update the input to remove the item.
			this.updateInput($list);
			
			// In variations, the Save button is disabled until a form field is changed,
			// so trigger a change on the search field.
			$bomBuilder.find('.wc-product-search').change();
			
			if (!$list.find('tbody tr').length) {
				const text: string = this.settings.get( $bomBuilder.hasClass('raw_materials') ? 'noRawMaterials' : 'noProductParts');
				$list.find('tbody').append(`<tr class="no-items"><td colspan="8">${ text }</td></tr>`)
			}
			
			$list.find('.select-all').prop('checked', false);

			this.setManageStockStatus();
			
		});
		
	}
	
	/**
	 * Update a BOM line
	 *
	 * @param {JQuery}   $bomItem
	 * @param {boolean}  noStock
	 */
	updateBomLine($bomItem: JQuery, noStock: boolean) {
		
		let $list: JQuery        = $bomItem.closest('.linked-boms'),
		    $bomItemCost: JQuery = $bomItem.find('.item-cost input'),
		    isRealCost: boolean  = $list.data('cost-calc') === 'real';
		
		// Update the status
		this.updateBomItemStatus($bomItem, noStock);
		
		if (isRealCost) {
			// Update the cost if needed.
			$bomItemCost.val( parseFloat($bomItem.find('.item-quantity input').val()) * parseFloat($bomItemCost.data('unit-cost')) );
		}
		
		this.calculateBomCost($list);
		this.updateInput($list);
		this.recalculateBomData($bomItem);
		
	}
	
	/**
	 * Update the status indicators for the specified BOM line
	 *
	 * @param {JQuery}  $bomItem
	 * @param {boolean} noStock
	 */
	updateBomItemStatus($bomItem: JQuery, noStock: boolean) {
		
		let $bomData: JQuery = $bomItem.next('.bom-data');
		
		// Update the status
		if (noStock === true) {
			$bomItem.addClass('outofstock').removeClass('instock').find('.bom-status').removeClass('atmi-checkmark-circle').addClass('atmi-cross-circle');
			$bomData.addClass('outofstock').removeClass('instock');
		}
		else {
			$bomItem.addClass('instock').removeClass('outofstock').find('.bom-status').removeClass('atmi-cross-circle').addClass('atmi-checkmark-circle');
			$bomData.addClass('instock').removeClass('outofstock');
		}
		
	}
	
	/**
	 * Calculate the total cost of the specified BOM list
	 *
	 * @param {JQuery} $bomList
	 */
	calculateBomCost($bomList: JQuery) {
		
		let bomCost: number     = 0,
		    isRealCost: boolean = $bomList.data('cost-calc') === 'real';
		
		$bomList.find('.linked-bom').each( (index: number, elem: Element) => {
			
			let $bomItem: JQuery = $(elem),
			    itemCost: number = parseFloat($bomItem.find('.item-cost input').val() || 0),
			    itemQty: number  = parseFloat($bomItem.find('.item-quantity input').val() || 0);
			
			bomCost += isRealCost ? itemCost : (itemQty * itemCost);
			
		});
		
		$bomList.closest('.bom-builder').find('.bom-list-total .total-badge').text( +bomCost.toFixed(2) );
		this.calculateTotalBomCost($bomList);
		
	}
	
	/**
	 * Calculate the total BOM cost (RM cost + PP cost)
	 *
	 * @param {JQuery} $bomList
	 */
	calculateTotalBomCost($bomList: JQuery) {
		
		let $variationsContainer: JQuery = $bomList.closest('.woocommerce_variation'),
		    $rmList: JQuery              = $variationsContainer.length ? $variationsContainer.find('.raw_materials') : this.$bomDataPanel.find('.raw_materials'),
		    $ppList: JQuery              = $variationsContainer.length ? $variationsContainer.find('.product_parts') : this.$bomDataPanel.find('.product_parts'),
		    rmCost: number               = parseFloat($rmList.find('.bom-list-total .total-badge').text() || '0'),
		    ppCost: number               = parseFloat($ppList.find('.bom-list-total .total-badge').text() || '0'),
		    totalBomCost: number         = rmCost + ppCost;
		
		// Variations.
		if ($variationsContainer.length) {
			
			let $curAtumPanel: JQuery = $bomList.closest('.atum-data-panel');
			
			$curAtumPanel.find('.total-bom-cost .total-badge').text(totalBomCost);
			
			// If the purchase price must be synced, update it.
			if ($curAtumPanel.find('#_sync_purchase_price').is(':checked')) {
				$('#variation_purchase_price' + $variationsContainer.index()).val(totalBomCost);
			}
			
		}
		// Regular products.
		else {
			
			this.$bomDataPanel.find('.total-bom-cost .total-badge').text(totalBomCost);
			
			// If the purchase price must be synced, update it.
			if (this.$bomDataPanel.find('#_sync_purchase_price').is(':checked')) {
				this.$bomDataPanel.find('.sync_purchase_price').find('.alert').slideDown();
				this.$wcMetaBox.find('#_purchase_price').val(totalBomCost);
			}
			
		}
		
	}
	
	/**
	 * Show/Hide the extra fields needed for purchasing the BOM product in shop.
	 *
	 * @param {JQuery} $button
	 */
	toggleExtraFields($button: JQuery) {
		
		let $variationsContainer: JQuery = $button.closest('.woocommerce_variation'),
		    $taxFields: JQuery,
		    $shippingFields: JQuery,
		    $sellingFields: JQuery,
		    $wrapper: JQuery,
		    classes: string;
		
		// Variations.
		if ($variationsContainer.length) {
			
			const variationIndex: number = $variationsContainer.index();
			
			$taxFields = $variationsContainer.find(`#variable_tax_class${ variationIndex }`).parent().parent();
			$shippingFields = $variationsContainer.find(`#variable_weight${ variationIndex }, #product_length`).closest('.form-field');
			$sellingFields = $variationsContainer.find(`.variable_backorders${ variationIndex }_field, ._backorders_field, ._out_stock_threshold_field`);
			$wrapper = $variationsContainer;
			classes = '';
			
		}
		// Regular products.
		else {
			
			$taxFields = this.$wcMetaBox.find('._tax_status_field').parent();
			$shippingFields = this.$wcMetaBox.find('.product_data_tabs').find('.shipping_options');
			$sellingFields = this.$wcMetaBox.find('._backorders_field, ._sold_individually_field, ._out_stock_threshold_field');
			$wrapper = this.$bomDataPanel;
			classes = 'show_if_product-part show_if_raw-material';
			
		}
		
		// When the BOM stock control is enabled, the backorders field is needed if the user wants to set it on the parent product.
		if (this.settings.get('bomStockControl') === 'yes') {
			$sellingFields = $sellingFields.not('._backorders_field');
		}
		
		// Wait until WC has completed the field visibility adjustments.
		setTimeout( () => {
			
			if (this.isPurchasable === 'yes') {
				$taxFields.add($shippingFields).addClass(classes).slideDown('fast');
				$sellingFields.show();
			}
			else {
				$taxFields.add($shippingFields).removeClass(classes).slideUp('fast');
				$sellingFields.hide();
			}
			
			this.maybeToggleBomStockControlFields($wrapper);
			
		}, 500);
		
		
	}
	
	/**
	 * Check whether we should hide/show the BOM Stock Control fields
	 *
	 * @param {JQuery} $wrapper
	 */
	maybeToggleBomStockControlFields($wrapper: JQuery) {
		
		if (this.settings.get('bomStockControl') !== 'yes') {
			return;
		}
		
		let $bomBuilders: JQuery           = $wrapper.find('.bom-builder'),
		    $bomStockControlFields: JQuery = $wrapper.find('.bom-stock-control-fields').length ? $wrapper.find('.bom-stock-control-fields') : this.$wcMetaBox.find('.bom-stock-control-fields'),
		    $stockField: JQuery            = $wrapper.is('.woocommerce_variation') ? $wrapper.find(`.variable_stock${ $wrapper.index() }_field` ) : this.$wcMetaBox.find('._stock_field');
		
		$bomBuilders = $bomBuilders.filter( (index: number, elem: Element) => {
			return $(elem).data('has-bom') === 'yes'
		});
		
		if ($bomBuilders.length) {
			
			$bomStockControlFields.show();
			$stockField.hide();
			
			if (!this.isBOM) {
				this.$manageStockCheckboxesFiltered.prop({
					disabled: true,
					checked : true,
					title   : this.settings.get('manageStockMsg'),
				});
			}
			
		}
		else {
			
			$bomStockControlFields.hide();
			$stockField.show();
			
			if (!this.isBOM) {
				this.$manageStockCheckboxesFiltered.prop('disabled', false);
			}
			
		}
		
	}
	
	/**
	 * Check whether we should disable the Allow Backorders field
	 *
	 * @param {JQuery} $wrapper
	 */
	maybeDisableAllowBackorders($wrapper: JQuery) {
	
		const productId: number = $wrapper.hasClass('woocommerce_variation') ?
			$wrapper.find(`[name="variable_post_id[${ $wrapper.index('.woocommerce_variation') }]"]`).val() :
			$('#post_ID').val();
		
		const allowBackOrderFields: any = this.settings.get('allowBackordersField');
		
		if (
			allowBackOrderFields && Object.keys(allowBackOrderFields).length &&
			allowBackOrderFields.hasOwnProperty(productId) && !allowBackOrderFields[productId]
		) {
			$wrapper.find('[name*="_backorders"]').each( (index: number, elem: Element) => {
			
				const $elem: JQuery = $(elem);
				
				// Multi-Inventory compatibility.
				if ($elem.attr('name').indexOf('atum_mi') === -1 || $elem.closest('.inventory-group.main').length) {
					$elem.prop('disabled', true).val('no').attr('title', this.settings.get('allowBackordersMsg'));
				}
				
			});
		}
	
	}
	
	/**
	 * Show/Hide the Stock Quantity field
	 */
	maybeToggleStockfield() {
		
		if (this.settings.get('bomStockControl') !== 'yes') {
			return;
		}
		
		const $variationsPanel: JQuery = this.$wcMetaBox.find('.woocommerce_variations');
		
		if ($variationsPanel.length && $variationsPanel.children().length) {
			
			$variationsPanel.find('.woocommerce_variation').each( (index: number, elem: Element) => {
				
				const $variationWrapper: JQuery = $(elem);
				
				if ($variationWrapper.find('.bom-stock-control-fields').length) {
					
					const $stockField: JQuery    = $variationWrapper.find(`.variable_stock${ index }_field`),
					      $mainInventory: JQuery = $variationWrapper.find('.inventory-group.main'); // MI compatibility.
					
					if ($variationWrapper.find('.bom-stock-control-fields').css('display') === 'none') {
						
						$stockField.show();
						
						// MI compatibility: show the stock field within the main inventory when present.
						if ($mainInventory.length) {
							$mainInventory.find(`._stock_${ $mainInventory.data('id') }${ index }_field`).show();
						}
						
					}
					else {
						
						$stockField.hide();
						
						// MI compatibility: hide the stock field within the main inventory when present.
						if ($mainInventory.length) {
							$mainInventory.find(`._stock_${ $mainInventory.data('id') }${ index }_field`).hide();
						}
						
					}
					
				}
				
				// For the products with calculated stock quantity, remove the original_stock field to avoid problems when saving.
				if ($variationWrapper.find(`#calc_stock_quantity${ index }`).length) {
					$variationWrapper.find(`[name="variable_original_stock[${ index }]"]`).remove();
				}
				
			});
		
		}
		else {
			
			if (this.$wcMetaBox.find('.bom-stock-control-fields').length) {
				
				const $stockField: JQuery    = this.$wcMetaBox.find('._stock_field'),
				      $mainInventory: JQuery = this.$wcMetaBox.find('.inventory-group.main'); // MI compatibility.
				
				if (this.$wcMetaBox.find('.bom-stock-control-fields').css('display') === 'none') {
					
					$stockField.show();
					
					// MI compatibility: show the stock field within the main inventory when present.
					if ($mainInventory.length) {
						$mainInventory.find(`._stock_${$mainInventory.data('id')}_field`).show();
					}
					
				}
				else {
					
					$stockField.hide();
					
					// MI compatibility: hide the stock field within the main inventory when present.
					if ($mainInventory.length) {
						$mainInventory.find(`._stock_${$mainInventory.data('id')}_field`).hide();
					}
					
				}
				
			}
			
			// For the products with calculated stock quantity, remove the original_stock field to avoid problems when saving.
			if (this.$wcMetaBox.find(`#calc_stock_quantity`).length) {
				this.$wcMetaBox.find(`[name="_original_stock"]`).remove();
			}
			
		}
	
	}
	
	/**
	 * Show/Hide the variation fields
	 */
	toggleVariationFields() {
		
		const $variationsPanel: JQuery = this.$wcMetaBox.find('.woocommerce_variations');
		
		$variationsPanel.find('.show_if_variable').show();
		$variationsPanel.find('.hide_if_variable').hide();
		
		// Adjust variation fields' visibility.
		if (this.productType === 'variable-product-part' || this.productType === 'variable-raw-material') {
			
			$variationsPanel.find('.woocommerce_variation').each( (index: number, elem: Element) => {
				
				const $variationWrapper: JQuery = $(elem);
				
				this.setPurchasable($variationWrapper.find('.purchasable_buttons input:radio:checked'));
				this.toggleExtraFields($variationWrapper.find('.purchasable_buttons'));
			});
			
			if (this.productType === 'variable-product-part') {
				$variationsPanel.find('.show_if_variation-product-part').show();
				$variationsPanel.find('.hide_if_variation-product-part').hide();
			}
			else {
				$variationsPanel.find('.show_if_variation-raw-material').show();
				$variationsPanel.find('.hide_if_variation-raw-material').hide();
			}

            $variationsPanel.find('.show_if_variable-subscription').hide();
		}
		
	}
	
	/**
	 * Show the change confirmation popup
	 *
	 * @param {Function} accept
	 * @param {Function} cancel
	 */
	confirmChange(accept: Function, cancel: Function) {
		
		this.swal({
			title            : this.settings.get('areYouSure'),
			text             : this.settings.get('insufficientStock'),
			type             : 'warning',
			showCancelButton : true,
			confirmButtonText: this.settings.get('proceed'),
			cancelButtonText : this.settings.get('cancel'),
		})
		.then( () => {
			if (typeof accept === 'function') {
				accept();
			}
		},
		(dismiss: string) => {
			if (typeof cancel === 'function') {
				cancel(dismiss);
			}
		})
		.catch(this.swal.noop);
		
	}
	
	/**
	 * Apply a bulk action for the selected BOM items
	 *
	 * @param {JQuery} $button
	 */
	applyBulkAction($button: JQuery) {
		
		let $bomList: JQuery      = $button.closest('.bom-builder').find('.linked-boms'),
		    $selectedBoms: JQuery = $bomList.find('.select-row').filter(':checked'),
		    bulkAction: string    = $button.siblings('.bom-bulk-action').val();
		
		if (!$selectedBoms.length) {
			
			this.swal({
				type             : 'warning',
				text             : this.settings.get('selectBoms'),
				confirmButtonText: this.settings.get('ok')
			});
			
		}
		else if (!bulkAction) {
			
			this.swal({
				type             : 'warning',
				text             : this.settings.get('selectBulkAction'),
				confirmButtonText: this.settings.get('ok')
			});
			
		}
		else {
			
			// Do the chosen action in bulk.
			switch (bulkAction) {
				
				case 'remove-bom':
					$selectedBoms.not('.select-all').each( (index: number, elem: any) => this.removeBom( $(elem).closest('.linked-bom') ) );
					break;
				
			}
			
		}
		
	}

	/**
	 * Sets the selector for the manage stock checkboxes that we need to control
	 */
	setManageStockSelector() {

		let manageStockSelector: string = '.variable_manage_stock, [name*="[_manage_stock]"]';

		if (this.productType.indexOf('variable') === -1) {
			manageStockSelector += ', #_manage_stock';
		}

		this.$manageStockCheckboxes = this.$wcMetaBox.find( ':checkbox' )
			.filter( manageStockSelector );

		this.$manageStockCheckboxesFiltered = this.$manageStockCheckboxes.filter( ( index: number, elem: Element ) => {

				const $elem: JQuery = $( elem );

				// Multi-Inventory compatibility.
				if ( $elem.attr( 'name' ).indexOf( 'atum_mi' ) > -1 ) {

					// If BOM control stock enabled and if it doesn't have linked BOMs all inventories are managed.
					if ( this.settings.get( 'bomStockControl' ) === 'yes' && ! this.hasLinkedBOMs ) {
						return true;

					}
					// (only the main inventory's manage stock should be controlled here).
					else if ( ! $elem.closest( '.inventory-group' ).hasClass( 'main' ) ) {
						return false;
					}

				}

				return true;

			} );

	}
	
}
