<?php
/*
 * Load All Core Initialisation classes
 * @author Flipper Code <hello@flippercode.com>
 * @package Core
 * Author URL : http://www.flippercode.com/
 * Version 2.0.0
 */

if ( ! class_exists( 'FlipperCode_Initialise_Core' ) ) {


	class FlipperCode_Initialise_Core {

		/*
		* Class Vars
		*/

		private $corePath;
		private $dbsettings;
		private $optionName;
		private $dboption;
		private $productTemplate;
		private $productDirectory;
		private $currentSettings;
		private $currentBasicSettings;
		private $currentTemplateBackups;

		public function __construct() {

			$this->_load_core_files();
			$this->_register_flippercode_globals();

		}

		public function _register_flippercode_globals() {

			if ( is_admin() ) {
				   add_action( 'admin_head', array( $this, 'hook_in_admin_header' ) );
			}
		}

		function hook_in_admin_header() { ?>
					<script>var fcajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";</script>
			<?php
		}

		public function _load_core_files() {

			$corePath  = plugin_dir_path( __FILE__ );
			$backendCoreFiles = array(
				'class.tabular.php',
				'class.template.php',
				'class.controller-factory.php',
				'class.model-factory.php',
				'class.controller.php',
				'class.model.php',
				'class.validation.php',
				'class.database.php',
				'class.importer.php',
				'class.plugin-overview.php',
				'class.emails.php',
				'class.widget-builder.php',
			);

			$frontendCoreFiles = array(
				'class.controller-factory.php',
				'class.model-factory.php',
				'class.emails.php',
				'class.model.php',
				'class.database.php',
				'class.widget-builder.php',
				'class.template.php',
			);

			foreach ( $backendCoreFiles as $file ) {

				if ( file_exists( $corePath . $file ) and is_admin() ) {
					require_once( $corePath . $file );
				}
			}

			foreach ( $frontendCoreFiles as $file ) {

				if ( file_exists( $corePath . $file ) ) {
					require_once( $corePath . $file );
				}
			}

		}

	}

	  return new FlipperCode_Initialise_Core();

}
