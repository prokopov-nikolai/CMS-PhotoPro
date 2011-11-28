<?php
/**
 * Класс для упаковки данных в XML-документ
 */
class Class_XML_Packer {

  /**
   * Объект XML-документа
   */
  private $_xml;

  /**
   * Корневой узел XML-документа
   */
  private $_rootNode;

  /**
   * Кодировка входных данных
   */
  private $_inputCharset;

  /**
   * Конструктор класса
   * @param inputCharset - кодировка входных данных (по умолчанию cp1251)
   */
  public function __construct($inputCharset = 'cp1251') {

    $this->_inputCharset = $inputCharset;
    $this->_xml = new \DOMDocument('1.0', 'utf-8');
    $this->_rootNode = $this->_xml->createElement($this->_encode('DocumentRoot'));
    $this->_xml->appendChild($this->_rootNode);

  }

  /**
   * Добавление данных в корневой узел XML-дерева
   * @param data - данные в виде ассоциативного массива
   * @return результат добавления данных (true/false)
   */
  public function AddData($data) {

    if (!is_array($data)) {
      return false;
    }

    $this->_packArrayToNode($data, $this->_rootNode);

    return true;

  }

  /**
   * Получение XML-документа в виде строки
   * @param formatOutput - флаг форматирования XML (по умолчанию true)
   * @return XML-документ в виде строки (false в случае ошибки)
   */
  public function Compose($formatOutput = true) {

    $this->_xml->formatOutput = $formatOutput;
    return $this->_xml->saveXML();

  }

  /**
   * Упаковка массива данных в XML-узел
   * @param arr - массив данных
   * @param node - XML-узел
   * @return void
   */
  private function _packArrayToNode($arr, $node) {

    foreach ($arr as $parentKey => $parentVal) {
      // Упаковка атрибутов
      if ($parentKey == '_attributes_') {
        foreach ($parentVal as $attbKey => $attbVal) {
          $node->setAttribute($this->_encode($attbKey), $this->_encode($attbVal));
        }
      }
      // Упаковка вложенного массива
      else if (is_array($parentVal)) {
        // Упаковка индексированного массива
        if ($this->_isIndexedArray($parentVal)) {
          foreach ($parentVal as $childKey => $childVal) {
            $childNode = $this->_xml->createElement($this->_encode($parentKey));
            $childNode->setAttribute('indexed_array', 1);
            if (is_array($childVal)) {
              $this->_packArrayToNode($childVal, $childNode);
            }
            else {
              $childNode->nodeValue = $this->_encode($childVal);
            }
            $node->appendChild($childNode);
          }
        }
        // Упаковка ассоциативного массива
        else {
          $childNode = $this->_xml->createElement($this->_encode($parentKey));
          $this->_packArrayToNode($parentVal, $childNode);
          $node->appendChild($childNode);
        }
      }
      // Упаковка простого значения
      else {
        $childNode = $this->_xml->createElement($this->_encode($parentKey));
        $value = $this->_encode($parentVal);
        if (htmlspecialchars($value, ENT_NOQUOTES, $this->_inputCharset) == $value) {
          $childValueNode = $this->_xml->createTextNode($value);
        }
        else {
          $childValueNode = $this->_xml->createCDATASection($value);
        }
        $childNode->appendChild($childValueNode);
        $node->appendChild($childNode);
      }
    }

  }

  /**
   * Проверка типа массива (ассоциативный/индексированный)
   * @param arr - массив для проверки
   * @return true для индексированного массива, false во всех остальных случаях
   */
  private function _isIndexedArray($arr) {

    $result = is_array($arr) ? array_values($arr) === $arr : false;
    return $result;

  }

  /**
   * Подготовка простого значения для упаковки в XML
   * @param val - значение во входной кодировке
   * @return значение в кодировке utf-8
   */
  private function _encode($val) {

    if ($val == '') {
      $result = $val;
    }
    else if (is_bool($val)) {
      $result = $val ? 1 : 0;
    }
    else {
      $result = (string)$val;
      $result = str_replace('<!', '#<#!#', $result);
      $result = str_replace(']>', '#]#>#', $result);
      $result = iconv($this->_inputCharset, 'utf-8', $result);
    }

    return $result;

  }

}