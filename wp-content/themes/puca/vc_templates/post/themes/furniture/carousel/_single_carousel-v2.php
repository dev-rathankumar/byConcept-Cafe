<?php $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';?>
<?php
  $post_category = "";
  $categories = get_the_category();
  $separator = ' | ';
  $output = '';
  $day = get_the_time('d', $post->ID);
  $month = get_the_time('M', $post->ID);
  if($categories){
    foreach($categories as $category) {
      $output .= '<a href="'.esc_url( get_category_link( $category->term_id ) ).'" title="' . esc_attr( sprintf( esc_html__( 'View all posts in %s', 'puca' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
    }
  $post_category = trim($output, $separator);
  }      
?>
<div class="post-grid v2">
  <article class="post"> 
    <span class="entry-date">
      <span class="day"><?php echo trim($day); ?></span>
      <span class="month"><?php echo trim($month); ?></span>
    </span>
    <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
        <a href="<?php the_permalink(); ?>"  class="entry-image tbay-image-loaded">
            <?php
                $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize );
            ?>
        </a>
    </figure>
      <div class="entry-header">
      <?php
        if (get_the_title()) {
        ?>
          <h4 class="entry-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h4>
        <?php
      }
      ?>
      <span class="entry-view"><i class="icon-people icons"></i><?php echo puca_get_post_views( get_the_ID(), esc_html__(' views', 'puca') ); ?></span>
      <?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
         <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comments', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
      <?php endif; ?>
    </div>  
    <a href="<?php the_permalink(); ?>" class="readmore" title="<?php esc_html_e( 'Read more', 'puca' ); ?>"><?php esc_html_e( 'Read more', 'puca' ); ?></a>
  </article>
    
</div>
