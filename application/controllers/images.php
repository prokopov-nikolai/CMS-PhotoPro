<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS PhotoPro
 * Контроллер пользователей
 * 
 * @author ProkopovNI
 * @site http://prokopov-nikolai.ru
 */
class Images extends CMS_Controller {

  /**
   * Конструктор
   * @return unknown_type
   */
  public function __construct () {
    parent::__construct();
    $this->load->model('gallery_model');
    $this->load->library('image');
    $name = $this->uri->segment(3);
    if (substr($name, strlen($name) - 6, strlen($name)) == '.nginx'){
      $size = explode('x', $this->uri->segment(2));
      $width = $size[0];
      $height = $size[1];
      $name = substr($name, 0, strlen($name) - 6);
      $this->resize($width, $height, $name);
      exit;
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем заресайз-картинку
   */
  public function resize($width, $height, $name, $wm = true) {
  	date_default_timezone_set('Europe/Moscow');
  	if (($filename = $this->gallery_model->image_get_filename($name)) === false){
  	  show_404();
  	}
  	// проверим существование исходника запрашиваемой картинки
  	$this->image->source_exists($filename);
    if ($wm) {
      $resize = ROOT . '/images/resize/' . $width . 'x' . $height . '/' . $filename;
    } else {
      $resize = ROOT . '/images/resize/' . $width . 'x' . $height . '/nw_' . $filename;
    }
    
    if (!file_exists($resize)) {
      // создадим новую картинку 
      $source = ROOT . '/images/source/' . $filename;
      $this->image->resize($source, $resize, $width, $height, $wm);
    }
    
    // отправим заголовки
    $lastModified = filemtime($resize);
    $slastModified = gmdate('D, d M Y H:i:s', $lastModified) . ' GMT';
    header('Last-Modified: ' . $slastModified);
    $expires = 3600 * 24 * 7;
    header('Cache-Control: max-age=' . $expires);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) .' GMT');
    
    // Отдадим новую фотку если она менялась
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
      $modifiedSince = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
      $modifiedSince = strtotime($modifiedSince[0]);
      if ($lastModified == $modifiedSince) {
        header('HTTP/1.1 304 Not Modified');
        exit;
      }
    }
    header('Content-Type: image/jpeg');
    readfile($resize);
  }
  // ---------------------------------------------------------------------------
  
}
