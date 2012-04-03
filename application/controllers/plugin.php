<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS PhotoPro
 * Контроллер для выполнения плагинов 
 * 
 * @author ProkopovNI
 * @site http://prokopov-nikolai.ru
 */
class Plugin extends CMS_Controller {
  
  public function __construct(){
    parent::__construct();
    $this->index($this->uri->rsegment(2)
               , $this->uri->rsegment(3)
               , $this->uri->rsegment(4)
               , $this->uri->rsegment(5)
               , $this->uri->rsegment(6)
               , $this->uri->rsegment(7)
               , $this->uri->rsegment(8)
    );
  }
  // ---------------------------------------------------------------------------
    
  /*
   * Вызов нужного нам плагина с необходимыми параметрами
   */
  public function index($plugin_name, $controller = '', $function = '', $param1 = '', $param2 = '', $param3 = '', $param4 = ''){
    #echo "$plugin_name, $controller, $function";
    if (!$function) $function = 'index';
    if (!$controller && isset($this->config->config[$plugin_name]['default_controller'])) {
      $controller = $this->config->config[$plugin_name]['default_controller'];
      if (!$this->load->plugin($plugin_name, $controller)) {
        show_404();
      }
      $plugin_name = $plugin_name.'_'.$controller;
    } else if ($controller && isset($this->config->config[$plugin_name]['default_controller'])) {
      // попробуем загрузить контроллер который передали
      if (!$this->load->plugin($plugin_name, $controller)) {
        // в случае неудачи попробуем подгрузить дефолтный контроллер
        $function = $controller;
        $controller = $this->config->config[$plugin_name]['default_controller'];
        if (!$this->load->plugin($plugin_name, $controller)) {
          show_404();
        }
      }
      $plugin_name = $plugin_name.'_'.$controller;
    } else {
      $this->load->plugin($plugin_name);
    }
    $plugin = $this->$plugin_name;
    if (method_exists($plugin, $function)) {
      $plugin->$function($param1, $param2, $param3, $param4);
    } else {
      show_404();
    }
  }
  // ---------------------------------------------------------------------------
}