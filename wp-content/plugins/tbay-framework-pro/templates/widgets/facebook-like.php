<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo ($before_title)  . trim( $title ) . $after_title;
}

if($page_url): ?>
	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.5";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>


	<div class="fb-page" data-href="<?php echo $page_url; ?>" data-tabs="timeline"
		data-width="<?php echo esc_attr( $width ); ?>" data-height="<?php echo esc_attr( $height ); ?>"
		data-small-header="<?php echo esc_attr( $show_header ); ?>" data-adapt-container-width="true"
		data-hide-cover="true" data-show-facepile="<?php echo esc_attr($show_faces); ?>">
		
	</div>


<?php endif;?>