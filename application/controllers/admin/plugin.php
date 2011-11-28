<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер настроек
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Plugin extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Основые настройки 
   */
  public function index(){
    $menu_add = array(
      'plugin' => array(
        array(
          'title' => 'Задачник',
          'name' => 'issues',
          'description' => 'Здесь вы можете добавить новую страницу'
        )
      )
    );
    $this->merge_data('SUBMENU', $menu_add);                       
    $plugin = array();
    $this->append_data('P', $plugin);
    $this->display('plugin.html');
  }
  // ---------------------------------------------------------------------------
}

/* End of file settings.php */
/* Location: ./application/controllers/admin/settings.php */