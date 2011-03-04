<?php if(!defined('__TOASTY')) die();
/**
 * JSON Output Plugin.
 *
 * @file    JSONPlugin.php
 * @author  Anupam Jain
 * @version 1.0
 * @addtogroup systemplugins toasty.system.plugins
 */

/**
 * Json Plugin
 * Provides a quick and dirty way to render all the page variables as JSON.
 *
 * @class   JSONPlugin
 * @author  Anupam Jain
 * @version 1.0
 * @ingroup systemplugins
 */
class JsonPlugin extends Plugin {

  /**
   * The renderer for the Data Transfer Object Variables.
   * Automatically renders any sub-widgets.
   *
   * This helps to present a uniform api to the view.
   *
   * FIXME: <b style="color:red">The name erroneously implies automatic echoing.
   * This is NOT the case. The function returns the rendered variable as a string.</b>
   *
   * @param string $var to be rendered.
   * @return Rendered string.
   */
  function renderVar($var) {
    $val = $this->self->getVar($var);
    // If the variable resolves to an Object with a render method,
    //  render the object and then return the string representation instead
    if(is_object($val) && method_exists($val, 'render')) {
      ob_start();
      $val->render();
      $ret = ob_get_contents();
      ob_end_clean();
      return $ret;
    }
    // Else return the value as is
    return $val;
  }

  /**
   * Getter for the entire Data Transfer object with nested widgets prerendered.
   *
   * @see JSONPlugin
   */
  function getRenderedData() {
    $ret = array();
    $data = $this->self->getData();
    foreach($data as $var=>$val) {
      $ret[$var] = $this->renderVar($var);
    }
    return $ret;
  }

  /**
   * Return all the variables set in the current page as a json array (string).
   * @return The json array as a string
   */
  function jsonOut() {
    return json_encode($this->getRenderedData());
  }
}
