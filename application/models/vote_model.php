<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель голосования
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
Class Vote_model extends CI_Model {
  
  /**
   * Конструктор
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляем опрос
   */
  public function insert($post){
  	$post['user_uniqid'] = $this->session->userdata('user_uniqid');
    if ($this->db->insert('vote', $post)) {
      $this->session->set_userdata(array('message' => 'Вопрос "'.$post['vote_question'].'" успешно добавлен!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка добавления вопроса в базу!'));
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем опрос
   */
  public function delete($vote_id){
  	if ($this->db->delete('vote', array('vote_id' => $vote_id))) {
      $this->session->set_userdata(array('message' => 'Опрос успешно удаден!'));
    } else {
      $this->session->set_userdata(array('error' => 'Ошибка удаления  опроса!'));
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем список опросов
   */
  public function get($vote_id = '', $limit = ''){
    $vote = array();
    $this->db->select('*');
    $this->db->from('vote');
    if ($vote_id != '') {
      $this->db->where('vote_id', $vote_id);
    }
    if ($limit != '') {
      $this->db->limit($limit);
    }
    $this->db->order_by('vote_date', 'desc');
    $query = $this->db->get();
    foreach ($query->result_array() as $row){
      $vote[$row['vote_id']] = array(
        'vote_id'  => $row['vote_id'],
        'question' => $row['vote_question'],
        'answers'  => $this->common->array_to_json($this->get_result($row['vote_id']))
      );
    }
    return $vote;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Голосуем
   */
  public function voted($answer_id){
  	$this->db->insert('vote_log', array('answer_id' => $answer_id));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавлеяем ответ
   */
  public function insert_answer($post){
  	$this->db->insert('vote_answer', $post);
  	return $this->get_result($post['vote_id']);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Получаем результаты опроса
   */
  public function get_result($vote_id){
  	$results = array();
    $sql = "SELECT a.vote_id
						     , a.answer_id
						     , a.answer_text
						     , count(l.answer_id) AS count
						FROM
						  {$this->db->dbprefix}vote_answer AS a
						LEFT JOIN {$this->db->dbprefix}vote_log AS l
						ON a.answer_id = l.answer_id
						WHERE
						  a.vote_id = {$vote_id}
						GROUP BY
						  a.vote_id
						, a.answer_id";
    $query = $this->db->query($sql);
    foreach ($query->result_array() as $row){
      $results[] = $row;
    }
    return $results;
  } 
  // ---------------------------------------------------------------------------
  
}