<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 24.03.2012
 */

class Catalogpro_gram extends CMS_Plugin {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function index(){
    $this->display('catalogpro/views/admin/index.html');
  }
  //----------------------------------------------------------------------------

  public function add_product($prod_id = null){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    
    $post = $this->input->post();
    if (count($post) > 1 && !isset($post['user_email'])) {
      $set = $post;
      if (!isset($post['product_pod_zakaz'])) $set['product_pod_zakaz'] = '0'; 
      if (!isset($post['product_hide'])) $set['product_hide'] = '0';
      if (!isset($post['product_in_slider'])) $set['product_in_slider'] = '0';
      $set['product_url'] = $this->common->get_url($set['product_title'], true, 'catalogpro_product', 'product_url');
      if (isset($set['product_id'])) {
        // обновляем
        $prod_id = $set['product_id'];
        unset($set['product_id']);
        $this->catalogpro_productmodel->update($set, $prod_id);
      } else {
        // добавляем
        $prod_id = $this->catalogpro_productmodel->add($set);
      }
    }
    
    if ($prod_id && !in_array($prod_id, array('slider', 'special'))) {
      $p = $this->catalogpro_productmodel->get($prod_id, true);
      if ($p === false) show_404();
      $this->append_data('P', $p);  
    } if (in_array($prod_id, array('slider', 'special'))) {
      $this->append_data('filter', $prod_id);
      $this->append_data('PRODUCTS', $this->catalogpro_productmodel->get(null, true, $prod_id));
    } else {
      $this->append_data('PRODUCTS', $this->catalogpro_productmodel->get(null, true));  
    }
    $this->append_data('MAKE', $this->catalogpro_makemodel->get());
    $this->append_data('CHAR', $this->catalogpro_charmodel->get_all());
    $this->display('catalogpro/views/admin/add_product.html');
  }
  //----------------------------------------------------------------------------
  
  public function delete_product($prod_id = null){
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->catalogpro_productmodel->delete($prod_id);
    header("Location: /" . config_item('admin_url') . "/catalogpro/add_product/");
    exit;
  }
  //----------------------------------------------------------------------------
  
  public function add_make($make_id = null){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    
    $post = $this->input->post();
    if (count($post) > 1 && !isset($post['user_email'])) {
      $set = array(
          'make_title' => $post['make_title']
        , 'make_url' => $this->common->get_url($post['make_title'], true, 'catalogpro_make', 'make_url')
        , 'make_keywords' => $post['make_keywords']
        , 'make_description' =>  $post['make_description']

      );
      if (isset($post['make_id'])) {
        // обновляем
        $this->catalogpro_makemodel->update($set, $post['make_id']);
      } else {
        // добавляем
        $this->catalogpro_makemodel->add($set);
      }
    }
    
    if ($make_id) {
      $this->append_data('M', $this->catalogpro_makemodel->get($make_id));  
    } else {
      $this->append_data('MAKES', $this->catalogpro_makemodel->get());  
    }
    
    
    $this->display('catalogpro/views/admin/add_make.html');
    
  }
  //----------------------------------------------------------------------------
 
  public function delete_make($make_id){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->catalogpro_makemodel->delete($make_id);
    header("Location: /" . config_item('admin_url') . "/catalogpro/add_make/");
    exit;
  }
  //----------------------------------------------------------------------------
  
  public function add_category($cat_id = null){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');
    
    $post = $this->input->post();
    if (count($post) > 1 && !isset($post['user_email'])) {
      $set = array(
          'category_title' => $post['category_title']
        , 'category_url' => $this->common->get_url($post['category_title'], true, 'catalogpro_category', 'category_url')
        , 'category_keywords' => $post['category_keywords']
        , 'category_description' =>  $post['category_description']
        , 'make_id' =>  $post['make_id']
      );
      if (isset($post['category_id'])) {
        // обновляем
        $this->catalogpro_catmodel->update($set, $post['category_id']);
      } else {
        // добавляем
        $this->catalogpro_catmodel->add($set);
      }
    } 
    $this->append_data('MAKES', $this->catalogpro_makemodel->get());
    if ($cat_id) {
      $this->append_data('C', $this->catalogpro_catmodel->get($cat_id));  
    } else {
      $this->append_data('CATEGORY', $this->catalogpro_catmodel->get());  
    }
    $this->display('catalogpro/views/admin/add_category.html');    
  }
  //----------------------------------------------------------------------------

  public function delete_category($cat_id){
    
  }
  //----------------------------------------------------------------------------
  
  public function add_char($char_id = null){
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    
    $post = $this->input->post();
    if (count($post) > 1 && !isset($post['user_email'])) {
      $set = array(
          'char_title' => $post['char_title']
        , 'char_type' =>  $post['char_type']
        , 'char_sort' =>  $post['char_sort']
        , 'char_unit' =>  $post['char_unit']
      );
      if (isset($post['char_id'])) {
        // обновляем
        $this->catalogpro_charmodel->update($set, $post['char_id']);
      } else {
        // добавляем
        $this->catalogpro_charmodel->add($set);
      }
    }
    if ($char_id) {
      $this->append_data('CH', $this->catalogpro_charmodel->get($char_id));  
    } else {
      $this->append_data('CHARS', $this->catalogpro_charmodel->get());  
    }
    
    $this->display('catalogpro/views/admin/add_char.html');    
  }
  //----------------------------------------------------------------------------
  
  public function delete_char($char_id){
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    $this->catalogpro_charmodel->delete($char_id);
    header("Location: /" . config_item('admin_url') . "/catalogpro/add_char/");
    exit;
  }
  //----------------------------------------------------------------------------
  
  public function add_char_select_value($char_id){
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    $post = $this->input->post();
    if (isset($post['char_value']) && $post['char_value'] != null){
      $this->catalogpro_charmodel->add_value($char_id, $post['char_value']);
      $this->common->success_true();
    }
    $this->common->success_false();
  }
  //----------------------------------------------------------------------------

  public function delete_char_select_value($char_id){
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    $post = $this->input->post();
    if (isset($post['char_value']) && $post['char_value'] != null){
      $this->catalogpro_charmodel->delete_value($char_id, $post['char_value']);
      $this->common->success_true();
    }
    $this->common->success_false();
  }
  //----------------------------------------------------------------------------
  
  public function get_make_category($make_id){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    echo $this->common->array_to_json($this->catalogpro_makemodel->get_category($make_id));
    exit;
  }
  //----------------------------------------------------------------------------
  
  public function upload_image($from = null){
    $this->load->library('image');
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $post = $this->input->post();
    if ($from == 'inet'){
      $_FILES['photo']['name'][] = basename($post['url']);
      $_FILES['photo']['tmp_name'][] = $post['url'];
    }
    $image_source = config_item('image_source');
    $max_width = $image_source['max_width'];
    $max_height = $image_source['max_height'];
    $res = array();
    foreach($_FILES['photo']['name'] as $k => $filename){
      $img = array();
      $img['image_id'] = uniqid();
      $ext = strtolower(substr($filename, strlen($filename)-4, 4));
      $filename = substr($filename, 0, strlen($filename)-4);
      $filename = $this->common->get_url($filename);
      $filepath = $_FILES['photo']['tmp_name'][$k];
      if ($from == 'inet'){
        $name = $filename . '-' .
                strtolower(substr(uniqid(), 0, 6));
      } else {
         $name = $filename . '-' .
                 strtolower(substr($filepath, strlen($filepath)-6, 6));
      }
      
      // сформируем название которое будет видеть пользователь
      $img['image_name'] = $name . $ext;
      $res[] = $name . $ext;
      // сформируем название файла на сервере
      $img['image_filename'] = $name . substr($img['image_id'], 6, 7) . $ext;
      $upfile = ROOT . "/images/source/{$img['image_filename']}";
      copy($filepath, $upfile);
      if (!$from) unlink($filepath);
      // изменим размер изображения
      $this->image->resize_source($upfile, $upfile, $max_width, $max_height);
      $img['user_uniqid'] = $this->session->userdata('user_uniqid');
      $img['product_id'] = $post['product_id'];
      $info = getimagesize($upfile);
      $img['image_width'] = $info[0];
      $img['image_height'] = $info[1];
      $img['image_size'] = filesize(ROOT . '/images/source/' . $img['image_filename']);
      if (file_exists($upfile))
        $this->catalogpro_productmodel->add_image($img);
    }
    if ($from == 'inet')
      echo $res[0];
    else
      echo $this->common->array_to_json($res);
    exit;
  }
  //----------------------------------------------------------------------------

  public function delete_image(){
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $post = $this->input->post();
    $this->catalogpro_productmodel->delete_image($post);
    exit;
  }
  //----------------------------------------------------------------------------
}
  