<?php if(!defined('__TOASTY')) die();
/**
 * This file defines the most common things that need to be
 * available to ALL files at ALL times (even during bootstrap).
 * Currently it includes -
 *    url/path helpers
 *      public_path() - To construct URLs to public (css/js/images) files
 *      public_path_css() - To construct URLs to css files
 *      public_path_js() - To construct URLs to js files
 *      public_path_image() - To construct URLs to image files
 *      _template_file() - To construct PATHs to template files
 *    url / path constants
 *      BASE - The base url for the application
 *      ROOT - The base filepath for the application
 *      SYSTEM_ROOT - The directory with the system files
 *      APP_ROOT - The directory with the application files
 *      TEMPLATES_ROOT - The directory with the template files
 *    dependency autoloading
 *      No public interface
 */

/* USER MODIFIABLE VALUES */
/**
 * The base url for the application. Assumed to be at the root of the server.
 * All URLs to be passed to the client are relative to this path.
 * Example:
 * Don't change anything if the index.php url is something like the following -
 * http://localhost/index.php
 * But if the index.php url is something like the following -
 * http://localhost/toasty/index.php
 * then please redefine the BASE constant to be '/toasty/'
 */
define('BASE', '/');

/* END OF USER MODIFIABLE VALUES */


/*****************************************************************************/
/* DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS YOU KNOW WHAT YOU ARE DOING */
/*****************************************************************************/


/* PUBLIC API */

// constructs an absolute URL to the specified file/directory
/* public */ function public_path($file) {
   return base() . BASE . '/' . $file;
}
/* public */ function public_path_css($theme, $file) {
   return public_path('lib/public/css/themes/'.$theme.'/'.$file.'.css');
}
/* public */ function public_path_js($file) {
   return public_path('lib/public/js/'.$file.'.js');
}
/* public */ function public_path_image($file) {
   return public_path('lib/public/images/'.$file);
}

// ROOT - The base filepath for the application
define('ROOT', dirname(__FILE__));
// SYSTEM_ROOT - The directory with the system files
define('SYSTEM_ROOT', ROOT.'/system');
// APP_ROOT - The directory with the application files
define('APP_ROOT', ROOT.'/app');

// TEMPLATES_ROOT - The directory with the template files
define('TEMPLATES_ROOT', ROOT.'/templates');

// constructs an absolute PATH to the specified template file
/* public */ function _template_file($template) {
   return TEMPLATES_ROOT . '/' . $template . '.template.php';
}


/* PRIVATE FUNCTIONS */

// class autoloading magic
// *PRIVATE* - Not to be used by clients
/* private */ $SEARCH_LOCATIONS = array(SYSTEM_ROOT, APP_ROOT);
/* private */ function __autoload($class_name) {
  global $SEARCH_LOCATIONS;
  $file = '/' . $class_name . '.php';
  $found = false;
  foreach($SEARCH_LOCATIONS as $loc) {
    if(file_exists($loc.$file)) {
      include $loc.$file;
      $found = true;
      break;
    }
  }
}

// Constructs the base path for urls
// *PRIVATE* - Not to be used by clients
/* private */ function base() {
  if(isset($_SERVER["HTTPS"])) $ret .= "https";
  else $ret = 'http';
  $ret .= "://";
  $ret .= $_SERVER["SERVER_NAME"];
  if($_SERVER["SERVER_PORT"] != "80") {
    $ret .= ":".$_SERVER["SERVER_PORT"];
  }
  $ret .= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
  return $ret;
}
