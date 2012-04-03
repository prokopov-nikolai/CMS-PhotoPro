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
}