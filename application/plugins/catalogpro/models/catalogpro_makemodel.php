<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для работы с каталогами
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 * $this->url()->page()->limit()->get();
 */

Class Catalogpro_makemodel extends CI_Model {
    
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function get($make_id = null){
    $this->db->select('*');
    $this->db->from('catalogpro_make');
    if ($make_id) {
      if ($make_id > 0)
        $this->db->where('make_id', $make_id);
      else 
        $this->db->where('make_url', $make_id);     
      $make = $this->db->get_array();
      if (isset($make[0]))
        return $make[0];
      else 
        return false;
    }
    $this->db->order_by('make_title');
    return $this->db->get_array();
  }
  //----------------------------------------------------------------------------

  public function add($set){
    $this->session->set_userdata(array('message' => 'Производитель "' . $set['make_title'] .'" успешно добавлен!'));    
    return $this->db->insert('catalogpro_make', $set);
  }
  //----------------------------------------------------------------------------
  
  public function delete($make_id){
    $make = $this->get($make_id);
    $this->session->set_userdata(array('error' => 'Производитель "' . $make['make_title'] .'" успешно удален!'));    
    return $this->db->delete('catalogpro_make', array('make_id' => $make_id));
  }
  //----------------------------------------------------------------------------

  public function update($set, $make_id){
    $this->session->set_userdata(array('message' => 'Производитель "' . $set['make_title'] .'" успешно обновлен!'));    
    return $this->db->update('catalogpro_make', $set, "make_id = {$make_id}");
  }
  //----------------------------------------------------------------------------
  
  public function get_category($make_id){
    $this->db->select('category_id, category_title');
    $this->db->from('catalogpro_category');
    if ($make_id > 0)
      $this->db->where('make_id', $make_id);
    return $this->db->get_array();
  }
  //----------------------------------------------------------------------------
  
  public function search($make_name){
    $return = array();
    $this->db->select('*');
    $this->db->from('catalogpro_make');
    $this->db->or_like('make_title', $make_name);
    $this->db->or_like('make_keywords', $make_name);
    foreach($this->db->get_array() as $make){
      $return[] = array(
                    'url' => '/make/' . $make['make_url'],
                    'name' => $make['make_title']
                  );
    }
    #echo $this->db->last_query();
    return $return;
  }
  //----------------------------------------------------------------------------
  
  public function search_cat($cat_name){
    $cat_name = explode(' ', $cat_name);
    foreach($cat_name as $k => $v){
      $cat_name[$k] = trim($v);
    }
    
    $return = array();
    $this->db->select('*');
    $this->db->from('catalogpro_category c');
    $this->db->join('catalogpro_make m', 'm.make_id = c.make_id', 'left');
    foreach($cat_name as $word) {
      if (strlen($word) > 1) {
        $this->db->or_like('category_title', $word);
        $this->db->or_like('category_keywords', $word);
      }
    }
    foreach($this->db->get_array() as $cat){
      $return[] = array(
                    'url' => '/make/' . $cat['make_url'] . '/' . $cat['category_url'] . '/',
                    'name' => $cat['make_title'] . ' ' . $cat['category_title']
                  );
    }
    #echo $this->db->last_query();
    return $return;
  }
  //----------------------------------------------------------------------------
} 