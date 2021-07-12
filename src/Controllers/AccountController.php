<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\AdminManager;
use App\Managers\SocialManager;

class AccountController extends Controller {
	public function login() {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/login.html.twig", [
			'socials' => $socials, 
			'catchPhrase' => $admin->getCatchPhrase(), 
		]);
	}

	public function register() {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/register.html.twig", [
			'socials' => $socials, 
			'catchPhrase' => $admin->getCatchPhrase(), 
		]);
	}
}