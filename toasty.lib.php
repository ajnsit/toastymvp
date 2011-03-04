<?php if(!defined('__TOASTY')) die();
/**
 * This file defines the most common things that need to be
 * available to ALL files at ALL times (even during bootstrap).
 * Currently it includes -
 *    utility functions
 *      redirect() - To redirect the browser to some other page
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
 *      MODULE_ROOT - The directory with the modules
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

/**
 * The list of enabled modules in the application.
 * All the files for module 'Foo' must be in the directory modules/Foo.
 */
$MODULES = array();

/* END OF USER MODIFIABLE VALUES */


/*****************************************************************************/
/* DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS YOU KNOW WHAT YOU ARE DOING */
/*****************************************************************************/


/* PUBLIC API */

// constructs an absolute URL to the specified file/directory
/* public */ function public_path($file) {
   return base() . BASE . $file;
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
// MODULE_ROOT - The directory with the modules
define('MODULES_ROOT', ROOT.'/modules');
// APP_ROOT - The directory with the application files
define('APP_ROOT', ROOT.'/app');

// TEMPLATES_ROOT - The directory with the template files
define('TEMPLATES_ROOT', ROOT.'/templates');

// constructs an absolute PATH to the specified template file
/* public */ function _template_file($template) {
   return TEMPLATES_ROOT . '/' . $template . '.template.php';
}


/**
 * Redirect the browser to another page immediately.
 * When invoked, this function stops all further processing and redirects the
 * browser to the specified url. The url is automatically converted from
 * relative to absolute. This function necessarily needs to be called before ANY
 * output has been send to the browser. Otherwise it will emit a warning (and do nothing).
 *
 * @param string $url The "relative" url to redirect to
 */
function redirect($url) {
  header("Location: ".public_path($url));
  session_write_close();
  exit;
}

/* PRIVATE FUNCTIONS */

// class autoloading magic
// *PRIVATE* - Not to be used by clients
/* private */ $SEARCHLOCATIONS = array(SYSTEM_ROOT, APP_ROOT);
foreach($MODULES as $module) $SEARCHLOCATIONS[] = MODULES_ROOT.'/'.$module;
/* private */ function __autoload($class_name) {
  global $SEARCHLOCATIONS;
  foreach($SEARCHLOCATIONS as $loc) {
    if(file_exists("$loc/$class_name.php")) {
      include "$loc/$class_name.php";
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
