<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер укстановки движка
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Install extends CMS_Controller {
  
  private $step_count;
  
  /**
   * Конструктор 
   */
  public function __construct(){
    parent::__construct();
    $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    ini_set('session.cookie_domain', ".{$host}");
    session_start();
    define('SITE_PATH', ROOT . '/' . APPPATH);
    $step = $this->uri->segment(3);
    $this->step_count = 3;
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
   * Шаги установки
   */
  public function step($num = 1){
    switch ($num) {
      case 1:
        $this->admin_settings();
      break;
      case 2:
        $this->database_settings();
        break;
      case 3:
        $this->install();
        break;
      case 4:
        $this->delete_files();
        break;
      default:
      break;
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Общие настройки
   */
  public function admin_settings(){
    $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    $this->append_data('host', $host);
    $this->append_data('step_name', "Шаг 1 из {$this->step_count}. Администратор сайта.");
    $this->display('install.html');
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Настройки базы данных
   */
  public function database_settings(){
    // Отправим сообщение об успешной установке
    $this->append_data('step_name', "Шаг 2 из {$this->step_count}. Настройки базы данных.");
    $this->display('install.html');
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
   * Установка 
   */
  public function install(){
    // извлечем схему
    $sql = $this->get_scheme();
    
    // восстановим дамп
    if (!$error = $this->restore_dump($sql)){   
      
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
      mail($email, $subject, $message, $headers);

      // проставим что установка завершена и укажем использовать базу для сессии
      $data = array(
        'sess_use_database' => 'TRUE',
        'cms_installed'     => 'TRUE',
        'sess_cookie_name'  => 'session'
      );
      $this->save($data);
      
      // сохраним все настройки
      $this->save($_SESSION);      
          
      $this->display('install.html');
    } else {
      echo $error;
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Удалим файлы 
   */
  public function delete_files(){
    // если установка прошла успешно  

    // удалим сессию
    $this->session->sess_destroy();
    session_destroy();

    // подменим автолод
    unlink(SITE_PATH . 'config/autoload.php');
    copy(SITE_PATH . 'config/autoload.php-install', SITE_PATH . 'config/autoload.php');
    unlink(SITE_PATH . 'config/autoload.php-install');
    
    // удалим шаблоны
    $dir_tpl =  ROOT . '/' . APPPATH . 'views/photopro/';
    unlink("{$dir_tpl}css/install.css");
    unlink("{$dir_tpl}install.html");
    unlink("{$dir_tpl}install-1.html");
    unlink("{$dir_tpl}install-2.html");
    unlink("{$dir_tpl}install-3.html");
    
    // переадресуем на главную, чтобы удалился контроллер установки
    header("Location: /"); exit;
  }
  // ---------------------------------------------------------------------------

  /**
   * Извлечем схему
   */
  private function get_scheme(){
    $sql = file_get_contents(SITE_PATH . 'dbobjects/photopro-0.1_scheme.sql');
    $sql = str_replace('{db_dbprefix}', $_SESSION['db_dbprefix'],$sql);
    $sql = str_replace('{db_char_set}', $_SESSION['db_char_set'],$sql);
    $sql = str_replace('{db_dbcollat}', $_SESSION['db_dbcollat'],$sql);
    $sql = str_replace('{db_table_type}', $_SESSION['db_table_type'],$sql);
    return $sql;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Восстанавливаем схему 
   */
  private function restore_dump($sql) {
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
    if (sizeof($error) > 1) {
      $glue = '<br/>';
    }
    return implode('<br/>', $error);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Сохраняем настройки
   */
  public function save($data){
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