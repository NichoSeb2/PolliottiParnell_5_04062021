<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\AdminManager;
use App\Managers\SocialManager;

class AccountController extends Controller {
	/**
	 * @return void
	 */
	public function login(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/login.html.twig", [
			'admin' => $admin, 
			'socials' => $socials, 
		]);
	}

	/**
	 * @return void
	 */
	public function register(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/register.html.twig", [
			'admin' => $admin, 
			'socials' => $socials, 
		]);
	}
}