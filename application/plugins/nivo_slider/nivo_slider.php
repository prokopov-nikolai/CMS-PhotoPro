<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nivo_slider extends CMS_Plugin {
  
  /**
   * Тип плагина. Когда выполняется
   * before | after | anytime
   */  
  public $type = 'anytime';
  
  /**
   * Стили плагина
   */
  public $style = array(
      '{{ path_plugin }}/nivo_slider/css/nivo-slider.css'
    , '{{ path_plugin }}/nivo_slider/themes/default/default.css'
  );
  
  /**
   * Стили плагина
   */
  public $script = array(
    '{{ path_plugin }}/nivo_slider/js/jquery.nivo.slider.pack.js'
  );
  
  public function __construct(){
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
    return $this->render('nivo_slider/views/gallery.html');
  }
  // ---------------------------------------------------------------------------

}