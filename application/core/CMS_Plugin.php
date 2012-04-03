<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Plugin  {
  
  private $ci;
  
  protected $db;
  
  protected $session;
  
  protected $load;

  public function __construct(){
    $CI = & get_instance();
    $this->ci = $CI;  
    if(isset($CI->db))
      $this->db = $CI->db;
    if(isset($CI->session))
      $this->session = $CI->session;
    if(isset($CI->load))
      $this->load = $CI->load;
  }
  // ---------------------------------------------------------------------------
  
  public function __get($model_name){
    if (isset($this->ci->$model_name))
      return $this->ci->$model_name;
    return false;
  }
  
  /**
   * Склеиваем   
   * @return unknown_type
   */
  protected function merge_data($key, $value){
    $this->ci->merge_data($key, $value);
  }
  // ---------------------------------------------------------------------------

  /**
   * Добавляем данные в шаблон
   * @param string $key
   * @param mixed $value
   * @return nothing
   */
  protected function append_data($key, $value) {
  	$this->ci->append_data($key, $value);
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Рендерит шаблон  
   * @return string
   */
  protected function render($template){
    return $this->ci->render($template);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выводит шаблон  
   * @return string
   */
  protected function display($template){
    return $this->ci->display($template);
  }
  // ---------------------------------------------------------------------------
  
  
  /**
   * Загружаем конфиги плагина
   * @return  boolean if the file was loaded correctly
   */
  function load_config($name = ''){
    $file_path = ROOT . '/' . APPPATH . 'plugins/' . $name . '/config.php';
    if (!file_exists($file_path)){
      show_error($file_path, 500, 'Не найден файл');
    }
    if (!isset($this->ci->config->config[$name])) {
      include($file_path);
      if (!isset($config)) {
        show_error('Файл ' . $file_path . ' не содержит массив $config' , 500, 'Ошибка конфигурационного файла');
      }
      $this->ci->config->set_item($name, $config);
      $this->ci->config->is_loaded[] = APPPATH . 'plugins/' . $name . '/config.php';
    }
    return $this->ci->config->item($name);
  }
}