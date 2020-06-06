<?php
   $job = get_post_meta( get_the_ID(), 'tbay_testimonial_job', true );
   $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
?>
<div class="testimonials-body media">
   
   <div class="testimonials-profile"> 
      <div class="wrapper-avatar">
         <div class=" testimonial-avatar tbay-image-loaded">
            <?php echo puca_tbay_get_attachment_image_loaded($post_thumbnail_id, 'puca_avatar_post_carousel'); ?>
         </div>
      </div>
      <div class="testimonial-meta">
         <span class="name-client"> <?php the_title(); ?></span>
         <span class="job"><?php echo esc_html($job); ?></span>
      </div>
   </div>
   <div class="description media-body">
     <?php echo get_the_excerpt(); ?>
   </div>
   
</div>