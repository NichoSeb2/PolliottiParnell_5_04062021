<?php
namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller {
	public function show404() {
		$this->render("@client/errors/404.html.twig");
	}

	public function show500() {
		$error = $this->params['message'];

		if (is_string($error)) {
			$this->render("@client/errors/500.html.twig", [
				"message" => $error, 
			]);
		} else {
			$this->render("@client/errors/500.html.twig", [
				"message" => trim(explode("Stack trace", $error->getMessage())[0]), 
				"file" => $error->getFile(), 
				"line" => $error->getLine(), 
				"traces" => $error->getTrace(), 
			]);
		}
	}
}