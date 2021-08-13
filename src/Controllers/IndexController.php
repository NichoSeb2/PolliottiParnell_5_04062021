<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\FormHandler;
use App\Managers\PostManager;
use App\Exceptions\FormException;
use App\Service\FormReturnMessage;

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
		if (!isset($_POST['submitButton'])) {
			$this->render("@client/pages/contact.html.twig");

			exit();
		}

		if (isset($_POST['submitButton'])) {
			try {
				(new FormHandler)->contact($_POST);

				$this->render("@client/pages/contact.html.twig", [
					'success' => FormReturnMessage::MESSAGE_SUCCESSFULLY_SEND, 
				]);
			} catch (FormException $e) {
				$this->render("@client/pages/contact.html.twig", [
					'error' => $e->getMessage(), 
				]);
			}
		}
	}
}