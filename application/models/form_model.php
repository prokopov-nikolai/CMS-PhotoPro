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
  
  /**
   * Отправляем собщение
   * 
   *[form_id] => 1
    [fio] => Агейчик
    [telefon] => (904) 033-52-33
    [vash-otziv-s-kartikoy] => 

dfdfsgsdfgdfg

    [vipadayuschiy-spisok] => зачение 1
    [u-menya-est-avto] => on
   */
  public function send_message($data) {
    $fields = $this->get_fields($data['form_id']);
    $message_content = '';
    foreach($fields as $f){
      if (isset($data[$f['field_url']])) {
        $value = $this->$f['field_validate']($data[$f['field_url']]);
        $message_content .= "<br>{$f['field_title']}: {$value}";
      }
    }
    /*$this->load->library('mailer');
    $this->mailer->type = TYPE_MES_MIXED;
    $this->mailer->mixed_main_type = TYPE_MIXED_HTML;
    $this->mailer->boundary = "Gramada-123456789";
    $this->mailer->from = config_item('smtp_login');
    $this->mailer->to = $fields[0]['message_email'];
    $this->mailer->subject = $fields[0]['message_subject'];
    $this->mailer->data = "<html><body>{$message_content}</body></html>";
    $this->mailer->build_and_send_message(); */
   
    /* укажем email на который будем отсылать письмо*/
    $to= $fields[0]['message_email']; 
     
    /* тема письма */
    $subject = $fields[0]['message_subject'] . "\r\n";
    $subject = iconv('utf-8', 'cp1251', $subject ); 
 
    /* сообщение для отправки в формате HTML */
    $message_content = str_replace('../images/upload/', 'http://www.gramada33.ru/images/upload/', $message_content);
    $message = "<html><body>{$message_content}</body></html>";
    $message = iconv('utf-8', 'cp1251', $message ); 
 
    /* Укажем необходимые заголовки */
    $headers= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=windows-1251\r\n";
    $headers .= "From: GrAmada33.ru <robot@gramada33.ru>\r\n"; 
     
    /* отправим письмо */
    mail($to, $subject, $message, $headers);
    #prex($message_content);
    return true;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Ничего не валидируем
   */
  private function none($value){
    return $value;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция валидациитекста
   */
  private function number($value){   
    preg_match_all(' /([0-9.,]+)/', $value, $matches, PREG_PATTERN_ORDER);
    if (isset($matches[0])){
      return implode('', $matches[0]);
    }
    return '';
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция валидации текста
   */
  private function text($value){
    return preg_replace('/([<>`~]+)/', '', $value);;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция валидации текста
   */
  private function number_text($value){
    preg_match_all('/([0-9.,!?\"\':;a-zа-я]+)/i', $value, $matches, PREG_PATTERN_ORDER);
    if (isset($matches[0])){
      return implode('', $matches[0]);
    }
    return '';
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция валидации телефона
   */
  private function phone($value){
    preg_match_all('/([0-9() +-]+)/', $value, $matches, PREG_PATTERN_ORDER);
    if (isset($matches[0])){
      return implode('', $matches[0]);
    }
    return '';
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция валидации мылв
   */
  private function email($value){
    return $this->common->check_email($value);
  }
  // ---------------------------------------------------------------------------

  
}