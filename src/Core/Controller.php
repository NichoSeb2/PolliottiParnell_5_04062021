<?php
namespace App\Core;

class Controller {
	protected $action;
	protected $params;

	public function __construct($action, $params = null) {
		$this->action = $action;
		$this->params = $params;
	}

	public function execute() {
		$method = $this->action;
		$this->$method();
	}
}