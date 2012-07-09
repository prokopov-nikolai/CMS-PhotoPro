<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 24.03.2012
 */

class Catalogpro_car extends CMS_Plugin {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function show($url){
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    $this->catalogpro_productmodel->increase_views($url);
    $car = $this->catalogpro_productmodel->get($url);
    if ($car === false) show_404();
    $this->append_data('CAR', $car);
    $this->append_data('CHAR', $this->catalogpro_charmodel->get());
    $this->append_data('MAKE', $this->catalogpro_makemodel->get());
    $this->append_data('CATSBYMAKE', $this->catalogpro_catmodel->get_by_make());
    $this->append_data('SIMILAR', $this->catalogpro_productmodel->get_similar($car['product_id'], $car['make_id']));
    $this->display('catalogpro/views/car.html');
  }
  //----------------------------------------------------------------------------
  
  public function last(){
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');

    $this->append_data('MAKE', $this->catalogpro_makemodel->get());
    $this->append_data('CATSBYMAKE', $this->catalogpro_catmodel->get_by_make());
    $this->append_data('M', array('make_title' => 'Новые поступления'));
    $this->append_data('PRODUCTS', $this->catalogpro_productmodel->get_last());

    $this->append_data('page_id', "car-last");
    $this->append_data('title', 'ГрАмада - Новые поступления');
    $this->append_data('keywords', 'ГрАмада - Новые поступления');
    $this->append_data('description', 'ГрАмада - Новые поступления');
    $this->display('catalogpro/views/make.html');
  }
  //----------------------------------------------------------------------------
  
  public function availabile($page_number = 1){
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');

    $this->append_data('MAKE', $this->catalogpro_makemodel->get());
    $this->append_data('CATSBYMAKE', $this->catalogpro_catmodel->get_by_make());
    $this->append_data('M', array('make_title' => 'Автомобили в наличии'));
    
    $products = $this->catalogpro_productmodel->get_availabile();
    $this->append_data('PRODUCTS', $products['products']);
    $total_rows = $products['count'];
    $total_pages = ceil($total_rows / $this->common->get_per_page());
    if ($total_pages == 0) $total_pages = 1;
    if ($page_number > $total_pages) { 
      header("Location: /car/availabile/");
      exit; 
    }
    $this->append_data('total_rows', $total_rows);
    $this->append_data('total_pages', $total_pages);
    $this->append_data('current_page', $page_number);
    $this->append_data('num_links', 4);
    $this->append_data('base_url', "/make/availabile/");
    
    $this->append_data('page_id', "car-availabile");
    $this->append_data('title', 'ГрАмада - Автомобили в наличии');
    $this->append_data('keywords', 'ГрАмада - Автомобили в наличии');
    $this->append_data('description', 'ГрАмада - Автомобили в наличии');    
    $this->display('catalogpro/views/make.html');
  }
  //----------------------------------------------------------------------------
}
  