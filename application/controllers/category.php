<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер рубрик
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Category extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
    $this->load->model('page_model');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница сайта
   */
  public function index() {
    	header('Location: /');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем главную страницу рубрики
   */
  public function show($cat_url, $page_number = 1) {
  	$cat = $this->page_model->cat_get_one($cat_url);
    if ($cat != null) {
    	$cat['category_descr'] = substr(strip_tags($cat['category_description']), 0, 255);
    	$this->append_data('C', $cat);
      $pages = $this->page_model->get_list($cat['category_url'], $page_number);
      $this->append_data('PAGES', $pages['pages']);
      $total_rows = $pages['total_rows'];
      $total_pages = ceil($total_rows / $this->common->get_per_page());
      if ($total_pages == 0) $total_pages = 1;
      if ($page_number > $total_pages) { 
        header("Location: /category/{$cat_url}/");
        exit; 
      }
      $this->append_data('total_rows', $total_rows);
      $this->append_data('total_pages', $total_pages);
      $this->append_data('current_page', $page_number);
      $this->append_data('num_links', 4);
      
      if ($cat_url == 'novosti') 
        $base_url = "/novosti/page/";
      else if ($cat_url)
        $base_url = "/category/{$cat_url}/page/";
      $this->append_data('base_url', $base_url);
      
      $this->append_data('page_id', $cat_url);
      
      $this->load->model('vote_model');
      $vote = $this->vote_model->get('', 1);
      if ($vote != null) $this->append_data('VOTE', $vote);
      
      $this->display('category.html');
    } else {
      show_404();
    }
  }
  // ---------------------------------------------------------------------------
}

/* End of file category.php */
/* Location: ./application/controllers/category.php */