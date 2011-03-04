<?php if(!defined('__TOASTY')) die();
/**
 * Store Plugin.
 *
 * @file    StorePlugin.php
 * @author  Anupam Jain
 * @version 1.0
 * @addtogroup systemplugins toasty.system.plugins
 */

/**
 * Store Plugin
 * A data storage mechanism.
 * This can be used to queue up data to be rendered,
 * and then the data can be emitted in one go at the end.
 *
 * This is useful, for example, for JS and CSS updates.
 *
 * @class   StorePlugin
 * @author  Anupam Jain
 * @version 1.0
 * @ingroup systemplugins
 */
class StorePlugin extends Plugin {
  /**
   * The data storage
   *
   * @var array $store
   */
  protected $store = array();

  /**
   * Store a value to be emmitted later.
   * This can store multiple values to the same variable at different points in
   * time during the lifecycle of the page, and then get them at once at the end.
   *
   * @param string $var The variable name
   * @param mixed  $val The value to be stored
   * @see StorePlugin::supply()
   */
  function store($var, $val) {
    if(!isset($this->store[$var])) $this->store[$var] = array();
    $this->store[$var][] = $val;
  }

  /**
   * Emit all the values stored till this point
   * Optionally accepts a join (separator) string, a pre, and a post string.
   *
   * @param string $var     The variable to return.
   * @param string $join    [optional] the string to use as a glue (default '').
   * @param string $pre     [optional] the prefix to the result.
   * @param string $post    [optional] the postfix to the result.
   */
  function supply($var, $join='', $pre='', $post='') {
    if(!isset($this->store[$var])) return '';
    return $pre . implode($join, $this->store[$var]) . $post;
  }
}
