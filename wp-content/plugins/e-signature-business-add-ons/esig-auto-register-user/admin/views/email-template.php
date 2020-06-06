
		<div id="email_container">
			<div style="width:570px; padding:0 0 0 20px; margin:50px auto 12px auto" id="email_header">
				<span style="background:#585858; color:#fff; padding:12px;font-family:trebuchet ms; letter-spacing:1px; 
					-moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px; 
					border-top-left-radius:5px;moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px; 
					border-top-right-radius:5px;">
					<?php echo $data['sitename'] ; ?>
				</div>
			</div>
		
		
			<div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px solid DeepSkyBlue;
			color:#454545;line-height:1.5em; " id="email_content">
				
				<h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px 1px solid #bbb">
					<?php _e('New wordpress user has been created.','esig');?>
				</h1>
				
                            <p><p><?php echo sprintf( __('New user,%s has been registered using Wp E-Signature.','esig'),  esigget('wp_username', $data));?></p>
	
	<p>
		<?php _e('Username is ','esig');?><strong style="color:DeepSkyBlue "><?php echo $data['wp_username']; ?></strong> <br>
		<?php _e('Password is ','esig');?><strong style="color:DeepSkyBlue "><?php echo $data['wp_password']; ?></strong> <br>
		
	</p>
	
	
				
				<p style="">
					<?php _e('Warm regards,','esig');?><br>
					
				</p>
				
				
				
			</div>
		</div>
	</body>
</html>