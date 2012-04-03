<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для работы с каталогами
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 * $this->url()->page()->limit()->get();
 */

Class Catalogpro_charmodel extends CI_Model {
    
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function get($char_id = null){
    $this->db->select('*');
    $this->db->from('catalogpro_char');
    if ($char_id) {
      $this->db->where('char_id', $char_id);
      $char = $this->db->get_array();
      $char = $char[0];
      if ($char['char_type'] == 'select')
        $char['char_values'] = $this->get_values($char_id);
      return $char;
    }
    $this->db->order_by('char_sort, char_title');
    return $this->db->get_array();
  }
  //----------------------------------------------------------------------------
  
  public function get_values($char_id){
    $this->db->select('char_value');
    $this->db->from('catalogpro_char_select');
    $this->db->where(array('char_id' => $char_id));
    return $this->db->get_array();  
  }
  //----------------------------------------------------------------------------
  
  public function get_search_values(){
    $prefix = $this->db->dbprefix;
    $sql = "SELECT 
       (SELECT max(char_value)
        FROM
          {$prefix}catalogpro_char_value
        WHERE
          char_id = 1) AS max_year
          
     , (SELECT min(char_value)
        FROM
          {$prefix}catalogpro_char_value
        WHERE
          char_id = 1) AS min_year
          
     , (SELECT max(char_value)
        FROM
          {$prefix}catalogpro_char_value
        WHERE
          char_id = 5) AS max_price
          
     , (SELECT min(char_value)
        FROM
          {$prefix}catalogpro_char_value
        WHERE
          char_id = 5) AS min_price
          
     , (SELECT max(char_value)
        FROM
          {$prefix}catalogpro_char_value
        WHERE
          char_id = 2) AS max_mileage
          
     , (SELECT min(char_value)
        FROM
          {$prefix}catalogpro_char_value
        WHERE
          char_id = 2) AS min_mileage";
    $query = $this->db->query($sql);    
    $row = $query->result_array();
    $chars = $row[0];
    
    $this->db->select('char_value');
    $this->db->from('catalogpro_char_select');
    $this->db->where('char_id', 10);
    $chars['kuzov'] = $this->db->get_array();

    $this->db->select('char_value');
    $this->db->from('catalogpro_char_select');
    $this->db->where('char_id', 8);
    $chars['dvig'] = $this->db->get_array();
        return $chars;  
  }
  //----------------------------------------------------------------------------
  
  public function get_all(){
    $chars = $this->get();
    foreach($chars as $k => $v){
      if ($v['char_type'] == 'select') {
        $chars[$k]['char_values'] = $this->get_values($v['char_id']);
      }
    }
    return $chars; 
  }
  //----------------------------------------------------------------------------
  
  public function add($set){
    $this->session->set_userdata(array('message' => 'Характеристика "' . $set['char_title'] .'" успешно добавлена!'));    
    return $this->db->insert('catalogpro_char', $set);
  }
  //----------------------------------------------------------------------------
  
  public function add_value($char_id, $char_value){
    $set = array(
      'char_id' => $char_id,
      'char_value' => trim($char_value)
    );
    return $this->db->insert('catalogpro_char_select', $set);
  }
  //----------------------------------------------------------------------------
  
  public function delete_value($char_id, $char_value){
    $where = array(
      'char_id' => $char_id,
      'char_value' => $char_value
    );
    return $this->db->delete('catalogpro_char_select', $where);
  }
  //----------------------------------------------------------------------------
  
  public function delete($char_id){
    $char = $this->get($char_id);
    $this->session->set_userdata(array('error' => 'Характеристика "' . $char['char_title'] .'" успешно удалена!'));    
    $this->db->delete('catalogpro_char', array('char_id' => $char_id));
    return $char['char_title'];
  }
  //----------------------------------------------------------------------------

  public function update($set, $char_id){
    $this->session->set_userdata(array('message' => 'Характеристика "' . $set['char_title'] .'" успешно обновлена!'));    
    return $this->db->update('catalogpro_char', $set, "char_id = {$char_id}");
  }
  //----------------------------------------------------------------------------
} 