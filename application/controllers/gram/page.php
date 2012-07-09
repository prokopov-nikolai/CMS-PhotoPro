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
   * Удаляем категорию
   */
  public function cateory_delete($category_url = ''){
  	$this->page_model->cat_delete($category_url);
  	header('Location: /' . config_item('admin_url') . '/page/category/');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выводим список форм
   */
  public function forms_list(){
    $this->load->model('form_model');
    $form = $this->input->post();
    if (isset($form['form_title'])) {
      $form['form_url'] = $this->common->get_url($form['form_title']);
      $form_id = $this->form_model->add($form);
      if ($form_id) {
        header('Location: /' . config_item('admin_url') . '/page/form/' . $form_id); 
        exit;
      }
    }
    $this->append_data('FORM', $this->form_model->get_list());
    $this->display('page/forms-list.html');
  }
  // ---------------------------------------------------------------------------  

  /**
   * Выводим форму
   */
  public function form($form_id = ''){
    $this->load->model('form_model');
    $field = $this->input->post();
    if (count($field) > 1 && isset($field['form_id'])) {
      $field['field_url'] = $this->common->get_url($field['field_title']);
      $this->form_model->add_field($field); 
    }
    $this->append_data('F', $this->form_model->get($form_id));
    $this->append_data('form_content', $this->common->array_to_json($this->form_model->get_fields($form_id)));
    $this->display('page/form.html');
  }
  // --------------------------------------------------------------------------- 
  
  /**
   * Удаляем форму
   */
  public function form_delete($form_id = ''){
    $this->load->model('form_model');
    $this->form_model->delete($form_id);
    header('Location: /' . config_item('admin_url') . '/page/forms_list/'); 
    exit;
  }
  // ---------------------------------------------------------------------------    
}

/* End of file image.php */
/* Location: ./application/controllers/admin/image.php */