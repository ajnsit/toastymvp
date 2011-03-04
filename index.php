<?php define('__TOASTY', 'OK');

require_once('toasty.lib.php');

class Index extends Presenter {
  protected $plugins = array('Observer', 'JSON');

  function __construct() {
    parent::__construct('index');
    $this->observe('header',array($this, 'callme'));
  }

  function callme() {
    echo 'Set the value of heading to: '. $this->getVar('header');
  }

  function setVar($var, $val) {
    parent::setVar($var, $val);
    // This hook is needed for the observe plugin to work
    $this->hookIn('after');
  }

  function populate() {
    $this->setVar('header', 'Hello Sweet World!');
    $this->setVar('json', $this->jsonOut());
  }
}

$page = new Index();
$page->render();
