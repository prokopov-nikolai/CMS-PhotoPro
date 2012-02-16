<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Debug tools" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 10.02.2012
 */

class Debug_tools extends CMS_Plugin {

  /**
   * Тип плагина. Когда выполняется
   * before | after | anytime
   */  
  public $type = 'after';
  
  /**
   * Стили плагина
   */
  public $style = array(
      '{{ path_plugin }}/debug_tools/css/debug-tools.css'
    , '{{ path_plugin }}/debug_tools/css/shCoreDefault.css'
  );
  
  /**
   * Стили плагина
   */
  public $script = array(
      '{{ path_plugin }}/debug_tools/js/jquery.debug-tools.js'
    , '{{ path_plugin }}/debug_tools/js/shCore.js'
    , '{{ path_plugin }}/debug_tools/js/shBrushSql.js'
  );
  
  
  public function __construct(){
    parent::__construct();
    $this->config = $this->load_config('debug_tools');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Главная функция плагина
   * @return 
   */
  public function index(){
    if (isset($this->db) && isset($this->session) && 
      ($this->config['access'] || $this->session->userdata('user_admin') != '')) {
      $this->append_data('query_count', $this->db->query_count);
      $this->append_data('query_time', number_format($this->db->benchmark, 2));
      $this->append_data('query_highlighting', $this->config['query_time_decimals']);
      $this->append_data('decimals', $this->config['query_time_decimals']);
      $this->append_data('debug_tools_height', $this->config['debug_tools_height']);
      $qtime = array();
      foreach($this->db->query_times as $t) {
        $qtime[] = number_format($t, $this->config['query_time_decimals']);
        $qspeed[] = $t < $this->config['query_speed'][0] 
                    ? 'fast' 
                    : ($t >= $this->config['query_speed'][1]
                      ? 'slow'
                      : 'middle');
      }
      $this->append_data('QTIME', $qtime);
      $this->append_data('QSPEED', $qspeed);
      $this->append_data('QUERY', $this->db->queries);
      
      return $this->render('debug_tools/views/debug_tools.html');
    } 
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Подсвечиваем SQL-запросы
   */
  private function _highlighting($query = array()){

    foreach($query as $k => $sql) {
      #$sql = preg_replace("#(=|+|-|>|<|~|==|!=|<|>|LIKE|NOT LIKE|REGEXP)#i", "<span style='color:orange'>\1</span>", $sql );
      #$sql = preg_replace("#(MAX|AVG|SUM|COUNT|MIN)(#i", "<span style='color: #DF0C16'>\1</span>(", $sql );
      #$sql = preg_replace("!("|'|')(.+?)("|'|')!i", "<span style='color:red'>\1\2\3</span>", $sql );
      #$sql = preg_replace("#s{1,}(AND|OR)s{1,}#i", " <span style='color: #DF0C16'>\1</span> ", $sql );
      #$sql = preg_replace("#(WHERE|MODIFY|CHANGE|AS|DISTINCT|IN|ASC|DESC|ORDER BY|SET)s{1,}#i", "<span style='color:green'>\1</span> ", $sql );
      #$sql = preg_replace("#(FROM|INTO)s{1,}(S+?)s{1,}#i", "<span style='color:green'>\1</span> <span style='color:orange'>\2</span> ", $sql );
      $sql = preg_replace("#(SELECT|INSERT|UPDATE |DELETE|ALTER TABLE|DROP)#i", "<span style='color:#DF0C16;font-weight:bold'>\1</span>", $sql );
      $query[$k] = $sql;
    }
    return $query;
  }
}