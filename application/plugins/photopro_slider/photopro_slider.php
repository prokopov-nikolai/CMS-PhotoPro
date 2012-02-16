<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photopro_slider extends CMS_Plugin {
  
  /**
   * Тип плагина. Когда выполняется
   * before | after | anytime
   */  
  public $type = 'anytime';
  
  /**
   * Стили плагина
   */
  public $style = array(
    '{{ path_plugin }}/photopro_slider/css/photopro-slider.css'
  );
  
  /**
   * Стили плагина
   */
  public $script = array(
    '{{ path_plugin }}/photopro_slider/js/jquery.photopro.slider.js'
  );
  
  public function __construct($CI){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * формируем кода галереи
   * @return unknown_type
   */
  public function get_gallery($gallery, $pause_time){
  	$this->append_data('G', $gallery);
  	$this->append_data('pauseTime', $pause_time);
    return $this->render('photopro_slider/views/gallery.html');
  }
  // ---------------------------------------------------------------------------
}