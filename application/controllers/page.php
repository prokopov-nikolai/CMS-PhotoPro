<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер страниц
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Page extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
    $this->load->model('page_model');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлечем страницу
   */
  public function show($page_url) {
  	#die($page_url);
  	$page = $this->page_model->get_one($page_url);
  	if ($page != null) {
  	  preg_match_all('/.*(\[form=([a-z0-9-]+)\])/', $page['page_content'], $matches, PREG_SET_ORDER);
      if (isset($matches[0][2])) {
        $this->load->model('form_model');
        $form = $this->input->post();
        if (count($form) > 1 && $form['form_id']) {
          prex($form);
          $form_content = 'Данные успешно отправлены!';
        } else {
          $form_content = $this->form_model->get_to_page($matches[0][2]);
        }
        $this->append_data('form_exist', 1);
        $page['page_content'] = str_replace($matches[0][1], $form_content, $page['page_content']);
      }
  		$this->append_data('P', $page);
      $this->append_data('page_id', $page['page_url']);
      $this->display('page.html');
  	} else {
  		$path = ROOT . '/' . APPPATH . 'views/' . config_item('site_template') . 
  		        '/page/' . $page_url . '.html';
  		if (file_exists($path)) {
        $this->append_data('page_id', $page_url);
  			$this->display("page/{$page_url}.html");
  		} else {
    		show_404();
  		}
  	}
  }
}

/* End of file page.php */
/* Location: ./application/controllers/page.php */