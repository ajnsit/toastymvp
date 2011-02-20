<?php if(!defined('__TOASTY')) die();
/**
 * Plugin Base class.
 *
 * @file    Plugin.php
 * @author  Anupam Jain
 * @version 1.0
 * @addtogroup systemcore toasty.system.core
 */

/**
 * Plugin Base class.
 * Defines the interface that a Plugin needs to follow.
 *
 * @class   Plugin
 * @author  Anupam Jain
 * @version 1.0
 * @ingroup systemcore
 */
abstract class Plugin {
  /**
   * The reference to the containing class that implements the HasPlugins interface.
   * @var HasPlugins $self the containing class that implements the HasPlugins interface.
   */
  protected $self;

  /**
   * Constructor.
   * It is passed a reference to the containing class that implements the HasPlugins interface.
   * 
   * Also initialises itself.
   *
   * @param HasPlugins $self the containing class that implements the HasPlugins interface.
   */
  function __construct(HasPlugins $self) {
    $this->self = $self;
    $this->init();
  }

  /**
   * The hook, that allows a child class to initialise itself.
   * Child classes may override this.
   * Does nothing by default.
   */
  function init() {}

}
