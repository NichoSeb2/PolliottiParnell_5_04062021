<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\PostManager;
use App\Managers\AdminManager;
use App\Managers\SocialManager;

class IndexController extends Controller {	
	public function showHome() {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$postManager = new PostManager();

		$post = $postManager->findBy([], [
			'created_at' => "DESC", 
		], 2, 0);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/index.html.twig", [
			'post' => $post, 
			'socials' => $socials, 
			'catchPhrase' => $admin->getCatchPhrase(), 
			'urlPicture' => $admin->getUrlPicture(), 
			'altPicture' => $admin->getAltPicture(), 
			'urlCV' => $admin->getUrlCV(), 
		]);
	}

	public function showContact() {
		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/contact.html.twig", [
			'socials' => $socials, 
			'catchPhrase' => $admin->getCatchPhrase(), 
		]);
	}
}