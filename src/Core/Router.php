<?php
namespace App\Core;

use App\Core\Controller;
use App\Exceptions\ConfigException;

class Router {
	private $controller;

	public function __construct() {
		$this->initController();
	}

	/**
	 * @return Controller
	 */
	public function getController(): Controller {
		return $this->controller;
	}

	/**
	 * @return Controller
	 */
	public function initController(): Controller {
		$confDir = CONF_DIR. "/routes.yml";
		$routes = yaml_parse_file($confDir);

		if (!$routes) {
			throw new ConfigException("Error loading ". $confDir);
		}

		$requestURI = explode("?", rtrim($_SERVER['REQUEST_URI'], "/"))[0];

		foreach ($routes as $route) {
			$route['uri'] = rtrim($route['uri'], "/");

			if (preg_match('#^'. $route['uri']. '$#', $requestURI, $matches)) {
				$controller = "\\App\\Controllers\\". $route['controller'];

				$params = array_combine($route['parameters'], array_slice($matches, 1));

				$this->controller = new $controller($route['action'], $params);
				return $this->controller;
			}
		}
	}
}