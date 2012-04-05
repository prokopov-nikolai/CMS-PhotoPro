<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для создания и обработки форм
 */

Class Form_model extends CI_Model {
  
  /**
   * Конструктор
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список галарей
   */
  public function get($form_id){
    $this->db->select('*');
    $this->db->from('form');
    $this->db->where('form_id', $form_id);
    $this->db->limit(1);
    return $this->db->get_row();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список галарей
   */
  public function get_list(){
    $this->db->select('*');
    $this->db->from('form');
    $this->db->order_by('form_title');
    return $this->db->get_array();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем поля формы
   */
  public function get_fields($form_id){
    $this->db->select('*');
    $this->db->from('form_field ff');
    $this->db->join('form f', 'ff.form_id = f.form_id');
    $this->db->where('f.form_id', $form_id);
    $this->db->or_where('f.form_url', $form_id);
    return $this->db->get_array();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * ХТМЛ для вставки на страницу
   */
  public function get_to_page($form_id){
    $form_content = $this->common->array_to_json($this->get_fields($form_id));
    return "<span class='form' data-form-id='{$form_id}' data-form-content='{$form_content}'></span>";
    
  }
  /**
   * Добавляет новую страницу
   */
  public function add($form){
    if ($this->db->set($form)->insert('form')) {
      $form_id = $this->db->insert_id();
      $this->session->set_userdata(array('message' => 'Форма "'.$form['form_title'].'" успешно создана! Теперь можете добавить в нее нужные поля!'));
      return $form_id;
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления формы в базу!'));
      return false;
    }
  }
  // ---------------------------------------------------------------------------
   
  /**
   * Удаляем страницу 
   */
  public function delete($form_id){
    $form = $this->get($form_id);
  	$this->db->delete('form', array('form_id' => $form_id));
    $this->session->set_userdata(array('message' => 'Форма "'.$form['form_title'].'" успешно удалена!'));
    return true;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список галарей
   */
  public function add_field($field){
    #prex($field);
    $this->db->insert('form_field', $field);
    $this->session->set_userdata(array('message' => 'Поле "'.$field['field_title'].'" успешно добавлено!'));
  }
  // ---------------------------------------------------------------------------
  
}