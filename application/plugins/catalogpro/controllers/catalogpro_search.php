<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 24.03.2012
 */

class Catalogpro_search extends CMS_Plugin {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function index(){
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');
    $get = $this->input->get();
    
    if(!isset($get['page'])) {
      $page_number = 1;
    } else {
      $page_number = intval($get['page']);
      if ($page_number == 0) $page_number = 1;
    }
    
    // если выбрали категорию
    $cat = false;
    $make = false;
    if (isset($get['make_id']) && intval($get['make_id']) > 0)
      $make = $this->catalogpro_makemodel->get($get['make_id']);
    if (isset($get['category_id']) && intval($get['category_id']) > 0)
      $cat = $this->catalogpro_catmodel->get($get['category_id']);
    if ($make) $get['make_name'] = $make['make_title'];
    if ($cat) $get['cat_name'] = $cat['category_title'];
    
    if (count($get) == 3) {        
      if ($make && $cat) {
        header("Location: /make/{$make['make_url']}/{$cat['category_url']}/");
        exit;
      } else if ($make) {
        header("Location: /make/{$make['make_url']}/");
        exit;
      } else if ($cat) {
        $make = $this->catalogpro_makemodel->get($cat['make_id']);
        header("Location: /make/{$make['make_url']}/{$cat['category_url']}/");
        exit;
      } else {
        $products = $this->catalogpro_productmodel->get_search($page_number);
      }
    }

    // рубрикатор
    $this->append_data('MAKE', $this->catalogpro_makemodel->get());
    $catsbymake = $this->catalogpro_catmodel->get_by_make(0);
    $this->append_data('CATSBYMAKE', $catsbymake);

    if (!isset($products)){
      $products = $this->catalogpro_productmodel->get_search($page_number);
    }
    $this->append_data('PRODUCTS', $products['products']);
    $total_rows = $products['count'];
    $total_pages = ceil($total_rows / $this->common->get_per_page());
    if ($total_pages == 0) $total_pages = 1;
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('&', $url);
    $bu = array();
    foreach($url as $k => $v){
      if (substr($v, 0, 4) == 'page'){
        $url[$k] = 'page=1';
      } else {
        $bu[] = $v;
      }
    }
    $url = implode('&', $url);
    $bu  = implode('&', $bu) . '&page=';
    if ($page_number > $total_pages) {
      header("Location: {$url}");
      exit; 
    }
    $this->append_data('total_rows', $total_rows);
    $this->append_data('total_pages', $total_pages);
    $this->append_data('current_page', $page_number);
    $this->append_data('num_links', 4);
    $this->append_data('base_url', $bu);
    
    if (isset($get['name'])) $this->append_data('name', $get['name']);
    $this->append_data('PARAMS', $get);
    $this->display('catalogpro/views/make.html');
  }
  //----------------------------------------------------------------------------
  
  public function auto(){
    $get = $this->input->get();
    if (!isset($get['search'])) return false;
    $search = trim($get['search']);
    /*$search = explode(' ', $get['search']);
    foreach($search as $k => $v){
      $search[$k] = trim($v);
    }*/
    
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');

    $result = array();
    $result = $this->catalogpro_makemodel->search($search);
    $result = array_merge($result, $this->catalogpro_makemodel->search_cat($search));
    $result = array_merge($result, $this->catalogpro_productmodel->search_autocomplete($search));
    
    echo $this->common->array_to_json(array('search' => $result));
    exit;
  }
  //----------------------------------------------------------------------------
}
  