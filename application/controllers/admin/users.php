<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер пользователей в админке
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Users extends CMS_Controller {
  
  public function __construct(){
    parent::__construct();
    $this->load->model('user_model');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница раздела пользователей
   */
  public function index() {
    $this->append_data('USERS', $this->user_model->get());
    $this->display('users/list.html');
  }
  // ---------------------------------------------------------------------------

  /**
   * Добавляет галерею для картинок
   * @return unknown_type
   */
  public function insert(){
    // если не переданы данные
    $post = $this->input->post();

    // вернем обратно на страницу добавления
    $this->locate_referer();
  }
  // ---------------------------------------------------------------------------

  /**
   * Обновляем данные пользователя
   * @return unknown_type
   */
  public function update($uniqid = ''){
    if ($uniqid == '') {
      header('Location: /' . config_item('admin_url') . '/users/');
      exit; 
    }

    // если переданы данные то обновим их
    $post = $this->input->post();
    if (sizeof($post) > 2){
    	if (isset($post['user_password']) && $post['user_password'] != '') {
    		$post['user_password'] = md5($post['user_password'] . config_item('encryption_key'));
    	}
    	$this->user_model->update($post, $uniqid);
    }
    
    // выведем данные
    $user = $this->user_model->get($uniqid);
    $this->append_data('U', $user[0]);
    $this->display('users/update.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Обновляем данные пользователя
   * @return unknown_type
   */
  public function delete($uniqid = ''){
    if ($uniqid == '') {
      header('Location: /' . config_item('admin_url') . '/users/');
      exit; 
    }
    
    $this->user_model->delete($uniqid);
    $this->locate_referer();
  }
  // ---------------------------------------------------------------------------
}

/* End of file image.php */
/* Location: ./application/controllers/admin/users.php */