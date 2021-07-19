<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\PostManager;

class IndexController extends Controller {	
	/**
	 * @return void
	 */
	public function showHome(): void {
		$postManager = new PostManager();

		$post = $postManager->findBy([], [
			'created_at' => "DESC", 
		], 2, 0);

		$this->render("@client/pages/index.html.twig", [
			'post' => $post, 
		]);
	}

	/**
	 * @return void
	 */
	public function showContact(): void {
		$this->render("@client/pages/contact.html.twig");
	}
}