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
  public function plugin($plugin = 'plugin'){
  	include_once ROOT . '/' . APPPATH . 'plugins/' . $plugin . '/' . $plugin . '.php';
  	$CI = &get_instance();
  	$this->_ci_init_class($plugin, '', $CI);
  
  }
}