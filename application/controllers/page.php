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
  		$this->append_data('P', $page);
      $this->display('page.html');
  	} else {
  		$path = ROOT . '/' . APPPATH . 'views/' . config_item('site_template') . 
  		        '/page/' . $page_url . '.html';
  		if (file_exists($path)) {
  			$this->display("page/{$page_url}.html");
  		} else {
    		show_404();
  		}
  	}
  }
}

/* End of file page.php */
/* Location: ./application/controllers/page.php */