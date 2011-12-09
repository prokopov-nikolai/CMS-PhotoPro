<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для раздела пользователи
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */

Class User_model extends CI_Model {
  private $page;
  private $per_page;
  private $uniqid;
  
  /**
   * Конструктор
   */
  public function index(){
    parent::__construct();
    $this->_reset_variables();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляем нового пользователя
   */
  public function insert($post){
    /*if ($this->db->set($post)->set('page_date_modified', 'NOW()', false)->insert('page')) {
      $this->session->set_userdata(array('message' => 'Страница "'.$post['page_title'].'" успешно добавлена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления страницы в базу!'));
    }*/
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Обновляем данне пользователя
   * @return bol 
   */
  public function update($post, $uniqid){
    // обновим права администратора 
    // проверим права того кто пытается создать нового админа
    if ($this->session->userdata('user_admin') != ''){
      $this->db->select('user_uniqid AS user_admin');
      $this->db->from('user_admin');
      $this->db->where('user_uniqid', $this->session->userdata('user_admin'));
      $this->db->limit(1);
      $query = $this->db->get();
      $user = $query->row_array();
      if (isset($user['user_admin']) && $user['user_admin'] != '') {
        // проверка пройдена добавим или удалим нового админа
        if (isset($post['user_admin']) && $post['user_admin'] == 'on'){
          $this->db->query("INSERT INTO {$this->db->dbprefix}user_admin
                            SET
                              user_uniqid = '{$uniqid}'
                            ON DUPLICATE KEY UPDATE
                              user_uniqid = '$uniqid'");
          unset($post['user_admin']);
        } else {
          $this->db->where('user_uniqid', $uniqid);
          $this->db->delete('user_admin');
        }
      }
    }
  	
  	// обновим данные пользователя
  	$this->db->where('user_uniqid', $uniqid);
  	if (isset($post['user_password']) && $post['user_password'] == '') {
  	  unset($post['user_password']);
  	} else {
  	  $post['user_password'] = $this->user->encrypt_password($post['user_password']);
  	}
  	
    if (isset($post['user_last_name']) && $post['user_last_name'] == ''){
      $this->session->set_userdata(array('error' => 'Укажите фамилию!'));
      return false;
    }
    preg_match_all("/[a-z0-9`~!@#$%^&*+=\\\";,\.\'\/]+$/", trim($post['user_last_name'], ' '), $out);
    if (sizeof($out[0]) > 0){
      $this->session->set_userdata(array('error' => 'Вы ввели недопустимые символы в фамилии!'));
      return false;
    } 
    
    if (isset($post['user_first_name']) && $post['user_first_name'] == null){
      $this->session->set_userdata(array('error' => 'Укажите имя!'));
      return false;
    }
    preg_match_all("/[a-z0-9`~!@#$%^&*+=\\\";,\.\'\/]+$/", trim($post['user_first_name'], ' '), $out);
    if (sizeof($out[0]) > 0){
      $this->session->set_userdata(array('error' => 'Вы ввели недопустимые символы в имени!'));
      return false;
    } 

    if (isset($post['user_patronymic']) && $post['user_patronymic'] != '') {
      preg_match_all("/[a-z0-9`~!@#$%^&*+=\\\";,\.\'\/]+$/", trim($post['user_patronymic'], ' '), $out);
      if (sizeof($out[0]) > 0){
        $this->session->set_userdata(array('error' => 'Вы ввели недопустимые символы в отчестве!'));
        return false;
      }   
    }
  	
    $post['user_about'] = strip_tags($post['user_about'], '<p></br>');
    
    if (isset($post['user_subscribe']) && $post['user_subscribe'] == 'on') {
      $post['user_subscribe'] = '1';
    } else {
      $this->session->set_userdata(array('user_subscribe' => '0'));
      $post['user_subscribe'] = '0';
    }
    
    // если все проверки прошли, то обновим данные пользователя в базе и занесем данные в сессию
    $this->db->where('user_uniqid', $uniqid);
    if ($this->db->update('user', $post)) {
      $this->session->set_userdata($post);
      $this->session->set_userdata(array('message' => 'Данные пользователя успешно обновлены!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка обновления данных пользователя!'));
      return false;
    }
        
    // загрузим фотку
    if (isset($_FILES['user_photo']['tmp_name']) && $_FILES['user_photo']['tmp_name'] != NULL){
      $this->load->library('image');
      $path = $_FILES['user_photo']['tmp_name'];
      if(!$this->image->check($path)){
        die('Hacking attempt!'); 
      }
      // проверим размеры 
      $info = getimagesize($path);
      if ((isset($info[0]) && $info[0] > 500) ||
          (isset($info[1]) && $info[1] > 500)) {
        $this->session->set_userdata(array('error' => 'Аватарка не загружена! Превышены допустимые значения!'));
        return false;
      }
        
      // скопируем  файл в буффер
      $filename = basename($path);
      $ext = strtolower(substr($_FILES['user_photo']['name'], strlen($_FILES['user_photo']['name'])-4, 4));
  
      // готовим запись к вставке  
      $img = array();
      
      // айдишник картинки
      $img['image_id'] = uniqid();
      
      // сформируем название которое будет видеть пользователь
      $img['image_name'] = 'avatar_' . $uniqid . $ext;
        
      // сформируем название файла на сервере
      $img['image_filename'] = 'avatar_' . $uniqid . substr($img['image_id'], 6, 7) . $ext;
         
      // скопируем картинку в нужное нам место.
      $source = ROOT . "/images/source/{$img['image_filename']}";
      copy($path, $source);
      
      // параметры картинки
      $info = getimagesize($source);
      $img['image_width'] = $info[0];
      $img['image_height'] = $info[1];
      $img['image_size'] = filesize($source);
      $img['user_uniqid'] = $uniqid;
      
      // удалим файл аватарки если она уже есть
      $query = $this->db->query("SELECT image_filename 
                        FROM {$this->db->dbprefix}image
                        WHERE user_uniqid = '{$uniqid}' AND gallery_id IS NULL
                        LIMIT 1;");
      if ($row = $query->row_array()){
        // удалим предыдущую запись и файл 
        $this->db->query("DELETE FROM {$this->db->dbprefix}image
                        WHERE user_uniqid = '{$uniqid}' AND gallery_id IS NULL");
        unlink(ROOT . "/images/source/{$row['image_filename']}");
      }
      
      // вставим запись в базу
      $this->db->insert('image', $img);
    }
    
    // вернем что все гуд
    return true;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем  
   */
  public function delete($uniqid){
  	$user = $this->get($uniqid);
  	$this->db->where('user_uniqid', $uniqid);
    if ($this->db->delete('user')) {
      $this->session->set_userdata(array('message' => 'Пользователь (' . $user[0]['user_email'] .  ') успешно удален!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка удаления пользователя!'));
    }
    #$this->db->last_query(); exit;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список пользователей
   */
  public function get(){
    $user = array();
    $this->db->select('SQL_CALC_FOUND_ROWS u.*', false);
    $this->db->select('ua.user_uniqid as user_admin');
    $this->db->select("(SELECT COUNT(gallery_id) 
                       FROM {$this->db->dbprefix}gallery
                       WHERE user_uniqid = u.user_uniqid) 
                       AS user_gallery_count", false);
    $this->db->from('user AS u');
    $this->db->join('user_admin AS ua', 'ua.user_uniqid = u.user_uniqid', 'left');
    $this->db->having('user_gallery_count > 0'); 
    $this->db->order_by('user_date', 'desc');
    if ($this->uniqid != '') {
      $this->db->where('u.user_uniqid', $this->uniqid);
      $this->db->limit(1);
    } else if($this->page > 0) {
      $this->db->limit($this->per_page, ($this->page - 1) * $this->per_page);
    }
    $query = $this->db->get();
    #echo $this->db->last_query();
    
    if ($this->uniqid != '') {
      $user = $query->row_array();
    } else {
      foreach ($query->result_array() as $row){
        $user[] = $row;
      }
    }
    
    return $user;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлечем общее количество пользователей без лимита
   * @return unknown_type
   */
  public function get_total(){
    $this->db->select('FOUND_ROWS() AS total_rows', false);
    $query = $this->db->get();
    $row = $query->row_array();
    return $row['total_rows'];
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим страницу с пользователями 
   */
  public function page($page){
    $this->page = $page;
    return $this;
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Установим по сколько выводить на странице
   * @return unknown_type
   */
  public function per_page($count){
    $this->per_page = $count;
    $this->input->set_cookie(array(
      'name'   => 'per_page',
      'value'  => $count,
      'expire' => time() + 3600*24*365
    ));
    return $this;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим идентификатор пользователя 
   */
  public function uniqid($uniqid){
    $this->uniqid = $uniqid;
    return $this;
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Сбросим значения переменных
   */
  private function _reset_variables(){
    $this->page = 0;
    $this->per_page = $this->common->get_per_page();
    $this->uniqid = '';
  }
  // ---------------------------------------------------------------------------
  
}