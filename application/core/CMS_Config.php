<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Config extends CI_config {
  
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Сохраним массивы настроек в файлы
   * @return unknown_type
   */
  public function save(){
    foreach($this->is_loaded as $f) {
      $file = file(ROOT . '/' . $f);
      $fn = fopen(ROOT . '/' . $f,"w");
      foreach($file as $key => $string) {
        if (preg_match_all('/^\$config\[\'([a-z_]+)\'\]\[\'([a-z_]+)\'\]/m', $string, $matches, PREG_SET_ORDER)) {
          if (isset($this->config[$matches[0][1]][$matches[0][2]])) {
            if ($this->config[$matches[0][1]][$matches[0][2]] === FALSE || 
                $this->config[$matches[0][1]][$matches[0][2]] == 'FALSE' || 
                $this->config[$matches[0][1]][$matches[0][2]] == '0') {
              $file[$key] = $matches[0][0] . " = FALSE;\n";
            } elseif ($this->config[$matches[0][1]][$matches[0][2]] === TRUE  || 
                      $this->config[$matches[0][1]][$matches[0][2]] == 'TRUE'  || 
                      $this->config[$matches[0][1]][$matches[0][2]] == '1') {
              $file[$key] = $matches[0][0] . " = TRUE;\n";
            } elseif(is_numeric($this->config[$matches[0][1]][$matches[0][2]])) {
              $file[$key] = $matches[0][0] . " = {$this->config[$matches[0][1]][$matches[0][2]]};\n";
            } else {
             $file[$key] = $matches[0][0] . " = '{$this->config[$matches[0][1]][$matches[0][2]]}';\n";
            }
          }
        } else if(preg_match_all('/^\$config\[\'([a-z_]+)\'\]/m', $string, $matches, PREG_SET_ORDER)) {
          if (isset($this->config[$matches[0][1]])) {
            if ($this->config[$matches[0][1]] === FALSE || 
                $this->config[$matches[0][1]] == 'FALSE' ||
                $this->config[$matches[0][1]] == '0') {
              $file[$key] = $matches[0][0] . " = FALSE;\n";
            } elseif ($this->config[$matches[0][1]] === TRUE || 
                      $this->config[$matches[0][1]] == 'TRUE' ||
                      $this->config[$matches[0][1]] == '1') {
              $file[$key] = $matches[0][0] . " = TRUE;\n";
            } elseif(is_numeric($this->config[$matches[0][1]]) &&
                     $matches[0][1] != 'db_password') {
              $file[$key] = $matches[0][0] . " = {$this->config[$matches[0][1]]};\n";
            } else {
             $file[$key] = $matches[0][0] . " = '{$this->config[$matches[0][1]]}';\n";
            }
          }
        }
        fwrite($fn, $file[$key]);
      }
      fclose($fn);
    }
    $CI = &get_instance();
    $CI->session->set_userdata(array('message' => 'Настройки успешно сохранены!'));
  }
}