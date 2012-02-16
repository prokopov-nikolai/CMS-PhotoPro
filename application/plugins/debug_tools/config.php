<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Debug tools" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 10.02.2012
 */

/**
 * Высота расскрытой панели 
 */
$config['debug_tools_height'] = 300;
 
/**
 * Количество знаков после запятой во времени sql запроса 
 */
$config['query_time_decimals'] = 3;

/**
 * Подсвечивть основные операторы
 */
$config['query_highlighting'] = true;

/**
 * Пороги скорости выполнения запросов  (0-0.25- быстрые; 0.25-0.5 - средние; > 0.5 - медленные)
 */
$config['query_speed'] = array(0.25, 0.5); 

/**
 * Кому разрешено видить панель 1 - всем; 0 - только администраторам
 */
$config['access'] = 0; 