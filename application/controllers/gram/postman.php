<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер Почтальона в админке
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Postman extends CMS_Controller {
  
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция по умолчанию
   */
  public function index(){
    $this->display('postman/index.html');
  }
}
