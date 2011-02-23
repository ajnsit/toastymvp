<?php define('__TOASTY', 'OK');

require_once('toasty.lib.php');

class Index extends Presenter {
  function __construct() {
    parent::__construct('index');
  }
  function populate() {
    $this->setVar('header', 'Hello Sweet World!');
  }
}

$page = new Index();
$page->render();
