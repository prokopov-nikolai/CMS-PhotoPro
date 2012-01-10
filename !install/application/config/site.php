<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//=========================
//===== КОНФИГИ САЙТА ===== 
//=========================

/*
|-------------------------------------------------------------------------------
| ШАБЛОНЫ 
|-------------------------------------------------------------------------------
*/
/**
 * Шаблон сайта
 */
$config['site_template'] = 'photopro';

/**
 * Шаблон админки
 */
$config['admin_template'] = 'default';



/*
|-------------------------------------------------------------------------------
| ИЗОБРАЖЕНИЯ 
|-------------------------------------------------------------------------------
*/
/**
 * Качество изображения картинок 
 */
$config['image_quality'] = 80;

/**
 * Выравнивание водяного знака
 */
$config['wm_vrt_alignment'] = 'bottom';
$config['wm_hor_alignment'] = 'left';


/**
 * Исходные изображения загружаемые на сервер
 */
$config['image_source']['quality'] = 100;
$config['image_source']['max_width'] = 1140;
$config['image_source']['max_height'] = 790;



/*
|-------------------------------------------------------------------------------
| ОБЩИЕ 
|-------------------------------------------------------------------------------
*/
/**
 * Текс для вывода когда сайт закрыт 
 */
$config['site_close'] = '';

/**
 * url 
 */
$config['site_url'] = '';

/**
 * Admin email 
 */
$config['admin_email'] = '';

/**
 * Показывать голосовани 1 - показать, 0 - скрыть
 */
$config['show_vote'] = FALSE;

/**
 * Блокировка браузеров заглушкой
 */
$config['block_browser_lower']['ie'] = 6;
$config['block_browser_lower']['firefox'] = 3.5;
$config['block_browser_lower']['opera'] = 10;
$config['block_browser_lower']['chrome'] = 11;
$config['block_browser_lower']['safari'] = 4;

/**
 * По скольку выводить на страницу
 */
$config['per_page'] = 12;

/*
|-------------------------------------------------------------------------------
| ГЛАВНАЯ СТРАНИЦА 
|-------------------------------------------------------------------------------
*/
$config['main_page']['gallery_url'] = 'nashi-detki';
$config['main_page']['gallery_delay'] = 5;
$config['main_page']['gallery_count'] = 4;

/*
|-------------------------------------------------------------------------------
| СТРАНИЦА ГАЛЕРЕИ 
|-------------------------------------------------------------------------------
*/
$config['gallery']['plugin'] = 'photopro_slider';
$config['gallery']['delay'] = 5;

