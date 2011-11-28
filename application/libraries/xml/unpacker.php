<?php
/**
 * Класс для распаковки данных из XML-документа
 */
class Unpacker {

  /**
   * Объект XML-документа
   */
  private $_xml;

  /**
   * Кодировка выходных данных
   */
  private $_output_charset;

  /**
   * Конструктор класса
   * @param xml - XML-документ для загрузки
   * @param outputCharset - кодиривка выходных данных (по умолчанию cp1251)
   */
  public function __construct(){}
  //----------------------------------------------------------------------------
  
  /*
   * Загружает 
   */
  public function initialize($file, $output_charset = 'utf-8') {
    $this->_output_charset = $output_charset;
    if (!isset($this->_xml)) {
      $this->_xml = new \DOMDocument('1.0', 'utf-8');
    }
    if ($file) {
      // Загружаем XML дважды, чтобы исправить
      // потенциально неправильное форматирование входного XML
      $this->_xml->formatOutput = true;
      $this->_xml->load($file);
      $xml = $this->_xml->saveXML();
      $this->_xml->load($file);
    }
  }
  // ---------------------------------------------------------------------------
  
  /**
   * Получение данных из XML-документа
   * @return ассоциативный массив с данными
   */
  public function get_data() {

    $data = $this->_convert_node_to_array($this->_xml->documentElement);
    $data = $this->_shape_data($data);
    return $data;

  }
  // ---------------------------------------------------------------------------
  
  /**
   * Преобразование XML-узла в ассоциативный массив
   * @param node - XML-узел
   * @return ассоциативный массив с данными узла (false в случае ошибки)
   */
  private function _convert_node_to_array($node) {

    $array = false;
    if ($node->hasAttributes()) {
      $array['_attributes_'] = array();
      foreach ($node->attributes as $attr) {
        $array['_attributes_'][$this->_decode($attr->nodeName)] =
          $this->_decode($attr->nodeValue);
      }
    }
    if ($node->hasChildNodes()) {
      if ($node->childNodes->length == 1) {
        $array[$this->_decode($node->firstChild->nodeName)] =
          $this->_decode($node->firstChild->nodeValue);
      }
      else {
        foreach ($node->childNodes as $childNode) {
          if ($childNode->nodeType != XML_TEXT_NODE) {
            $array[$this->_decode($childNode->nodeName)][] =
              $this->_convert_node_to_array($childNode);
          }
        }
      }
    }
    return $array;

  }
  // ---------------------------------------------------------------------------
  
  /**
   * Преобразование распакованных данных к исходому виду
   * @param data - ассоциативный массив распакованных данных
   * @return преобразованный ассоциативный массив (false в случае ошибки)
   */
  private function _shape_data($data) {

    if (is_array($data)) {
      if ((isset($data[0])) && (count($data) == 1) &&
        (!isset($data[0]['_attributes_']['indexed_array']) ||
        !$data[0]['_attributes_']['indexed_array'])) {
        $data = $data[0];
      }
      if (isset($data['_attributes_']['indexed_array']) &&
        count($data['_attributes_']) == 1) {
        unset($data['_attributes_']);
      }
      if (isset($data['#text'])) {
        $data = $data['#text'];
      }
      else if (isset($data['#cdata-section'])) {
        $data = $data['#cdata-section'];
      }
      else if (is_array($data)) {
        foreach ($data as $key => $val) {
          $data[$key] = $this->_shape_data($val);
        }
      }
    }
    return $data;

  }
  // ---------------------------------------------------------------------------
  
  /**
   * Преобразование кодировки значения
   * @param str - значение в кодировке utf-8
   * @return значение в выходной кодировке
   */
  private function _decode($str) {

    $result = iconv('utf-8', $this->_output_charset, (string)$str);
    $result = str_replace('#<#!#', '<!', $result);
    $result = str_replace('#]#>#', ']>', $result);
    return $result;

  }
  // ---------------------------------------------------------------------------
}