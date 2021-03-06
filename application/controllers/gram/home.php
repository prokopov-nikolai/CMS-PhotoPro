<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер домашней страницы
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Home extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница сайта
   */
  public function index() {
    $advertising = file_get_contents('http://prokopov-nikolai.ru/cms-photopro-advertising.php');
    $this->append_data('advertising', $advertising);
    $this->display('home.html');
  }
}

/* End of file home.php */
/* Location: ./application/controllers/admin/home.php */