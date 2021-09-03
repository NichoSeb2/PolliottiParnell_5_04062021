<?php
namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller {
	/**
	 * Display a 403
	 * 
	 * @return void
	 */
	public function show403(): void {
		$this->render("@client/errors/403.html.twig");
	}

	/**
	 * Display a 404
	 * 
	 * @return void
	 */
	public function show404(): void {
		$this->render("@client/errors/404.html.twig");
	}

	/**
	 * Display a 500
	 * 
	 * @return void
	 */
	public function show500(): void {
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