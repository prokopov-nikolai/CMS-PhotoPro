<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Голосования
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Vote extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
    $this->load->model('vote_model');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница сайта
   */
  public function index() {
  	$this->append_data('VOTES', $this->vote_model->get());
    $this->display('vote/list.html');
  }
  
  /**
   * Добавляем вопрос
   * @return unknown_type
   */
  public function insert(){
    // если не переданы данные
    $post = $this->input->post();
    if (!isset($post['vote_question'])) {
      $this->session->set_userdata(array('error' => 'Не указан вопрос!'));
    } elseif (isset($post['vote_question']) && $post['vote_question'] == ''){
      $this->session->set_userdata(array('error' => 'Вопрос не может быть пустым!'));
    } else {    
      // добавим новый вопрос
      $this->vote_model->insert($post);
    }

    // вернем обратно на страницу добавления
    $this->locate_referer();
  }
  // ---------------------------------------------------------------------------

  /**
   * Удаляем опрос
   * @return unknown_type
   */
  public function delete($vote_id){
    // если не переданы данные
     $this->vote_model->delete($vote_id);
     
    // вернем обратно на страницу добавления
    $this->locate_referer();
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Добавляем ответы
   * @return unknown_type
   */
  public function insert_answer(){
  	$post = $this->input->post();
  	$answer = $this->vote_model->insert_answer($post);
  	echo $this->common->array_to_json($answer);
  }
  // ---------------------------------------------------------------------------
}

/* End of file vote.php */
/* Location: ./application/controllers/admin/vote.php */