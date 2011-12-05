<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS PhotoPro
 * Контроллер пользователей
 * 
 * @author ProkopovNI
 * @site http://prokopov-nikolai.ru
 */
class Users extends CMS_Controller {

  /**
   * Конструктор
   * @return unknown_type
   */
  public function __construct () {
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем список всех пользователей
   * @return unknown_type
   */
  public function index() {
    $this->page();
  }
  // ---------------------------------------------------------------------------
  
  public function page($num = 1){
    // извлечем всех пользователй
    $this->load->model('user_model');
    $user = $this->user_model;
    $user->per_page($this->common->get_per_page());
    $user->page($num);
    $users = $user->get();
    $total_rows = $user->get_total();
    $total_pages = ceil($total_rows / $this->common->get_per_page());
    if ($num > $total_pages) { 
      header("Location: /users/");
      exit; 
    }
    // добавим в шаблон в необходимые данные для вывода с пагинацией
    $this->append_data('USER', $users);
    $this->append_data('total_rows', $total_rows);
    $this->append_data('total_pages', $total_pages);
    $this->append_data('current_page', $num);
    $this->append_data('num_links', 4);
    $this->append_data('base_url', '/users/page/');
    $this->append_data('paging_name', 'Пользователей');
    
    // извлечем все галереи пользователей
    $uniqids = array();
    foreach($users as $u) {
      $uniqids[] = $u['user_uniqid'];
    } 
    $this->load->model('gallery_model');
    $gallery = $this->gallery_model;
    $gallery->user($uniqids);
    $gals = $gallery->get();
    
    // отформатируем полученные галереи в удобоваримый массив
    $user_gals = array();
    foreach($gals as $g){
      $user_gals[$g['user_uniqid']][] = $g;
    }
    $this->append_data('GALLERY', $user_gals);
    $this->append_data('active_photographers', 'current');
    
    // выведем страницу
    $this->display('page/user-list.html');
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем форму авторизации
   */
  public function login() {
    if ($this->session->userdata('user_uniqid') > 0){
      $this->locate_referer();
    }
    $this->append_data('user_email', $this->input->post('user_email'));
    $this->display('login.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выведем форму регистрации
   */
  public function registration() {
    if ($this->session->userdata('user_id') > 0) {
      header('Location: /');
    }
    $result = $this->user->registrate();    	
    if ($result !== true ) {
      if (isset($result['error'])) {
        $this->append_data('error', $result['error']);
      }
      if (isset($result['user_email'])) {
        $this->append_data('user_email', $result['user_email']);
      }
      if (isset($result['user_password'])) {
        $this->append_data('user_password', $result['user_password']);
      }
      $this->display('registration.html');
    } else {
      $this->session->set_userdata(array('first_visit' => 1));
      header("Location: /users/person_data/");
    }
    
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выйдем из сервиса
   */
  public function logout() {
    $this->user->logout();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Личные данные пользователя
   */
  public function person_data() {
    if (!$this->session->userdata('user_uniqid')) {
      header("Location: /"); exit;
    }
    $post = $this->input->post();
    #pr($post);
    if (isset($post['user_last_name'])) {
      $this->load->model('user_model');
      $this->user_model->update($post, $this->session->userdata['user_uniqid']);
    }
    $this->append_data('first_visit', $this->session->userdata('first_visit'));
    $this->session->unset_userdata('first_visit');
    $this->display('person_data.html');
  }
}