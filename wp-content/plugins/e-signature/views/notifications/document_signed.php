<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

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
    <div style="background-color:#efefef;margin:0;padding:0;font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;width:100%;max-width:600px;margin: 0 auto;">
         <!--[if mso]>
                        <table border="0" width="100%" cellpadding="0" bgcolor="#efefef" cellspacing="0" class="container" style="width:100%;" align="center">
                         <tr>
                            <td  align="center">
                        <table border="0" width="600" cellpadding="0" cellspacing="0" style="width: 600px;">
                            <tr>
                                <td valign="top">
        <![endif]-->
      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr style="border-collapse:collapse"> <td style="font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse" align="center" bgcolor="#efefef">
                      <table border="0" cellpadding="10" cellspacing="0" width="100%"> <tbody><tr style="border-collapse:collapse"><td style="font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse" align="left" width="100%"> <div style="margin:0 0 20px 0">
                        <?php $logo_alignment= apply_filters('esig-logo-alignment','',esigget('wpUserId', $data)); $logo_alignment = !empty( $logo_alignment) ?  $logo_alignment : 'style="text-align:left;"' ; ?>  <div <?php echo $logo_alignment;  ?>> <?php echo $data['esig_logo']; ?></div>
                                                                                                                                                                                                        <p <?php echo $logo_alignment;  ?>><?php echo $data['esig_header_tagline']; ?><br></p></div>
                                                                                                                                                                                                        <table width="100%">
                                                                                                                                                                                                        <tbody><tr><td style="background-color:#f7fafc;padding:8px 10px;border:1px solid #ccc;color:#444;font-weight:bold;margin-bottom:10px;text-align:center" bgcolor="#F7FAFC"><?php _e('Document has been signed','esig') ; ?></td></tr></tbody></table>
                                                                                                                                                                                                        <table width="100%" style="width: 100%;border-collapse: collapse; "><tbody><tr><td style="background-color:#ffffff;border:1px solid #ccc;padding:15px;" bgcolor="FFFFFF"><h1 style="font-size:18px;margin:0 0 10px 0;font-weight:bold"><?php _e('Document Name:','esig'); ?> <?php echo $data['document_title']; ?></h1><?php _e('Document ID:', 'esig' );?> (<?php echo $data['document_checksum']; ?>)
                                                                                                                                                                                                        <p style="line-height:1.4em;font-size:14px;margin:10px 0px"> <span style="color:#8c8c8c"><?php _e('From:','esig');?> <?php echo $data['sender']; ?> (<a href="mailto:<?php echo $data['owner_email']; ?>" target="_blank"><?php echo $data['owner_email']; ?></a>) </span> </p>
                                                                                                                                                                                                        <hr style="color:#cccccc;background-color:#cccccc;min-height:1px;border:none">
                                                                                                                                                                                                        <p style="line-height:1.4em;font-size:14px;font-family:sans-serif;margin:10px 0px"><?php _e('Hi','esig'); ?> <?php echo $data['signer_name']; ?>,<br><?php $custom_message = apply_filters('esig_signer_confirmation_custom_message','',$document_checksum=$data['document_checksum']); echo $custom_message; ?> <br><?php _e('All signees have signed this document.','esig'); ?><br><?php _e(' Audit Trail Serial#:','esig');?> <?php echo $data['document_id']; ?></p>
                                                                                                                                                                                                        <hr style="color:#cccccc;background-color:#cccccc;min-height:1px;border:none">
                                                                                                                                                                                                        <div style="margin:20px 0px 20px 0px">
                                                                                                                                                                                                        <!-- signed button start here --> 
                                                                                                                                                                               
                                                                                                                                                                                                        <table cellspacing="0" cellpadding="0"> <tr>   <td align="center" width="200" height="50" bgcolor="<?php echo $data['background_color']; ?>" style="color: #ffffff; display: block;"><a href="<?php echo $data['view_url']; ?>" style="font-size:14px;font-family:sans-serif; text-decoration: none; line-height:50px; width:100%; display:inline-block"><span style="color: #ffffff;"><?php _e('View Signed Document','esig'); ?></span></a></td>   </tr> </table> 
                                                                                                                                                                                                        <!-- signed button end here --> </div>                               <hr style="color:#cccccc;background-color:#cccccc;min-height:1px;border:none">                               <p style="margin:10px 0px;font-size:14px;line-height:1.4em;color:#ff0000">                               	<?php _e('Warning: Do not forward this email to others or else they will have access to your document (on your behalf).', 'esig' );?></p>                             </td>                           </tr>                         </tbody>                       </table>                       <?php                                             $footer_enable = apply_filters('esig-email-footer-text-enable','',esigget('wpUserId', $data));                                                if($footer_enable !='hide')                         {                                               ?>                       <table width="100%">                         <tbody>                           <tr>                             <td style="background-color:#ffffff;margin-top:10px;border:1px solid #ccc;padding:40px 40px 30px 40px" bgcolor="#FFFFFF">                               <h1 style="font-size:18px;color: #9d9e9e;margin:0 0 10px 0;font-weight:bold">                                 <?php echo $data['esig_footer_head']; ?>                               </h1>                               <p style="line-height:1.4em;font-size:14px;color: #9d9e9e;margin:10px 0px"><?php echo $data['esig_footer_text']; ?> </p>                             </td>                           </tr>                         </tbody>                       </table>                       <?php                                             }                                             ?>                     </td>                   </tr>                 </tbody>               </table>             </td>           </tr>         </tbody>       </table>
      
          <table style="width:100%;background:#cccccc;border-top:1px solid #999999;border-bottom:1px solid #999999;padding:0 0 30px 0" border="0" cellpadding="0" cellspacing="0" width="100%">         <tbody>           <tr style="border-collapse:collapse">             <td style="font-family:'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse" align="center" bgcolor="#cccccc"> <table style="margin-top:20px" border="0" cellpadding="20" cellspacing="0" width="100%"> <tbody> <tr style="border-collapse:collapse"> <td style="padding:0px 12px 0px 0px;vertical-align:top;font-family:'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:12px;line-height:1.4em;border-collapse:collapse;color:#555" align="center"> <br><a href="#" target="_blank"><img src="<?php echo $data['assets_dir']; ?>/images/verified-email.jpg" alt="WP E-Signature" border="0" style="width: 175px;margin-top: -8px;" class="emailClass" height="69" width="200"> 							                     </td>                                     </tr>                 </tbody>               </table>             </td>           </tr>         </tbody>       </table>
      <div class="adL"> </div>
    </div>
                <!--[if mso]>
                            </td>
                        </tr>
                    </table>
                        </td>
                        </tr>
                    </table>
            <![endif]-->
    <div class="adL"> </div>
  </div>
</div>

