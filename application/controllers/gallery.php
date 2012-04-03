<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер галерей - выставок
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Gallery extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
    $this->load->model('gallery_model');
    $this->append_data('active_gallery', 'current');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем список галерей первая страницы
   */
  public function index() {
    $this->page();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем список галерей нужной страницы
   */
  public function page($num = 1) {
    $gallery = $this->gallery_model;
    $gallery->per_page($this->common->get_per_page());
    $gallery->page($num);
    $gals = $gallery->get();
    $total_rows = $gallery->get_total();
    $total_pages = ceil($total_rows / $this->common->get_per_page());
    if ($total_pages == 0) $total_pages = 1;
    if ($num > $total_pages) { 
      header("Location: /gallery/");
      exit; 
    }
    $this->append_data('GALLERY', $gals);
    $this->append_data('total_rows', $total_rows);
    $this->append_data('total_pages', $total_pages);
    $this->append_data('current_page', $num);
    $this->append_data('num_links', 4);
    $this->append_data('base_url', '/gallery/page/');
    $this->append_data('paging_name', 'выставок');
    $this->display('page/gallery-list.html');
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Выведем галерею и ее содержимое
   */
  public function show($gallery_url) {
    // ивлечем плагин верстальщик галереи
    $config_gallery = config_item('gallery');
    $this->load->plugin($config_gallery['plugin']);
    $plugin = $this->$config_gallery['plugin'];
    
    // достанем данные галереи и пихнем их в плагин и выведем все
    $gallery = $this->gallery_model;
    $gallery->url($gallery_url);
    $gallery = $gallery->get();
    $this->append_data('title', 'CMS PhotoPro - ' . $gallery['gallery_title']);
    $this->append_data('keywords', $gallery['gallery_keywords']);
    $this->append_data('description', $gallery['gallery_description']);
    $this->append_data('gallery_content', $plugin->get_gallery($gallery, $config_gallery['delay']));
    $this->display('page/gallery.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем галереи пользователя
   */
  public function user($uniqid = '', $page='', $num = 1) {
    // проверим пользователя
    $this->load->model('user_model');
    $this->user_model->uniqid($uniqid);
    if ($user = $this->user_model->get()){
      // если нашли, то извлечем его галереи
      $gallery = $this->gallery_model;
      $gallery->user($user['user_uniqid']);
      $gallery->per_page($this->common->get_per_page());
      $gallery->page($num);
      $gals = $gallery->get();
      if (sizeof($gals) == 0 && $num > 1) { 
        header("Location: /gallery/user/{$user['user_uniqid']}/");
        exit; 
      }
      $this->append_data('GALLERY', $gals);
      $total_rows = $gallery->get_total();
      $this->append_data('total_rows', $total_rows);
      $this->append_data('total_pages', ceil($total_rows/ $this->common->get_per_page()));
      $this->append_data('current_page', $num);
      $this->append_data('num_links', 4);
      $this->append_data('base_url', "/gallery/user/{$user['user_uniqid']}/page/");
      $this->append_data('paging_name', 'выставок');
      $this->append_data('A', $user);
      $this->display('page/gallery-list.html');
    } else {
      show_404();
    }
    
    
  }
  // ---------------------------------------------------------------------------
}

/* End of file gallery.php */
/* Location: ./application/controllers/gallery.php */