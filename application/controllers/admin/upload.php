<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Контроллер загрузки изображений в админке
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Upload extends CMS_Controller {
  
  public function __construct(){
    parent::__construct();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница раздела пользователей
   */
  public function index() {
    $this->load->library('image');
    $_FILES['file']['type'] = strtolower($_FILES['file']['type']);
    if  ($_FILES['file']['type'] == 'image/png' 
      || $_FILES['file']['type'] == 'image/jpg' 
      || $_FILES['file']['type'] == 'image/gif' 
      || $_FILES['file']['type'] == 'image/jpeg'
      || $_FILES['file']['type'] == 'image/pjpeg'){ 

      $filename = $_FILES['file']['name'];
      $ext = strtolower(substr($filename, strlen($filename)-4, 4));
      $filename = substr($filename, 0, strlen($filename)-4);
      $filename = $this->common->get_url($filename);
      $path_result = "/images/upload/" . $filename . '-' . strtolower(substr(uniqid(), 0, 6)) . $ext;     
      $filepath = $_FILES['file']['tmp_name'];
      $upfile = ROOT . $path_result;
      copy($filepath, $upfile);
  
      // изменим размер изображения
      $info = getimagesize($upfile);
      $this->image->resize_source($upfile, $upfile, $info[0], $info[1]);
      exit($path_result);
    }
  }
  // ---------------------------------------------------------------------------

  /**
   * Удаляем картинку
   */
  public function  file_delete() {
    echo $_GET['delete'];
  }
}

/* End of file upload.php */
/* Location: ./application/controllers/admin/upload.php */