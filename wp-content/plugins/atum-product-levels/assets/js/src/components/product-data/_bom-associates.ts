/* =======================================
   BOM ASSOCIATES TAB
   ======================================= */

import EnhancedSelect from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_enhanced-select';
import Popover from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_popover';
import Tooltip from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/components/_tooltip';

export default class BomAssociates {
	
	$bomAssociatesPanel: JQuery;
	
	constructor(
		private tooltip: Tooltip,
		private popover: Popover,
		private enhancedSelect: EnhancedSelect
	) {
		
		this.$bomAssociatesPanel = $('#bom_associates_data');
		
		// Bind selects.
		this.enhancedSelect.doSelect2( this.$bomAssociatesPanel.find('.atum-select2') );
		
		// Bind popovers.
		this.bindPopovers();
		
		// Add tooltips.
		this.tooltip.addTooltips(this.$bomAssociatesPanel);
		
	}
	
	/**
	 * Bind the editable meta cells within the BOM associates table
	 */
	bindPopovers() {
		
		// Runs once the popover's set-meta button is clicked.
		$('body').on('click', '.popover button.set', (evt: JQueryEventObject) => {
			
			let $button: JQuery   = $(evt.currentTarget),
			    $popover: JQuery  = $button.closest('.popover'),
			    popoverId: string = $popover.attr('id'),
			    $setMeta: JQuery  = $(`[data-popover="${ popoverId }"]`),
			    newValue: any     = $popover.find('input').val() || '';
			
			// Check that the meta exists and is placed within the BOM associates meta box.
			if ($setMeta.length && $setMeta.closest( this.$bomAssociatesPanel ).length) {
				
				$.ajax({
					url     : window['ajaxurl'],
					method  : 'post',
					dataType: 'json',
					data    : {
						action    : 'atum_set_bom_control_prop',
						token     : $setMeta.closest('table').data('nonce'),
						meta      : $setMeta.data('meta'),
						value     : newValue,
						product_id: $setMeta.closest('tr').data('id'),
					},
					beforeSend: () => $button.prop('disabled', true),
					success: (response: any) => {
						
						const messageAtts = {
							text : response.data,
							class: `alert ${ response.success === true ? 'alert-success' : 'alert-danger' }`,
						};
						
						$popover.find('.popover-content').prepend( $('<div/>', messageAtts) );
						
						if (true === response.success) {
							$setMeta.text(newValue);
							setTimeout( () => this.popover.destroyPopover($setMeta), 1500);
						}
						
					},
				});
				
			}
			
		});
		
	}
	
}
