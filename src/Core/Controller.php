<?php
namespace App\Core;

class Controller {
	protected $action;
	protected $params;
	protected $twig;

	public function __construct($action, $params = null) {
		$this->action = $action;
		$this->params = $params;
		$this->twig = new Twig();
	}

	public function execute() {
		$method = $this->action;
		$this->$method();
	}

	public function render($template, $array = []) {
		echo $this->twig->render($template, $array);
	}
}