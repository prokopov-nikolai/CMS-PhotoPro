<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер укстановки движка
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Install extends CMS_Controller {
  
  private $_step_count;
  
  /**
   * Конструктор 
   */
  public function __construct(){
	$this->_check_permissions();
    parent::__construct();
    $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    ini_set('session.cookie_domain', ".{$host}");
    session_start();
    define('SITE_PATH', ROOT . '/' . APPPATH);
    $step = $this->uri->segment(3);
    $this->_step_count = 3;
    if ($step) {
      $this->append_data('step', 'install-'.$step.'.html');
      $this->append_data('step'.$step, 'current');
      ++$step;
      $post = $this->input->post();
      if (sizeof($post) > 1) {
        $_SESSION = array_merge($_SESSION, $post);
        header('Location: /install/step/' . $step . '/');
        exit;
      }
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Переадресуем на первый шаг
   */
  public function index(){
    header('Location: /install/step/1/');
    exit;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Проверка соединения с базой данных
   */
  public function check_connection(){
    $post = $this->input->post();
    mysql_connect($post['host'], $post['user'], $post['pass']);
    mysql_query("USE {$post['base']}");
    if (mysql_error() != '') {
      echo json_encode(array('success' => 'false'));
    } else {
      echo json_encode(array('success' => 'true'));
    }
    exit;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Шаги установки
   */
  public function step($num = 1){
    switch ($num) {
      case 1:
        $this->_admin_settings();
      break;
      case 2:
        $this->_database_settings();
        break;
      case 3:
        $this->_install();
        break;
      case 4:
        $this->_delete_files();
        break;
      default:
      break;
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Общие настройки
   */
  private function _admin_settings(){
    $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    $this->append_data('host', $host);
    $this->append_data('step_name', "Шаг 1 из {$this->_step_count}. Администратор сайта.");
    $this->display('install.html');
  }
  // ---------------------------------------------------------------------------
  
  /*
   * Проверим права на файлы и папки
   */
  private function _check_permissions(){  
    $path = ROOT . '/' . APPPATH;
    $error = '';
      
    if (file_exists("{$path}config/autoload.php-install") &&
        substr(decoct(fileperms("{$path}config/autoload.php-install")), -3) != 777){
      if (!@chmod("{$path}config/autoload.php-install", 0777))
        $error .= "<br>- {$path}config/autoload.php-install";
    }

    $file_list = array(
        "{$path}config/autoload.php"
      , "{$path}config/config.php"
      , "{$path}config/database.php"
      , "{$path}config/site.php"
      , "{$path}controllers/install.php"
    );
    foreach($file_list as $file){
      if (substr(decoct(fileperms($file)), -3) != 777){
        if (!@chmod($file, 0777))
          $error .= "<br>- {$file}";
      }
    }
    if ($error) show_error("Файлы:{$error}", 500, "Установите права на запись (777) в файлах");

    
    $dir_list = array(
        ROOT . '/images/buffer/'
      , ROOT . '/images/resize/'
      , ROOT . '/images/source/'
      , ROOT . '/' . APPPATH . 'cache/db/'
      , ROOT . '/' . APPPATH . 'cache/twig/'
      );
    foreach($dir_list as $dir){
      if (substr(decoct(fileperms($dir)), -3) != 777){
        if (!@chmod($dir, 0777))
          $error .= "<br>- {$dir}";
      }
    }
    if ($error) show_error("Папки:{$error}", 500, "Установите права на запись (777) в папках");
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Настройки базы данных
   */
  private function _database_settings(){
    // Отправим сообщение об успешной установке
    $this->append_data('step_name', "Шаг 2 из {$this->_step_count}. Настройки базы данных.");
    $this->display('install.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удалим файлы 
   */
  private function _delete_files(){
    // если установка прошла успешно  

    // удалим сессию
    $this->session->sess_destroy();
    session_destroy();

    // подменим автолод
    if (file_exists(SITE_PATH . 'config/autoload.php-install')) {
      @unlink(SITE_PATH . 'config/autoload.php');
      if (!@copy(SITE_PATH . 'config/autoload.php-install', SITE_PATH . 'config/autoload.php'))
        show_error("Переименуйте файл<br>" . SITE_PATH . 'config/autoload.php-install<br>' . 'в файл<br>' . SITE_PATH . 'config/autoload.php', 500, 'Переименуйте файл');
      @unlink(SITE_PATH . 'config/autoload.php-install');
    }
    
    // удалим шаблоны
    $error = '';
    $dir_tpl =  ROOT . '/' . APPPATH . 'views/photopro/';
    $file_list = array(
          "{$dir_tpl}css/install.css"
        , "{$dir_tpl}install.html"
        , "{$dir_tpl}install-1.html"
        , "{$dir_tpl}install-2.html"
        , "{$dir_tpl}install-3.html"
      );
    foreach($file_list as $file){
      if (!@unlink($file))
        $error .= "<br>- {$file}";
    }
    /*if ($error) {
      show_error("Файлы:{$error}", 500, 'Удалите файлы и <a href="">обновите страницу</a>');
    }*/
    
    // переадресуем на главную, чтобы удалился контроллер установки
    header("Location: /"); exit;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Извлечем схему
   */
  private function _get_scheme(){
    $sql = file_get_contents(SITE_PATH . 'dbobjects/photopro-0.1.4_scheme.sql');
    $sql = str_replace('{db_dbprefix}', $_SESSION['db_dbprefix'],$sql);
    $sql = str_replace('{db_char_set}', $_SESSION['db_char_set'],$sql);
    $sql = str_replace('{db_dbcollat}', $_SESSION['db_dbcollat'],$sql);
    $sql = str_replace('{db_table_type}', $_SESSION['db_table_type'],$sql);
    return $sql;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Установка 
   */
  private function _install(){
    // извлечем схему
    $sql = $this->_get_scheme();
    // восстановим дамп
    if (!$error = $this->_restore_dump($sql)){   
      
      // зарегистрируем админа
      $user_email = $_SESSION['admin_email'];
      $user_password = md5($_SESSION['user_password'] . $_SESSION['encryption_key']);
      $user_uniqid = str_replace('.', '', uniqid('', 1));
      
      mysql_query("INSERT INTO {$_SESSION['db_dbprefix']}user
                   SET user_email = '{$user_email}'
                     , user_password = '{$user_password}'
                     , user_uniqid = '{$user_uniqid}'");      
      mysql_query("INSERT INTO {$_SESSION['db_dbprefix']}user_admin
                   SET user_uniqid = '{$user_uniqid}'");

      // отправим письмо об успешной установке
      $email = $_SESSION['admin_email'];
      $pass  = $_SESSION['user_password'];
      
      /* тема письма */
      $subject = "Успешная установка CMS PhotoPro!\r\n";
     
      /* сообщение для отправки в формате HTML */
      $message = '<html><body>Поздравляем!<br> 
                   Вы успешно установили CMS PhotoPro на хостинговую площадку!<br>
                   &nbsp;<br>Ваши данные доступа:<br>
                   <b>логин: </b>'   . $email .'<br>
                   <b>пароль: </b>' . $pass . ' <br>
                   &nbsp;<br>
                   <a href="http://www.cms-photorpo.com">www.cms-photopro.com</a></body></html>';
     
      /* Укажем необходимые заголовки */
      $headers= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=utf-8\r\n";
      $headers .= "From: no-reply@cms-photopro.com <no-reply@cms-photopro.com>\r\n"; 
     
      /* отправим письмо */
      @mail($email, $subject, $message, $headers);

      // проставим что установка завершена и укажем использовать базу для сессии
      $data = array(
        'sess_use_database' => 'TRUE',
        'cms_installed'     => 'TRUE',
        'sess_cookie_name'  => 'session',
        'cookie_domain' => ".{$_SERVER['HTTP_HOST']}",
        'show_vote' => 'TRUE'
      );
      $this->_save($data);
      
      // сохраним все настройки
      $this->_save($_SESSION);
          
      $this->display('install.html');
    } else {
      echo $error;
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Восстанавливаем схему 
   */
  private function _restore_dump($sql) {
    $host = $_SESSION['db_hostname'];
    $user = $_SESSION['db_username'];
    $pass = $_SESSION['db_password'];
    $base = $_SESSION['db_database'];
    mysql_connect($host, $user, $pass);
    mysql_set_charset("{$_SESSION['db_char_set']}");
    mysql_query("USE {$base}");
    
    $query = explode(';', $sql);
    $error = array(); 
    for ($i = 1; $i < sizeof($query); ++$i) {
      if (trim($query[$i]) !=  '')
        mysql_query("{$query[$i]};");
      if (mysql_error() != '' ) {
        $error[] = mysql_error();
      }
    }
    $glue = '';
    if (sizeof($error) > 0) {
      $glue = '<br/>';
    }
    return implode('<br/>', $error);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Сохраняем настройки
   */
  private function _save($data){
    $this->config->load('config');
    $this->config->load('database');
    
    if (sizeof($data) > 1) {
      foreach($data as $k => $v) {
        $this->config->set_item($k, $v);
        $_SESSION[$k] = $v;
      }
      $this->config->save();
      return true;
    }
  }
  // ---------------------------------------------------------------------------

}

/* End of file page.php */
/* Location: ./application/controllers/install.php */