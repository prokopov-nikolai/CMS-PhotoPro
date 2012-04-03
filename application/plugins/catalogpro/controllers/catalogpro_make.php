<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 24.03.2012
 */

class Catalogpro_make extends CMS_Plugin {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function show($make_url, $category_url = null, $page_number = 1){
    if ($page_number == '') $page_number = 1;
    if ($category_url == 'page') {
      $category_url = null;
    }
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');
    #$this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    
    // производитель 
    $make = $this->catalogpro_makemodel->get($make_url);
    $this->append_data('M', $make);
    
    // рубрикатор
    $this->append_data('MAKE', $this->catalogpro_makemodel->get());
    $catsbymake = $this->catalogpro_catmodel->get_by_make($make['make_id']);
    $this->append_data('CATSBYMAKE', $catsbymake);
    $subcats = $catsbymake[$make['make_id']];
    $this->append_data('SUBCATS', $subcats);

    $step = (int) ceil(count($subcats) / 4);
    $d = $step * 4 - count($subcats);
    $this->append_data('next4', 4 * $step - 1 - $d);
    if ($d != 0) --$d; 
    $this->append_data('next3', 3 * $step - 1 - $d);
    if ($d != 0) --$d; 
    $this->append_data('next2', 2 * $step - 1 - $d);
    if ($d != 0) --$d; 
    $this->append_data('next1', 1 * $step - 1 - $d);
        
    // категори (модель)
    $cat['category_id'] = null;
    if ($category_url && $category_url != 'page') {
      $cat = $this->catalogpro_catmodel->get($category_url);
      $this->append_data('CAT', $cat);
    }
    
    // товары (авто)
    $products = $this->catalogpro_productmodel->get_make_cat($make['make_id'], $cat['category_id'], $page_number);
    $this->append_data('PRODUCTS', $products['products']);
    $total_rows = $products['count'];
    $total_pages = ceil($total_rows / $this->common->get_per_page());
    if ($total_pages == 0) $total_pages = 1;
    if ($page_number > $total_pages) { 
      header("Location: /make/{$make_url}/");
      exit; 
    }
    $this->append_data('total_rows', $total_rows);
    $this->append_data('total_pages', $total_pages);
    $this->append_data('current_page', $page_number);
    $this->append_data('num_links', 4);
    if ($category_url) 
      $base_url = "/make/{$make_url}/{$category_url}/page/";
    else 
      $base_url = "/make/{$make_url}/page/";
    $this->append_data('base_url', $base_url);
    
    
    $this->display('catalogpro/views/make.html');
  }
  //----------------------------------------------------------------------------
  
  public function get_category($make_id){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    echo $this->common->array_to_json($this->catalogpro_makemodel->get_category($make_id));
    exit;
  }
  //----------------------------------------------------------------------------
}
  