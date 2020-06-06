<?php

class WP_E_View {

    private $rootDir;
    public $classname = 'View';
    public $model;
    private static $instance;

    /**
     * Store all alerts to be displayed to user
     * 
     * @var [Array]
     * @access private
     * 
     * Structure: array('type'=>'error|success|warning' => 'error message');
     * Note type must be either 'error' or 'success' or 'warning'.
     */
    private $alerts;
    public $sidebars;

    public function __construct() {
        $this->rootDir = ESIGN_PLUGIN_PATH . ESIG_DS . "views";
    }

    public static function instance() {
        if (!isset(self::$instance) && !( self::$instance instanceof WP_E_View )) {
            self::$instance = new WP_E_View;
        }
        return self::$instance;
    }

    /**
     * Displays a view (HTML/CSS/Javascript)
     * 
     * @since 0.1.0
     * @param $controller (String) name of calling controller
     * @param $template (String) name of view to display
     * @param $data (Array) an array of passed on data
     * @return void
     */
    public function render($controller, $template, $data = array()) {

        $screen = $template;
        $viewDir = $this->rootDir . ($controller != 'index' ? ESIG_DS . lcfirst($controller) : '');
        $template_path = $viewDir . ESIG_DS . $template . ".php";
        $data['assets_dir'] = ESIGN_ASSETS_DIR_URI;

        if (!file_exists($template_path)) {
            
            die("View::render failed: '" . $template_path . "' does not exist");
        }

        // push any alerts to ['alert']
        if (!empty($this->alerts)) {
            array_push($data, array("alerts" => $this->renderAlerts()));
        }

        $admin_screens = array(
            'add-form',
            'edit-form'
        );

        if (in_array($screen, $admin_screens)) {
           
            /*echo '<div class="wrap">';
            echo '<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">';
            include($template_path);
            echo '</div></div></div></div>';

            echo '<div align="center"><img src="' . ESIGN_ASSETS_DIR_URI . '/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature"></div>';*/
           
           // echo '<div class="container" style="width:100% !important;"><div class="row">';
            //echo '<div class="col-sm-8 noPadding" style="width:75% !important;border:1px solid red;">';
             echo '<div class="wrap">';
                echo '<div id="poststuff">';
                echo '<div id="post-body" class="metabox-holder columns-2">';
                      
                      // body content start here
                echo '<div id="post-body-content" style="position: relative;">';
                        include($template_path);
                 echo '</div>'; // body content end here  
                 // right start here
                 echo '<div id="postbox-container-1" class="postbox-container">';
                     echo '<div id="side-sortables" class="meta-box-sortables ui-sortable">';
                     
                        echo '<div id="esig-logo-box" class="postbox" align="center"><img src="' . ESIGN_ASSETS_DIR_URI . '/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature"></div>';
                        do_action("esig_display_right_sidebar");
                        include($this->rootDir . ESIG_DS . "partials/_rightside.php");
                     echo '</div>' ;  
                 echo '</div>';
                 // right side end here
                 
                 
                // 
                  //
                  
                        
                echo '</div></div></div>';
           // echo '</div><div class="col-sm-4 noPadding" style="width:25% !important;border:1px solid green;">';
                
                
               
              //echo $this->renderPartial('_rightside');
           // echo '</div></div></div>';
            
        } else {
             
            echo '<div class="wrap">';
                include($template_path);
            echo '</div>';
            
        }
        
    }

    /**
     * Returns a partial view (HTML/CSS/Javascript).
     * 
     * @since 0.1.1
     * @param $template (String) name of view to display
     * @param $data (Array) an array of passed on data
     * @param $echo (Boolean) set to true if you want to echo the html
     * @param $partials_dir set to override the partials directory. Allows you to use templates in other dirs
     * @param $file_path overrides the whole damn file path. Should include the file name.
     * @return void
     */
    public function renderPartial($template, $data = array(), $echo = false, $partials_dir = null, $file_path = null) {

        $partials_dir || $partials_dir = 'partials';
        $viewDir = $this->rootDir . ESIG_DS . $partials_dir;
        $template_path = $viewDir . ESIG_DS . $template . '.php';

        if ($file_path)
            $template_path = $file_path;

        //$data['assets_dir'] = ESIGN_ASSETS_DIR_URI;
        if (!file_exists($template_path)) {
            die("View::renderPartial failed: '" . $template_path . "' does not exist");
        }

        // Include into a buffer.
        ob_start();
        include($template_path);
        $html = ob_get_contents();
        ob_end_clean();

        if ($echo)
            echo $html;

        unset($data); // Clear out data vars so we don't cause problems with other views
        return $html;
    }

    public function html($template_path, $data = array(), $echo = false) {

        //$data['assets_dir'] = ESIGN_ASSETS_DIR_URI;
        if (!file_exists($template_path)) {
            die("View::renderPartial failed: '" . $template_path . "' does not exist");
        }

        // Include into a buffer.
        ob_start();
        include($template_path);
        $html = ob_get_contents();
        ob_end_clean();

        $results = $this->replace_variable($html, $data);
        unset($data); // Clear out data vars so we don't cause problems with other views
        if ($echo) {
            echo $results;
        } else {
            return $results;
        }
    }

    public function replace_variable($html, $data) {

        return preg_replace_callback('/\{{([A-Za-z0-9_]+)\}}/', function($match) use ($data) {
            if(array_key_exists($match[1], $data)){
                return $data[$match[1]];
            }
            
        }, $html);
    }

    // Depreciated. Not used, unless by plugins.
    public function setVars($vars, $namespace) {
        if (!isset($this->{$namespace})) {
            $this->{$namespace} = (object) $vars;
        }
    }

    // Will default values to empty string if they don't exist. Prevents php notification messages.
    // $data (Array) the array of values you are defaulting.
    // $indexes (Array) an array of keys for $data to be defaulted
    public function default_vals(&$data, $indexes) {
        foreach ($indexes as $index) {
            if (!isset($data[$index])) {
                $data[$index] = '';
            }
        }
    }

    /**
     * Set Model
     *
     * Set the current Model to our view
     * Allows us direct access to the current Models data
     * 
     * @since 1.0.1
     * @param String ($modelName)
     * @return void
     */
    public function setModel($modelName) {
        $this->model = new $modelName();
    }

    /**
     * Set Alert
     *
     * Set an Alert to our Alert stack
     * 
     * @since 1.0.1
     * @param Array in format: array('type'=>'error|updated|warning', 'title' => 'error title', 'message' => 'error message')
     * @return Bool
     */
    public function setAlert($alert) {

        // String
        if (is_string($alert)) {
            $this->alerts[] = array('message' => $alert);

            // Array (preferred)
        } else if (!array($alert)) {
            die("View::setAlert() expects Array, " . gettype($alert) . " given<br />\n");
        }
        $alert['title'] = isset($alert['title']) ? $alert['title'] : '';
        $this->alerts[] = $alert;
        return 1;
    }

    /**
     * Render Alerts
     *
     * Build from the alert stack HTML output
     * 
     * @since 1.0.1
     * @return void
     * @output HTML
     */
    public function renderAlerts() {
        $alert_msg = '';
        if (!empty($this->alerts)) {
            foreach ($this->alerts as $alert) {
                $alert_msg .= '<div class="' . $alert["type"] . '">';
                if (empty($alert["message"])) {
                    continue;
                }
                $alert_msg .= '<div class="title">' . (isset($alert["title"]) ? $alert["title"] : '') . '</div><p class="message">' . $alert["message"] . '</p></div>';
            }

            return $alert_msg;
        }
    }

    public function setSidebar($title, $content, $classtitle, $classbody) {


        $side = '';
        $side .= '<div id="esig-'. $classtitle .'-box" class="postbox" >
			<button type="button" class="handlediv '. $classtitle .'" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Publish</span><span class="toggle-indicator" aria-hidden="true"></span></button>';
        $side .="<h3 class='hndle ui-sortable-handle esig-section-title'><span>" . $title
                . "</span></h3>";
        $side .= '<div class="inside ' . $classbody . '">' .
                $content
                . '</div></div> ';

        $this->sidebars = $side;
        return 1;
    }

    public function renderSidebar() {

        $side_bar = '';

        if (!empty($this->sidebars)) {
           // $side_bar .= '<div id="postbox-container-1" class="esig-postbox-container">
			//<div id="side-sortables" class="meta-box-sortables">';


            $side_bar .=$this->sidebars;

           // $side_bar .= '</div></div>';

            return $side_bar;
        }
    }

}
