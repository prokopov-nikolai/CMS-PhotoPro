<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 24.03.2012
 */

class Catalogpro_index extends CMS_Plugin {

  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
  }
  //----------------------------------------------------------------------------
  
  public function home(){
    $this->load->plugin_model('catalogpro', 'catalogpro_makemodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_productmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_catmodel');
    $this->load->plugin_model('catalogpro', 'catalogpro_charmodel');
    $makes = $this->catalogpro_makemodel->get();
    $this->append_data('MAKES', $makes);
    
    $step = (int) ceil(count($makes) / 4);
    $d = $step * 4 - count($makes);
    $this->append_data('next4', 4 * $step - 1 - $d);
    if ($d != 0) --$d; 
    $this->append_data('next3', 3 * $step - 1 - $d);
    if ($d != 0) --$d; 
    $this->append_data('next2', 2 * $step - 1 - $d);
    if ($d != 0) --$d; 
    $this->append_data('next1', 1 * $step - 1 - $d);
    
    $this->load->model('vote_model');
    $vote = $this->vote_model->get('', 1);
    if ($vote != null) $this->append_data('VOTE', $vote);
    
    $meta = config_item('main_page');
    $this->append_data('title', $meta['title']);
    $this->append_data('keywords', $meta['keywords']);
    $this->append_data('description', $meta['description']);
    $this->append_data('CATS', $this->catalogpro_catmodel->get());
    $this->append_data('POPULAR', $this->catalogpro_productmodel->get_popular());
    $this->append_data('SPECIAL', $this->catalogpro_productmodel->get_special());
    $this->append_data('SLIDER', $this->catalogpro_productmodel->get_slider());
    $this->append_data('SEARCH_CHAR', $this->catalogpro_charmodel->get_search_values());
    $this->display('catalogpro/views/home.html');
  }
  //----------------------------------------------------------------------------
}
  