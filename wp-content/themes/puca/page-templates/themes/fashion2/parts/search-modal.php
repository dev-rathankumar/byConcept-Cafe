<?php 

$_id = puca_tbay_random_key(); 

?>
<div id="search-form-modal-<?php echo esc_attr($_id); ?>" class="search-modal">
	<button type="button" class="btn-search-totop" data-toggle="modal" data-target="#searchformshow-<?php echo esc_attr($_id); ?>">
	   <i class="icon-magnifier"></i>
	</button>
</div>

<div class="modal fade search-form-modal" id="searchformshow-<?php echo esc_attr($_id); ?>" tabindex="-1" role="dialog" aria-labelledby="searchformlable-<?php echo esc_attr($_id); ?>">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="searchformlable-<?php echo esc_attr($_id); ?>"><?php esc_html_e('Search', 'puca'); ?></h4>
      </div>
      <div class="modal-body">
			<?php puca_tbay_get_page_templates_parts( 'productsearchform' ); ?>
      </div>
    </div>
  </div>
</div>

