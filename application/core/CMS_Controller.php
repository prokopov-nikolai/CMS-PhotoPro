<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер рубрик
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class CMS_Controller extends CI_Controller {
  /**
   * Список подключенных плагинов
   * @var array
   */
  protected $plugin_list = array();
  
  /**
   * Код для вставки в шаблон до вывода основного контента
   * @var string
   */
  public $plugin_content_top = '';
  
  /**
   * РКод для вставки в шаблон после вывода основного контента
   * @var string
   */
  public $plugin_content_bottom = '';
  
  /**
   * Подключаемые стили и скрипты для плагинов
   */
  private $_plugin_head = array(
      'script' => array()
    , 'style'  => array()
  );
  
  /**
   * Объект синглтона
   */
	private static $instance;
  
  /**
  * Массив передаваемых данных в шаблон
  * @var array
  */
  private $_data = array();

  /**
   * Путь до шаблона (front/back)
   */
  private $_path_template = '';
  
  /**
   * Путь до плагинов
   */
  private $_path_plugin = '';

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
    parent::__construct();

    // Подключим твиг вначала иначе не сможем вывести форму авторизации
    include_once ROOT . '/' . SYSDIR . '/PEAR.php';
    include_once ROOT . '/' . SYSDIR . '/Twig/Autoloader.php';
    Twig_Autoloader::register();

    // определим основые пути
    $this->_set_path();

    // создадим объект шаблонизатора
    $this->_set_twig();    

    header("Content-type: text/html; charset=utf-8");

    // проверим установлен ли движок
    if (config_item('cms_installed') === false && $this->uri->segment(1) != 'install'){
      die('<h3>CMS PhotoPro еще не установлена!</h3><a href="/install/step/1">Установить</a>');
    } elseif ($this->uri->segment(1) != 'install') {
      $file_install = ROOT . '/' . APPPATH . 'controllers/install.php';
      if (file_exists($file_install)) { 
        if (!@unlink($file_install)) {
            show_error("- {$file_install}", 500, "Удалите файл установки");
        }
      }
    }    
    
    // авторизуем пользователя
    $this->user_login();
    if ($this->uri->segment(1) == config_item('admin_url') && $this->session->userdata('user_uniqid') == '') {
      $this->display("login.html");
    }

    // загрузим список плагинов
    $this->_load_plugin_list();
    
    // выполним плагины before
    $this->_run_plugin('before');
    
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
    
    // не будем выполнять плагины after
    // выполним их в функции display 
    // $this->_run_plugin('after');
    
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
    // выполним плагины after
    $this->_run_plugin('after');
    
    $this->_load_data();
    $this->_load_template($template);
    
    
    echo $this->_template->render($this->_data);
    exit;
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Возвращает результат подстановки переменных в шаблон
   * @param unknown_type $template
   * @return unknown_type
   */
  public function render($template) {
    $this->_load_data();
    $this->_load_template($template);
    return $this->_template->render($this->_data);
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
   * Логиним пользователя
   */
  public function user_login(){
    // проверим авторизован ли пользователь
    if ($this->session->userdata('user_uniqid') != '') {
      return true;
    }
    
    // авторизуем пользователя по переданным его данным 
    // и не получилось автоматически залогинить
    $post = $this->input->post();
    $error = '';
    if (!$this->_auto_login() && 
        isset($post['user_email']) && 
        isset($post['user_password'])){
      if ($post['user_email'] == null) {
        $error = 'Введите Email!';
      } else if (!$this->common->check_email(trim($post['user_email']))){
        $error = 'Не корректный email!';
      } else if ($post['user_password'] == null) {
        $error = 'Пароль не может быть пустым!';
      }
      if ($error) {
        // если возникли ошибки при проверке данных
        $this->append_data('user_email', $post['user_email']);
        $this->append_data('error', $error);   
        return false;
      } else {
        $user = $this->user_model->user_login($post['user_email'], $post['user_password']);
        if (!is_array($user)) {
          // если возникли ошибки при авторизации
          $this->append_data('user_email', $post['user_email']);
          $this->append_data('error', $user);   
          return false;
        }
        if ($post['remember']) {
          set_cookie(array(
              'name'   => 'user_email'
            , 'value'  => $post['user_email']
            , 'expire' => '31104000'
          ));
          set_cookie(array(
              'name'   => 'user_password'
            , 'value'  => $this->user_model->encrypt_password($post['user_password'])
            , 'expire' => '31104000'
          ));
        }
        // если нашелся пользователь, то сохнарим его в сессию
        $this->session->set_userdata($user);
        return true;
      }      
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Пробуем автологинить пользователя по кукам 
   */
  private function _auto_login(){    
    // если не авторизован и есть куки, то попробуем найти пользователя
    $user_email = get_cookie('user_email', true);
    $user_password = get_cookie('user_password', true);
    if ($user_email != '' && $user_email != '') {
      $auth = $this->user_model->search_user($user_email, $user_password);
      if (isset($auth['error'])) {
        // если возникли ошибки при авторизации
        $this->append_data('error', $auth['error']);
        $this->display("login.html");
      } else {
        // если нашелся пользователь, то сохнарим его в сессию
        $this->session->set_userdata($auth);
        return true;
      }      
    }
    return false;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Устанавливает пути для шаблонов и плагинов 
   */
  private function _set_path () {
    // путь до шаблонов
    if ($this->uri->segment(1) == config_item('admin_url')){
      $this->_path_template = ROOT . '/'. APPPATH . 'views/' . config_item('admin_url') . '/' . config_item('admin_template');
    } else {
      $this->_path_template = ROOT . '/'. APPPATH . 'views/' . config_item('site_template');
    }
    
    // путь до плагинов
    $this->_path_plugin =  ROOT . '/'. APPPATH . 'plugins';
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Устанавливает шаблон
   * @return unknown_type
   */
  private function _set_twig () {
    $template_dir = array(
        $this->_path_template
      , $this->_path_plugin
    );   
    
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
      // режим в котором работает сайт
      $this->_data['environment'] = ENVIRONMENT;
      
      // код для вставки в начало шаблона
      $this->_data['plugin_content_top'] = $this->plugin_content_top;
      
      // код для вставки в конец шаблона
      $this->_data['plugin_content_bottom'] = $this->plugin_content_bottom;
      
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
      
      // загрузим скрипты и стили плагинов
      $this->_data['PLUGIN_HEAD'] = $this->_plugin_head; 
      
      // время выполнения скрипта
      $this->benchmark->mark('total_execution_time_end');
      $this->_data['benchmark_time'] = substr(current(explode(' ', end($this->benchmark->marker))), 0, 4);
      
      // память занимаемая скриптом
      $this->_data['benchmark_memory'] = round(memory_get_usage(true)/1048576,2)." mb";
      
      // память занимаемая скриптом (пик)
      $this->_data['benchmark_memory_peak'] = round(memory_get_peak_usage(true)/1048576,2)." mb";
      
      // вызываемый контроллер
      $this->_data['cms_controller'] = $this->router->class; 
      
      // вызываемая функция
      $this->_data['cms_method'] = $this->router->method;
    }

    // путь до шаблона с учетом домена
    $this->_data['path_template'] = str_replace(ROOT, "http://{$_SERVER['HTTP_HOST']}", $this->_path_template);
    
    // путь до плагинов с учетом домена
    $this->_data['path_plugin'] = str_replace(ROOT, "http://{$_SERVER['HTTP_HOST']}", $this->_path_plugin);
      
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
    #prex($this->_twig);
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
   * Подготавливает информацию для меню в админке
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
  
  /**
   * Загружаем плагины по списку
   */
  private function _load_plugin_list(){
    $this->plugin_list = file("{$this->_path_plugin}/plugins.dat");
    $plugin_path = str_replace(ROOT, "http://{$_SERVER['HTTP_HOST']}", $this->_path_plugin);
    foreach($this->plugin_list as $k => $plugin_name) {
      $plugin_name = trim($plugin_name);
      $this->plugin_list[$k] = $plugin_name;
      $this->load->plugin($plugin_name);
      foreach($this->$plugin_name->script as $src) {
        $this->_plugin_head['script'][] = str_replace('{{ path_plugin }}', $plugin_path, $src);
      }
      foreach($this->$plugin_name->style as $stl) {
        $this->_plugin_head['style'][] = str_replace('{{ path_plugin }}', $plugin_path, $stl);
      }
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Выполняем плагины
   */
  private function _run_plugin($type){
    foreach($this->plugin_list as $plugin_name){
      if ($this->$plugin_name->type == $type) {
        $this->plugin_content_bottom .= $this->$plugin_name->index();
      }
    }
  }
  // ---------------------------------------------------------------------------
  
  /*
   * На всякий случай убъем клона
   */
  private function __clone(){  }  

}