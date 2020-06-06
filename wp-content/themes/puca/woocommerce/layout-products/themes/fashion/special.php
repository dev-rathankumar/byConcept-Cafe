<?php
$screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
$screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
$screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
$screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;

$count = 0;
$data_responsive = '';

if( isset($responsive_type) ) {    

    $data_responsive .= ' data-xlgdesktop='. $columns .'';
    $data_responsive .= ' data-desktop='. $screen_desktop .'';
    $data_responsive .= ' data-desktopsmall='. $screen_desktopsmall .'';
    $data_responsive .= ' data-tablet='. $screen_tablet .'';
    $data_responsive .= ' data-mobile='. $screen_mobile .''; 

}

$active_theme = puca_tbay_get_part_theme();

$columns = isset($columns) ? $columns : 4;
$class = ($columns <= 1) ? 'w-products-list' : 'products products-grid';
?>
<div class="<?php echo esc_attr( $class ); ?>">


    <?php if( isset($attr_row) && !empty($attr_row) ) : ?>
        <div <?php echo trim($attr_row); ?>>
    <?php else : ?>
        <div class="row grid" <?php echo esc_attr($data_responsive); ?>>
    <?php endif; ?>
            
		<?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>

			<?php wc_get_template( 'item-product/'.$active_theme.'/special.php', array('columns' => $columns, 'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile) ); ?>

		<?php endwhile; ?>
	</div>
</div>

<?php wp_reset_postdata(); ?>