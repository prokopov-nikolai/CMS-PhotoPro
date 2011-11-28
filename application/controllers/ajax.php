<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер рубрик
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
Class Ajax extends CMS_Controller {
	public function __construct(){
		parent::__construct();
	}
	/*
	 * Заявка на email
	 */
  public function order(){
  	$post = $this->input->post();
  	$mail = '';
  	foreach ($post as $k => $v){
      switch ($k) {
      	case 'tarif_name':
      	  $mail .= "Тариф: {$v}\n" ;
      	  break;
        case 'fio':
          $mail .= "ФИО: {$v}\n" ;
          break;
        case 'address':
          $mail .= "Адрес: {$v}\n" ;
          break;
        case 'phone':
          $mail .= "Контактный телефон: {$v}\n" ;
          break;
          
      	default:
      		;
      	break;
      }
  	}
    mail(config_item('admin_email'), 'Новая заявка на подключение', $mail); 
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Ставим голос для пользователя
   */
  function voted(){
    $post = $this->input->post();
  	$this->load->model('vote_model');
  	$this->vote_model->voted($post['aid']);
  	set_cookie('vote-' . $post['vid'], $post['aid'], 60*60*24*356);
  	echo $this->common->array_to_json($this->vote_model->get_result($post['vid']));
  }
}