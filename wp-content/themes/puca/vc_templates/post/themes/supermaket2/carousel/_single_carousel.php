<?php 
  $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';
?>
<?php
  $post_category = "";
  $categories = get_the_category();
  $separator = ' , ';
  $output = '';
  if($categories){
    foreach($categories as $category) {
      $output .= '<a href="'.esc_url( get_category_link( $category->term_id ) ).'" title="' . esc_attr( sprintf( esc_html__( 'View all posts in %s', 'puca' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
    }
  $post_category = trim($output, $separator);
  }      
?>
<div class="post-grid">
    <article class="post">   
        <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
            <div class="entry-info">
              <span class="day"><?php the_time( 'd' ); ?></span>
              <span class="month"><?php the_time( 'M' ); ?></span>
            </div>  
            <a href="<?php the_permalink(); ?>"  class="entry-image tbay-image-loaded">
                <?php
                    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize );
                ?>
            </a>  
        </figure>
        <div class="entry-content">
            <div class="entry-category">
              <?php echo trim($post_category); ?>
            </div>
            <div class="entry-meta">
                <?php
                    if (get_the_title()) {
                    ?>
                        <h4 class="entry-title">
                            <a href="<?php the_permalink(); ?>"><?php echo puca_tbay_subschars(get_the_title(), 50, '...' ); ?></a>
                        </h4>
                    <?php
                }
                ?>
                  
            </div>
            <?php
                if (! has_excerpt()) {
                    echo "";
                } else {
                    ?>
                        <div class="readmore"><a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read More', 'puca' ); ?>"><?php esc_html_e( 'Read More', 'puca' ); ?></a></div>
                    <?php
                }
            ?>
           
        </div>
    </article>
    
</div>
