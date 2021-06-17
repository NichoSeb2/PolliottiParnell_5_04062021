<?php
namespace App\Core;

use App\Exceptions\ConfigException;

class Router {
	private $controller;

	public function __construct() {
		$this->setController();
	}

	public function getController() {
		return $this->controller;
	}

	public function setController() {
		$routes = yaml_parse_file(CONF_DIR. "/routes.yml");

		if (!$routes) {
			throw new ConfigException("Error loading ". CONF_DIR. "/routes.yml");
		}

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
	}
}