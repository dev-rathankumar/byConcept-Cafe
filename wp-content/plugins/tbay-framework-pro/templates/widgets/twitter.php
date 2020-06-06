<?php
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo ($before_title)  . trim( $title ) . $after_title;
}

$chrome = '';

if ($instance['show_header'] == 0) {
    $chrome .= 'noheader ';
}
if ($instance['show_footer'] == 0) {
   $chrome .= 'nofooter ';
}
if ($instance['show_border'] == 0) {
   $chrome .= 'noborders ';
}

if ($instance['transparent'] == 0) {
    $chrome .= 'transparent';
}
$js = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
//Test
?>
<div class="widget-twitter block">
	<div class="block_content">
		<div id="tbay-twitter<?php echo esc_attr( $user ); ?>" class="tbay-twitter">
			<a class="twitter-timeline" data-dnt="true" <?php echo !empty($width) ? 'width="'.esc_attr( $width ).'px"' : ''; ?> <?php echo !empty($height) ? 'height="'.esc_attr( $height ).'px"' : ''; ?> data-chrome="<?php echo esc_attr( $chrome ); ?>" data-border-color="<?php echo esc_attr( $border_color ); ?>"  data-tweet-limit="<?php echo esc_attr($limit); ?>" data-link-color="<?php echo esc_attr( $link_color ); ?>"  data-show-replies="<?php echo esc_attr( $show_replies ); ?>" href="https://twitter.com/<?php echo esc_attr( $user ); ?>"  data-widget-id="<?php echo esc_attr( $twitter_id ); ?>">Tweets by @<?php echo esc_html( $user ); ?></a>
			<?php print trim($js); ?>
		</div>	
	</div>
</div>
<script type="text/javascript">
	(function($) {
		// Customize twitter feed
		var hideTwitterAttempts = 0;
		function hideTwitterBoxElements() {
		 setTimeout( function() {
		  if ( $('[id*=tbay-twitter<?php echo esc_js( $user ); ?>]').length ) {
		   $('#tbay-twitter<?php echo esc_js( $user ); ?> iframe').each( function(){

		    var ibody = $(this).contents().find( 'body' );
			var show_scroll = <?php echo esc_js( $show_scrollbar ); ?>; 
			var height =  <?php echo esc_js( $height ); ?>+'px'; 
		    if ( ibody.find( '.timeline-Body .timeline-TweetList li.timeline-TweetList-tweet' ).length ) {
				ibody.find( '.timeline-Tweet-text' ).css( 'color', '<?php echo esc_js( $text_color ); ?>' );
				ibody.find( '.SummaryCard-content' ).css( 'color', '<?php echo esc_js( $text_color ); ?>' );
				ibody.find( '.timeline-Tweet-author' ).css( 'color', '<?php echo esc_js( $name_color ); ?>' );
				if(show_scroll == 1){
					ibody.find( '.timeline .stream' ).css( 'max-height', height );
					ibody.find( '.timeline .stream' ).css( 'overflow-y', 'auto' );	
					ibody.find( '.timeline .twitter-timeline' ).css( 'height', 'inherit !important' );	
				}
		    } else {
		     $(this).hide();
		    }
		   });
		  }
		  hideTwitterAttempts++;
		  if ( hideTwitterAttempts < 3 ) {
		   hideTwitterBoxElements();
		  }
		 }, 1500);
		}
		// somewhere in your code after html page load
		hideTwitterBoxElements();
	})(jQuery);
</script>

