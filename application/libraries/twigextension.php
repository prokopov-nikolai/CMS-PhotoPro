<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS PhotoPro
 * Контроллер рубрик
 * 
 * @author ProkopovNI
 * @site http://www.prokopov-nikolai.ru
 */
class TwigExtension extends Twig_Extension_Core{
  
	/**
	 * Расширяем методы твига
	 */
  public function getFilters(){
    return array_merge(
      parent::getFilters(),
      array(
        'size' => new Twig_Filter_Method($this, 'size'),
        'ceil' => new Twig_Filter_Method($this, 'ceil'),
        'floor' => new Twig_Filter_Method($this, 'floor'),
        'array_shift' => new Twig_Filter_Method($this, 'array_shift'),
        'unset_array_shift' => new Twig_Filter_Method($this, 'unset_array_shift'),
        'pr' => new Twig_Filter_Method($this, 'pr'),
        'mktime' => new Twig_Filter_Method($this, 'mktime'),
        'striptags' => new Twig_Filter_Method($this, 'striptags'),
      ) 
    );
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Количество элементов в массиве
   * @param $array
   * @return unknown_type
   */
  public function size($array) {
    return sizeof($array);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Округляем вверх
   * @param $array
   * @return unknown_type
   */
  public function ceil($array) {
    return ceil($array);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Округляем вниз
   * @param $array
   * @return unknown_type
   */
  public function floor($array) {
    return floor($array);
  }
  // ---------------------------------------------------------------------------

  /**
   * Вернуть первое значение массива 
   * @param $array
   * @return unknown_type
   */
  public function array_shift($array) {
    return array_shift($array);
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Вырезать первое значение массива
   * @param $array
   * @return unknown_type
   */
  public function unset_array_shift($array) {
    $keys = array_keys($array);
    unset($array[$keys[0]]);
    return $array;
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Вспомогательная функция для просмотра массива
   * @param unknown_type $array
   * @return unknown_type
   */
  public function pr($array) {
    $a = print_r($array, true);
    $b = '<pre>' . $a . '</pre>';
    return $b;
  }
  // ---------------------------------------------------------------------------
  
    /**
   * Вспомогательная функция для просмотра массива
   * @param unknown_type $array
   * @return unknown_type
   */
  public function mktime() {
    return mktime();
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Очищаем от тегов текст
   * @param unknown_type $array
   * @return unknown_type
   */
  public function striptags($text) {
    return str_replace("\n", ' ', strip_tags(nl2br($text)));
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Разница между двумя датами
   * 1 д. 14 ч.
   */
  public function dateDiff($dateFrom, $dateTo, $long = 2, $full = true){
    $diff = date_diff(new \DateTime($dateTo), new \DateTime($dateFrom));
    $res = '';
    if ($full) {
      // развернуто
      if ($diff->y) {
        $res .= '<nobr>' . $diff->y . ' ' . $this->sklonenie($diff->y, array('год', 'года', 'лет')) . '</nobr> ';
        --$long;
      }
      if ($diff->m && $long) {
        $res .= '<nobr>' . $diff->m . ' ' . $this->sklonenie($diff->m, array('месяц', 'месяца', 'месяцев')) . '</nobr> ';
        --$long;
      }
      if ($diff->d && $long) {
        $res .= '<nobr>' . $diff->d . ' ' . $this->sklonenie($diff->d, array('день', 'дня', 'дней')) . '</nobr> ';
        --$long;
      }
      if ($diff->h && $long) {
        $res .= '<nobr>' . $diff->h . ' ' . $this->sklonenie($diff->h, array('час', 'часа', 'часов')) . '</nobr> ';
        --$long;
      }
      if ($diff->i && $long) {
        $res .= '<nobr>' . $diff->i . ' ' . $this->sklonenie($diff->i, array('минута', 'минуты', 'минут')) . '</nobr> ';
        --$long;
      }
      if ($diff->s && $long) {
        $res .= '<nobr>' . $diff->s . ' ' . $this->sklonenie($diff->s, array('секунда', 'секунды', 'секунд')) . '</nobr> ';
        --$long;
      }
    } else {
      // кратко
      if ($diff->y) {
        $res .= $diff->y . ' г. ';
        --$long;
      }
      if ($diff->m && $long) {
        $res .= $diff->m . ' м. ';
        --$long;
      }
      if ($diff->d && $long) {
        $res .= $diff->d . ' д. ';
        --$long;
      }
      if ($diff->h && $long) {
        $res .= $diff->h . ' ч. ';
        --$long;
      }
      if ($diff->i && $long) {
        $res .= $diff->i . ' м. ';
        --$long;
      }
      if ($diff->s && $long) {
        $res .= $diff->s . ' с. ';
        --$long;
      }
    }
    return trim($res);
  }
}