<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pretty_photo extends CMS_Plugin {
  
  public $plugin_dir;
  
  public function __construct(){
    parent::__construct();
    $dir =  str_replace(basename(__FILE__), '', str_replace('\\', '/', __FILE__));
    $this->plugin_dir = "http://{$_SERVER['HTTP_HOST']}" . str_replace(ROOT, '', $dir);        
    $this->_get_head();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * формируем код галлереи
   * @return unknown_type
   */
  public function get_gallery($gallery, $pause_time){
  	$this->append_data('G', $gallery);
  	$this->append_data('pauseTime', $pause_time);
    return $this->fetch('pretty_photo/views/gallery.html');
  }
  // ---------------------------------------------------------------------------

  /*
   * Сформируем заголовки плагина ля страницы и добавим их к остальным
   */
  private function _get_head(){
    $headers = '<link type="text/css" href="' . $this->plugin_dir . 'css/prettyPhoto.css" rel="stylesheet" />' . "\n";
    $headers .= '  <script type="text/javascript" src="' . $this->plugin_dir . 'js/jquery.prettyPhoto.js"></script>' . "\n";
    $this->merge_data('plugin_headers', $headers);
  }
  // ---------------------------------------------------------------------------
}