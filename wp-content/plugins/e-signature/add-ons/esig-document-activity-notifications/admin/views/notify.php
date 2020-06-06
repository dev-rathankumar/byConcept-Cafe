<?php 

// To default a var, add it to an array
	$vars = array(
		'esig_logo', // will default $data['esig_logo']
		'esig_header_tagline', 
		'document_title',
		'document_checksum',
		'owner_first_name',
		'signer_name',
		'signer_email',
		'view_url',
		'assets_dir',
		
	);
	$this->default_vals($data, $vars);
?>
<style type="text/css">
    
        .emailClass{
            height:auto !important;
            max-width:200px !important;
            width: 100% !important;
        }
    
</style>

<div id=":zs" class="ii gt m1436f203bed358e3 adP adO">
  <div id=":zr" style="overflow: hidden;">
    <div class="adM"> </div>
    <div style="background-color:#efefef;margin:0;padding:0;font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;width:100%;width:100%">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tbody>
              <tr style="border-collapse:collapse">             
                  <td style="font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse" align="center" bgcolor="#efefef">               
                      <table border="0" cellpadding="20" cellspacing="0" width="100%">                 
                          <tbody>                   
                              <tr style="border-collapse:collapse">                     
                                  <td style="font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse" align="left" width="100%">                       
                                      <div style="margin:0 0 20px 0">
                                          <?php $logo_alignment= apply_filters('esig-logo-alignment','',esigget('wpUserId', $data)); $logo_alignment = !empty( $logo_alignment) ?  $logo_alignment : 'style="text-align:left;"' ; ?> 
                                          <div <?php echo $logo_alignment;  ?>>
                                                <?php echo $data['esig_logo']; ?>                                                                                                                                              
                                          </div>                         
                                          <p <?php echo $logo_alignment;  ?>>
                                                    <?php echo $data['esig_header_tagline']; ?><br>
                                          </p>                       
                                       </div>                                             
            <table width="100%">                         <tbody>                           <tr>                             <td style="background-color:#ffffff;border:1px solid #ccc;padding:40px 40px 30px 40px" bgcolor="FFFFFF">                               <h1 style="font-size:18px;margin:0 0 10px 0;font-weight:bold"><?php  printf( __( 'Document Viewed: %s','esig'),$data['document_title']);?></h1>                               <?php printf( __( 'Document ID: (%s)','esig'),$data['document_checksum']);?>                                                             <hr style="color:#cccccc;background-color:#cccccc;min-height:1px;border:none">                               <p style="line-height:1.4em;font-size:14px;margin:10px 0px"><?php printf( __( 'Hi %s','esig'),$data['owner_first_name'])?>,<br>                                 <br>                                 <?php printf( __( "%s %s  has viewed the document. 							  We'll let you know if they sign it. ","esig"),$data['signer_name'],$data['signer_email']);?>                                 </p> 								<p style="line-height:1.4em;font-size:14px;margin:10px 0px"><?php _e(" If you'd like more information, you can visit the documents page below.","esig") ;?></p>                               <hr style="color:#cccccc;background-color:#cccccc;min-height:1px;border:none">                               <div style="margin:20px 0px 20px 0px">                                 <!-- button style here -->                                 <?php                                                                                 $background_color_bg= apply_filters('esig-invite-button-background-color','',  esigget('wpUserId', $data));                                                                                $background_color = !empty( $background_color_bg) ?  $background_color_bg : '#0083c5' ;                                                                               ?>                                 <!--[if mso]>   <v:rect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo $data['view_url']; ?>" style="height:50px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="<?php echo  $background_color; ?>">     <w:anchorlock/>     <center style="color:#ffffff;font-family:sans-serif;font-size:14px;">        View Viewed Document     </center>   </v:rect>   <![endif]-->   <![if !mso]>   <table cellspacing="0" cellpadding="0"> <tr>   <td align="center" width="200" height="50" bgcolor="<?php echo  $background_color; ?>" style="color: #ffffff; display: block;"><a href="<?php echo $data['view_url']; ?>" style="font-size:14px;font-family:sans-serif; text-decoration: none; line-height:50px; width:100%; display:inline-block"><span style="color: #ffffff;"><?php _e('View Viewed Document','esig');?></span></a></td>   </tr> </table>   <![endif]>                                                                 <!-- button style end here -->                               </div>                               <hr style="color:#cccccc;background-color:#cccccc;min-height:1px;border:none">                               <p style="margin:10px 0px;font-size:14px;line-height:1.4em;color:#ff0000">                               	<?php _e('Warning: Do not forward this email to others or                                 else they will have access to your document (on your behalf).','esig');?></p>                             </td>                           </tr>                         </tbody>                       </table>                     </td>                   </tr>                 </tbody>               </table>             </td>           </tr>         </tbody>       </table>
      <table style="width:100%;background:#cccccc;border-top:1px solid #999999;border-bottom:1px solid #999999;padding:0 0 30px 0" border="0" cellpadding="0" cellspacing="0" width="100%">         <tbody>           <tr style="border-collapse:collapse">             <td style="font-family:'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse" align="center" bgcolor="#cccccc">               <table style="margin-top:20px" border="0" cellpadding="20" cellspacing="0" width="100%">                 <tbody>                   <tr style="border-collapse:collapse">                     <td style="padding: 16px 12px 0px 0px;vertical-align: top;font-family: 'Helvetica Neue',Arial,Helvetica,sans-serif;font-size: 12px;line-height: 1.5em;border-collapse: collapse;color: #555;" align="left"></td>                     <td style="padding:0px 12px 0px 0px;vertical-align:top;font-family:'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:12px;line-height:1.4em;border-collapse:collapse;color:#555" align="left"> <br>                     </td>                     <td style="padding:0px 0px 0px 0px;vertical-align:top;font-family:'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:12px;line-height:1.4em;border-collapse:collapse;color:#555" align="left"> <a href="https://approveme.com" target="_blank"> 					<img src="<?php echo $data['assets_dir']; ?>/images/approveme-badge.png" alt="WP E-Signature" border="0" style="width: 175px;margin-top: -8px;" class="emailClass" height="49" width="154"></a><br>                     </td>                   </tr>                 </tbody>               </table>             </td>           </tr>         </tbody>       </table>
      <div class="adL"> </div>
    </div>
    <div class="adL"> </div>
  </div>
</div>

