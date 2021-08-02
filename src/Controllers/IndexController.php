<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\SendMail;
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
		$success = null;
		$error = null;

		if (isset($_POST['submitButton'])) {
			extract($_POST);

			if (!empty($name) && !empty($email) && !empty($message) && !empty($subject)) {
				(new SendMail)->sendContactMail($name, $email, $subject, $message);

				$success = "Votre message a bien été envoyé, une réponse vous sera transmise au plus vite.";
			} else {
				$error = "Un champ n'est pas correctement remplie.";
			}
		}

		$this->render("@client/pages/contact.html.twig", [
			'success' => $success, 
			'error' => $error, 
		]);
	}
}