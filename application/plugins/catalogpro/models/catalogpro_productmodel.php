<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Модель для работы с каталогами
 * CMS PhotoPro
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 * 
 */

Class Catalogpro_productmodel extends CI_Model {
    
  public function __construct(){
    parent::__construct();
    #exit('!!!');
  }
  //----------------------------------------------------------------------------
  
  public function get($prod_id = null, $admin = false, $filter = null){
    $this->db->select('*');
    $this->db->from('catalogpro_product p');
    $this->db->join('catalogpro_make m', 'm.make_id = p.make_id');
    if ($prod_id) {
      if ($prod_id > 0)
        $this->db->where('product_id', $prod_id);
      else 
        $this->db->where('product_url', $prod_id);
      $prod = $this->db->get_array();
      if (!isset($prod[0])) return false;
      $prod[0]['char_values'] = $this->_get_char_values($prod[0]['product_id']);
      $prod[0]['photo'] = $this->_get_photo($prod[0]['product_id']);
      return $prod[0];
    }
    if (!$admin) $this->db->where('product_hide', 0);
    if ($filter == 'slider') $this->db->where('product_in_slider', 1);  
    if ($filter == 'special') $this->db->where('product_special', 1);  
    $this->db->order_by('make_title, product_title');
    return $this->db->get_array();
  }
  //----------------------------------------------------------------------------

  public function get_search($page_number = 1){
    $get = $this->input->get();
    $this->db->select('SQL_CALC_FOUND_ROWS *
                       , ch.char_value as product_price
                       , ch2.char_value as product_dvig
                       , ch3.char_value as product_kuzov', false);
                       #, ch1.char_value as product_kpp
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    #$this->db->join('catalogpro_char_value ch1', 'ch1.product_id = p.product_id AND ch1.char_id = 3', 'left');
    $this->db->join('catalogpro_char_value ch2', 'ch2.product_id = p.product_id AND ch2.char_id = 8', 'left');
    $this->db->join('catalogpro_char_value ch3', 'ch3.product_id = p.product_id AND ch3.char_id = 10', 'left');
    // пробег
    $this->db->join('catalogpro_char_value ch4', 'ch4.product_id = p.product_id AND ch4.char_id = 2', 'left');
    // год выпуска
    $this->db->join('catalogpro_char_value ch5', 'ch5.product_id = p.product_id AND ch5.char_id = 1', 'left');
    if ($page_number) {
      $per_page = $this->common->get_per_page(); 
      $this->db->limit($per_page, ($page_number - 1) * $per_page);
    }
    
    // условия поиска
    if (isset($get['category_id']) && $get['category_id'] > 0)
      $this->db->where('p.category_id', $get['category_id']);
    if (isset($get['make_id']) && $get['make_id'] > 0)
      $this->db->where('p.make_id', $get['make_id']);
    if (isset($get['dvig']))
      $this->db->where('ch2.char_value', urldecode($get['dvig']));
    if (isset($get['kuzov']))
      $this->db->where('ch3.char_value', urldecode($get['kuzov']));
    if (isset($get['price_range'])){
      preg_match_all('/([0-9\\s]+)\\s-\\s([0-9\\s]+)/', $get['price_range'], $price_range, PREG_SET_ORDER);
      $this->db->where('ch.char_value >=', intval(str_replace(' ', '', $price_range[0][1])));
      $this->db->where('ch.char_value <=', intval(str_replace(' ', '', $price_range[0][2])));
    }
    if (isset($get['mileage_range'])){
      preg_match_all('/([0-9\\s]+)\\s-\\s([0-9\\s]+)/', $get['mileage_range'], $mileage_range, PREG_SET_ORDER);
      $this->db->where('ch4.char_value >=', intval(str_replace(' ', '', $mileage_range[0][1])));
      $this->db->where('ch4.char_value <=', intval(str_replace(' ', '', $mileage_range[0][2])));
    }
    if (isset($get['year_range'])){
      preg_match_all('/([0-9\\s]+)\\s-\\s([0-9\\s]+)/', $get['year_range'], $year_range, PREG_SET_ORDER);
      $this->db->where('ch5.char_value >=', intval(str_replace(' ', '', $year_range[0][1])));
      $this->db->where('ch5.char_value <=', intval(str_replace(' ', '', $year_range[0][2])));
    }
    
    if (isset($get['name'])){
      $this->db->or_like('product_title', $get['name']);
      $this->db->or_like('product_keywords', $get['name']);
    }

    $this->db->group_by('p.product_id');
    $this->db->order_by('product_title');
    $products = $this->db->get_array();
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    #echo $this->db->last_query();
    
    $this->db->select('FOUND_ROWS() AS total_rows', false);
    $query = $this->db->get();
    $row = $query->row_array();
    
    return array(
      'products' => $products,
      'count'    => $row['total_rows']
    );
  }
  //----------------------------------------------------------------------------
  
  public function search_autocomplete($search){
    // попробуем найти точное совпадение
    $return = array();
    
    $this->db->select('*');
    $this->db->from('catalogpro_product');
    $this->db->or_like('product_title', $search);
    $this->db->or_like('product_keywords', $search);
    foreach($this->db->get_array() as $prod){
      $return[] = array(
                    'url' => '/car/' . $prod['product_url'] . '/',
                    'name' => $prod['product_title']
                  );
    }
    #echo $this->db->last_query(); 
    return $return;
  }
  //----------------------------------------------------------------------------
  
  public function get_make_cat($make_id, $category_id, $page_number = 1){
    $this->db->select('SQL_CALC_FOUND_ROWS *, ch.char_value as product_price', false);
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->where('make_id', $make_id);
    if ($category_id)
      $this->db->where('category_id', $category_id);
    if ($page_number) {
      $per_page = $this->common->get_per_page(); 
      $this->db->limit($per_page, ($page_number - 1) * $per_page);
    }
    $this->db->group_by('p.product_id');
    $this->db->order_by('product_title');
    $products = $this->db->get_array();
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    
    $this->db->select('FOUND_ROWS() AS total_rows', false);
    $query = $this->db->get();
    $row = $query->row_array();
    
    return array(
      'products' => $products,
      'count'    => $row['total_rows']
    );
  } 
  //----------------------------------------------------------------------------
  
  public function get_popular(){
    $this->db->select('*, ch.char_value as product_price');
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->group_by('p.product_id');
    $this->db->order_by('product_views desc');
    $this->db->limit(config_item('count_popular'));
    $products = $this->db->get_array();
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    return $products;
  } 
  //----------------------------------------------------------------------------
  
  public function get_special(){
    $this->db->select('*, ch.char_value as product_price');
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->where('product_special', 1);
    $this->db->group_by('p.product_id');
    $this->db->order_by('product_price desc');
    $this->db->limit(4);
    $products = $this->db->get_array();
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    return $products;
  } 
  //----------------------------------------------------------------------------
  
  public function get_slider(){
    $this->db->select('*
                       , ch.char_value as product_price
                       , ch1.char_value as product_kpp
                       , ch2.char_value as product_dvig
                       , ch3.char_value as product_kuzov');
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->join('catalogpro_char_value ch1', 'ch1.product_id = p.product_id AND ch1.char_id = 3', 'left');
    $this->db->join('catalogpro_char_value ch2', 'ch2.product_id = p.product_id AND ch2.char_id = 8', 'left');
    $this->db->join('catalogpro_char_value ch3', 'ch3.product_id = p.product_id AND ch3.char_id = 10', 'left');
    $this->db->where('p.product_in_slider', 1);
    $this->db->group_by('p.product_id');
    $this->db->order_by('product_title');
    $products = $this->db->get_array();
    #echo $this->db->last_query(); exit;
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    return $products;

  } 
  //----------------------------------------------------------------------------
  
  public function get_similar($product_id, $make_id){
    $this->db->select('*, ch.char_value as product_price');
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->group_by('p.product_id');
    $this->db->order_by('RAND()', false);
    $this->db->where('p.product_id !=', $product_id);
    $this->db->where('p.make_id', $make_id);
    $this->db->limit(4);
    $products = $this->db->get_array();
    #echo  $this->db->last_query(); exit;
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    return $products;

  } 
  //----------------------------------------------------------------------------
  
  public function get_last(){
    $this->db->select('*, ch.char_value as product_price');
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->group_by('p.product_id');
    $this->db->order_by('p.product_date_add DESC');
    $this->db->limit($this->common->get_per_page());
    $products = $this->db->get_array();
    #echo  $this->db->last_query(); exit;
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    return $products;
  }
  //----------------------------------------------------------------------------
  
  public function get_availabile($page_number = 1){
    $this->db->select('SQL_CALC_FOUND_ROWS *, ch.char_value as product_price', false);
    $this->db->from('catalogpro_product p');
    $this->db->join('image i', 'i.product_id = p.product_id', 'left');
    $this->db->join('catalogpro_char_value ch', 'ch.product_id = p.product_id AND ch.char_id = 5', 'left');
    $this->db->group_by('p.product_id');
    $this->db->order_by('p.product_title');
    if ($page_number) {
      $per_page = $this->common->get_per_page(); 
      $this->db->limit($per_page, ($page_number - 1) * $per_page);
    }
    $products = $this->db->get_array();
    #echo  $this->db->last_query(); exit;
    foreach($products as $k => $v) 
      $products[$k]['product_price'] = number_format($products[$k]['product_price'], 0, '.',' ');
    
    $this->db->select('FOUND_ROWS() AS total_rows', false);
    $query = $this->db->get();
    $row = $query->row_array();
    
    return array(
      'products' => $products,
      'count'    => $row['total_rows']
    );
  }
  //----------------------------------------------------------------------------
  
  public function add($set){
    $chars = $set['char_values'];
    unset($set['chor_ar_values']);
    $this->session->set_userdata(array('message' => 'Автомобиль "' . $set['product_title'] .'" успешно добавлен!'));    
    $this->db->insert('catalogpro_product', $set);
    $prod_id = $this->db->insert_id();
    
    $this->_update_char_values($prod_id, $chars);
    return $prod_id;
  }
  //----------------------------------------------------------------------------
  
  public function add_image($img){
    return $this->db->insert('image', $img);
  }
  //----------------------------------------------------------------------------
  
  public function delete($prod_id){
    $prod = $this->get($prod_id);
    $this->session->set_userdata(array('error' => 'Автомобиль "' . $prod['product_title'] .'" успешно удален!'));    
    return $this->db->delete('catalogpro_product', array('product_id' => $prod_id));
  }
  //----------------------------------------------------------------------------

  public function delete_image($where){
    $this->db->select('image_filename');
    $this->db->from('image');
    $this->db->where($where);
    $this->db->limit(1);
    $img = $this->db->get_array();
    unlink(ROOT . '/images/source/' . $img[0]['image_filename']);
    return $this->db->delete('image', $where);
  }
  //----------------------------------------------------------------------------
  
  public function update($set, $prod_id){
    $chars = $set['char_values'];
    unset($set['char_values']);
    unset($set['product_url']);
    $this->session->set_userdata(array('message' => 'Автомобиль "' . $set['product_title'] .'" успешно обновлен!'));    
    $this->db->update('catalogpro_product', $set, "product_id = '{$prod_id}'");
    
    $this->_update_char_values($prod_id, $chars);
    return true;
  }
  //----------------------------------------------------------------------------
  
  public function increase_views($product_url){
    return $this->db->query("UPDATE {$this->db->dbprefix}catalogpro_product
                      SET product_views = product_views + 1
                      WHERE product_url = '{$product_url}'");
  }
  //----------------------------------------------------------------------------
  
  private function _get_char_values($prod_id){
    $this->db->select('*');
    $this->db->from('catalogpro_char_value cv');
    $this->db->join('catalogpro_char c', 'c.char_id = cv.char_id');
    $this->db->order_by('char_sort');
    $this->db->where('product_id', $prod_id);
    $values = array();
    foreach($this->db->get_array() as $ch) {
      if (is_numeric($ch['char_value']) && (intval($ch['char_value']) > 2030 || intval($ch['char_value']) < 1970)) 
        $ch['char_value'] = number_format($ch['char_value'], 0, '.',' ');
      $values[$ch['char_id']] = $ch['char_value'];
    }
    return $values;
  }
  //----------------------------------------------------------------------------
  
  private function _get_photo($prod_id){
    $this->db->select('*');
    $this->db->from('image');
    $this->db->where('product_id', $prod_id);
    $photo = array();
    foreach($this->db->get_array() as $img) {
      $photo[] = $img['image_name'];
    }
    return $photo;
  }
  //----------------------------------------------------------------------------
  
  private function _update_char_values($prod_id, $chars){
    $this->db->delete('catalogpro_char_value', array('product_id' => $prod_id));
    foreach($chars as $k => $v) {
      if ($v){
        if ($k == 5 || $k == 2) $v = str_replace(' ', '', $v);
        $this->db->insert('catalogpro_char_value', array(
            'product_id' => $prod_id
          , 'char_id' => $k
          , 'char_value' => $v));
      }
    }
    return true;
  }
  //----------------------------------------------------------------------------
} 