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
   * @param The variable to be returned.
   * @return The value of the variable.
   */
  function setVar($var, $val) {
    $this->data[$var] = $val;
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
}
