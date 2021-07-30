<?php
namespace App\Controllers;

use App\Core\Mail;
use App\Core\Twig;
use App\Core\Controller;
use App\Managers\PostManager;
use App\Managers\AdminManager;

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

			if (!empty($name) && !empty($email) && !empty($message)) {
				$mail = new Mail();

				$twig = new Twig();

				$html = $twig->render("@mail/pages/contact.html.twig", [
					'from' => $name, 
					'subject' => $subject, 
					'text' => $message, 
				]);

				$admin = (new AdminManager)->findById(1);

				$mail->send([$email, $name], [[$admin->getEmail(), $admin->getFirstName(). " ". $admin->getLastName()]], $subject, $html, "Message de contact provenant de : $name\nDissant : $message");

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