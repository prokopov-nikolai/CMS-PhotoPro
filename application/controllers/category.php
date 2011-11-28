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
  public function show($cat_url) {
  	$cat = $this->page_model->cat_get_one($cat_url);
    if ($cat != null) {
    	$cat['category_descr'] = substr(strip_tags($cat['category_description']), 0, 255);
    	$this->append_data('C', $cat);
      $this->append_data('PAGES', $this->page_model->get_list($cat['category_url']));
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