<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Loader extends CI_loader {
  
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Загрузим плагин
   * @return unknown_type
   */
  public function plugin($plugin_name = 'plugin', $controller = ''){
    if ($controller){
      $file = ROOT . '/' . APPPATH . 'plugins/' . $plugin_name . '/controllers/' . $plugin_name . '_' . $controller . '.php';
      if (!file_exists($file)) return false;
      include_once $file;
      $class_name = $plugin_name.'_'.$controller;
    } else {
      include_once ROOT . '/' . APPPATH . 'plugins/' . $plugin_name . '/' . $plugin_name . '.php';
      $class_name = $plugin_name;
    }
    $CI = &get_instance();
    $this->_ci_init_class($class_name, '', $CI);
    return true;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Загрузим модель плагина
   */
  public function plugin_model($plugin_name, $model_name){
    $file = ROOT . '/' . APPPATH . 'plugins/' . $plugin_name . '/models/' . $model_name . '.php';
    if (file_exists($file)) {
      include_once $file;
      $class_name = $model_name;
      $CI = &get_instance();
      $this->_ci_init_class($class_name, '', $CI);
      #prex($CI->$class_name);
      return true;
    }
    show_error('Undefined model ' . $model_name . '.php');
  }
  // ---------------------------------------------------------------------------
}