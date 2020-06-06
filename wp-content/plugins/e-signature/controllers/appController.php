<?php

/**
 * appController
 * @since 0.1.0
 * @author Micah Blu
 */
abstract class WP_E_appController {

    private $viewsRootDir;
    public $view; //hold the view class
    public $controller;

    public function __construct() {

        $this->view = new WP_E_View();

        $this->user = new WP_E_User();

        $this->setting = new WP_E_Setting();
    }

    abstract function calling_class();

    public function getAssetDirectoryURI() {
        return ESIGN_ASSETS_DIR_URI;
    }

    /**
     * Instantiates View class, sets the current Model and fetches the requested view
     *
     * @since 1.1.0
     * @param void
     * @return void Outputs requested view HTML
     */
    public function fetchView($view, $data = null) {

        $model = $this->extractModelName($this->calling_class());
        $controller_file = $this->extractControllerName($this->calling_class());
        $this->view->setModel($model);

        // Globals
        $data['ESIGN_ASSETS_DIR_URI'] = ESIGN_ASSETS_DIR_URI;
        $data['ESIGN_PLUGIN_PATH'] = ESIGN_PLUGIN_PATH;

        $this->view->render($controller_file, $view, $data);
    }

    private function extractControllerName($classname) {
        $classname = preg_replace('/Controller$/', '', $classname);
        $classname = preg_replace('/^WP_E_/', '', $classname);
        return $classname;
    }

    private function extractModelName($classname) {
        $classname = preg_replace('/Controller$/', '', $classname);
        return ucfirst(substr($classname, 0, -1));
    }

    public function get_query_array() {

        $pairs = explode('&', $_SERVER['QUERY_STRING']);
        $query_array = array();

        foreach ($pairs as $pair) {
            if (strpos($pair,'=')) {
                list($name, $value) = explode('=', $pair);
                $query_array[$name] = $value;
            }
        }

        return $query_array;
    }

    public function get_query_var($var) {
        $query_array = $this->get_query_array();

        if (isset($query_array[$var]))
            return $query_array[$var];
        else
            return false;
    }

    /**
     * Global scope abstraction layer for controllers to the native get_pages method
     *
     * @since 0.1.0
     * @param null
     * @return [Array]
     */
    public function getPages() {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        return $pages;
    }

}
