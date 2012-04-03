<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер страниц в админке
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Page extends CMS_Controller {
  
  public function __construct(){
    parent::__construct();
    $this->load->model('page_model');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * 
   */
  public function index() {
    $this->display('page/index.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляем новую страницу
   */
  public function add() {
  	$post = $this->input->post();
  	if (sizeof($post) > 1 && !isset($post['user_email'])) {
  		$post['page_url'] = $this->common->get_url($post['page_title'], true, 'page');
  		$post['user_id'] = $this->session->userdata('user_admin');
  		$this->page_model->insert($post);
  	}
  	#pr($post);exit;
  	$this->append_data('CATS', $this->page_model->cat_get_list('category_id'));
  	$this->append_data('act', 'add');
    $this->display('page/add.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Редактируем страницу
   */
  public function edit($page_url) {
    $post = $this->input->post();
    if (sizeof($post) > 1) {
      $post['user_id'] = $this->session->userdata('user_admin');
      $this->page_model->update($post);
    }
    #pr($post);exit;
    $this->append_data('CATS', $this->page_model->cat_get_list('category_id'));
    $this->append_data('act', 'edit');
    $this->append_data('P', $this->page_model->get_one($page_url));
    $this->display('page/add.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем страницу
   */
  public function delete($page_url) {
    $this->page_model->delete($page_url);
    header('location: /' . config_item('admin_url') . '/page/get_list/');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Список страниц
   */
  public function get_list() {
    $p = $this->page_model->get_list('', false);
  	$this->append_data('PAGES', $p['pages']);
    $this->display('page/list.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Рубрики страниц
   */
  public function category($category_url = '') {
  	$post = $this->input->post();
  	if ($category_url == '') {
	  	if (sizeof($post) > 1){
	  		$post['category_url'] = $this->common->get_url($post['category_title'], true, 'category');
	  		$this->page_model->cat_insert($post);
	  	}
	  	$this->append_data('ds', 'none');
  	  $this->append_data('CATS', $this->page_model->cat_get_list());
  	} else {
  		$cat = $this->page_model->cat_get_one($category_url);
  		if($cat == null){
  			header('Location: /' . config_item('admin_url') . '/page/category/');
  			exit;
  		}
  		if (sizeof($post) > 1){
  			$this->page_model->cat_update($post, $category_url);
  			$cat = $this->page_model->cat_get_one($category_url);
  		}
  		$this->append_data('ds', 'block');
  		$this->append_data('CAT', $cat);
  	}
    $this->display('page/category.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * 
   */
  public function category_delete($category_url = ''){
  	$this->page_model->cat_delete($category_url);
  	header('Location: /' . config_item('admin_url') . '/page/category/');
  }
}

/* End of file image.php */
/* Location: ./application/controllers/admin/image.php */