<?php
namespace App\Core;

use App\Core\Twig;

class Controller {
	protected string $action;

	protected array $params;

	protected Twig $twig;

	public function __construct(string $action, array $params = []) {
		$this->action = $action;
		$this->params = $params;
		$this->twig = new Twig();
	}

	/**
	 * @return void
	 */
	public function execute(): void {
		$method = $this->action;

		$this->$method();
	}

	/**
	 * @param string $template
	 * @param array $array
	 * 
	 * @return void
	 */
	public function render(string $template, array $array = []): void {
		echo $this->twig->render($template, $array);
	}
}