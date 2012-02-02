<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер рубрик
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class CMS_Controller extends CI_Controller {
	private static $instance;
 /**
  * Массив передаваемых данных в шаблон
  * @var array
  */
  private $_data = array();

  /**
  * Шаблонизатор
  * @var obj
  */
  private $_twig;

  /**
  * экземпляр шаблона
  * @var obj
  */
  private $_template;   
  
  public function __construct(){
    self::$instance =& $this;
    
    // Подключим твиг
    include_once ROOT . '/' . SYSDIR . '/PEAR.php';
    include_once ROOT . '/' . SYSDIR . '/Twig/Autoloader.php';
    Twig_Autoloader::register();
    
    parent::__construct();

    // проверим установлен ли движок
    if (config_item('cms_installed') === false && $this->uri->segment(1) != 'install'){
      header("Content-type: text/html; charset=utf-8");
      die('<h3>CMS PhotoPro еще не установлена!</h3><a href="/install/step/1">Установить</a>');
    } elseif ($this->uri->segment(1) != 'install') {
      $file_install = ROOT . '/' . APPPATH . 'controllers/install.php';
      if (file_exists($file_install)) { 
        if (!@unlink($file_install)) {
            show_error("- {$file_install}", 500, "Удалите файл установки");
        }
      }
    }
    
    // подключим настройки для админки
    if ($this->uri->segment(1) == config_item('admin_url')){
      $menu = array();
      $sub_menu = array();
      define('ADMINPATH', APPPATH . 'controllers/'. config_item('admin_url') . '/');
      if ($handledir = opendir(ADMINPATH . 'config/')) {
      	$this->load->library('xml/unpacker');
        while (false !== ($file = readdir($handledir))) {
          if (substr($file, strlen($file)-4, 4) == '.xml' &&
              file_exists(ADMINPATH . substr($file, 0, strlen($file)-4)) . '.php') {
            $this->unpacker->initialize(ADMINPATH . 'config/' . $file);
            #pr($this->unpacker->get_data()); 
            $this->_extract_menu($menu, $sub_menu, $this->unpacker->get_data());
          }
        }
      }
      closedir($handledir);
      
      // добавим меню
      ksort($menu);
      $this->append_data('MENU', $menu);
      
      //добавим подменю
      $this->append_data('SUBMENU', $sub_menu);
      #pr($menu);
      #pr($sub_menu);
      
      // добавим текущие пункты
      if ($this->uri->segment(2) != '') {
      	$this->append_data('controller', $this->uri->segment(2));
      }
      if ($this->uri->segment(3) != '') {
        $this->append_data('method', $this->uri->segment(3));
      }
      $this->_add_headers($menu, $sub_menu);
    }
    
    
    // проверим браузер
    $this->_check_browser();
    
    // закроем сайт если нужно
    $this->_site_close();
    
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Синглтон
   * @return unknown_type
   */
  public static function &get_instance() {
    return self::$instance;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляем переменную в шаблон
   * @param unknown_type $key
   * @param unknown_type $value
   * @return unknown_type
   */
  public function append_data($key, $value){
    $this->_data[$key] = $value;
  } 
  // ---------------------------------------------------------------------------

  /**
   * Дозаписываем переменную шаблона
   * @param unknown_type $key
   * @param unknown_type $value
   * @return unknown_type
   */
  public function merge_data($key, $value){
    if (isset($this->_data[$key])) {
      if (is_array($this->_data[$key])) {
        if (is_array($value)) {
          foreach ($value as $k => $v){
            $this->_data[$key][$k] = $v;
          }
        }
      } else { 
        $this->_data[$key] .= $value;
      }
    } else {
      $this->append_data($key, $value);
    }
  } 
  // ---------------------------------------------------------------------------
  
  /**
   * Выводит шаблон
   * @param unknown_type $template
   * @return unknown_type
   */
  public function display($template) {
    $this->_set_template_path();
    $this->_load_data();
    $this->_load_template($template);
    $this->_template->display($this->_data);    
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Возвращает результат подстановки переменных в шаблон
   * @param unknown_type $template
   * @return unknown_type
   */
  public function fetch($template) {
    ob_start();
    $this->display($template);
    return ob_get_clean();    
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Переадресовывает туда от куда пришел
   */
  public function locate_referer(){
    if(isset($_SERVER['HTTP_REFERER']) && 
       $_SERVER['HTTP_REFERER'] != null && 
       !strpos($_SERVER['HTTP_REFERER'], 'login')) {
      header("Location: {$_SERVER['HTTP_REFERER']}");
    } else {
      header("Location: http://{$_SERVER['HTTP_HOST']}");
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Устанавливает шаблон
   * @return unknown_type
   */
  private function _set_template_path () {
    $template_dir = array();   
    if ($this->uri->segment(1) == config_item('admin_url')){
      $template_dir[] = ROOT . '/'. APPPATH . 'views/' . config_item('admin_url') . '/' . config_item('admin_template');
    } else {
      $template_dir[] = ROOT . '/'. APPPATH . 'views/' . config_item('site_template');
    }
    
    // добавим путь к папке шаблона
    $this->_data['path_template'] = str_replace(ROOT, "http://{$_SERVER['HTTP_HOST']}", $template_dir[0]);
    $template_dir[] =  ROOT . '/'. APPPATH . 'plugins';
    $loader = new Twig_Loader_Filesystem($template_dir);
    $config = array( 
       'charset'    => 'utf-8',
       'autoescape' => true
    );
    if (ENVIRONMENT == 'production') {
      $config['cache'] = ROOT . '/' . APPPATH . 'cache/twig';
    }
    $this->_twig = new Twig_Environment($loader, $config);

    // подключим расширения для твига
    $this->load->library('TwigExtension');
    $this->_twig->addExtension($this->twigextension);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Загрузим все переменные в шаблон
   */
  private function _load_data(){
    if ($this->uri->segment(1) != 'install') {
      $this->_data['environment'] = ENVIRONMENT;
          
      // урл админки
      $this->_data['admin_url'] = config_item('admin_url');
      
      // показывать голосование
      $this->_data['show_vote'] = config_item('show_vote');
      
      // версия движка
      $this->_data['cms_version'] = config_item('cms_version');
      
      // nginx
      $this->_data['nginx'] = config_item('nginx');
      
      // количество на странице
      $this->_data['per_page'] = $this->common->get_per_page();
      
      // добавим данные пользователя
      $this->_data['U'] = $this->session->userdata;      
    }
    
    // удалим сообщение и ошибки
    if ($this->session->userdata('message') != '') {
      $this->_data['message'] = $this->session->userdata('message');
   	  $this->session->unset_userdata('message');
    } 
    if ($this->session->userdata('error') != '') {
      $this->_data['error'] = $this->session->userdata('error');
      $this->session->unset_userdata('error');
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Загружает шаблон
   * @param unknown_type $templateName
   * @return unknown_type
   */
  private function _load_template ($template_name) {
    $this->_template = $this->_twig->loadTemplate($template_name);   
  }  
  // ---------------------------------------------------------------------------
  
  /**
   * Проверяет устарел ли браузер
   */
  private function _check_browser(){
    #pr($this);
    $this->load->library('user_agent');
    $this->append_data('browser_name', $this->agent->browser());
    $this->append_data('browser_version', $this->agent->version());
    // определим браузер
    if ($this->agent->is_browser()){
       if ($this->agent->browser() == 'Internet Explorer' && intval($this->agent->version()) < config_item('lower_ie')) {
        $this->_bad_browser();
       }
       if ($this->agent->browser() == 'Opera' && floatval($this->agent->version()) < config_item('lower_opera')) {
         $this->_bad_browser();
       }
       if ($this->agent->browser() == 'Chrome' && floatval($this->agent->version()) < config_item('lower_chrome')) {
         $this->_bad_browser();
       }
       if ($this->agent->browser() == 'Firefox' && floatval($this->agent->version()) < config_item('lower_firefox')) {
         $this->_bad_browser();
       }
       if ($this->agent->browser() == 'Safari' && floatval($this->agent->version()) < config_item('lower_safari')) {
         $this->_bad_browser();
       }
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выводит сообщение что браузер устарел
   * @return unknown_type
   */
  private function _bad_browser(){
    $this->display('bad_browser.html'); 
    exit;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Закроем сайт если нужно
   * @return unknown_type
   */
  private function _site_close(){
    $site_close = config_item('site_close');
    if ($site_close != '' && 
        $this->session->userdata('user_admin') == '' && 
        $this->uri->segment(1) != config_item('admin_url')) {
      $this->append_data('site_close', $site_close);
      header('HTTP/1.0 503 Service Unavailable');
      header('Retry-After: 86400');
      $this->display('site_close.html'); 
      exit;
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Подготавливает информацию для меню
   * $menu arr
   * $info arr 
   */
  private function _extract_menu(& $menu, & $sub_menu, $info){
  	if (isset($info['sort'])) {
  		$sort = intval($info['sort']);
  		while(isset($menu[$sort])) {
  			++$sort;
  		}
  		$menu[$sort] = array();
  	} else {
  		$menu[] = array();
  		$sort = sizeof($menu) - 1;
  	}
  	
  	$menu[$sort] = array(
  	  'title'       => $info['title'],
      'name'        => $info['name'],
      'description' => $info['description']
  	);
  	if (isset($info['script'])) {
  	  $menu[$sort]['script'] = $info['script'];
  	}
    if (isset($info['style'])) {
      $menu[$sort]['style'] = $info['style'];
    }

    if (isset($info['method']) && $info['method'] != null) {
	  	foreach($info['method'] as $key => $method){
	  	  if (is_numeric($key) === true){ 
	  	    $s = array(
            'title'       => $method['title'],
            'name'        => $method['name'],
            'description' => $method['description']
          );
          if (isset($method['script'])) {
            $s['script'] = $method['script'];
          }
          if (isset($method['style'])) {
            $s['style'] = $method['style'];
          }
          if (isset($method['_attributes_'])) {
            foreach ($method['_attributes_'] as $k => $v) {
              $s[$k] = $v;
            }
          }
          $sub_menu[$info['name']][] = $s;
	  	  } else {
	  	    $s = array(
            'title'       => $info['method']['title'],
            'name'        => $info['method']['name'],
            'description' => $info['method']['description']
          );
	  	    if (isset($info['method']['script'])) {
            $s['script'] = $info['method']['script'];
          }
          if (isset($info['method']['style'])) {
            $s['style'] = $info['method']['style'];
          }
          if (isset($info['method']['_attributes_'])) {
            foreach ($info['method']['_attributes_'] as $k => $v) {
              $s[$k] = $v;
            }
          }
          
          $sub_menu[$info['name']][] = $s;
          break;
	  		}
	  	}
    } 
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Добавляет скрипты и линки в хэдер
   */
  private function _add_headers($menu, $sub_menu){
    $headers = '';
    $controller = $this->uri->segment(2);
    $method = $this->uri->segment(3);
    $path_template = '/'. APPPATH . 'views/' . config_item('admin_url') . '/' . config_item('admin_template') . '/';
    if ($controller != '') {
      foreach ($menu as $m) {
        if ($m['name'] == $controller) {
          if (isset($m['script']) && is_array($m['script'])) {
            foreach ($m['script'] as $s) {
              $s = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $s);
              $s = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $s);
              $headers .= '<script type="text/javascript" src="'.$s.'"></script>' . "\n  ";
            }
          } elseif(isset($m['script']) && $m['script'] != '') {
            $s = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $m['script']);
            $s = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $s);
            $headers .= '<script type="text/javascript" src="'.$s.'"></script>' . "\n  ";
          }
          if (isset($m['style']) && is_array($m['style'])) {            
            foreach ($m['style'] as $st) {
              $st = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $st);
              $st = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $st);
              $headers .= '<link type="text/css" href="'.$st.'" rel="stylesheet" />' . "\n  ";
            }
          } elseif(isset($m['style']) && $m['style'] != '') {
            $st = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $m['style']);
            $st = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $st);
            $headers .= '<link type="text/css" href="'.$st.'" rel="stylesheet" />' . "\n  ";
          }
          break;
        }
      }
    }
    if ($method != '' && isset($sub_menu[$controller])) {
      foreach ($sub_menu[$controller] as $m) {
        if ($m['name'] == $method) {
          if (isset($m['script']) && is_array($m['script'])) {
            foreach ($m['script'] as $s) {
              $s = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $s);
              $s = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $s);
              $headers .= '<script type="text/javascript" src="'.$s.'"></script>' . "\n  ";
            }
          } elseif(isset($m['script']) && $m['script'] != '') {
            $s = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $m['script']);
            $s = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $s);
            $headers .= '<script type="text/javascript" src="'.$s.'"></script>' . "\n  ";
          }
          if (isset($m['style']) && is_array($m['style'])) {
            foreach ($m['style'] as $st) {
              $st = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $st);
              $st = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $st);
              $headers .= '<link type="text/css" href="'.$st.'" rel="stylesheet" />' . "\n  ";
            }
          } elseif(isset($m['style']) && $m['style'] != '') {
            $st = str_replace('{{ path_template }}', "http://{$_SERVER['HTTP_HOST']}$path_template", $m['style']);
            $st = str_replace('{{path_template}}', "http://{$_SERVER['HTTP_HOST']}$path_template", $st);
            $headers .= '<link type="text/css" href="'.$st.'" rel="stylesheet" />' . "\n  ";
          }
        }
      }
    }
    $this->append_data('headers', $headers);
  }
  // ---------------------------------------------------------------------------
  
  /*
   * На всякий случай убъем клона
   */
  private function __clone(){  }  

}