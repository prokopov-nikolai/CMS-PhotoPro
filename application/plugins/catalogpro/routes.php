<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * @name   Plugin for CMS PhotoPro "Debug tools" 
 * @author Николай Прокопов
 * @site   http://prokopov-nikolai.ru
 * @version v 0.1
 * @date 10.02.2012
 */

// клиентские правила 
$plugin_name = 'catalogpro';
$route['default_controller']               = 'plugin/' . $plugin_name . '/index/home';
$route['car/last']                         = 'plugin/' . $plugin_name . '/car/last';
$route['car/availabile/page/([0-9]+)']     = 'plugin/' . $plugin_name . '/car/availabile/$1';
$route['car/availabile']                   = 'plugin/' . $plugin_name . '/car/availabile/1';
$route['car/([a-z0-9-_]+)']                = 'plugin/' . $plugin_name . '/car/show/$1';
$route['make/([a-z0-9-_]+)']               = 'plugin/' . $plugin_name . '/make/show/$1';
$route['make/([a-z0-9-_]+)/page/([0-9]+)'] = 'plugin/' . $plugin_name . '/make/show/$1//$2';
$route['make/get_category/([0-9]+)']       = 'plugin/' . $plugin_name . '/make/get_category/$1';
$route['make/([a-z0-9-_]+)/([a-z0-9-_]+)'] = 'plugin/' . $plugin_name . '/make/show/$1/$2';
$route['make/([a-z0-9-_]+)/([a-z0-9-_]+)/page/([0-9]+)'] = 'plugin/' . $plugin_name . '/make/show/$1/$2/$3';
$route['search']                = 'plugin/' . $plugin_name . '/search';
$route['search/([a-z0-9-_]+)']                = 'plugin/' . $plugin_name . '/search/$1';
// админские правила
$admin_url =  config_item('admin_url');
$route[$admin_url] = $admin_url . '/home';
$route[$admin_url . '/' . $plugin_name] = 'plugin/'. $plugin_name . '/gram';
$route[$admin_url . '/' . $plugin_name . '/([a-z0-9-_]+)'] = 'plugin/'. $plugin_name .'/gram/$1';
$route[$admin_url . '/'. $plugin_name .'/([a-z0-9-_]+)/([a-z0-9-_]+)'] = 'plugin/' . $plugin_name .'/gram/$1/$2';
$route[$admin_url . '/'. $plugin_name .'/([a-z0-9-_]+)/([a-z0-9-_]+)/([a-z0-9-_]+)'] = 'plugin/' . $plugin_name .'/gram/$1/$2/$3';