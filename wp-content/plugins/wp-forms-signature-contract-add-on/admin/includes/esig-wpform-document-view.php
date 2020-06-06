<?php
/**
 *
 * @package ESIG_WPFORM_DOCUMENT_VIEW
 * @author  Arafat Rahman <arafatrahmank@gmail.com>
 */



if (! class_exists('esig-wpform-document-view')) :
class esig_wpform_document_view {
    
    
            /**
        	 * Initialize the plugin by loading admin scripts & styles and adding a
        	 * settings page and menu.
        	 * @since     0.1
        	 */
        	final function __construct() {
                        
        	}
        	
        	/**
        	 *  This is add document view which is used to load content in 
        	 *  esig view document page
        	 *  @since 1.1.0
        	 */
        	
        	final function esig_wpform_document_view()
        	{
        	    
        	    if(!function_exists('WP_E_Sig'))
                                return ;
                    
                    
                    
                    
        	    
        	   
        	    $assets_dir = ESIGN_ASSETS_DIR_URI;
        	    
                    
        	   $more_option_page = ''; 
        	   
        	    
        	    $more_option_page .= '<div id="esig-wp-option" class="esign-form-panel" style="display:none;">
        	        
        	        
                	               <div align="center"><img src="' . $assets_dir .'/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;"></div>
                    			
                                    
                    				<div id="esig-wpform-form-first-step">
                        				
                                        	<div align="center" class="esig-popup-header esign-form-header">'.__('What Are You Trying To Do?', 'esig').'</div>
                                            	
                        				<p id="create_wpform" align="center">';
                                	    
                                	    $more_option_page .=	'
                        			
                        				<p id="select-wpform-form-list" align="center">
                                	    
                        		        <select data-placeholder="Choose a Option..." class="chosen-select" tabindex="2" id="esig-wpform-id" name="esig-wpform-id">
                        			     <option value="sddelect">'.__('Select a WP form', 'esig').'</option>';
                                            
                                            if(!class_exists('WPForms_Form_Handler'))
                                                        return ;
                                            
                                            $wpform = new WPForms_Form_Handler();
                                            $wp_form = $wpform->get($id = '', $args = array());
                                          
                                            
                                         
                                            if(!empty($wp_form)){
                                            
                                	    foreach($wp_form as $form)
                                	    {
                                             
                                                
                                	       
                                	        $more_option_page .=	'<option value="'. $form->ID . '">'.$form->post_title.'</option>';
                                	    }
                                            }
                                           
                                	    $more_option_page .='</select>
                                	    
                        				</p>
                         	  
                                	    </p>
                                	    
                                        <p id="upload_wpform_button" align="center">
                                           <a href="#" id="esig-wpform-create" class="button-primary esig-button-large">'.__('Next Step', 'esig').'</a>
                                         </p>
                                     
                                    </div>  <!-- Frist step end here  --> ';
                            
                                    
                 $more_option_page .='<!-- Cf7 form second step start here -->
                                            <div id="esig-wpform-second-step" style="display:none;">
                                            
                                        	<div align="center" class="esig-popup-header esign-form-header">'.__('What WP form field data would you like to insert?', 'esig').'</div>
                                            
                                            <p id="esig-wpform-field-option" align="center">
                               



                                             </p>
                                            
<p id="select-wpform-field-display-type" align="center">
                                	    
                        		        <select data-placeholder="Choose a Option..." class="chosen-select" tabindex="2" id="esig-wpform-form-id" name="esig_wpform_value_display_type">
                        			     <option value="value">'.__('Select a display type', 'esig').'</option>
                                          
                                         
                                           <option value="value">Display value</option>
                                           <option value="label">Display label</option>
                                           <option value="label_value">Display label + value</option>';
                                	   
                                           
                                	    $more_option_page .='</select>
                                	    
                        				</p>
                                            
                                             <p id="upload_wpform_button" align="center">
                                           <a href="#" id="esig-wpform-insert" class="button-primary esig-button-large" >'.__('Add to Documentt', 'esig').'</a>
                                         </p>
                                            
                                            </div>
                                    <!-- wpform form second step end here -->';           
                                    
                                    
        	    
        	    $more_option_page .= '</div><!--- wpform option end here -->' ;
        	    
        	    
        	    return $more_option_page ; 
        	}
        	
        	
	   
    }
endif ; 

