<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\PostManager;
use App\Managers\AdminManager;
use App\Managers\SocialManager;

class IndexController extends Controller {	
	/**
	 * @return void
	 */
	public function showHome(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$postManager = new PostManager();

		$post = $postManager->findBy([], [
			'created_at' => "DESC", 
		], 2, 0);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/index.html.twig", [
			'connected' => !empty($_SESSION['id']) && is_numeric($_SESSION['id']), 
			'admin' => $admin, 
			'post' => $post, 
			'socials' => $socials, 
		]);
	}

	/**
	 * @return void
	 */
	public function showContact(): void {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/contact.html.twig", [
			'connected' => !empty($_SESSION['id']) && is_numeric($_SESSION['id']), 
			'admin' => $admin, 
			'socials' => $socials, 
		]);
	}
}