	
    <div class="esig-settings-wrap">
        <table class="form-table esig-settings-form">

            <tbody>
            <h3><?php _e('Customize The Document Signing Page', 'esig-ulab'); ?></h3>
            <tr> 
                <th> <label for="Document page header color" id="header-color-label"><?php _e('Document Page Header Color', 'esig-ulab') ?></label></th>
                <td> 
                    <input name="esig-front-header-color" id="esig-front-header-color" type="textbox" value="<?php echo Esig_branding_signing_page::get_header_color(); ?>" class="esig-color-picker" />
                </td>
            </tr>
           

            <tr>
                <th><label for="Document Page Header Color" id="footer-color-label"><?php _e('Document Page Header Color', 'esig-ulab') ?></label></th>
                <td>
                    <input name="esig-front-footer-color" id="esig-front-footer-color" type="textbox" value="<?php
                          echo Esig_branding_signing_page::get_footer_color();
                           ?>" class="esig-color-picker" />
                </td>
            </tr>

            
            </tbody>
        </table>
    </div>
