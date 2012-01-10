<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Контроллер галерей в админке
 *  CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Gallery extends CMS_Controller {
  
  public function __construct(){
    parent::__construct();
    $this->load->library('image');
    $this->load->model('gallery_model');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная страница раздела галерей
   */
  public function index() {
  	$this->append_data('GALLERY', $this->gallery_model->get());
	  $this->display('gallery/index.html');
  }
  // ---------------------------------------------------------------------------

  /**
   * Добавляем галерею
   * @return unknown_type
   */
  public function insert(){
    // если не переданы данные
    $post = $this->input->post();
    $post['user_uniqid'] = $this->session->userdata('user_uniqid');
    if (sizeof($post) < 2 || !isset($post['gallery_title'])) {
      $this->session->set_userdata(array('error' => 'Не указаны данные для добавления галереи!'));
    } elseif (isset($post['gallery_title']) && $post['gallery_title'] == ''){
      $this->session->set_userdata(array('error' => 'Укажите название галереи!'));
    } else {    
      // добавим новую галерею
      $post['gallery_url'] = $this->common->get_url($post['gallery_title'], true, 'gallery');
      $this->gallery_model->insert($post);
    }

    // вернем обратно на страницу добавления
    $this->locate_referer();
  }
  // ---------------------------------------------------------------------------

  /**
   * Удаляем галерею 
   * @return unknown_type
   */
  public function delete($gallery_url){
    $this->gallery_model->delete($gallery_url);

    // вернем обратно на страницу добавления
    $this->locate_referer();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выводим содержимое и загружаем новое
   * @return unknown_type
   */
  public function load($gallery_url = ''){
    if ($gallery_url == ''){
      $this->locate_referer();
    }
    $gallery = $this->gallery_model;
    $gallery->url($gallery_url);
    $gallery = $gallery->get();
    #pr($gallery);
    $this->append_data('G', $gallery);
    $this->append_data('description_main', $gallery['gallery_description']);
    $this->append_data('subsubsection', $gallery['gallery_title']);
    $this->display('gallery/content-load.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Загружаем картинки в галерею
   */
  public function images_load(){
  	$post = $this->input->post();
  	#pr($post);
  	$image_source = config_item('image_source');
  	$max_width = $image_source['max_width'];
    $max_height = $image_source['max_height'];
  	foreach($post['upfile'] as $k => $path){
  	  $img = array();
  	  $img['image_id'] = uniqid();
      $filename = basename($path);
  	  $ext = strtolower(substr($filename, strlen($filename)-4, 4));
  	  
  	  $name = strtolower(substr($filename, 0, strlen($filename)-4)) . '-' . strtolower($k);
      
  	  // сформируем название которое будет видеть пользователь
      $img['image_name'] = $name . $ext;
  	  
      // сформируем название файла на сервере
  	  $img['image_filename'] = $name . substr($img['image_id'], 6, 7) . $ext;
  	  if (file_exists(ROOT . $path)) {
  	    $upfile = ROOT . "/images/source/{$img['image_filename']}";
    	  copy(ROOT . $path, $upfile);
    	  unlink(ROOT . $path);
    	  // изменим размер изображения
    	  $this->image->resize_source($upfile, $upfile, $max_width, $max_height);
    	  $img['user_uniqid'] = $this->session->userdata('user_uniqid');
    	  $img['gallery_id'] = $post['gallery_id'];
    	  $info = getimagesize($upfile);
    	  $img['image_width'] = $info[0];
        $img['image_height'] = $info[1];
        $img['image_size'] = filesize(ROOT . '/images/source/' . $img['image_filename']);
        $this->gallery_model->image_add($img);
        $tag = array();
        $tag['image_id'] = $img['image_id'];
    	  $tags = explode(',', $post['tags'][$k]);
    	  foreach($tags as $t) {
    	    if (trim($t) != '') {
            $tag['tag_name'] = trim($t);
    	      $this->gallery_model->tag_add($tag);
    	    }
    	  }
  	  }
  	}
  	header('Location: /' . config_item('admin_url') . '/gallery/load/' . $post['gallery_url'] . '/');
  } 
  
  /**
   * Удаляем картинку
   */
  public function image_delete(){
    $post = $this->input->post();
    if ($this->gallery_model->image_delete($post['image_ids']) == true) {
      $this->common->success_true(); 
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Устанавливаем обложку
   */
  public function cover_set() {
  	$post = $this->input->post();
  	$this->gallery_model->cover_update($post);
  	$this->common->success_true();
  }
}

/* End of file gallery.php */
/* Location: ./application/controllers/admin/gallery.php */