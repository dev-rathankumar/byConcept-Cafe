(function ( $ ) {
	"use strict";

	$(function () {

		tinymce.create('tinymce.plugins.ESIG_SIF', {
			/**
			 * Initializes the plugin, this will be executed after the plugin has been created.
			 * This call is done before the editor instance has finished it's initialization so use the onInit event
			 * of the editor instance to intercept that event.
			 *
			 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
			 * @param {string} url Absolute URL to where the plugin is located.
			 */
			init : function(ed, url) {
				ed.addButton('esig_sif', {
					title : 'Add a signer input field',
					cmd : 'esig_sif',
					image : url + '../../../../assets/images/pen_icon_gray.svg'
				});
				
				ed.addCommand('esig_sif', function() {
					var btn = $('.mceIcon.mce_esig_sif'); // The button the user clicked
					esig_sif_admin_controls.mainMenuShow('mce', btn);
				});
				
				ed.onLoadContent.add(function(ed, o) {
					esig_sif_admin_controls.mainMenuInit(ed);
				});
			},
 
			/**
			 * Creates control instances based in the incomming name. This method is normally not
			 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
			 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
			 * method can be used to create those.
			 *
			 * @param {String} n Name of the control to create.
			 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
			 * @return {tinymce.ui.Control} New control instance or null if no control was created.
			 */
			createControl : function(n, cm) {
				return null;
			},
 
			/**
			 * Returns information about the plugin as a name/value array.
			 * The current keys are longname, author, authorurl, infourl and version.
			 *
			 * @return {Object} Name/value array containing information about the plugin.
			 */
			getInfo : function() {
				return {
					longname : 'ESIG-SIF Buttons',
					author : 'Michael Medaglia',
					authorurl : 'http://vitaminmlabs.com',
					infourl : '',
					version : "0.1"
				};
			}
		});
 
		// Register plugin
		tinymce.PluginManager.add( 'esig_sif', tinymce.plugins.ESIG_SIF );

	});
	
	
}(jQuery));