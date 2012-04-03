<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для работы с каталогами
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 * $this->url()->page()->limit()->get();
 */

Class Catalogpro_catmodel extends CI_Model {
    
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function get($cat_id = null){
    $this->db->select('*');
    $this->db->from('catalogpro_category');
    if ($cat_id) {
      if ($cat_id > 0)
        $this->db->where('category_id', $cat_id);
      else 
        $this->db->where('category_url', $cat_id);
      $cat = $this->db->get_array();
      if (isset($cat[0]))
        return $cat[0];
      else 
        return false;
    }
    $this->db->order_by('category_title');
    return $this->db->get_array();
  }
  //----------------------------------------------------------------------------

  public function get_by_make(){
    $by_make = array();
    foreach($this->get() as $cat)
      $by_make[$cat['make_id']][] = $cat;
    return $by_make;
  }
  //----------------------------------------------------------------------------

  public function add($set){
    $this->session->set_userdata(array('message' => 'Модель "' . $set['category_title'] .'" успешно добавлена!'));    
    return $this->db->insert('catalogpro_category', $set);
  }
  //----------------------------------------------------------------------------
  
  public function delete($cat_id){
    $cat = $this->get($cat_id);
    $this->session->set_userdata(array('error' => 'Модель "' . $cat['category_title'] .'" успешно удалена!'));    
    return $this->db->delete('catalogpro_category', array('category_id' => $cat_id));
  }
  //----------------------------------------------------------------------------

  public function update($set, $cat_id){
    $this->session->set_userdata(array('message' => 'Модель "' . $set['category_title'] .'" успешно обновлена!'));    
    return $this->db->update('catalogpro_category', $set, "category_id = {$cat_id}");
  }
  //----------------------------------------------------------------------------
} 