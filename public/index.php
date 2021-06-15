<?php
define('ROOT_DIR', realpath(dirname(__DIR__)));
define('CONF_DIR', ROOT_DIR. '/config');
define('TEMPLATE_DIR', ROOT_DIR. '/templates');

require_once(ROOT_DIR. '/vendor/autoload.php');

use App\Core\Router;

try {
	$router = new Router();

	$controller = $router->getController();

	if (!is_null($controller)) {
		$controller->execute();
	} else {
		echo "404";
	}
} catch (\Exception $e) {
	echo "Router initialization failed";
}