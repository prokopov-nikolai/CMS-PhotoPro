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
    $this->append_data('USERS', $this->user_model->get(true));
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
    $user = '';
    if (sizeof($post) > 2){
    	if (isset($post['user_password']) && $post['user_password'] != '') {
    		$post['user_password'] =$this->user->encrypt_password($post['user_password']);
    	}    	
      if (!$this->user_model->update($post, $uniqid)){
        $user = $post; 
      }
    }
    
    // выведем данные
    if (!$user) {
      $this->user_model->uniqid($uniqid); 
      $user = $this->user_model->get($uniqid); 
    }
    #pr($user);
    #pr($this->session->userdata);
    
    $this->append_data('USER', $user);
    $this->display('users/update.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем пользователя
   * @return unknown_type
   */
  public function delete($uniqid = ''){
    if ($uniqid == '') {
      header('Location: /' . config_item('admin_url') . '/users/');
      exit; 
    }
    
    $this->user_model->delete($uniqid);
    $this->locate_referer();
    exit;
  }
  // ---------------------------------------------------------------------------
}

/* End of file image.php */
/* Location: ./application/controllers/admin/users.php */