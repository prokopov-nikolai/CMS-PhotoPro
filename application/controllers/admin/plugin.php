<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер настроек
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Plugin extends CMS_Controller {

  private  $_plugins_info;
  
  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
    $this->_plugins_info = $this->_get_plugins_info();
    #prex($this->_plugins_info = $this->_get_plugins_info());
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Список для управления плагинов  
   */
  public function index(){
    $menu_add = array();
    foreach($this->_plugins_info as $plugin) {
      if (isset($plugin['method'])) {
        $menu_add['plugin'][] = array(
          'title'       => $plugin['title'],
          'name'        => $plugin['name'],
          'description' => $plugin['description']
        );
      }
    }
    $this->merge_data('SUBMENU', $menu_add);                       
    $this->append_data('P', $this->_plugins_info);
    $this->display('plugin.html');
  }
  // ---------------------------------------------------------------------------

  /**
   * Активируем / Деактивируем плагины
   */
  public function activate(){
    $post = $this->input->post();
    if (count($post) > 0) {
      $activated = array_flip($this->_get_plugins_active());
      foreach($post['plugin_list'] as $plugin_name) {
        if ($post['action'] == 'deactivated') {
          unset($activated[$plugin_name]);
        } else if ($post['action'] == 'activated') {
          $activated[] = $plugin_name;
        }
      }
      $this->_set_plugins_active($activated);
      $this->common->success_true();
    } else {
      show_404();
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем описание плагинов
   */
  private function _get_plugins_info(){
    $activated = $this->_get_plugins_active();
    $info =  array();
    $plugins_dir = ROOT . '/' . APPPATH . 'plugins/';
    if ($handledir = opendir($plugins_dir)) {
      $this->load->library('xml/unpacker');
      while (false !== ($folder = readdir($handledir))) {
        if (is_dir($plugins_dir . $folder) && file_exists($plugins_dir . $folder . '/info.xml')) {
          $this->unpacker->initialize($plugins_dir . $folder . '/info.xml');
          $i = $this->unpacker->get_data();
          $i['activate'] = 'activated';
          if (in_array($i['name'], $activated)){
            $i['activate'] = 'deactivated';
          }
          $info[$i['name']] = $i;
        }
      }
    }
    closedir($handledir);
    return $info;
  }
  // ---------------------------------------------------------------------------

  /**
   * Извлечем список активных плагинов 
   */
  private function _get_plugins_active(){
    $file = ROOT . '/' . APPPATH . 'plugins/plugins.dat';
    return file($file);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Сохраняем список активных плагинов 
   */
  private function _set_plugins_active($list){
    $file = ROOT . '/' . APPPATH . 'plugins/plugins.dat';
    $fn = fopen($file,"w");
    fwrite($fn, implode("\n", $list));
    fclose($fn);
    return true;
  }
  // ---------------------------------------------------------------------------
}

/* End of file plugin.php */
/* Location: ./application/controllers/admin/plugin.php */