<?php 
  $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';
?>  
<div class="entry-content media">

  <?php
  if ( has_post_thumbnail() ) {
      ?>
        <div class="media-left">
          <figure class="entry-thumb">
              <a href="<?php the_permalink(); ?>"  class="entry-image tbay-image-loaded">
                  <?php 
                    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize );
                  ?>
              </a>  
          </figure>
        </div>
      <?php
  }
  ?>
  <div class="media-body">
    <span class="entry-date"><?php echo puca_time_link(); ?></span>
    <?php
        if (get_the_title()) {
        ?>
            <h4 class="entry-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h4>
        <?php
    }
    ?>
    <div class="entry-content-inner clearfix">       
      <?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
      <span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( esc_html__( '0 comment', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
      <span class="entry-view"><i class="icon-user icons"></i> 
        <?php echo puca_get_post_views(get_the_ID()), esc_html__(' views', 'puca'); ?>
      </span>
      <?php endif; ?>
    </div>
  </div>
</div>      
