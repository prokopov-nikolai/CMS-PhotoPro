<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для раздела картинок
 */

Class Page_model extends CI_Model {
  
  /**
   * Конструктор
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляет новую страницу
   */
  public function insert($post){
    if (!intval($post['category_id'])) unset($post['category_id']);
    if ($this->db->set($post)->set('page_date_modified', 'NOW()', false)->insert('page')) {
      $this->session->set_userdata(array('message' => 'Страница "'.$post['page_title'].'" успешно добавлена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления страницы в базу!'));
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Обновляем страницу 
   */
  public function update($post){
  	$this->db->where('page_url', $post['page_url']);
    if ($post['category_id'] == '') {
      unset($post['category_id']);
    }
    if ($this->db->set('page_date_modified', 'NOW()', false)->update('page', $post)) {
      $this->session->set_userdata(array('message' => 'Страница "'.$post['page_title'].'" успешно обновлена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления страницы в базу!'));
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем страницу 
   */
  public function delete($page_url){
  	$page = $this->get_one($page_url);
  	if ($page != null) {
	    $this->db->where('page_url', $page_url);
	    $this->db->delete('page');
	    $this->session->set_userdata(array('message' => 'Страница "'.$page['page_title'].'" успешно удалена!'));  		
  	}
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список галарей
   */
  public function get_list($cat_url = '', $page_number = 1){
    $page['pages'] = array();
    $this->db->select('SQL_CALC_FOUND_ROWS p.*', false);
    $this->db->select('c.category_title, c.category_url');
    $this->db->from('page AS p');
    $this->db->join('category AS c', 'p.category_id = c.category_id', 'left');
    $this->db->order_by('category_title');
    if ($cat_url != '') 
      $this->db->where('c.category_url', $cat_url);
    if ($page_number) {
      $per_page = $this->common->get_per_page(); 
      $this->db->limit($per_page, ($page_number - 1) * $per_page);
    }
    $query = $this->db->get();
    #echo $this->db->last_query();
    foreach ($query->result_array() as $row){
    	$cut = explode('[cut]', $row['page_content']);
    	$row['page_content_cut'] = $cut[0];
      $page['pages'][] = $row;
    }
    $this->db->select('FOUND_ROWS() AS total_rows', false);
    $query = $this->db->get();
    $row = $query->row_array();
    $page['total_rows'] = $row['total_rows'];
    return $page;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем страницу
   */
  public function get_one($page_url){
    $this->db->select('p.*, c.category_url, c.category_title');
    $this->db->from('page p');
    $this->db->join('category c', 'c.category_id = p.category_id', 'left');
    $this->db->where('page_url', $page_url);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row_array();
    if ($row != null) {
      $row['page_content_raw'] =$row['page_content'];
      $cut = explode('[cut]', $row['page_content']);
      $row['page_content_cut'] = $cut[0];
      $row['page_content'] = str_replace('[cut]', '', $row['page_content']);
    }
    return $row; 
  }
  // ---------------------------------------------------------------------------

  /**
   * Добавлеет новую категорию
   */
  public function cat_insert($post){
    if ($this->db->insert('category', $post)) {
      $this->session->set_userdata(array('message' => 'Рубрика "'.$post['category_title'].'" успешно добавлена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления категории в базу!'));
    }
  }
  // ---------------------------------------------------------------------------
 
  /**
   * Обновляем категорю
   */
  public function cat_update($post, $category_url){
  	$this->db->where('category_url', $category_url);
    if ($this->db->update('category', $post)) {
      $this->session->set_userdata(array('message' => 'Рубрика "'.$post['category_title'].'" успешно обновлена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка обновления категории в базе!'));
    }
  }
  // ---------------------------------------------------------------------------
   
  /**
   * Обновляем категорю
   */
  public function cat_delete($category_url){
  	$cat = $this->cat_get_one($category_url);
    if ($this->db->delete('category', array('category_url' => $category_url))) {
      $this->session->set_userdata(array('message' => 'Категория "'.$cat['category_title'].'" успешно удалена!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка удаления категории из базы!'));
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список категори
   */
  public function cat_get_list($key = 'category_url'){
    $cat = array();
    $this->db->select('*');
    $this->db->from('category');
    $this->db->order_by('category_title');
    $query = $this->db->get();
    foreach ($query->result_array() as $row){
      $cat[$row[$key]] = $row['category_title'];
    }
    return $cat;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем категори
   */
  public function cat_get_one($category_url){
    $this->db->select('*');
    $this->db->from('category');
    $this->db->where('category_url', $category_url);
    $query = $this->db->get();
    return $query->row_array();
  }
  // ---------------------------------------------------------------------------  
}