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
  private $_pluginproperties = array();
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
      if(is_array($methods)) foreach($methods as $method) $this->_pluginmethods[$method] = $plugin;
    }
  }

  // Magic method for property isset
  function __isset($name) {
    if(isset($this->_pluginproperties[$name])) {
      return isset($this->_pluginproperties[$name]->$name);
    }
    return false;
  }

  // Magic method for property get
  function __get($name) {
    if(isset($this->_pluginproperties[$name])) {
      return $this->_pluginproperties[$name]->$name;
    }
    return null;
  }

  // Magic method for property set
  function __set($name, $value) {
    if(isset($this->_pluginproperties[$name])) {
      $this->_pluginproperties[$name]->$name = $value;
    }
  }

  // Magic method for property unset
  function __unset($name) {
    if(isset($this->_pluginproperties[$name])) {
      unset($this->_pluginproperties[$name]->$name);
    }
  }

  // Magic method for method calls
  function __call($name, $args) {
    if(isset($this->_pluginmethods[$name]))
      return call_user_func_array(array($this->_pluginmethods[$name], $name), $args);
    echo "Can't find method $name"; die();
  }
}
