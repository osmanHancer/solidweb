<?php
define('BASE_URL', ''); // it just delete as many chars as BASE_URL length from the start. for example /blog:  path rendering igrore /blog path
define('DEFAULT_CONT', 'home'); // default controller preselect for  controller. for example actions: /page1, /page2 ...  with same controller  and if missing action run name of controller method
define('AUTO_RENDER_VIEW', true); // auto render phtml  file in  views folder which matc controller/action.phtml
define('INDEX_PATH', 'home'); // if missing controller or action in url  it select home/home action and view
define('LANG_MODE', 'sqlite'); //  if path like : /en/home/page2 > it's ignore first path 2 chars (/en/) 
define('LANG_KNOWN', ["tr", "en", "fr", "ru"]); //  if path like : /en/home/page2 > it's ignore first path 2 chars (/en/) 
$lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if (!in_array($lang, LANG_KNOWN)) {
   $lang="en";
}
define('LANG_DEFAULT',$lang); //  if path like : /en/home/page2 > it's ignore first path 2 chars (/en/) 
define('LANG_FOLDER',ROOT_PATH. '/langs/');

define('DB_PATH', ROOT_PATH . '/../dbplang.sqlite3');

define('DEV_MODE', '1'); 

//todo :  this file and hole app folder should be not required


ini_set('display_errors', DEV_MODE);
ini_set('display_startup_errors', DEV_MODE);
error_reporting(DEV_MODE?(E_ALL):(E_ALL & ~E_DEPRECATED & ~E_STRICT));
define('HOST_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://$_SERVER[HTTP_HOST]".BASE_URL : "http://$_SERVER[HTTP_HOST]"));
define('FULL_URL',HOST_URL . $_SERVER['REQUEST_URI']);