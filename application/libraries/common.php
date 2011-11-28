<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS PhotoPro
 * Общие функции
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class Common {
  private $db;
  private $input;
  private $image_lib;
  
  public function __construct(){
    $CI = & get_instance();
    $this->db = $CI->db;
    $this->input = $CI->input;
    $config = array();
    $config['image_library'] = 'gd2'; // выбираем библиотеку
    $config['quality'] = config_item('image_quality'); 
    $config['maintain_ratio'] = true; // сохранять пропорции
    $config['create_thumb'] = false; // ставим флаг создания эскиза
    $config['new_image'] = false;
    $CI->load->library('image_lib', $config);
    $this->image_lib = $CI->image_lib;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Функция генерирует пароль случайным образом
   * @param int $length Длина пароля
   * @return str
   */
  public function generate_password($length = 10){
    $work_str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= substr($work_str, rand(0,strlen($work_str)-1), 1);
    }
    return $password;
  }
  // ---------------------------------------------------------------------------

  /**
   * Функция преобразует входящую строку в уникальную ссылку
   * @param $name - входная строка
   * @param $unique - проверка уникальности в указанной таблице
   * @param $table - таблица в которой проверять уникальность ссылки без префикса
   * @return str
   */
  public function get_url($name, $unique = false, $table = '') {
    $rus_c=array('А','Б','В','Г','Д','Е','Ё','Ж', 'З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю', 'Я',' ',',','(',')','+','"','«');
    $rus = array('а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю', 'я','&','.','!','/','_',"'",'»');
    $eng = array('a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sch','', 'i','', 'e','yu','ya','-','' ,'' ,'', '_','', '');
    $rname=str_replace($rus_c,$rus,trim($name));
    $rname=strtolower(str_replace($rus,$eng,$rname));
    $rname=preg_replace('/[^a-zA-Z0-9_\-]+/','',$rname);
    if ($unique === true) {
      $this->db->select('*');
      $this->db->from($table);
      $this->db->where($table . '_url', $rname);
      $this->db->limit(1);
      $query = $this->db->get();
      $i = 1;
      while ($query->num_rows() > 0) {
        if (substr($rname, strlen($rname) - 2, 2) == '-' . $i) {
          $i++;
          $rname = substr($rname, 0, strlen($rname) - 2) . '-' . $i;
        } elseif(substr($rname, strlen($rname) - 3, 3) == '-' . $i) {
          $i++;
          $rname = substr($rname, 0, strlen($rname) - 3) . '-' . $i;
        } else {
          $rname .= '-' . $i;
        }
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '_url', $rname);
        $this->db->limit(1);
        $query = $this->db->get();
      }
    }
    return $rname;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Узнаем реальный ip пользователя
   * @return unknown_type
   */
  public function get_real_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }
  // ---------------------------------------------------------------------------
  
  /*
   * Вернем саксес тру
   */
  public function success_true(){
  	echo $this->array_to_json(array('success' => "true"));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Проверим email
   */
  public function check_email($email) {
  	return preg_match("/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,4}$/",trim($email));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Переводим в верхний регистр кирилицу
   */
  public function str_to_upper($str) {
    $rus_upper = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М',
                       'Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ',
                       'Ы','Ь','Э','Ю','Я',' ',',','(',')','+','"','«');
    $rus_lower = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м',
                       'н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ',
                       'ы','ь','э','ю','я','&','.','!','/','_',"'",'»');
    return str_replace($rus_lower, $rus_upper, $str);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Переводим в верхний регистр кирилицу
   */
  public function str_to_lower($str) {
    $rus_upper = array('А','Б','В','Г','Д','Е','Ё','Ж', 'З','И','Й','К','Л','М',
                       'Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ',
                       'Ы','Ь','Э','Ю', 'Я',' ',',','(',')','+','"','«');
    $rus_lower = array('а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м',
                       'н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ',
                       'ы','ь','э','ю', 'я','&','.','!','/','_',"'",'»');
        return str_replace($rus_upper, $rus_lower, $str);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Опеделеяем по скольку элементов выводить на странице
   * @return unknown_type
   */
  public function get_per_page(){
    if ($this->input->post('per_page') && $this->input->post('per_page') <= 100){
      $this->input->set_cookie(array(
        'name'   => 'per_page',
        'value'  => $this->input->post('per_page'),
        'expire' => time() + 3600*24*365
      ));
      return $this->input->post('per_page');
    } else if ($this->input->cookie('per_page')){
      return $this->input->cookie('per_page');
    } else {
      return config_item('per_page'); 
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Преобразует массив в Json формат
   * @param unknown_type $array
   * @return string|string
   */
  public function array_to_json($array){
    if (!is_array($array)) {
      return false;
    }
    $associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
    if ($associative) {
      $construct = array();
      foreach ($array as $key => $value){
        if (is_numeric($key)){
          $key = 'key_' . $key;
        }
        $key = '"' . addslashes($key) . '"';
        if (is_array($value)){
          $value = $this->array_to_json($value);
        } else if (!is_numeric($value) || is_string($value)){
          $value = '"' . addslashes($value) . '"';
          $value = preg_replace('/\r\n/','\n',$value);
        }
        $construct[] = "$key: $value";
      }
      $result = '{ ' . implode( ", ", $construct ) . ' }';
    } else {
      $construct = array();
      foreach ($array as $value){
        if (is_array($value)){
          $value = $this->array_to_json($value);
        } else if( !is_numeric($value) || is_string($value)){
          $value = '\'' . addslashes($value) . '\'';
          $value = preg_replace('/\r\n/','\n',$value);
        }
        $construct[] = $value;
      }
      $result = '[ ' . implode( ', ', $construct ) . ' ]';
    }
    return $result;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Сумма прописью
   * @author runcore
   */
  public function number_in_words($inn, $stripkop=false) {
    $nol = 'ноль';
    $str[100]= array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот');
    $str[11] = array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать');
    $str[10] = array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто');
    $sex = array(
      array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
      array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
    );
    $forms = array(
      array('копейка', 'копейки', 'копеек', 1), // 10^-2
      array('рубль', 'рубля', 'рублей',  0), // 10^ 0
      array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
      array('миллион', 'миллиона', 'миллионов',  0), // 10^ 6
      array('миллиард', 'миллиарда', 'миллиардов',  0), // 10^ 9
      array('триллион', 'триллиона', 'триллионов',  0), // 10^12
    );
    $out = $tmp = array();

    // Поехали!
    $tmp = explode('.', str_replace(',','.', $inn));
    $rub = number_format($tmp[ 0], 0,'','-');
    if ($rub== 0) $out[] = $nol;

    // нормализация копеек
    $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
    $segments = explode('-', $rub);
    $offset = sizeof($segments);
    if ((int)$rub== 0) { // если 0 рублей
      $o[] = $nol;
      $o[] = $this->_morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
    } else {
      foreach ($segments as $k=>$lev) {
        $sexi= (int) $forms[$offset][3]; // определяем род
        $ri = (int) $lev; // текущий сегмент
        if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
          $offset--;
          continue;
        }
        
        // нормализация
        $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);

        // получаем циферки для анализа
        $r1 = (int)substr($ri, 0,1); //первая цифра
        $r2 = (int)substr($ri,1,1); //вторая
        $r3 = (int)substr($ri,2,1); //третья
        $r22= (int)$r2.$r3; //вторая и третья

        // разгребаем порядки
        if ($ri>99) $o[] = $str[100][$r1]; // Сотни
        if ($r22>20) {// >20
          $o[] = $str[10][$r2];
          $o[] = $sex[ $sexi ][$r3];
        } else { // <=20
          if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
          elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
        }

        // Рубли
        $o[] = $this->_morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
        $offset--;
      }
    }
    
    // Копейки
    if (!$stripkop) {
      $o[] = $kop;
      $o[] = $this->_morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
    }
    return preg_replace("/\s{2,}/",' ',implode(' ',$o));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Склоняем словоформу
   */
  private function _morph($n, $f1, $f2, $f5) {
    $n = abs($n) % 100;
    $n1= $n % 10;
    if ($n>10 && $n<20) return $f5;
    if ($n1>1 && $n1<5) return $f2;
    if ($n1==1) return $f1;
    return $f5;
  }
  // ---------------------------------------------------------------------------
}