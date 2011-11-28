<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для раздела картинок
 */

Class Image  {
  
  /**
   * Конструктор
   */
  public function index(){
    $config = array();
    $config['image_library'] = 'gd2'; // выбираем библиотеку
    $config['quality'] = config_item('image_quality'); 
    $config['maintain_ratio'] = true; // сохранять пропорции
    $config['create_thumb'] = false; // ставим флаг создания эскиза
    $config['new_image'] = false;
    $CI = & get_instance();
    $CI->load->library('image_lib', $config);
    $this->image_lib = $CI->image_lib;
    
    pr($this);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Проверяем наличие исходника 
   */
  public function source_exists($filename){
    if (!file_exists(ROOT . '/images/source/' . $filename)) {
      show_404();
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Создаем новую картинку по заданым размерам
   * @param $source
   * @param $resize
   * @param $width
   * @param $height
   * @param $use_watermark
   * @return unknown_type
   */
  public function resize($source, $resize, $width, $height, $watermark = false, $ratio = true){
  	if ($this->check($source)){
  	  // проверим наличие папки ресайзов
      $dir = ROOT . "/images/resize/";
      if (!is_dir($dir)){
        mkdir($dir, 0755);
      }
      
  		// проверим наличие папки
  		$dir = ROOT . "/images/resize/{$width}x{$height}/";
  		if (!is_dir($dir)){
  			mkdir($dir, 0755);
  		}
  		
  		// создадим новое изображение
  		$CI = & get_instance();
  		$config = array();
  		$config['source_image'] = $source; // исходник
      $config['new_image'] = $resize; // новый файл
      $config['width'] = $width; // задаем ширину
      $config['height'] = $height; // задаем высоту
      $config['quality'] = config_item('image_quality'); 
      $config['maintain_ratio'] = $ratio; // сохранять пропорции
      $config['image_library'] = 'gd2'; // выбираем библиотеку
	    $config['create_thumb'] = false; // ставим флаг создания эскиза
	    $CI->load->library('image_lib', $config);
	    $this->image_lib = $CI->image_lib;
	    $this->image_lib->initialize($config); 
      $this->image_lib->resize();
      $this->image_lib->clear();
      
      // наложим вотемарк если необходимо
      if ($watermark && ($width > 270 || $height > 500)){
      	$watermark = ROOT . '/'. APPPATH . 'views/' . config_item('site_template') . '/watermark.png';
      	if (!file_exists($watermark)) {
      		unlink($resize);
      		show_error('Не найден файл ' . $watermark);
      	}
        $config['source_image'] = $resize;
        $config['wm_type'] = 'overlay';
        $config['wm_overlay_path'] = $watermark;
        $config['wm_vrt_alignment'] = config_item('wm_vrt_alignment');
        $config['wm_hor_alignment'] = config_item('wm_hor_alignment');
        $config['create_thumb'] = FALSE; // явно указать значение, в противном случае изображения не уменьшаются
        $this->image_lib->initialize($config);
        $this->image_lib->watermark();
        $this->image_lib->clear();
      }
      
  	}
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Создаем новую картинку по заданым размерам
   * @param $source
   * @param $resize
   * @param $width
   * @param $height
   * @param $use_watermark
   * @return unknown_type
   */
  public function resize_source($source, $resize, $width, $height, $watermark = false, $ratio = true){
    if ($this->check($source)){
      // создадим новое изображение
      $CI = & get_instance();
      $config = array();
      $config['source_image'] = $source; // исходник
      $config['new_image'] = $resize; // новый файл
      $config['width'] = $width; // задаем ширину
      $config['height'] = $height; // задаем высоту
      $config['quality'] = config_item('image_source_quality'); 
      $config['maintain_ratio'] = $ratio; // сохранять пропорции
      $config['image_library'] = 'gd2'; // выбираем библиотеку
      $config['create_thumb'] = false; // ставим флаг создания эскиза
      $CI->load->library('image_lib', $config);
      $this->image_lib = $CI->image_lib;
      $this->image_lib->initialize($config); 
      $this->image_lib->resize();
      $this->image_lib->clear();
    }
  }
  // ---------------------------------------------------------------------------
  
  /** 
   * Проверяет является ли загружаемый файл картинкой
   * @param $key
   * @return boolean
   */
  public function check($source){
    // проверим расширение заргужаемого файла
    $blacklist = array(".php", ".phtml", ".php3", ".php4");
    foreach ($blacklist as $item) {
      if(preg_match("/$item\$/i", $source)) {
        return false;
      }
    }
    
    // проверим mime-тип
    $imageinfo = getimagesize($source);
    if($imageinfo['mime'] != 'image/jpeg' && 
		   $imageinfo['mime'] != 'image/gif' && 
		   $imageinfo['mime'] != 'image/png') {
      return false;
    }
    
    return true;
  }
}