<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS PhotoPro
 * Библиотека почтальона
 * Имеет возможность:
 *  - напрямую подключаться к SMTP серверу по указанным хосту и порту;
 *  - отсылать как простые текстовые файлы, так и в формате html;
 *  - прикреплять файлы к письму

 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Mailer {
  // Константы -----------------------------------------------------------------
  

  
  // Поля ----------------------------------------------------------------------
  private $_send_date; // Дата отправки письма (по умолчанию текущая)
  private $_from; // От кого письмо
  private $_reply_to; // Адрес для ответа (по умолчанию _from)
  private $_to; // Кому отправляется письмо
  private $_subject; // Тема письма
  private $_type; // Тип письма
  private $_data; // Данные письма (не доступны если тип письма ALT)
  private $_mixed_main_type; // Тип основной части смешанного письма
                            // (доступен если тип письма MIXED
  private $_boundary; // Разделитель частей письма (доступен если тип письма
                       // MIXED или ALT)
  private $_smtp_host; // SMTP хост (по умолчанию localhost)
  private $_smtp_port; // порт SMTP сервера (по умолчанию 25)
  private $_smtp_login; // Логин для авторизации на сервере
  private $_smtp_password; // Пароль для авторизации на сервере
  
  // Переменные ----------------------------------------------------------------
  private $_ci; // Переменная фреймворка
  private $_arr_alt_parts; // Массив частей для алтернативного письма
  private $_arr_inline_files; // Массив файлов вставляемых прямо в письмо
  private $_arr_attachments; // Массив файлов прикрепляемых к письму
  private $_arr_types; // Массив с типами данных (Content-Type)
  private $_message; // Текст письма для отсылки согласно стандарту
  
  // Методы --------------------------------------------------------------------
  /**
   * Конструктор класса
   * 
   * @return InigsMailer
   */
  public function __construct() {
    // Тип письма //
    /**
     * Тип письма - простой текст
     * @var int
     */
    define('TYPE_MES_TEXT', 0);
    
    /**
     * Тип письма - html-текст
     * @var int
     */
    define('TYPE_MES_HTML', 1);
    
    /**
     * Тип письма - смешанное состоящее из нескольких частей
     * @var int
     */
    define('TYPE_MES_MIXED', 2);
    
    /**
     * Тип письма - альтернативное состоящее из нескольких частей
     * @var int
     */
    define('TYPE_MES_ALT', 3);
    
    // Тип основной части смешанного письма //
    /**
     * Тип основной части для смешанного письма - простой текст
     * @var int
     */
    define('TYPE_MIXED_TEXT', 0);
    
    /**
     * Тип основной части для смешанного письма - enriched-текст
     * @var int
     */
    define('TYPE_MIXED_ENRICHED', 1);
    
    /**
     * Тип основной части для смешанного письма - html-текст
     * @var int
     */
    define('TYPE_MIXED_HTML', 2);
    
    // Типы частей альтернативного письма //
    /**
     * Тип части альтернативного письма - простой текст
     * @var int
     */  
    define('TYPE_ALT_TEXT', 0);
    
    /**
     * Тип части альтернативного письма - enriched-текст
     * @var int
     */
    define('TYPE_ALT_ENRICHED', 1);
    
    /**
     * Тип части альтернативного письма - html-текст
     * @var int
     */
    define('TYPE_ALT_HTML', 2);
   $this->_send_date = date('D, j M Y G:i:s');
    $this->_from = "";
    $this->_reply_to = "";
    $this->_to = "";
    $this->_subject = "";
    $this->_data = "";
    $this->_type = TYPE_MES_TEXT;
    $this->_mixed_main_type = TYPE_MIXED_TEXT;
    $this->_boundary = "";
    
    $this->_ci = & get_instance();
    $this->_ci->config->load('mailer');
    $config = $this->_ci->config->config;
    
    $this->_smtp_host = $config['smtp_host'];
    $this->_smtp_port = $config['smtp_port'];
    $this->_smtp_login = $config['smtp_login'];
    $this->_smtp_password = $config['smtp_password'];
    
    // Заполним массив типов данных
    $this->_arr_types["jpeg"] = "image/jpeg";
    $this->_arr_types["jpg"] = "image/jpeg";
    $this->_arr_types["jpe"] = "image/jpeg";
    $this->_arr_types["gif"] = "image/gif";
    $this->_arr_types["bmp"] = "image/bmp";
    $this->_arr_types["png"] = "image/png";
    $this->_arr_types["tiff"] = "image/tiff";
    $this->_arr_types["tif"] = "image/tiff";
    $this->_arr_types["css"] = "text/css";
    $this->_arr_types["htm"] = "text/html";
    $this->_arr_types["html"] = "text/html";
    $this->_arr_types["shtml"] = "text/html";
    $this->_arr_types["shtm"] = "text/html";
    $this->_arr_types["asc"] = "text/plain";
    $this->_arr_types["txt"] = "text/plain";
    $this->_arr_types["php"] = "text/plain";
    $this->_arr_types["rtf"] = "text/rtf";
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Метод для установки значений свойств класса
   * 
   * @param string $field название свойства
   * @param mixed $value значение свойства
   */
  public function __set($field, $value){
    switch ($field) {
      case "send_date":
        if ($this->_send_date != $value) {
          $this->_send_date = $value;
        }
        break;
        
      case "from":
        if ($this->_from != $value) {
          if ($this->_reply_to == $this->_from) {
            $this->_reply_to = $value;
          }
          $this->_from = $value;
        }
        break;
        
      case "reply_to":
        if ($this->_reply_to != $value) {
          $this->_reply_to = $value;
        }
        break;
        
      case "to":
        if ($this->_to != $value) {
          $this->_to = $value;
        }
        break;
        
      case "subject":
        if ($this->_subject != $value) {
          $this->_subject = $value;
        }
        break;
        
      case "data":
        if ($this->_type == TYPE_MES_ALT) {
          die("Class_Inigs_Mailer: в режиме \"Альтернативное письмо\" свойство " .
              "\"{$field}\" не доступно");
        } else {
          if ($this->_data != $value) {
            $this->_data = $value;
          }
        }
        break;
        
      case "type":
        if ($this->_type != $value) {
          $this->_type = $value;
        }
        break;
        
      case "mixed_main_type":
        if ($this->_type == TYPE_MES_MIXED) {
          if ($this->_mixed_main_type = $value) {
            $this->_mixed_main_type = $value;
          }
        } else {
          die("Class_Mailer: свойство \"{$field}\" доступно только в режиме " .
              "\"Смешанное письмо\"");
        }
        break;
        
      case "boundary":
        if ($this->_boundary != $value) {
          $this->_boundary = $value;
        }
        break;
        
      case "smtp_host":
        if ($this->_smtp_host != $value) {
          $this->_smtp_host = $value;
        }
        break;
        
      case "smtp_port":
        if ($this->_smtp_port != $value) {
          $this->_smtp_port = $value;
        }
        break;
        
      case "smtp_login":
        if ($this->_smtp_login != $value) {
          $this->_smtp_login = $value;
        }
        break;
        
      case "smtp_pssword":
        if ($this->_smtp_password != $value) {
          $this->_smtp_password = $value;
        }
        break;
        
      default:
        die("Mailer: свойство \"{$field}\" не существует");
        break;
    }
  }
  // ---------------------------------------------------------------------------  
  
  /**
   * Метод для получения значений свойств класса
   * 
   * @param string $field название свойства
   * @return mixed
   */
  public function __get($field) {
    switch ($field) {
      case "send_date":
        return $this->_send_date;
        break;
      case "from":
        return $this->_from;
        break;
      case "reply_to":
        return $this->_reply_to;
        break;
      case "to":
        return $this->_to;
        break;
      case "subject":
        return $this->_subject;
        break;
      case "data":
        if ($this->_type == TYPE_MES_ALT) {
          die("Class_Mailer: в режиме \"Альтернативное письмо\" свойство " .
              "\"{$field}\" не доступно");
        } else {
          return $this->_data;
        }
        break;
      case "type":
        return $this->_type;
        break;
      case "mixed_main_type":
        if ($this->_type == TYPE_MES_MIXED) {
          return $this->_mixed_main_type;
        } else {
          die("Class_Mailer: свойство \"{$field}\" доступно только в режиме " .
              "\"Смешанное письмо\"");
        }
        break;
      case "boundary":
        return $this->_boundary;
        break;
        
      case "smtp_host":
        return $this->_smtp_host;
        break;
      case "smtp_port":
        return $this->_smtp_port;
        break;
      case "smtp_login":
        return $this->_smtp_login;
        break;
      case "smtp_password":
        return $this->_smtp_password;
        break;
        
      default:
        die("InigsMailer: свойство \"{$field}\" не существует");
        break;
    }
  }
  // ---------------------------------------------------------------------------  
  
  /**
   * Функция, которая возвращает MIME-тип по файлу
   * 
   * @param string $file файл, тип которого надо определить
   * @return string
   */
  private function _get_mime_type($file){
    $result = "";
    $char = strlen($file);
    while ($char > 0) {
      $char--;
      if (substr($file, $char, 1) == ".") {
        $fileType = substr($file, $char + 1);
        if (isset($this->_arr_types[$fileType])) {
          $result = $this->_arr_types[$fileType];
        } else {
          $result = "application/octet-stream";
        }
        break;
      }
    }
    return $result;
  }
  // ---------------------------------------------------------------------------
    
  /**
   * Функция которая возвращает ответ Socket соединения
   * 
   * @param int $smtp_conn дискриптор Socket соединения
   * @return string
   */
  private function _get_socket_data($smtp_conn) {
    $data = "";
    while ($str = fgets($smtp_conn, 515)) {
      $data .= $str;
      if (substr($str, 3, 1) == " ") {
        break;
      }
    }
    return $data;
  }
  // ---------------------------------------------------------------------------  
  
  /**
   * Функция для создания заголовка письма
   * 
   * @return string
   */
  private function _get_message_header() {
    $result = "";
    
    // Дата написания письма
    if ($this->_send_date != "") {
      $result .= "Date: {$this->_send_date} +0700\r\n";
    }
    
    // От кого отправлено письмо
    if ($this->_from != "") {
      $from_name = "";
      $from_mail = "";
      // Выделим имя адресата если оно есть и почтовый ящик
      if ($beginpos = strpos($this->_from, "<")) {
        if (!$endpos = strpos($this->_from, ">", $beginpos)) {
          $endpos = strlen($this->_from);
        }
        $from_name = substr($this->_from, 0, $beginpos - 1);
        $from_mail = substr($this->_from, $beginpos + 1,
                           $endpos - $beginpos - 1);
      } else {
        $from_name = "";
        $from_mail = $this->_from;
      }
      if ($from_name != "") {
        $from_name = "=?utf-8?Q?"
                  . str_replace("+", "_", 
                    str_replace("%", "=", urlencode($from_name)))
                  . "?=";
      }
      $result .= "From: {$from_name} <{$from_mail}>\r\n";
    }
    
    // Кому ответ слать
    if ($this->_reply_to != "") {
      $from_name = "";
      $from_mail = "";
      // Выделим имя если оно есть и почтовый ящик
      if ($beginpos = strpos($this->_reply_to, "<")) {
        if (!$endpos = strpos($this->_reply_to, ">", $beginpos)) {
          $endpos = strlen($this->_reply_to);
        }
        $from_name = substr($this->_reply_to, 0, $beginpos - 1);
        $from_mail = substr($this->_reply_to, $beginpos + 1,
                           $endpos - $beginpos - 1);
      } else {
        $from_name = "";
        $from_mail = $this->_reply_to;
      }
      if ($from_name != "") {
        $from_name = "=?utf-8?Q?"
                  . str_replace("+", "_", 
                    str_replace("%", "=", urlencode($from_name)))
                  . "?=";
      }
      $result .= "Reply-To: {$from_name} <{$from_mail}>\r\n";
    }
    
    // Кому письмо слать
    $to_name = "";
    $to_mail = "";
    // Выделим имя если оно есть и почтовый ящик
    if ($beginpos = strpos($this->_to, "<")) {
      if (!$endpos = strpos($this->_to, ">", $beginpos)) {
        $endpos = strlen($this->_to);
      }
      $to_name = substr($this->_to, 0, $beginpos - 1);
      $to_mail = substr($this->_to, $beginpos + 1,
                         $endpos - $beginpos - 1);
    } else {
      $to_name = "";
      $to_mail = $this->_to;
    }
    if ($to_name != "") {
      $to_name = "=?utf-8?Q?"
              . str_replace("+", "_", 
                str_replace("%", "=", urlencode($to_name)))
              . "?=";
    }
    $result .= "To: {$to_name} <{$to_mail}>\r\n";
    
    // Тема письма
    if ($this->_subject != "") {
      $subject = "=?utf-8?Q?"
               . str_replace("+", "_", 
                 str_replace("%", "=",
                 urlencode($this->_subject)))
               . "?=";
      $result .= "Subject: {$subject}\r\n";
    }
    
    // Почтовый клиент
    $result .= "X-Mailer: The Bat! (v3.99.3) Professional\r\n";
    
    // Приоритет письма
    $result .= "X-Priority: 3 (Normal)\r\n";
    
    // Айдишник письма
    // Сначала выделим название сервера из адреса кому отправляется письмо
    if ($beginpos = strpos($this->_to, "@")) {
      if (!$endpos = strpos($this->_to, ">", $beginpos)) {
        $endpos = strlen($this->_to);
      }
      $to_server = substr($this->_to, $beginpos + 1,
                         $endpos - $beginpos - 1);
      $date = date("YmjHis");
      $result .= "Message-ID: <" . rand(1000, 1000000000) .
                 ".{$date}@{$to_server}>\r\n";
    };
    
    // MIME-Version
    $result .= "MIME-Version: 1.0\r\n";
    
    // Тип письма
    switch ($this->_type) {
      case TYPE_MES_TEXT:
        $result .= "Content-Type: text/plain; charset=utf-8\r\n";
        break;
      case TYPE_MES_HTML:
        $result .= "Content-Type: text/html; charset=utf-8\r\n";
        break;
      case TYPE_MES_MIXED:
        if ($this->_boundary != "") {
          $result .= "Content-Type: multipart/mixed; "
                   . "boundary=\"{$this->_boundary}\"\r\n";
        } else {
          die("Class_Mailer: не установлен разделитель частей письма");
        }
        break;
      case TYPE_MES_ALT:
        if ($this->_boundary != "") {
          $result .= "Content-Type: multipart/alternative; "
                   . "boundary=\"{$this->_boundary}\"\r\n";
        } else {
          die("Class_Mailer: не установлен разделитель частей письма");
        }
        break;
    }
    
    // Тип кодирования передачи данных
    switch ($this->_type) {
      case TYPE_MES_TEXT:
        // break опущен специально
      case TYPE_MES_HTML:
        $result .= "Content-Transfer-Encoding: 8bit\r\n";
        break;
    }

    return $result;
  }
  // ---------------------------------------------------------------------------  
  
  /**
   * Функция для создания основной части письма
   * 
   * @return string
   */
  private function _get_message_main_part() {
    $result = "";
    switch ($this->_type) {
      case TYPE_MES_TEXT:
        // break опущен специально
      case TYPE_MES_HTML:
        $result .= "{$this->_data}\r\n";
        break;
      case TYPE_MES_MIXED:
        if ($this->_boundary == "") {
          die("Class_Mailer: не установлен разделитель частей письма");
        }
        // Узнаем какой тип у основной части письма
        switch ($this->_mixed_main_type) {
          case TYPE_MIXED_TEXT:
            $contentType = "text/plain";
            break;
          case TYPE_MIXED_ENRICHED:
            $contentType = "text/enriched";
            break;
          case TYPE_MIXED_HTML:
            $contentType = "text/html";
            break;
          default:
            $contentType = "text/plain";
            break;
        }
        
        $result .= "--{$this->_boundary}\r\n";
        $result .= "Content-Type: {$contentType}; charset=utf-8\r\n";
        $result .= "Content-Transfer-Encoding: 8bit\r\n";
        $result .= "\r\n";
        $result .= "{$this->_data}\r\n";
        break;
      case TYPE_MES_ALT:
        if ($this->_arr_alt_parts != NULL) {
          foreach ($this->_arr_alt_parts as $altPart) {
            $result .= $altPart;
          }
        }
        break;
    }
    return $result;
  }
  // --------------------------------------------------------------------------- 
   
  /**
   * Функция для создания конца письма
   * 
   * @retun string
   */
  private function _get_message_end() {
    $result = "";
    switch ($this->_type) {
      case TYPE_MES_TEXT:
        // break опущен специально
      case TYPE_MES_HTML:
        $result .= ".\r\n";
        break;
      case TYPE_MES_MIXED:
        // break опущен специально
      case TYPE_MES_ALT:
        if ($this->_boundary == "") {
          die("Class_Mailer: не установлен разделитель частей письма");
        }
        $result .= "--{$this->_boundary}--\r\n\r\n.\r\n";
        break;
    }
    return $result;
  }
  // ---------------------------------------------------------------------------  
  
  /**
   * Функция, которая подключается к SMTP серверу и отправляет письмо
   */
  private function _send_message() {
    if ($this->_smtp_host == "" || $this->_smtp_port == "") {
      die("Class_Mailer: не полностью указаны параметры подключения " .
          "SMTP-серверу");
    }
    // Подключимся к SMTP серверу
    $smtp_conn = fsockopen($this->_smtp_host, $this->_smtp_port, $errno,
                           $errstr, 20);
    if (!$smtp_conn) {
      fclose($smtp_conn);
      die("Class_Mailer: не удалось подключиться к SMTP-серверу");
    }
    $data = $this->_get_socket_data($smtp_conn);
    
    // Представимся SMTP серверу
    fputs($smtp_conn, "EHLO Prokopov\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 250) {
       fclose($smtp_conn);
       die("Class_Mailer: SMTP-сервер не захотел поздароваться");
    }
    
    // Попробуем авторизоваться
    fputs($smtp_conn, "AUTH LOGIN\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 334) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не принимает команду авторизации");
    }
    
    // Передадим логин
    fputs($smtp_conn, base64_encode($this->_smtp_login) . "\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 334) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не принимает логин");
    }
    
    // Передадим пароль
    fputs($smtp_conn, base64_encode($this->_smtp_password) . "\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 235) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не принимает пароль");
    }
    
    fputs($smtp_conn, "MAIL FROM:{$this->_from}\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 250) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не принил поле \"От\"");
    }
    
    fputs($smtp_conn, "RCPT TO:{$this->_to}\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 250 AND $code != 251) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не принил поле \"Кому\"");
    }
    
    fputs($smtp_conn, "DATA\r\n");
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 354) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не хочет принимать тело письма");
    }
    
    fputs($smtp_conn, $this->_message);
    $code = substr($this->_get_socket_data($smtp_conn), 0, 3);
    if ($code != 250) {
      fclose($smtp_conn);
      die("Class_Mailer: SMTP-сервер не принил тело письма");
    }
    
    fputs($smtp_conn, "QUIT\r\n");
    fclose($smtp_conn);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция форматирует Имя или Отчество или Фамилию в Правильный формат с загл.
   * @param str $name
   * @return str
   */
  private function _format_name($name) {
    $name = strtolower(trim($name));
    $name = strtoupper(substr($name,0,1)) . substr($name, 1, strlen($name) - 1);
    return $name;
  }
  // ===========================================================================
  
  /**
   * Функция, которая строит текст сообщения
   */
  public function build_and_send_message() {
    if ($this->_to == "") {
      die("Class_Mailer: не указан адрес получателя");
    }
    $this->_message = "";
    $this->_message .= $this->_get_message_header();
    $this->_message .= "\r\n";
    $this->_message .= $this->_get_message_main_part();
    
    switch ($this->_type) {
      case TYPE_MES_MIXED:
        // break опущен специально
      case TYPE_MES_ALT:
        if ($this->_arr_attachments != NULL) {
          foreach ($this->_arr_attachments as $attach) {
            $this->_message .= $attach;
          }
        }
        if ($this->_arr_inline_files != NULL) {
          foreach ($this->_arr_inline_files as $inline) {
            $this->_message .= $inline;
          }
        }
        break;
    }

    $this->_message .= "\r\n";
    $this->_message .= $this->_get_message_end();
    
    // Отправка письма
    $this->_send_message();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Процедура для добавления файла прикрепляемого к письму
   * 
   * @param string $file путь к прикрепляемому файлу
   * @param string $file_name название файла, которое будет у него в письме
   */
  public function add_attachment($file, $file_name = NULL) {
    if ($this->_type == TYPE_MES_TEXT ||
        $this->_type == TYPE_MES_HTML) {
      die("Class_Mailer: в режимах \"Текстовое письмо\" и \"HTML письмо\" " .
          "метод \"add_attachment\" не доступен");
    } else {
      if ($this->_boundary == "") {
        die("Class_Mailer: не установлен разделитель частей письма");
      }
      if ($file_name == NULL) {
        $file_name = basename($file);
      }
      
      if (is_file($file)) {
        // Получим кодированный текст файла
        $file_discrip = fopen($file, "rb");
        $file_code = chunk_split(base64_encode(fread($file_discrip,
          filesize($file))));
        fclose($file_discrip);
        
        // Узнаем MIME-тип файла
        $file_mime_type = $this->_get_mime_type($file);
        
        $text .= "\r\n";
        $text .= "--{$this->_boundary}\r\n";
        $text .= "Content-Type: {$file_mime_type}; name=\"{$file_name}\"\r\n";
        $text .= "Content-transfer-encoding: base64\r\n";
        $text .= "Content-Disposition: attachment; "
               . "filename=\"{$file_name}\"\r\n";
        $text .= "\r\n";
        $text .= "{$file_code}\r\n";
        
        $this->_arr_attachments[] = $text;
      } else {
        die("Class_Mailer: метод \"add_attachment\" - файл не найден");
      }
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Процедура для добавления файла вставляемого прямо в письмо
   * 
   * @param string $file путь к прикрепляемому файлу
   * @param string $cid идентификатор файла для ссылки на него
   * @param string $file_name название файла, которое будет у него в письме
   */
  public function add_inline_file($file, $cid, $file_name = NULL) {
    if ($this->_type == TYPE_MES_TEXT ||
        $this->_type == TYPE_MES_HTML) {
      die("Class_Mailer: в режимах \"Текстовое письмо\" и \"HTML письмо\" " .
          "метод \"add_inline_file\" не доступен");
    } else {
      if ($this->_boundary == "") {
        die("Class_Mailer: не установлен разделитель частей письма");
      }
      if ($file_name == NULL) {
        $file_name = basename($file);
      }
      
      if (is_file($file)) {
        // Получим кодированный текст файла
        $file_discrip = fopen($file, "rb");
        $file_code = chunk_split(base64_encode(fread($file_discrip,
          filesize($file))));
        fclose($file_discrip);
        
        // Узнаем MIME-тип файла
        $file_mime_type = $this->_get_mime_type($file);
        
        $text .= "\r\n";
        $text .= "--{$this->_boundary}\r\n";
        $text .= "Content-Type: {$file_mime_type}; name=\"{$file_name}\"\r\n";
        $text .= "Content-transfer-encoding: base64\r\n";
        $text .= "Content-Disposition: inline; filename=\"{$file_name}\"\r\n";
        $text .= "Content-ID: <{$cid}>\r\n";
        $text .= "\r\n";
        $text .= "{$file_code}\r\n";
        
        $this->_arr_inline_files[] = $text;
      } else {
        die("Class_Mailer: метод \"add_inline_file\" - файл не найден");
      }
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Метод, который добавляет альтернативную часть письма
   * 
   * @param mixed $data данные
   * @param int $type тип данных
   */
  public function add_alt_part($data, $type) {
    if ($this->_type != TYPE_MES_ALT) {
      die("Class_Mailer: метод \"add_alt_part\" доступен только в режиме " .
          " \"Альтернативное письмо\"");
    } else {
      if ($this->_boundary == "") {
        die("Class_Mailer: не установлен разделитель частей письма");
      }
      
      switch ($type) {
        case TYPE_ALT_TEXT:
          $contentType = "text/plain";
          break;
        case TYPE_ALT_ENRICHED:
          $contentType = "text/enriched";
          break;
        case TYPE_ALT_HTML:
          $contentType = "text/html";
          break;
        default:
          $contentType = "text/plain";
          break;
      }
      
      $text .= "\r\n";
      $text .= "--{$this->_boundary}\r\n";
      $text .= "Content-Type: {$contentType}; charset=utf-8\r\n";
      $text .= "Content-Transfer-Encoding: 8bit\r\n";
      $text .= "\r\n";
      $text .= "{$data}\r\n";
      
      $this->_arr_alt_parts[] = $text;
    }
  }
  // ---------------------------------------------------------------------------
}