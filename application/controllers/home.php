<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер домашней страницы
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Home extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница сайта
   */
  public function index() {
    // извлечем конфига для главной страницу array(gallery_url, gallery_delay, gallery_count)
    $config_main = config_item('main_page');
    
    // загрузим галерею
    $this->load->model('gallery_model');
    $this->gallery_model->url($config_main['gallery_url']);
    $this->gallery_model->image_size(array('width' => 1130, 'height' => 800));
    $gallery = $this->gallery_model->get();
    $this->append_data('main_gallery', $this->nivo_slider->get_gallery($gallery, $config_main['gallery_delay']));
    
    // извлечем выставки для главной
    $this->append_data('GALLERY', $this->gallery_model->limit($config_main['gallery_count'])->get());
    
    $this->display('page/home.html');
  }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */