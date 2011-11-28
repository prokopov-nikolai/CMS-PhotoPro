<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS PhotoPro
 * Контроллер для выполнения плагинов 
 * 
 * @author ProkopovNI
 * @site http://prokopov-nikolai.ru
 */
class Plugin extends CMS_Controller {
  /*
   * Вызов нужного нам плагина с необходимыми параметрами
   */
  public function name($plugin_name, $function = 'index', $param1 = '', $param2 = ''){
    $this->load->plugin($plugin_name);
    if (method_exists($this->$plugin_name, $function)) {
      $this->$plugin_name->$function($param1, $param2);
    } else {
      show_404();
    }
  }
  // ---------------------------------------------------------------------------
}