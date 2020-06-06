


<div marginwidth="0" marginheight="0">
    <table  border="0" cellpadding="0"
           cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td height="20"><br>
                </td>
            </tr>
            
            <tr>
                <td align="center">
                    <table bgcolor="e4e8eb" border="0" cellpadding="0"
                           cellspacing="0" width="600" align="center">
                        <tbody>
                            <tr>
                                <td height="30"><br>
                                </td>
                            </tr>

                            <tr>
                                <td align="center">
                                    <table bgcolor="cdd0d3" border="0"
                                           cellpadding="0" cellspacing="0" width="580"
                                           align="center">
                                        <tbody>
                                            <tr>
                                                <td align="center">
                                                    <table bgcolor="ffffff" border="0"
                                                           cellpadding="0" cellspacing="0"
                                                           width="578" align="center">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center">
                                                                    <table border="0"
                                                                           cellpadding="0"
                                                                           cellspacing="0" width="540"
                                                                           align="center">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0"
                                                                                           cellpadding="0"
                                                                                           cellspacing="0"
                                                                                           width="540"
                                                                                           align="left">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td height="10"><br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td
                                                                                                    style="font-size:14px;font-family:Helvetica,Arial,sans-serif;line-height:24px"
                                                                                                    align="left">
                                                                                                    <p><?php
                                                                                                    
                                                                                                    _e('Hi','esig');?>  <?php echo esigHtml($data->user_info->first_name); ?>,
  <p>                                                                                                   
<?php                                                                                                      
          echo sprintf(__('You have been copied on <b>%s</b> by %s, which is a public document sent to collect a signature.','esig'),$data->doc->document_title,$data->owner_email) ; ?></p>
            
   
        <p>    <?php _e("There's nothing you need to do. We will email you the final version once the document has been signed.","esig") ; ?></p>
        
         <hr> 
         
             <?php
            
             $background_color_bg = apply_filters('esig-invite-button-background-color','#0083c5',esigget('wpUserId',$data));
             $background_color = !empty( $background_color_bg) ?  $background_color_bg : '#0083c5' ; 
             ?>
         
        <!--[if mso]>
  <v:rect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo $data->signed_link ;?>" style="height:50px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="<?php echo $background_color; ?>">
    <w:anchorlock/>
    <center style="color:#ffffff;font-family:sans-serif;font-size:14px;">
      <?php _e('View Document','esig'); ?>
    </center>
  </v:rect>
  <![endif]-->
  <![if !mso]>
  <table cellspacing="0" cellpadding="0"> <tr> 
  <td align="center" width="200" height="50" bgcolor="<?php echo $background_color ?>" style="color: #ffffff; display: block;">
    <a href="<?php echo $data->signed_link ;?>" style="font-size:14px;font-family:sans-serif; text-decoration: none; line-height:50px; width:100%; display:inline-block">
    <span style="color: #ffffff;">
      <?php _e('View Document','esig'); ?>
    </span>
    </a>
  </td> 
  </tr> </table> 
  <![endif]>

                                                                                                    <hr> 

                                                                                                    <?php echo $data->owner_name ?><br>
                                                                                                    <?php echo $data->organization_name; ?></p>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td height="15"><br>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>

                            </tr>
                            <tr>
                                <td align="center">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="40"><br>
                </td>
            </tr>
        </tbody>
    </table>
</div>

