<?php
namespace App\Controllers;

use App\Core\Controller;

class AccountController extends Controller {
	public function login() {
		$this->render("@client/pages/login.html.twig", [
			'action' => "login"
		]);
	}

	public function register() {
		$this->render("@client/pages/login.html.twig", [
			'action' => "register"
		]);
	}
}