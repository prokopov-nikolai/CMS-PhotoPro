<?php
// клиентские правила 
$route['issues'] = 'plugin/name/issues';
$route['issues/([a-z0-9-_]+)'] = 'plugin/name/issues/$1';
$route['issues/([a-z0-9-_]+)/([a-z0-9-_]+)'] = 'plugin/name/issues/$1/$2';

// админские правила
$admin_url =  config_item('admin_url');
$route[$admin_url .'/plugin/issues'] = 'plugin/name/issues/admin_';
$route[$admin_url .'/plugin/issues/([a-z0-9-_]+)'] = 'plugin/name/issues/admin_$1';
$route[$admin_url .'/plugin/issues/([a-z0-9-_]+)/([a-z0-9-_]+)'] = 'plugin/name/issues/admin_$1/$2';
