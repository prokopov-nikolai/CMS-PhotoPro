<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Класс авторизации на сайте
 * @author Николай
 * @site http://prokopov-nikolai.ru
 */
class User {
  
  private $session;
  
  private $db;
    
  public function __construct(){
  	
    $CI = CMS_Controller::get_instance();
    $CI->load->library('session');
    
    $this->session = $CI->session;
    
    
    $CI->load->helper('cookie');
    
    $CI->load->database();
    $this->db = $CI->db;

    // автологинезация
    $auth = $this->_auto_login();

    // проверим авторизован ли пользователь
    // если мы выходим или регистрируем, то не осуществляем проверку 
    if (!$this->session->userdata('user_uniqid') && 
         $CI->uri->segment(1) != 'logout' &&
         $CI->uri->segment(1) != 'registration') {
      if (isset($auth['error'])) {
        $CI->append_data('error', $auth['error']);
      }
      $CI->append_data('user_email', $CI->input->post('user_email'));
      
      // Расскоментируйте следующие две строки если доступ к сайту открыт только 
      // после авторизации
      #$CI->Display('login.html');
      #exit; 
    }

    // обязательная авторизация для админки
    if ($CI->uri->segment(1) == config_item('admin_url') && 
        $this->session->userdata('user_admin') == '') {
      $CI->display('login.html');
      exit;
    }
  }
  // ---------------------------------------------------------------------------
  
  /*
   * Автологинезация пользователя по кукам
   */
  private function _auto_login() {
    // проверим авторизован ли пользователь
    if ($this->session->userdata('user_uniqid') != '') {
      #$this->AppendData('USER', $this->session->userdata);
      return true;
    }
    
    $user_email = get_cookie('user_email', true);
    $user_password = get_cookie('user_password', true);
    if ($user_email != '' && $user_password != '') {
      // попробуем авторизовать по кукам
      return $this->_search_user($user_email, $user_password);
    } else {
      // проверим по данным кот передал пользователь
      return $this->_user_login();
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Логинит пользователя
   * @param str $secretKey
   * @return mixed
   */
  private function _user_login(){  
    $user_email = '';
    $user_password = '';

    // определим переданные email и пароль
    $CI = & get_instance();
    $post = $CI->input->post();
    $get  = $CI->input->get();
    
    if (isset($post['user_email']) && $post['user_email'] != null){
      $user_email = $post['user_email'];
      if (!$CI->common->check_email(trim($user_email))){
        return array('error' => 'Не корректный email!');
      }
    } elseif (isset($post['user_email']) && $post['user_email'] == null) {
    	return array('error' => 'Введите Email!');
    }
    
    if (isset($post['user_password']) && $post['user_password'] != null){
      $user_password = md5($post['user_password'] . config_item('encryption_key'));
    } elseif (isset($post['user_password']) && $post['user_password'] == null) {
      return array('error' => 'Пароль не может быть пустым!');
    }
      
    // пробуем найти пользователя
    if ($user_email != '' && $user_password != ''){
      return $this->_search_user($user_email, $user_password);
    }
    
    return false;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Ищет пользовател по базе
   * @param str $userEmail
   * @param str $userPassword
   * @return unknown_type
   */
  private function _search_user($user_email, $user_password){
    $this->db->select('user.*, user_admin.user_uniqid AS user_admin');
    $this->db->from('user');
    $this->db->join('user_admin', 'user.user_uniqid = user_admin.user_uniqid', 'left');
    $this->db->where('user_email', $user_email);
    $this->db->where('user_password', $user_password);
    $this->db->limit(1);
    $query = $this->db->get();
    #echo $this->db->last_query(). '<br />'; #die();
    if ($query->num_rows() == 1) {
      $user = $query->row_array();
      $this->session->set_userdata($user);
      #$this->AppendData('USER', $user);
      
      // запомним пользователя если попросили
      if (isset($_POST['remind']) && $_POST['remind'] != null) {
        $host = str_replace('www', '', $_SERVER['HTTP_HOST']);
        set_cookie('user_email', $user['user_email'], 60*60*24*30, ".{$host}");
        set_cookie('user_password', $user['user_password'], 60*60*24*30, ".{$host}");
      }
      return true;
    } else {
    	$this->db->select('user_email');
    	$this->db->from('user');
    	$this->db->where('user_email', $user_email);
    	$this->db->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() == 1) {
        return array('error' => 'Не верный пароль!');
      } else {
      	return array('error' => 'Пользователь с таким email не найден!');
      }
    }
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляет данные пользователя из сессии и кук
   * @return nothing
   */
  public function logout() {
    $CI = & get_instance();
    // разрушим сессию
    $CI->session->sess_destroy();
    
    // удалим куки
    $host = str_replace('www', '', $_SERVER['HTTP_HOST']);
    delete_cookie('user_email', ".{$host}");
    delete_cookie('user_password', ".{$host}");
    
    // вернем пользователя обратно
    $CI->locate_referer();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Регистрирует пользователя
   */
  public function registrate(){
  	$CI = & get_instance();
  	$post = $CI->input->post();
  	if (sizeof($post) < 2) {
  		return false;
  	}
  	if (isset($post['user_email']) && $post['user_email'] == null){
  		$post['error'] = 'Укажите email';
      return $post;
    }
    
    if (!$CI->common->check_email(trim($post['user_email']))) {
      return array('error' => 'Некорректрый email');
    }
    $this->db->select('user_email');
    $this->db->from('user');
    $this->db->where('user_email', $post['user_email']);
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      $post['error'] = 'Пользователь с таким email уже существует <a href="/fogot_password/?email='.$post['user_email'].'">Забыли пароль?</a>';
      return $post;
    }
    
    if (isset($post['user_password']) && $post['user_password'] == null){
      $post['error'] = 'Укажите пароль';
      return $post;
    }
    
    $post['user_password'] = $this->encrypt_password($post['user_password']);
    unset($post['user_show_password']);
    
    $post['user_uniqid'] = str_replace('.', '', uniqid('', 1));
    $this->db->insert('user', $post);
    return $this->_auto_login();
  }
  // ---------------------------------------------------------------------------
  
  /*
   * Шифруем пароль
   */
  public function encrypt_password($password){
    return md5($password . config_item('encryption_key'));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Сохраняет пользовательские данные
   */
  public function save_data($post){
    die('Вот здесь надо поправить application/libraries/user.php line 220');
  }
}
