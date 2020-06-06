/* =======================================
   MANUFACTURING CENTRAL LIST TABLES
   ======================================= */

import Settings from '../../../../../../atum-stock-manager-for-woocommerce/assets/js/src/config/_settings';

export default class MCListTable {
	
	forceEdit: boolean = false;
	swal: any = window['swal'];
	
	constructor(
		private settings: Settings
	) {
		
		// Check if there is enough stock to cover the committed materials before editing.
		$('body').on('click', '.popover .set', (evt: JQueryEventObject) => {
			
			// Only run in Manufacturing Central list.
			if (!$('.atum-list-table.manufacturing-central-list').length) {
				return false;
			}
			
			let $button: JQuery   = $(evt.currentTarget),
			    newValue: number  = parseFloat($button.siblings('.meta-value').val()),
			    $metaCell: JQuery = $(`[data-popover="${ $button.closest('.popover').attr('id') }"]`);
			
			// Only run on stock cells
			if ($metaCell.data('meta') !== 'stock') {
				return false;
			}
			
			const committedAmt: number = parseFloat($metaCell.closest('tr').children('.calc_committed').text());
			
			if (this.settings.get('bomStockControl') === 'yes') {
				this.forceEdit = true;
				return false;
			}
			else {
				
				if (newValue >= committedAmt || this.forceEdit === true) {
					this.forceEdit = false;
					return false;
				}
				
				evt.stopImmediatePropagation();
				
				this.swal({
					title            : this.settings.get('areYouSure'),
					text             : this.settings.get('insufficientStock'),
					type             : 'warning',
					showCancelButton : true,
					confirmButtonText: this.settings.get('proceed'),
					cancelButtonText : this.settings.get('cancel'),
				})
				.then(() => {
					
					this.forceEdit = true;
					$button.click();
					
				})
				.catch(this.swal.noop);
				
			}
			
		});
		
	}
	
}
