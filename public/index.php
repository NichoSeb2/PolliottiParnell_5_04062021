<?php
session_start();

define('ROOT_DIR', realpath(dirname(__DIR__)));
define('CONF_DIR', ROOT_DIR. '/config');
define('TEMPLATE_DIR', ROOT_DIR. '/templates');

require_once(ROOT_DIR. '/vendor/autoload.php');

use App\Core\Router;
use App\Exceptions\SQLException;
use App\Exceptions\MailException;
use App\Exceptions\TwigException;
use App\Exceptions\ConfigException;
use App\Controllers\ErrorController;
use App\Exceptions\ControllerNotFound;
use App\Exceptions\FileServerException;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\RequestedEntityNotFound;

try {
	$router = new Router();

	$controller = $router->getController();

	if (is_null($controller)) {
		throw new ControllerNotFound();
	}

	$controller->execute();
}
catch (AccessDeniedException $e) {
	$controller = new ErrorController("show403");

	$controller->execute();
}
catch (ControllerNotFound | RequestedEntityNotFound $e) {
	$controller = new ErrorController("show404");

	$controller->execute();
}
catch (TwigException | ConfigException | SQLException | PDOException | MailException | FileServerException $e) {
	$controller = new ErrorController("show500", [
		"message" => $e
	]);

	$controller->execute();
}
catch (\Exception $e) {
	$controller = new ErrorController("show500", [
		"message" => "Router initialization failed with unhandled exception : ". $e, 
	]);

	$controller->execute();
}