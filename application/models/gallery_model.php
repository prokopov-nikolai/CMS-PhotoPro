<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для работы с галереями и картиками
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 * $this->url()->page()->limit()->get();
 */

Class Gallery_model extends CI_Model {
  private $url;
  private $page;
  private $per_page;
  private $limit;
  private $image_size;
  private $user;
  
  /**
   * Конструктор
   */
  public function __construct(){
    parent::__construct();
    $this->_reset_variables();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляет новую галерею
   */
  public function insert($post){
    if ($this->db->insert('gallery', $post)) {
      $this->session->set_userdata(array('message' => 'Галерея "'.$post['gallery_title'].'" успешно добавлена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления галереи в базу!'));
    }
  }
  // ---------------------------------------------------------------------------

  /**
   * Удаляет галерею
   */
  public function delete($gallery_url){
    $this->url($gallery_url);
    $gallery = $this->get($gallery_url);

    // удалим исходники картинок
    foreach($gallery['images'] as $img) {
      if (file_exists(ROOT . "/images/source/{$img['filename']}"))
        unlink(ROOT . "/images/source/{$img['filename']}");
    }
    
    // удалим картинки и саму галерею из базы 
    if ($this->db->delete('gallery', array('gallery_url' => $gallery_url))) {
      $this->session->set_userdata(array('message' => 'Галерея "'.$gallery['gallery_title'].'" успешно удалена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка удаления галереи из базы!'));
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляем картинку к галереи
   */
  public function image_add($img){
    $this->db->insert('image', $img);
    return $this->db->insert_id();
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем картинки для галереи
   * @param unknown_type $gallery_url
   * @return unknown_type
   */
  public function image_get(){
  	$images = array();
    $this->db->select('i.*');
    $this->db->from('image AS i');
    $this->db->join('gallery AS g', 'g.gallery_id = i.gallery_id');
    $this->db->where('g.gallery_url', $this->url);
    $query = $this->db->get();
    $img_ids = array();
    foreach ($query->result_array() as $row){
    	if (isset($this->image_size['width']) && $this->image_size['width'] > 0) {
    		$width = $this->image_size['width'];
    	} else {
    		$width = $row['image_width'];
    	}
      if (isset($this->image_size['height']) && $this->image_size['height'] > 0) {
        $height = $this->image_size['height'];
      } else {
        $height = $row['image_height'];
      }
      $images[$row['image_id']] = array(
        'id'       => $row['image_id'],
        'url'      => '/image/' . $width . 'x' . $height . '/' . $row['image_name'] . config_item('nginx'),
        'name'     => $row['image_name'] . config_item('nginx'),
        'filename' => $row['image_filename'],
        'width'    => $width,
        'height'   => $height
      );
      $img_ids[] = $row['image_id'];
    }
    foreach($this->tag_get($img_ids) as $key => $value){
    	$images[$key]['tag'] = $value;
    }
    return $images;
  }
  // ---------------------------------------------------------------------------

  /**
   * Извлекаем реальное название файла картики по его имени из урла
   * @param $image_name - название картинки в урле 
   *                      (не совпадает с реальным названием файла)
   * @return unknown_type
   */
  public function image_get_filename($image_name){
    $this->db->select('image_filename');
    $this->db->from('image');
    $this->db->where('image_name', $image_name);
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() == 1){
      $row = $query->row_array();
      return $row['image_filename']; 
    }
    return false;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем картинки 
   * @param unknown_type $gallery_url
   * @return unknown_type
   */
  public function image_delete($image_ids){
  	$this->db->select('image_filename');
  	$this->db->from('image');
  	$this->db->where_in('image_id', $image_ids);
  	$query = $this->db->get();
  	foreach ($query->result_array() as $row) {
  	  if (file_exists(ROOT . '/images/source/' . $row['image_filename']))
  		  unlink(ROOT . '/images/source/' . $row['image_filename']);
  	}
    $this->db->where_in('image_id', $image_ids);
    return $this->db->delete('image');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляем теги к картинке
   */
  public function tag_add($tag){
    $this->db->insert('tag', $tag);
    return $this->db->insert_id();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Обловляем обложку галереи
   * @return $post - данные из аякса
   */
  public function cover_update($post){
  	$this->db->where('gallery_url', $post['gallery_url']);
  	$this->db->update('gallery', array('gallery_cover_id' => $post['cover_id']));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаеv теги
   */
  public function tag_get($img_ids = '') {
    if($img_ids == null) {
      return array();
    }
  	$tag = array();
  	$this->db->select('tag_name, image_id');
  	$this->db->from('tag');
  	$this->db->where_in('image_id', $img_ids);
  	$query = $this->db->get();
  	foreach ($query->result_array() as $row){
  		$tag[$row['image_id']][] = $row['tag_name'];
  	}
  	return $tag;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список галарей или указанную галерею по заданным параметрам
   */
  public function get(){
    $gallery = array();
    $this->db->select('SQL_CALC_FOUND_ROWS g.*', false);
    $this->db->select("IF (i.image_name IS NULL
             , (SELECT image_name 
                FROM {$this->db->dbprefix}image 
                WHERE gallery_id = g.gallery_id LIMIT 1)
             , i.image_name) AS image_name", false);
    $this->db->select('CONCAT(u.user_first_name, " ", u.user_last_name) as user_name', false);
    $this->db->select("(SELECT COUNT(image_id) FROM {$this->db->dbprefix}image WHERE gallery_id = g.gallery_id) as gallery_image_count");
    $this->db->from('gallery as g');
    $this->db->join('image as i', 'i.image_id = g.gallery_cover_id' , 'left');
    $this->db->join('user as u', 'g.user_uniqid = u.user_uniqid');
    
      // галерея по урлу
    if ($this->url != '') {
      $this->db->where('g.gallery_url', $this->url);
      $this->db->limit(1);
      // все галереи пользователя 
    } else if ($this->user != '') {
      if (is_array($this->user)){
        $this->db->where_in('g.user_uniqid', $this->user);
      } else {
        $this->db->where('g.user_uniqid', $this->user);
      }
    }
    
      // просто лимит последних галерей
    if ($this->limit != 0) {
      $this->db->limit($this->limit);
      // галереи с какой-то страницы
    } else if ($this->page != 0) {
      $this->db->limit($this->per_page, ($this->page - 1) * $this->per_page);
    }
    
    $query = $this->db->get();
    #echo $this->db->last_query();
    
    if ($this->url != '') {
      $gallery = $query->row_array();
      $gallery['images'] = $this->image_get();
      $gallery['image_name'] = $gallery['image_name'] . config_item('nginx'); 
    } else {
      foreach ($query->result_array() as $row){
        $row['image_name'] = $row['image_name'] . config_item('nginx'); 
        $gallery[] = $row;
      }
    }
    
    // сбросим параметры
    $this->_reset_variables();
    return $gallery;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлечем общее количество галерей без лимита
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
   * Установим url для галереи 
   */
  public function url($gallery_url){
    $this->url = $gallery_url;
    return $this;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим страницу с галереями 
   */
  public function page($page){
    $this->page = $page;
    return $this;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим пользователя галереями 
   */
  public function user($user){
    $this->user = $user;
    return $this;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим страницу с галереями 
   */
  public function limit($limit){
    $this->limit = $limit;
    return $this;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим размеры изображений  в галереями 
   */
  public function image_size($size){
    $this->image_size = $size;
    return $this;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установим по сколько выводить на странице
   * @return obj
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
  
  /**
   * Сбросим значения переменных
   */
  private function _reset_variables(){
    $this->url = '';
    $this->page = 0;
    $this->per_page = $this->common->get_per_page();
    $this->limit = 0;
    $this->image_size = array('width' => 0, 'height' => 0);
    $this->user = '';
  }
  // ---------------------------------------------------------------------------
}