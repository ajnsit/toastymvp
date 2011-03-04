<?php if(!defined('__TOASTY')) die();
/**
 * Observer Plugin.
 *
 * @file    ObserverPlugin.php
 * @author  Anupam Jain
 * @version 1.0
 * @addtogroup systemplugins toasty.system.plugins
 */

/**
 * A plugin that implements observers
 * This allows other classes to listen for updates
 * to any of the data transfer object fields.
 *
 * This can be used, for example, to allow one Presenter to affect another.
 *
 * IMPORTANT NOTE:
 * To use the plugin, you need to enable 'afterSetVar' hook by overriding setVar().
 * Do something like the following -
 * <pre>
 *   function setVar($var, $val) {
 *     parent::setVar($var, $val);
 *     // This hook is needed for the observe plugin to work
 *     $this->hookIn('after');
 *   }
 * </pre>
 *
 * @class   ObserverPlugin
 * @author  Anupam Jain
 * @version 1.0
 * @ingroup systemplugins
 */
class ObserverPlugin extends Plugin {

  /**
   * The array of registered observers.
   *
   * @var array $observers
   */
  protected $observers = array();

  /**
   * The setter for the Data Transfer Object Variables.
   *
   * Also invokes the callbacks if any.
   *
   * @hook to be called at the end of execution of setVar
   * @param The variable to be returned.
   * @return The value of the variable.
   */
  function afterSetVar($var, $val) {
    if(isset($this->observers[$var])) {
      foreach($this->observers[$var] as $callback) {
        call_user_func($callback, $var, $val, $this->self);
      }
    }
  }

  /**
   * Add a new observer to this page.
   * This observer will be invoked whenever a page variable changes.
   *
   * @param string $var
   * @param callback $callback
   */
  function observe($var, $callback) {
    if(!isset($this->observers[$var])) {
      $this->observers[$var] = array();
    }
    $this->observers[$var][] = $callback;
  }
}
