<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер настроек
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Settings extends CMS_Controller {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Основые настройки 
   */
  public function index(){
    $this->config->load('config');
    $this->config->load('database');
    $post = $this->input->post();
    if (sizeof($post) > 1) {
    	foreach($post as $k => $v) {
    	  if ($v == 'FALSE') $v = FALSE;
        if ($v == 'TRUE') $v = TRUE;
    	  $this->config->set_item($k, $v);
    	}
      $this->config->save();
      header('Location: /' . config_item('admin_url') . '/settings/');
      exit;
    }
    $this->load->model('gallery_model');
    $this->append_data('TEMPLATE', $this->_get_templates());
    $this->append_data('CONFIG', $this->config->config);
    $this->append_data('GALLERY', $this->gallery_model->get());
    $this->display('settings.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удаляем кеш системы
   */
  public function cache_delete() {
  	$dir = ROOT . '/application/cache/';
    $this->_delete_cache($dir);
    $this->session->set_userdata(array('message' => 'Кэш успешно сброшен!'));
    header('Location: /' . config_item('admin_url') . '/'); exit;
  }
  // ---------------------------------------------------------------------------
  
  /*
   * Удаляем кеш
   */
  private function _delete_cache($dir){
  	if (is_dir($dir)) {
  		if ($dh = opendir($dir)) {
	  		while (($file = readdir($dh)) !== false) {
	  			if (filetype($dir . $file) == 'dir' &&
	  			    $file != '.' && $file != '..' && $file != '.svn') {
	  			  $this->_delete_cache($dir . $file . '/');
	  			} else if (filetype($dir . $file) == 'file' &&
	  			    $file != '.htaccess' && $file != 'index.html'){
	  			  unlink($dir . $file);
	  			}
               
	      }
	      if ($file != '.' && $file != '..' && $file != '.svn'){
          closedir($dir);
          opendir($dir.'.');
	      	rmdir($dir);
	      }
  		}
  	}
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлекаем шаблоны
   */
  private function _get_templates(){
  	$template = array();
  	$view_path = ROOT . '/' . APPPATH . 'views/';
  	if ($dh = opendir($view_path)) {
  		while(($f = readdir($dh)) !== false){
  			if (filetype($view_path . $f) == 'dir' && $f != 'admin' && $f != '.' && $f != '..'){
  				$screen = $view_path . $f . '/screenshot.jpg';
  				if (file_exists($screen)) {
  					$screen = '/' . APPPATH . 'views/' . $f . '/screenshot.jpg';
  				} else {
  					$screen = '/images/no-screenshot.png';
  				}
  				$template[] = array('template_name' => $f, 'screenshot' => $screen);
  			}
  		}
  	}
  	return $template;
  }
  // ---------------------------------------------------------------------------
}

/* End of file settings.php */
/* Location: ./application/controllers/admin/settings.php */