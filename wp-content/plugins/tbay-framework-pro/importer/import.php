<?php
/**
 * Importer for tbay themer
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Themer
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
class Tbay_Import {
	
	public $errors = array();
	public $sucess = array();
	public $steps = array(
			'first_settings' => 'redux_option',
			'redux_option' => 'content',
			'content' => 'widgets',
			'widgets' => 'settings', 
			'settings' => 'revslider',
			'revslider' => 'done'
		);
	public $steps_config = array(
		'first_settings2' => 'redux_option2',
		'redux_option2' => 'widgets2',
		'widgets2' => 'settings2', 
		'settings2' => 'done',
	);
	public function __construct() {

		define( 'TBAY_IMPORT_CONFIG_DIR', get_template_directory() . '/inc/samples/'  );

		$demo_data_file_path = TBAY_IMPORT_CONFIG_DIR . 'sample-data.php';
		if ( is_file( $demo_data_file_path ) ) {
			require $demo_data_file_path;
		}
		if ( isset($demo_import_base_dir) ) {
    		define( 'TBAY_IMPORT_SAMPLES_DIR', $demo_import_base_dir );
    	} else {
    		define( 'TBAY_IMPORT_SAMPLES_DIR', get_template_directory() . '/inc/samples/' );
    	}

		define( 'TBAY_RECOMMEND_MEMORY_LIMIT', 268435456 );
      	define( 'TBAY_RECOMMEND_EXECUTION_TIME', - 1 );
     	define( 'TBAY_RECOMMEND_PHP_VERSION', '5.6.3' );
     	define( 'TBAY_RECOMMEND_POST_MAX_SIZE', 33554432 );
     	define( 'TBAY_RECOMMEND_UPLOAD_MAX_FILESIZE', 33554432 );

		add_action('admin_menu', array( &$this, 'create_admin_menu' ) );
		add_action( 'wp_ajax_tbay_import_sample', array( $this, 'import_sample' ) );
		add_action( 'admin_init', array( $this, 'get_remote_sampledata') );
	
	}

	public function create_admin_menu() {	
		add_menu_page(
			__( 'Demo Import', 'tbay-framework' ),
			__( 'Demo Import', 'tbay-framework' ),
			'manage_options',
			'tbay-import-demo', 
			array( $this, 'tbay_page_content' ),
			plugin_dir_url( dirname( __FILE__ ) ) . '/assets/images/icon-import.svg', 
			'62'
		);
	}

	public function get_remote_sampledata() {
 		if ( isset($_GET['doaction']) && $_GET['doaction'] == 'download-sample' ) {
			if ( !is_dir(TBAY_IMPORT_SAMPLES_DIR) ) {
				mkdir(TBAY_IMPORT_SAMPLES_DIR, 0777);
			}
			$theme_info = wp_get_theme();
			$source 	= isset($_GET['source']) ? $_GET['source'] : '';
			$type 		= isset($_GET['type']) ? $_GET['type'] : 'wpbakery';


			$theme_name = $theme_info->get( 'TextDomain' ) . (!empty($source) ? '-'.$source : '');

			if ( $theme_name ) {
				$lpackage = TBAY_IMPORT_SAMPLES_DIR.'samples.zip';

				$remote_file = 'https://bitbucket.org/devthembay/update-plugin/raw/master/demosamples/'.$theme_name.'/'. $type .'.zip';	 
				
				$data = file_get_contents( $remote_file );
				$file = fopen( $lpackage, "w+" );
				fputs($file, $data);
				fclose($file);

				if ( file_exists($lpackage) ) {
					WP_Filesystem();
					unzip_file( $lpackage , TBAY_IMPORT_SAMPLES_DIR );
				}
				@unlink( $lpackage );
				wp_redirect( admin_url('tools.php?page=tbay-import-demo') );
			}
 		}
 	}

	public function import_sample() {
		@ini_set( 'max_execution_time', '1200' );
		@ini_set( 'post_max_size', '64M');
		
		$demo_source = isset($_REQUEST['demo_source']) ? $_REQUEST['demo_source'] : '';
		$import_type = isset($_REQUEST['import_type']) ? $_REQUEST['import_type'] : '';
		$ajax = isset($_REQUEST['ajax']) ? $_REQUEST['ajax'] : '';
		$res = array();
		if ( $demo_source && $import_type ) {
			$fnc_call = 'import_'.$import_type;
			$res = call_user_func(array($this, $fnc_call), $demo_source);
		}

		echo json_encode($res); die();
	}

	public function outputJson( $status, $msg, $log = '', $loop = false ) {
		$res = array(
			'status'  => $status,
			'msg' => $mgs,
			'log'     => $log,
			'loop'	  => $loop,
			'loopnumber' => 0
		);
		$import_type = isset($_REQUEST['import_type']) ? $_REQUEST['import_type'] : '';

		if ($loop) {
			$res['next'] = $import_type;
		} else {
			$res['next'] = isset($this->steps[$import_type]) ? $this->steps[$import_type] : 'error';
		}
		return $res;
	}	

	public function outputJson2( $status, $msg, $log = '', $loop = false ) {
		$res = array(
			'status'  => $status,
			'msg' => $mgs,
			'log'     => $log,
			'loop'	  => $loop,
			'loopnumber' => 0
		);
		$import_type = isset($_REQUEST['import_type']) ? $_REQUEST['import_type'] : '';

		if ($loop) {
			$res['next'] = $import_type;
		} else {
			$res['next'] = isset($this->steps_config[$import_type]) ? $this->steps_config[$import_type] : 'error';
		}
		return $res;
	}

	/**
	 * Import Redux Option
	 */
	public function import_redux_option($source) {
		// return $this->outputJson( true, __("Import Redux Options Error", "tbay-framework"),  $log );
		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/redux_options.json';
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			$theme_info = wp_get_theme();
			$source = isset($_GET['source']) ? $_GET['source'] : '';
			$theme_name = $theme_info->get( 'TextDomain' ) . (!empty($source) ? '-'.$source : '');

			$redux_framework = \ReduxFrameworkInstances::get_instance( ''.$theme_name.'_tbay_theme_options' );
			if ( isset( $redux_framework->args['opt_name'] ) ) {
				// Import Redux settings.
				$redux_framework->set_options( $datas );
				return $this->outputJson( true, __("Import Redux Options Successful", "tbay-framework"),  $log );
			}
		}
		return $this->outputJson( false, __("Import Redux Options Error", "tbay-framework"),  $log );
	}

	/**
	 * Import Redux Option
	 */
	public function import_redux_option2($source) {
		// return $this->outputJson( true, __("Import Redux Options Error", "tbay-framework"),  $log );
		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/redux_options.json';
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			$theme_info = wp_get_theme();
			$source = isset($_GET['source']) ? $_GET['source'] : '';
			$theme_name = $theme_info->get( 'TextDomain' ) . (!empty($source) ? '-'.$source : '');

			$redux_framework = \ReduxFrameworkInstances::get_instance( ''.$theme_name.'_tbay_theme_options' );
			if ( isset( $redux_framework->args['opt_name'] ) ) {
				// Import Redux settings.
				$redux_framework->set_options( $datas );
				return $this->outputJson2( true, __("Import Redux Options Successful", "tbay-framework"),  $log );
			}
		}
		return $this->outputJson2( false, __("Import Redux Options Error", "tbay-framework"),  $log );
	}

	/**
	 * Import first settings
	 */
	public function import_first_settings($source) {
		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/first_settings.json';
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			if ( count( array_filter( $datas ) ) < 1 ) {
				return $this->outputJson( false, esc_html__( 'Data is error! file: ', 'tbay-framework') . $file, '' );
			}

			foreach ($datas as $key => $options) {
				if ( $key == 'page_options' ) {
					$this->import_page_options($options);
				}
			}
		}
		return $this->outputJson( true, __("Import First Settings Successful", "tbay-framework"),  $log );
	}	

	/**
	 * Import first settings
	 */
	public function import_first_settings2($source) {
		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/first_settings.json';
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			if ( count( array_filter( $datas ) ) < 1 ) {
				return $this->outputJson2( false, esc_html__( 'Data is error! file: ', 'tbay-framework') . $file, '' );
			}

			foreach ($datas as $key => $options) {
				if ( $key == 'page_options' ) {
					$this->import_page_options($options);
				}
			}
		}
		return $this->outputJson2( true, __("Import First Settings Successful", "tbay-framework"),  $log );
	}
	/**
	 * Import data sample from xml.
	 */
	public function import_content($source) {
		session_start();
		$return = apply_filters( 'tbay_themer_cancel_import_content', false );
		if ( $return ) {
			$data = $this->outputJson( true, '' );
		}
		$file_name = apply_filters( 'tbay_themer_get_xml_file_name', 'data.xml' );

		$sources 		= 	explode( '/', $source );
		$end 	 		= 	end($sources);
		$source_parent  = 	str_replace($end, '', $source); 

		$path = TBAY_IMPORT_SAMPLES_DIR.'/'.$source_parent.''.$file_name;
		if ( file_exists($path) ) {

			if (!class_exists('WP_Importer')) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) ) {
					require_once( $class_wp_importer );
				}
			}
			ob_start();
            
			require_once TBAY_FRAMEWORK_DIR . 'importer/wordpress-importer.php';
            
			$tbay_import = new WP_Import();

			if( !isset($_SESSION['importpostcount']) ){
            	$_SESSION['importpoststart'] = 0;
            	$_SESSION['importpostcount'] = 0;	
            	if( method_exists("deleteCaches", $tbay_import)){
            		$this->deleteCaches();
            	}
            }

			set_time_limit(0);
			
			$tbay_import->fetch_attachments = true;
			$returned_value = $tbay_import->import($path);

			$log = ob_get_clean();
  			$data = $this->outputJson( true, '',  $log, !$returned_value );
			$data['loopnumber'] = $_SESSION['importpostcount'];

			if( $returned_value == true ){
				unset( $_SESSION['importpoststart'] );
				unset( $_SESSION['importpoststart'] );
			}
			$this->res_json = $data;
			return $this->res_json;
		} else {
			$data = $this->outputJson( false, __("Error loading data.xml file", "tbay-framework"), '' );
		}
		$this->res_json = $data;
		return $this->res_json;
	}

	public function import_widgets( $source ){
 		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/widgets.json';
		$res = array();
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$options = json_decode( $datas, true );
			if( $options['widgets'] ){
				foreach ( (array) $options['widgets'] as $id_widget => $widget_data ) {
					update_option( 'widget_' . $id_widget, $widget_data );
				}
				return $this->import_sidebars_widgets($options);
			}
		} else {
			return $this->outputJson( false, __("Error loading widgets.json file", "tbay-framework"), '' );
		}
		return $this->outputJson( true, __("Widgets imported successfully", "tbay-framework"), '' );
	}	

	public function import_widgets2( $source ){
 		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/widgets.json';
		$res = array();
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$options = json_decode( $datas, true );
			if( $options['widgets'] ){
				foreach ( (array) $options['widgets'] as $id_widget => $widget_data ) {
					update_option( 'widget_' . $id_widget, $widget_data );
				}
				return $this->import_sidebars_widgets2($options);
			}
		} else {
			return $this->outputJson2( false, __("Error loading widgets.json file", "tbay-framework"), '' );
		}
		return $this->outputJson2( true, __("Widgets imported successfully", "tbay-framework"), '' );
	}

	public function import_sidebars_widgets( $options ) { 

		$sidebars = get_option("sidebars_widgets");
		unset($sidebars['array_version']);
		
		if ( is_array($options['sidebars']) ) {
			$sidebars = array_merge( (array) $sidebars, (array) $options['sidebars'] );
			
			unset($sidebars['wp_inactive_widgets']);
			
			$sidebars = array_merge(array('wp_inactive_widgets' => array()), $sidebars);
			$sidebars['array_version'] = 2;
			wp_set_sidebars_widgets($sidebars);
		} else {
			return $this->outputJson( false, __("Missing widgets data", "tbay-framework"), '' );
		}

		return $this->outputJson( true, __("Import Sidebars Widgets Successful", "tbay-framework"),  $log );
	}	

	public function import_sidebars_widgets2( $options ) { 

		$sidebars = get_option("sidebars_widgets");
		unset($sidebars['array_version']);
		
		if ( is_array($options['sidebars']) ) {
			$sidebars = array_merge( (array) $sidebars, (array) $options['sidebars'] );
			
			unset($sidebars['wp_inactive_widgets']);
			
			$sidebars = array_merge(array('wp_inactive_widgets' => array()), $sidebars);
			$sidebars['array_version'] = 2;
			wp_set_sidebars_widgets($sidebars);
		} else {
			return $this->outputJson2( false, __("Missing widgets data", "tbay-framework"), '' );
		}

		return $this->outputJson2( true, __("Import Sidebars Widgets Successful", "tbay-framework"),  $log );
	}

	/**
	 * Import data to revolutions
	 */
	public function import_revslider($source) {
		if ( ! class_exists( 'RevSliderAdmin' ) ) {
			require( RS_PLUGIN_PATH . '/admin/revslider-admin.class.php' );			
		}
		if ( is_dir(TBAY_IMPORT_SAMPLES_DIR . 'data/revslider/') ) {
			$path = TBAY_IMPORT_SAMPLES_DIR . 'data/revslider/';
		} else {
			$sources 		= 	explode( '/', $source );
			$end 	 		= 	end($sources);
			$source_parent  = 	str_replace($end, '', $source); 
			$path 			= 	TBAY_IMPORT_SAMPLES_DIR . '/' . $source_parent . 'revslider/';
		}

		if ( is_dir($path) ) {
			$rev_files = glob( $path . '*.zip' );
			if (!empty($rev_files)) {
				ob_start();
				foreach ($rev_files as $rev_file) {
					$_FILES['import_file']['error'] = UPLOAD_ERR_OK;
					$_FILES['import_file']['tmp_name']= $rev_file;

					$slider = new RevSlider();
					$slider->importSliderFromPost( true, true );
				}
				ob_get_clean();
			}
		} else {
			return $this->outputJson( false, esc_html__( 'revslider folder is not exists! folder: ', 'tbay-framework') . $path, '' );
		}
		return $this->outputJson( true, __("Import Slider", "tbay-framework"),  $log );
	}
	
	public function import_settings($source) {
		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/settings.json';
		$res = array();
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			if ( count( array_filter( $datas ) ) < 1 ) {
				return $this->outputJson( false, esc_html__( 'Data is error! file: ', 'tbay-framework') . $file, '' );
			}

			if ( !empty($datas['page_options']) ) {
				$this->import_page_options($datas['page_options']);
			}
			if ( !empty($datas['metadata']) ) {
				$this->import_some_metadatas($datas['metadata']);
			}
			if ( !empty($datas['menu']) ) {
				$this->import_menu($datas['menu']);
			}
		} else {
			return $this->outputJson( false, esc_html__( 'File is not exists! file:', 'tbay-framework') . $file, '' );
		}
		return $this->outputJson( true, __("Import Settings Successful", "tbay-framework"),  $log );
	}	

	public function notification_install_plugins() {
		?>
		<div class="tbay-import-notification">
		    <p><?php _e( 'Before start importing, you have to install all required plugins and other plugins that you want to use.', 'tbay-framework' ) ?></p>
		    <p><strong><?php _e( 'Important: you need to choose a demo is correct with the page builder(WPBakery or Elementor) plugin you installed.', 'tbay-framework' ) ?></strong></p>
		</div>
		<p class="description"><?php _e( 'It usally take few minutes to finish. Please be patient.', 'tbay-framework' ) ?></p>
		<?php
	}

	public function import_settings2($source) {
		$file = TBAY_IMPORT_SAMPLES_DIR.'/'.$source.'/settings.json';
		$res = array();
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			if ( count( array_filter( $datas ) ) < 1 ) {
				return $this->outputJson2( false, esc_html__( 'Data is error! file: ', 'tbay-framework') . $file, '' );
			}

			if ( !empty($datas['page_options']) ) {
				$this->import_page_options($datas['page_options']);
			}
			if ( !empty($datas['metadata']) ) {
				$this->import_some_metadatas($datas['metadata']);
			}
			if ( !empty($datas['menu']) ) {
				$this->import_menu($datas['menu']);
			}
		} else {
			return $this->outputJson2( false, esc_html__( 'File is not exists! file:', 'tbay-framework') . $file, '' );
		}
		return $this->outputJson2( true, __("Import Settings Successful", "tbay-framework"),  $log );
	}

	public function import_menu($datas) {
		global $wpdb;
		$terms_table = $wpdb->prefix . "terms";

		if ( $datas ) { 
			$menu_array = array();
			foreach ($datas as $registered_menu => $menu_slug) {
				$term_rows = $wpdb->get_results("SELECT * FROM $terms_table where slug='{$menu_slug}'", ARRAY_A);
				if(isset($term_rows[0]['term_id'])) {
					$term_id_by_slug = $term_rows[0]['term_id'];
				} else {
					$term_id_by_slug = null;
				}
				$menu_array[$registered_menu] = (int)$term_id_by_slug;
			}

			set_theme_mod('nav_menu_locations', $menu_array );
		}
	}

	public function import_page_options($datas) {
		if ( $datas ) {
			foreach ($datas as $option_name => $page_id) {
				update_option( $option_name, $page_id);
			}
		}
	}
	
	public function import_some_metadatas($datas) {
		if ( $datas ) {
			foreach ($datas as $slug => $post_types) {
				if ( $post_types ) {
					foreach ($post_types as $post_type => $metas) {
						if ( $metas ) {
							$args = array(
			                    'name'        => $slug,
			                    'post_type'   => $post_type,
			                    'post_status' => 'publish',
			                    'numberposts' => 1
			                );
			                $posts = get_posts($args);
			                if ( $posts && isset($posts[0]) ) {
								foreach ($metas as $meta) {
									update_post_meta( $posts[0]->ID, $meta['meta_key'], $meta['meta_value'] );
									if ( $meta['meta_key'] == '_mc4wp_settings' ) {
										update_option( 'mc4wp_default_form_id', $posts[0]->ID );
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function set_error($text) {
		$this->errors[] = $text;
	}

	public function set_sucess($text) {
		$this->sucess[] = $text;
	}

	public function get_ini_configs($key) {
		$all_ini_configs = ini_get_all();
		$value = ini_get( $key );

		$arr_value = $all_ini_configs[ $key ];
		if ( isset($arr_value['local_value']) ) {
			$value = $arr_value['local_value'];
		}
		return $value;
	}

	public function system_requirements() {
		?><div class="update-nag tbay_notification">
			<p>
				<?php _e( '<strong>Warning:</strong> If you have already used this feature before and you want to try it again, your content may be duplicated. Please consider resetting your database back to defaults with <a href="//wordpress.org/plugins/wordpress-reset/">this plugin</a>.', 'tbay-framework' ); ?>
			</p>
		</div>
		
			<?php 
			$dir = wp_upload_dir();

			$memory_limit 		= $this->get_ini_configs('memory_limit');
			$memory_limit_byte 	= wp_convert_hr_to_bytes($memory_limit);

			$max_execution_time = $this->get_ini_configs('max_execution_time'); 

			$post_max_size 		= $this->get_ini_configs('post_max_size');
			$post_max_size_byte = wp_convert_hr_to_bytes($post_max_size);


			$upload_max_filesize = $this->get_ini_configs('upload_max_filesize');
			$upload_max_filesize_byte = wp_convert_hr_to_bytes($upload_max_filesize);

			$phpversion 	= floatval(phpversion());
			$phpversion_str = phpversion();

			$writeable_boolean = wp_is_writable($dir['basedir'].'/');

			$is_ok = true;

			$is_phpversion 				= true;
			$is_memory_limit 			= true;
			$is_post_max_size 			= true;
			$is_upload_max_filesize 	= true;
			$is_parent_theme			= true;
			$is_max_execution_time		= true;


			if (get_template_directory() != get_stylesheet_directory()) {
				$is_parent_theme = false;
				$is_ok = false;
			}

			if ($phpversion < TBAY_RECOMMEND_PHP_VERSION) {
				$is_phpversion = false;
				$is_ok = false;
			}  
			if ( intval( $memory_limit_byte ) < TBAY_RECOMMEND_MEMORY_LIMIT ) {
				$is_memory_limit = false;
				$is_ok = false;
			}  
			if ( intval( $post_max_size_byte ) < TBAY_RECOMMEND_POST_MAX_SIZE ) {
				$is_post_max_size = false;
				$is_ok = false;
			} 
			if ( intval( $upload_max_filesize_byte ) < TBAY_RECOMMEND_UPLOAD_MAX_FILESIZE ) {
				$is_upload_max_filesize = false;
				$is_ok = false;
			}

			if ( intval( $max_execution_time ) < TBAY_RECOMMEND_EXECUTION_TIME ) {
				$is_max_execution_time = false;
				$is_ok = false;	
			}

			$class_status = '';
			if( $is_ok ) {
				$class_status = 'tbay-fw-status-green-wrap';
			} else {
				$class_status = 'tbay-fw-status-red-wrap';
			}


		?>


		<div class="viewWrapper" id ="viewWrapper">

			<div class="tbay-fw-dash-widget" id="system_dw">
				<div class="tbay-fw-dash-title-wrap <?php echo trim($class_status); ?>">
					<div class="tbay-fw-dash-title"><?php esc_html_e('System Requirements', 'tbay-framework'); ?></div>
					<div class="tbay-fw-dash-title-button tbay-fw-status-red"><i class="icon-problem-found"></i><?php esc_html_e('Problem Found', 'tbay-framework'); ?></div>
					<a class="tbay-fw-status-red tbay-fw-dash-title-button requirement-link" target="_blank" href="https://thembay.com"><i class="eg-icon-info"></i></a> <div class="tbay-fw-dash-title-button tbay-fw-status-green"><i class="icon-no-problem-found"></i><?php esc_html_e('No Problems', 'tbay-framework'); ?></div>
				</div>
				<div class="tbay-fw-dash-widget-inner">

					<span class="tbay-fw-dash-label"><?php esc_html_e('Active parent theme', 'tbay-framework'); ?></span>

					<?php
						//check if uploads folder can be written into
						if($is_parent_theme){
							echo '<i class="revgreenicon eg-icon-ok"></i>';
						}else{
							echo '<i class="revredicon eg-icon-cancel"></i><span style="margin-left:16px" class="rs-dash-more-info"><i class="eg-icon-info"></i><span class="rs-dash-red-content">'. esc_html__('You activated child theme, so you can not import the demo', 'tbay-framework')  .'</span></span>';
						}
					?>


					<div class="tbay-fw-dash-content-space-small"></div>


					<span class="tbay-fw-dash-label"><?php esc_html_e('Uploads folder writable', 'tbay-framework'); ?></span>

					<?php
						//check if uploads folder can be written into
						if($writeable_boolean){
							echo '<i class="revgreenicon eg-icon-ok"></i>';
						}else{
							echo '<i class="revredicon eg-icon-cancel"></i><span style="margin-left:16px" class="rs-dash-more-info" data-title="'.esc_html__('Error with File Permissions', 'tbay-framework').'" data-content="'.esc_html__('Please set write permission (755) to your wp-content/uploads folders to make sure the Slider can save all updates and imports in the future.', 'tbay-framework').'"><i class="eg-icon-info"></i></span>';
						}
					?>


					<div class="tbay-fw-dash-content-space-small"></div>


					<!-- Check php version -->
					<span class="tbay-fw-dash-label"><?php esc_html_e('PHP version', 'tbay-framework'); ?></span>
					<i style="margin-right:20px" class="revgreenicon <?php echo ($is_phpversion) ? 'eg-icon-ok' : 'eg-icon-cancel';?> "></i>
					<?php if( $phpversion < TBAY_RECOMMEND_PHP_VERSION ) : ?>

						<span class="rs-dash-red-content"><?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $phpversion_str ); ?></span>
						<span class="rs-dash-strong-content" style="margin-left:20px"><?php printf( __( '<strong>(min: %1$s)</strong>', 'tbay-framework' ), TBAY_RECOMMEND_PHP_VERSION ); ?></span>

					<?php else : ?>


						<span class="tbay-fw-dash-strong-content">
							<?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $phpversion_str ); ?>
						</span>	

					<?php endif; ?>
					<div class="tbay-fw-dash-content-space-small"></div>



					<!-- Check Memory Limit -->
					<span class="tbay-fw-dash-label"><?php esc_html_e('Memory Limit', 'tbay-framework'); ?></span>
					<i style="margin-right:20px" class="revgreenicon <?php echo ($is_memory_limit) ? 'eg-icon-ok' : 'eg-icon-cancel';?> "></i>
					<?php if( intval( $memory_limit_byte ) < TBAY_RECOMMEND_MEMORY_LIMIT ) : ?>

						<span class="<?php echo ($is_memory_limit) ? 'rs-dash-strong-content' : 'rs-dash-red-content'; ?>"><?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $memory_limit ); ?></span>
						<span class="rs-dash-strong-content" style="margin-left:20px"><?php _e('<strong>(min:256M)</strong>', 'tbay-framework'); ?></span>

					<?php else : ?>


						<span class="tbay-fw-dash-strong-content">
							<?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $memory_limit ); ?>
						</span>	

					<?php endif; ?>
					<div class="tbay-fw-dash-content-space-small"></div>						



					<!-- Upload Max Filesize -->
					<span class="tbay-fw-dash-label"><?php esc_html_e('Upload Max. Filesize', 'tbay-framework'); ?></span>
					<i style="margin-right:20px" class="revgreenicon <?php echo ($is_upload_max_filesize) ? 'eg-icon-ok' : 'eg-icon-cancel';?> "></i>
					<?php if( intval( $upload_max_filesize_byte ) < TBAY_RECOMMEND_UPLOAD_MAX_FILESIZE ) : ?>

						<span class="<?php echo ($is_upload_max_filesize) ? 'rs-dash-strong-content' : 'rs-dash-red-content'; ?>"><?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $upload_max_filesize ); ?></span>
						<span class="rs-dash-strong-content" style="margin-left:20px"><?php _e('<strong>(min:32M)</strong>', 'tbay-framework'); ?></span>

					<?php else : ?>


						<span class="tbay-fw-dash-strong-content">
							<?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $upload_max_filesize ); ?>
						</span>	

					<?php endif; ?>
					<div class="tbay-fw-dash-content-space-small"></div>

					<!-- Max Post Size -->
					<span class="tbay-fw-dash-label"><?php esc_html_e('Max. Post Size', 'tbay-framework'); ?></span>
					<i style="margin-right:20px" class="revgreenicon <?php echo ($is_post_max_size) ? 'eg-icon-ok' : 'eg-icon-cancel';?> "></i>
					<?php if( intval( $post_max_size_byte ) < TBAY_RECOMMEND_POST_MAX_SIZE ) : ?>

						<span class="<?php echo ($is_post_max_size) ? 'rs-dash-strong-content' : 'rs-dash-red-content'; ?>"><?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $post_max_size ); ?></span>
						<span class="rs-dash-strong-content" style="margin-left:20px"><?php _e('<strong>(min:32M)</strong>', 'tbay-framework'); ?></span>


					<?php else : ?>


						<span class="tbay-fw-dash-strong-content">
							<?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $post_max_size ); ?>
						</span>	

					<?php endif; ?>
					<div class="tbay-fw-dash-content-space-small"></div>

					<!-- Max execution time  Size -->
					<span class="tbay-fw-dash-label"><?php esc_html_e('Max execution time', 'tbay-framework'); ?></span>
					<i style="margin-right:20px" class="revgreenicon <?php echo ($is_max_execution_time) ? 'eg-icon-ok' : 'eg-icon-cancel';?> "></i>
					<?php if( intval( $max_execution_time ) < TBAY_RECOMMEND_EXECUTION_TIME ) : ?>

						<span class="<?php echo ($is_max_execution_time) ? 'rs-dash-strong-content' : 'rs-dash-red-content'; ?>"><?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $max_execution_time ); ?></span>
						<span class="rs-dash-strong-content" style="margin-left:20px"><?php printf( __( '<strong>(min: %1$s)</strong>', 'tbay-framework' ), TBAY_RECOMMEND_EXECUTION_TIME ); ?></span>


					<?php else : ?>


						<span class="tbay-fw-dash-strong-content">
							<?php printf( __( 'Currently: %1$s', 'tbay-framework' ), $max_execution_time ); ?>
						</span>	

					<?php endif; ?>
					<div class="tbay-fw-dash-content-space-small"></div>



			</div>
			</div>

			<div class="tbay-fw-dash-widget" id="support_dw">
				<div class="tbay-fw-dash-title-wrap">
					<div class="tbay-fw-dash-title"><?php esc_html_e('Thembay Support', 'tbay-framework'); ?></div>				
				</div>			
				<div class="tbay-fw-dash-widget-inner">			

					<div class="tbay-fw-dash-icon tbay-fw-dash-ticket"></div>
					<div class="tbay-fw-dash-content-with-icon">
						<div class="tbay-fw-dash-strong-content"><?php esc_html_e('Ticket support', 'tbay-framework'); ?></div>
						<div><?php printf( __( 'Please send the ticker <a href="%1$s" target="_blank">here</a>', 'tbay-framework' ), 'https://tickets.thembay.com/' ); ?></div>
					</div>
					<div class="tbay-fw-dash-content-space"></div>				
					<div class="tbay-fw-dash-icon tbay-fw-dash-mail"></div>
					<div class="tbay-fw-dash-content-with-icon">
						<div class="tbay-fw-dash-strong-content"><?php esc_html_e('Email Support', 'tbay-framework'); ?></div>
						<div><?php printf( __( 'Please send the email: <a href="%1$s" target="_blank">here</a>', 'tbay-framework' ), 'mailto:thembayteam@gmail.com' ); ?></div>
					</div>						

					<div class="tbay-fw-dash-content-space"></div>				
					<div class="tbay-fw-dash-icon tbay-fw-dash-youtube"></div>
					<div class="tbay-fw-dash-content-with-icon">
						<div class="tbay-fw-dash-strong-content"><?php esc_html_e('Youtube channel', 'tbay-framework'); ?></div>
						<div><?php printf( __( 'Watch video tutorial <a href="%1$s" target="_blank">here</a>', 'tbay-framework' ), 'https://www.youtube.com/channel/UCIkuoXjv4tS6SUHhEBAg9Ew' ); ?></div>
					</div>
				</div>
				
			</div>

		</div>
		<?php
	}
	public function tabs_data_demos($type) {
		$demo_data_file_path = TBAY_IMPORT_CONFIG_DIR . 'sample-data.php';
		if ( is_file( $demo_data_file_path ) ) {
			require $demo_data_file_path;
		} else {
			${"demo_datas_" . $type} = array();
		}

		?>
		<div class="tbay-demo-import-wrapper">
			<div class="themes">

				<?php 
					if( isset(${"demo_datas_" . $type}) && !empty(${"demo_datas_" . $type}) ) {
						$this->tabs_data_contents($type, ${"demo_datas_" . $type}, ${"path_dir_" . $type}, ${"path_uri_" . $type});
					} else {
						$this->tabs_data_btn_download($type);
					}
				?>

			</div>
		</div>
		<?php
	}	

	public function tabs_data_contents($type, $demo_datas, $path_dir, $path_uri) {	
		?>
		<div class="row container">
			<?php foreach ($demo_datas as $key_theme => $value_theme) { ?>
				<div class="tbay-theme-item">
					<h2 class="theme-header">Theme: <?php echo $key_theme ?></h2>
					<div class="row container">
						<?php foreach ($value_theme as $key => $value) { ?>
							<div class="tbay-demo-item">
							<div class="tbay-demo-item-img">
								<img src="<?php echo (file_exists($path_dir . esc_attr($key_theme) . '/' . esc_attr($key) . '/' . $value['screenshot']) ? $path_uri . esc_attr($key_theme) . '/' . esc_attr($key) . '/' . $value['screenshot'] : TBAY_FRAMEWORK_URL . 'assets/none_image.jpg'); ?>" />
							</div>
							<div class="tbay-demo-item-action">
								<h3 style="display: none;"><?php echo esc_html($key_theme); ?></h3>
								<h2 style=""><?php echo $value['title']; ?></h2>
								<button class="button button-primary tbay-btn-import"><?php esc_html_e( 'Import All', 'tbay-framework' ); ?>
					             <input class="hidden tbay-import-value" value="<?php echo esc_attr($type) . '/' . esc_attr($key_theme). '/' . esc_attr($key); ?>"/>
					            </button>												
					            <button class="button button-primary tbay-btn-config"><?php esc_html_e( 'Only Active', 'tbay-framework' ); ?>
					             <input class="hidden tbay-import-value" value="<?php echo esc_attr($type) . '/' . esc_attr($key_theme). '/' . esc_attr($key); ?>"/> 
					            </button>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	public function tabs_data_btn_download($type) {
		?>
		<div class="update-nag tbay-data-warning">
		    <?php printf( __( 'Click to the follow buttons to get sample demo from our live sites, the package will put into ROOT/wp-content/uploads. <br> Please make sure this folder has writeable permision. <br> If "Download Demos" don&rsquo;t work, you should upload the sample data manually <a href="%s" target="_blank">View video</a>.', 'tbay-framework' ), 'https://youtu.be/vmo3oo48p4U');
		    ?>
		</div>
		<br>
		<br> 
			<div class="download-btn" style="text-align: left;">
				<?php
					$btn_html = '<a class="button button-primary" href="'.admin_url( 'tools.php?page=tbay-import-demo', 'http' ).'&doaction=download-sample&type='. $type .'">'.esc_html__('Download Demos', 'tbay-framework').'</a>';
					$download_btns = apply_filters( 'tbay_themer_get_download_buttons', $btn_html );

					echo $download_btns;
				?>
		</div>
		<?php
	}

	public function tabs_downloads_datas() {
		?>
        <div class="tbay-import-tabs">
            <ul class="tabs-nav">
                <li><a href="#" class="active"><?php esc_html_e( 'WPBakery Demo', 'tbay-framework' ); ?></a></li>
                <li><a href="#"><?php esc_html_e( 'Elementor Demo', 'tbay-framework' ); ?></a></li>
            </ul>
            <div class="tabs-content">
                <div class="tabs-panel active">
                    <div class="demos-container">
						<?php $this->tabs_data_demos('wpbakery'); ?>
                    </div>
                </div>

                <div class="tabs-panel">
                    <div class="demos-container">
						<?php $this->tabs_data_demos('elementor'); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function tbay_page_content() {
		// script
		wp_enqueue_style( 'tbay-framework-backend', TBAY_FRAMEWORK_URL . 'assets/backend.css', array(), TBAY_FRAMEWORK_VERSION );
		wp_enqueue_script( 'tbay-framework-import', TBAY_FRAMEWORK_URL . 'assets/import.js', array( 'jquery' ), TBAY_FRAMEWORK_VERSION, true );

		$demo_data_file_path = TBAY_IMPORT_CONFIG_DIR . 'sample-data.php';
		$demo_data_dir_path  = TBAY_IMPORT_CONFIG_DIR;
		if ( is_file( $demo_data_file_path ) ) {
			require $demo_data_file_path;
		} else {
			$demo_datas = array();
		}

		?>
		
		<div class="wrap">
			<h1><?php esc_html_e( 'TbayTheme Demo Importer', 'tbay-framework' ); ?></h1>

			<?php $this->system_requirements(); ?>
			<?php $this->notification_install_plugins(); ?>
			<?php $this->tabs_downloads_datas(); ?>
		</div>

		<section class="tbay-popup">
			<div class="container">
				<div class="wrapper-content">

					<button type="button" class="notice-dismiss tbay-close-import-popup">
						<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'tbay-framework' ); ?></span>
					</button>
					<h1><?php esc_html_e( 'Importing', 'tbay-framework' ); ?></h1>

					<div class="row">
						<div class="tbay_progress_import tbay_progress_import_1" style="display: none;">
							<p class="note"><?php esc_html_e( 'The import process can take about 10 minutes. Please don\'t refresh the page.', 'tbay-framework' ); ?></p>
							<ol class="steps">
							<?php
								$steps = array(
									'first_settings' => array( 'default' => __('Install First Settings', 'tbay-framework'), 'installing' => __('Installing First Settings ...', 'tbay-framework'), 'installed' => __('Installed First Settings', 'tbay-framework') ),
									'redux_option' => array( 'default' => __('Install Redux Option', 'tbay-framework'), 'installing' => __('Installing Redux Option ...', 'tbay-framework'), 'installed' => __('Installed Redux Option', 'tbay-framework') ),
									'content' => array( 'default' => __('Install Demo Content', 'tbay-framework'), 'installing' => __('Installing Demo Content ...', 'tbay-framework'), 'installed' => __('Installed Demo Content', 'tbay-framework') ),
									'widgets' => array( 'default' => __('Install Widgets', 'tbay-framework'), 'installing' => __('Installing Widgets ...', 'tbay-framework'), 'installed' => __('Installed Widgets', 'tbay-framework') ),
									'settings' => array( 'default' => __('Install Settings', 'tbay-framework'), 'installing' => __('Installing Settings ...', 'tbay-framework'), 'installed' => __('Installed Settings', 'tbay-framework') ),
									'revslider' => array( 'default' => __('Install Revolution Slider', 'tbay-framework'), 'installing' => __('Installing Revolution Slider ...', 'tbay-framework'), 'installed' => __('Installed Revolution Slider', 'tbay-framework') ),
								);
								foreach ($steps as $key => $step) {
									?>
									<li class="<?php echo esc_attr($key); ?>">
										<span class="default"><?php echo $step['default']; ?></span>
										<span class="installing" style="display: none;"><?php echo $step['installing']; ?></span>
										<span class="installed" style="display: none;"><?php echo $step['installed']; ?></span>
									</li>
									<?php
								}
							?>					
							</ol>							
							
						</div>						

						<div class="tbay_progress_import tbay_progress_import_2" style="display: none;">
							<p class="note"><?php esc_html_e( 'The import process can take about 10 minutes. Please don\'t refresh the page.', 'tbay-framework' ); ?></p>
							<ol class="steps steps2">
							<?php
								$steps2 = array(
									'first_settings2 active' => array( 'default' => __('Install First Settings', 'tbay-framework'), 'installing' => __('Installing First Settings ...', 'tbay-framework'), 'installed' => __('Installed First Settings', 'tbay-framework') ),
									'redux_option2' => array( 'default' => __('Install Redux Option', 'tbay-framework'), 'installing' => __('Installing Redux Option ...', 'tbay-framework'), 'installed' => __('Installed Redux Option', 'tbay-framework') ),
									'widgets2' => array( 'default' => __('Install Widgets', 'tbay-framework'), 'installing' => __('Installing Widgets ...', 'tbay-framework'), 'installed' => __('Installed Widgets', 'tbay-framework') ),
									'settings2' => array( 'default' => __('Install Settings', 'tbay-framework'), 'installing' => __('Installing Settings ...', 'tbay-framework'), 'installed' => __('Installed Settings', 'tbay-framework') ),
								);
								foreach ($steps2 as $key => $step2) {
									?>
									<li class="<?php echo esc_attr($key); ?>">
										<span class="default"><?php echo $step2['default']; ?></span>
										<span class="installing" style="display: none;"><?php echo $step2['installing']; ?></span>
										<span class="installed" style="display: none;"><?php echo $step2['installed']; ?></span>
									</li>
									<?php
								}
							?>									
							</ol>					
							
						</div>

						<div class="tbay_progress_error_message">
							<div class="tbay-error">
								<h4><?php esc_html_e( 'Failed to import!', 'tbay-framework' ); ?></h4>
								<div class="content text_note tbay_notification"></div>
							</div>
							<div class="log update-nag tbay_notification">
								<h4><?php esc_html_e( 'Log', 'tbay-framework' ); ?></h4>
								<div class="content text_note"></div>
							</div>
							<a class="button button-primary tbay-support" href="#" target="_blank"><?php esc_html_e( 'Get support', 'tbay-framework' ); ?></a>
							<a class="button button-secondary tbay-visit-dashboard" href="<?php echo esc_url( get_admin_url() ); ?>"><?php esc_html_e( 'Dashboard', 'tbay-framework' ); ?></a>
						</div>

						<div class="tbay-complete tbay-complete1">
							<h3 class=""><?php esc_html_e( 'Importing is successful!', 'tbay-framework' ); ?></h3>
							<div class="content-message"></div> 
						</div>						

						<div class="tbay-complete tbay-complete2">
							<h3 class=""><?php esc_html_e( 'Switch homepage successfully!', 'tbay-framework' ); ?></h3>
							<div class="content-message"></div> 
						</div>
						<br class="clear">
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

new Tbay_Import();