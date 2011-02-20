<?php if(!defined('__TOASTY')) die();
/**
 * Presenter Base class.
 *
 * @file    Presenter.php
 * @author  Anupam Jain
 * @version 1.0
 * @addtogroup systemcore toasty.system.core
 */

/**
 * Presenter Base class.
 * A Presenter represents something that gets rendered on the GUI.
 * 
 * It interacts with the business logic (Model) to decide what needs to get
 * rendered. It also holds the data that the View (Templates) need to render
 * themeselves.
 *
 * Presenters allow plugins through the HasPlugins interface.
 * 
 * Presenters can be nested. Nested Presenters are called Widgets.
 *
 * @class   Presenter
 * @author  Anupam Jain
 * @version 1.0
 * @ingroup systemcore
 */
abstract class Presenter extends HasPlugins {
  
  /**
   * A reference to the View (Template)
   * 
   * @var string $template
   */
  protected $template;
  /**
   * The Data Transfer Object
   * It holds the data that controls the rendering of the view.
   * 
   * @var array $data
   */
  protected $data = array();

  /**
   * A data storage mechanism.
   * This can be used to queue up data to be rendered,
   * and then the data can be emitted in one go at the end.
   *
   * This is useful, for example, for JS and CSS updates.
   * 
   * @var array $store
   */
  protected $store = array();
  
  /**
   * An observer mechanism.
   * This allows other classes to listen for updates
   * to any of the data transfer object fields.
   * 
   * This can be used, for example, to allow one Presenter to affect another.
   *
   * @var array $observers
   */
  protected $observers = array();

  /**
   * Constructor.
   * The constructor needs to be passed the name of the master template file.
   * This template filename is automatically expanded and should not include the path
   * or the extension.
   *
   * @param string $template
   */
  function __construct($template) {
    session_start();
    parent::__construct();
    $this->template = $template;
  }

  /**
   * The renderer for the Data Transfer Object Variables.
   * Automatically renders any sub-widgets.
   *
   * This helps to present a uniform api to the view.
   *
   * <b style="color:red">FIXME: The name erroneously implies automatic echoing.
   * This is NOT the case. The function returns the rendered variable as a string.</b>
   *
   * @param string $var to be rendered.
   * @return Rendered string.
   */
  function renderVar($var) {
    $val = $this->getVar($var);
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
   * The getter for the Data Transfer Object Variables.
   *
   * @param The variable to be returned.
   * @return The value of the variable.
   */
  function getVar($var) {
    return $this->data[$var];
  }

  /**
   * The setter for the Data Transfer Object Variables.
   *
   * Also invokes the callbacks if any.
   *
   * @param The variable to be returned.
   * @return The value of the variable.
   */
  function setVar($var, $val) {
    $this->data[$var] = $val;
    if(isset($this->observers[$var])) {
      foreach($this->observers[$var] as $callback) {
        call_user_func($callback, $var, $val, $this);
      }
    }
  }

  /**
   * Getter for the master template.
   */
  function getTemplate() {
    return $this->template;
  }

  /**
   * Setter for the master template.
   */
  function setTemplate($template) {
    $this->template = $template;
  }

  /**
   * Getter for the entire Data Transfer object
   */
  function getData() {
    return $this->data;
  }

  /**
   * Getter for the entire Data Transfer object with nested widgets prerendered.
   * Its exceptionally useful for the JSON plugin.
   *
   * @see JSONPlugin
   */
  function getRenderedData() {
    $ret = array();
    foreach($this->data as $var=>$val) {
      $ret[$var] = $this->renderVar($var);
    }
    return $ret;
  }

  /**
   * When called by the clients, this renders the page.
   * This function automatically invokes Presnter::populate()
   */
  function render() {
    $this->populate();
    include _template_file($this->template);
  }

  /**
   * Populates the page based on the business logic.
   * It MUST be implemented by the child classes.
   *
   * @see Presenter::render()
   * @see Presenter::getVar()
   * @see Presenter::setVar()
   */
  abstract function populate();

  /**
   * Store a value to be emmitted later.
   * This can store multiple values to the same variable at different points in
   * time during the lifecycle of the page, and then get them at once at the end.
   *
   * @param string $var The variable name
   * @param mixed  $val The value to be stored
   * @see Presenter::supply()
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

  /**
   * Redirect the browser to another page immediately.
   * Static Utility function. When invoked, this function stops all further processing
   * and redirects the browser to the specified url.
   * The url is automatically converted from relative to absolute.
   * This function necessarily needs to be called before ANY output has been
   * send to the browser. Otherwise it will emit a warning (and do nothing).
   *
   * @param string $url The "relative" url to redirect to
   */
  static function redirect($url) {
    header("Location: ".public_path($url));
    session_write_close();
    exit;
  }
}
