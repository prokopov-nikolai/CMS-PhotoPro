<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Catalog Pro" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 24.03.2012
 */

class CatalogPro extends CMS_Plugin {

  /**
   * Тип плагина. Когда выполняется
   * before | after | anytime
   */  
  public $type = 'anytime';
  
  /**
   * Стили плагина
   */
  public $style = array(
      '{{ path_plugin }}/catalogpro/css/catalogpro.css'
  );
  
  /**
   * Стили плагина
   */
  public $script = array(
      '{{ path_plugin }}/catalogpro/js/jquery.catalogpro.js'
    , '{{ path_plugin }}/catalogpro/js/ajaxupload.js'
  );
  
  
  public function __construct(){
    parent::__construct();
    $this->config = $this->load_config(basename(__DIR__));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установка плагина
   * @return 
   */
  public function install(){
 
  }
  // ---------------------------------------------------------------------------
}
