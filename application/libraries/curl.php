<?php
/**
 * Для http и https запросов
 * Реализован через curl
 *
 * @author ProkopovNI
 * @site http://prokopov-nikolai.ru
 */
Class Curl {

  private $_curl;
  
  /**
   * Инициализируем настройки по умолчанию
   * @return unknown_type
   */
  public function __construct(){
    $this->_curl = curl_init();
    curl_setopt_array($this->_curl, array(
       CURLOPT_POST           => false,
       CURLOPT_HEADER         => false,
       CURLOPT_HTTPGET        => false,
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_FRESH_CONNECT  => true
    ));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Устанавливаем параметры через массив 
   * @param unknown_type $options
   * @return unknown_type
   */
  public function set_options_array($options){
    foreach ($options AS $key => $value) {
      if (method_exists($this, $key)){
      	$this->$key($value);
      } else {
        $this->set_curlopt($key, $value);
      }
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим указанную опцию
   * @param str $option
   * @param str $value
   * @return unknown_type
   */
  public function set_curlopt($option, $value) {
    curl_setopt($this->_curl, constant($option), $value);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Укажем откуда пришли 
   * @param str $referer
   * @return unknown_type
   */
  public function set_referer($referer){
    curl_setopt($this->_curl, CURLOPT_REFERER, $referer);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Указываем полный путь до файла в котором будем хранить куки 
   * @param str $user_cookie_file
   * @return unknown_type
   */
  public function set_cookiefile($cookiefile) {
    curl_setopt($this->_curl, CURLOPT_COOKIEFILE, $cookiefile);
    curl_setopt($this->_curl, CURLOPT_COOKIEJAR, $cookiefile);
  }  
  // ---------------------------------------------------------------------------  
  
  /**
   * Передаем пост данные
   * @param unknown_type $data
   * @return unknown_type
   */
  public function set_postfields($data){
    if (is_array($data)) {
      $data = $this->_array_to_string($data);
    }
    curl_setopt($this->_curl, CURLOPT_HEADER, true);
    curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
    curl_setopt($this->_curl, CURLOPT_POST, true);
    curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $data); 
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Укажем браузер
   * @param unknown_type $user_agent
   * @return unknown_type
   */
  public function set_user_agent($user_agent){
    curl_setopt($this->_curl, CURLOPT_USERAGENT, $user_agent);
  }
  // ---------------------------------------------------------------------------
   
  /**
   * Укажем урл запроса
   * @param str $url
   * @return unknown_type
   */
  public function set_url($url){
    if (substr($url,0, 5) == 'https') {
      curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($this->_curl, CURLOPT_URL, $url);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Получаем код ответа сервера
   * @return unknown_type
   */
  public function get_respose_code() {
    curl_setopt($this->_curl, CURLOPT_HEADER, true);
    curl_setopt($this->_curl, CURLOPT_NOBODY, true);
    curl_exec($this->_curl);
    return curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Получаем заголовки ответа
   * @param unknown_type $as_array
   * @return unknown_type
   */
  public function get_response_headers($as_array = false){
    curl_setopt($this->_curl, CURLOPT_HEADER, true);
    curl_setopt($this->_curl, CURLOPT_NOBODY, true);
    if ($as_array === true) {
      $headers = explode("\n", curl_exec($this->_curl));
      return $headers; 
    } else {
      return curl_exec($this->_curl);
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Получаем тело ответа
   */
  public function get_response_body($return = false){
    curl_setopt($this->_curl, CURLOPT_HEADER, false);
    if ($return === true) {
      return curl_exec($this->_curl); 
    } else {
      echo curl_exec($this->_curl);
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Преобразуем массив в строку
   * @param фкк $array
   * @return str
   */
  private function _array_to_string($array){
    $new_array = array();
    foreach ($array AS $key => $value) {
      $new_array[] = "{$key}={$value}"; 
    }
    return implode('&', $new_array);
  } 
  // ---------------------------------------------------------------------------
}