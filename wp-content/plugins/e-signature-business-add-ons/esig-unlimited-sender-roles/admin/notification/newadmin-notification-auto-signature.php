
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
    <div
style="background-color:#efefef;margin:0;padding:0;font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;width:100%;width:100%">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody>
          <tr style="border-collapse:collapse">
            <td style="font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse"
              align="center" bgcolor="#efefef">
              <table border="0" cellpadding="20" cellspacing="0"
                width="100%">
                <tbody>
                  <tr style="border-collapse:collapse">
                    <td style="font-family:'HelveticaNeue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse"
                      align="left" width="100%">
                      <div style="margin:0 0 20px 0">
                        <div style="text-align:left">
                          <?php echo $data['esig_logo']; ?>
                        </div>
                        <p style="margin:5px 0 0 0;color:#666">
                          <?php echo $data['esig_header_tagline']; ?><br>
                        </p>
                      </div>
                      
                      <table width="100%">
                        <tbody>
                          <tr>
                            <td
                              style="background-color:#ffffff;border:1px
                              solid #ccc;padding:40px 40px 30px 40px"
                              bgcolor="FFFFFF">
                             
                            
                              <p
                                style="line-height:1.4em;font-size:14px;margin:10px
                                0px"><?php printf( __( 'Hi %s','esig'),$data['new_first_name'])?>,
                                </p>
								<p
                                style="line-height:1.4em;font-size:14px;margin:10px
                                0px"><?php printf( __(" A WP E-Signature document, <strong>%s</strong> has been transfered to you! This document on %s was configured to auto add the previous document owner's signature. Since you haven't reviewed this document, we can't add your signature yet. If you would like to continue automatically signing this document you will need to review it.","esig"),$data['document_title'],  home_url());?> 
                                </p>
                             
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
      <table style="width:100%;background:#cccccc;border-top:1px
        solid #999999;border-bottom:1px solid #999999;padding:0 0 30px
        0" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody>
          <tr style="border-collapse:collapse">
            <td style="font-family:'Helvetica
Neue',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.4em;border-collapse:collapse"
              align="center" bgcolor="#cccccc">
              <table style="margin-top:20px" border="0" cellpadding="20"
                cellspacing="0" width="100%">
                <tbody>
                  <tr style="border-collapse:collapse">
                    <td style="padding: 16px 12px 0px 0px;vertical-align: top;font-family: 'Helvetica Neue',Arial,Helvetica,sans-serif;font-size: 12px;line-height: 1.5em;border-collapse: collapse;color: #555;"
                      align="left"></td>
                    <td style="padding:0px 12px 0px
                      0px;vertical-align:top;font-family:'Helvetica
Neue',Arial,Helvetica,sans-serif;font-size:12px;line-height:1.4em;border-collapse:collapse;color:#555"
                      align="left"> <br>
                    </td>
                    <td style="padding:0px 0px 0px
                      0px;vertical-align:top;font-family:'Helvetica
Neue',Arial,Helvetica,sans-serif;font-size:12px;line-height:1.4em;border-collapse:collapse;color:#555"
                      align="center"> <a href="https://approveme.com" target="_blank"><img
src="<?php echo ESIGN_ASSETS_DIR_URI; ?>/images/verified-email.jpg"
                              alt="WP E-Signature" border="0" style="width: 175px;margin-top: -8px;" class="emailClass"
                              height="49" width="154"></a><br>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="adL"> </div>
    </div>
    <div class="adL"> </div>
  </div>
</div>

