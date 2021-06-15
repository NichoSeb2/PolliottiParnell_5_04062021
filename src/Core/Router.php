<?php
namespace App\Core;

class Router {
	private $controller;

	public function __construct() {
		$this->setController();
	}

	public function getController() {
		return $this->controller;
	}

	public function setController() {
		try {
			$routes = yaml_parse_file(CONF_DIR. "/routes.yml");

			$request_uri = rtrim($_SERVER['REQUEST_URI'], "/");

			foreach ($routes as $route) {
				$route['uri'] = rtrim($route['uri'], "/");

				if (preg_match('#^'. $route['uri']. '$#', $request_uri, $matches)) {
					$controller = "\\App\\Controllers\\". $route['controller'];

					$params = array_combine($route['parameters'], array_slice($matches, 1));

					$this->controller = new $controller($route['action'], $params);
					return $this->controller;
				}
			}
		} catch (\Exception $e) {
			echo "Config not found";
		}
	}
}