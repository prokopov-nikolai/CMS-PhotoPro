<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
// добавим роутинги на все контроллеры, поскольку по умолчанию выводим страницу
// т.е. срабатыет правило $route['([a-z0-9-_]+)'] = 'page/show/$1';
$dir_controllers = ROOT . '/' . APPPATH . 'controllers/';
if ($dh = opendir($dir_controllers)) {
  while (($file = readdir($dh)) !== false) {
    if (substr($file, strlen($file)-4, 4) == '.php'){
      $controller = str_replace('.php', '', $file);
      $route[$controller] = $controller;
    }    
  }
  closedir($dh);
}

// основные правила роутинга движка
$route['default_controller'] = "home";
$route['404_override'] = '';
$route['login'] = 'users/login';
$route['registration'] = 'users/registration';
$route['logout'] = 'users/logout';
$route['category/([a-z0-9-_]+)'] = 'category/show/$1';
$route['gallery/([a-z0-9-_]+)'] = 'gallery/show/$1';
$route['image/([0-9]+)x([0-9]+)/([a-z0-9-_\.]+)\.(jpg|gif|png|nginx)'] = 'images/resize/$1/$2/$3.$4';
$route['([a-z0-9-_]+)'] = 'page/show/$1';

// подключим правила роутинга из плагинов
$file = ROOT . '/' . APPPATH .'plugins/plugins.dat';
if(!file_exists($file)){
  $fn = fopen($file,"w");
  fclose($fn); 
}
$plugins = file($file);
foreach($plugins as $name) {
  $file = ROOT . '/' . APPPATH . "plugins/{$name}/routes.php";
  if (file_exists($file)){
    include_once($file);
  }
}

/* End of file routes.php */
/* Location: ./application/config/routes.php */