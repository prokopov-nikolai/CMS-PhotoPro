<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Plugin  {
  
  private $ci;
  
  protected $db;

  public function __construct(){
    $CI = & get_instance();
    $this->ci = $CI;  
    $this->db = $CI->db; 
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Склеиваем заголовки  
   * @return unknown_type
   */
  protected function merge_data($key, $value){
    $this->ci->merge_data($key, $value);
  }
  // ---------------------------------------------------------------------------

  /**
   * Добавляем данные в шаблон
   * @param unknown_type $key
   * @param unknown_type $value
   * @return unknown_type
   */
  protected function append_data($key, $value) {
  	$this->ci->append_data($key, $value);
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Выводим шаблон  
   * @return unknown_type
   */
  protected function fetch($template){
    return $this->ci->fetch($template);
  }
  // ---------------------------------------------------------------------------
}