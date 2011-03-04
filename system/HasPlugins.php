<?php if(!defined('__TOASTY')) die();
/**
 * HasPlugins.
 *
 * @file    HasPlugins.php
 * @author  Anupam Jain
 * @version 1.0
 * @addtogroup systemcore toasty.system.core
 */

/**
 * A simple plugin mechanism for classes.
 * Any class that extends HasPlugins can have their functionality extended
 * through Plugins (classes that extend the class Plugin).
 * 
 * <b>The typical scheme for implementing this is</b> -
 * <ol>
 * <li>Base class extends HasPlugins. This gives all child classes the ability
 *    to have plugins.</li>
 * <li>Child classes extend Base class. And define a $this->plugins property,
 *    enumerating the list of class names that will act as plugins.
 *    This allows each child to have its own set of plugins.</li>
 * <li>Now any public methods defined inside the plugin class get merged into the
 *    functionality of the corresponding child class. This allows us to access
 *    plugin methods through the child class like so - $childClassObj->method().</li>
 * <li>Child classes allow hooks. At any point inside a method (say 'foo'),
 *     the class may call hookIn() with a string argument (say 'before').
 *     Then at that point of time, all plugins' methods with the name 'beforeFoo'
 *     will be invoked in order of plugin inclusion.</li>
 * </ol>
 *
 * For more information on defining new plugins, look at the documentation of the
 * Plugin class.
 * 
 * @class   HasPlugins
 * @author  Anupam Jain
 * @version 1.0
 * @ingroup systemcore toasty.system.core
 * @see Plugin
 */

class HasPlugins {
  // The plugins array
  protected $plugins = array();

  // Private properties
  private $_pluginmethods = array();
  private $_pluginobjs = array();

  // Constructor caches all plugin methods for easy access later on
  function __construct() {
    foreach($this->plugins as $pluginclass) {
      $pluginclass .= "Plugin";
      if(get_parent_class($pluginclass)!='Plugin') continue;
      $plugin = new $pluginclass($this);
      $this->_pluginobjs[$pluginclass] = $plugin;
      $methods = get_class_methods($pluginclass);
      if(is_array($methods)) foreach($methods as $method) $this->_pluginmethods[$method][] = $plugin;
    }
  }

  // The hook mechanism
  protected function hookIn($name) {
    list(,$caller) = debug_backtrace(false);
    $name = $name.ucfirst($caller['function']);
    if(isset($this->_pluginmethods[$name])) {
      foreach($this->_pluginmethods[$name] as $plugin) {
        $ret = call_user_func_array(array($plugin, $name), $caller['args']);
      }
    }
    if(isset($ret)) return $ret;
  }

  // Magic method for method calls
  function __call($name, $args) {
    if(isset($this->_pluginmethods[$name][0]))
      return call_user_func_array(array($this->_pluginmethods[$name][0], $name), $args);
    echo "Can't find method $name"; die();
  }
}
