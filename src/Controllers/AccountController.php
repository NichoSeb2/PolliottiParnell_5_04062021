<?php
namespace App\Controllers;

use App\Model\User;
use App\Core\Controller;
use App\Service\UserLogged;
use App\Managers\UserManager;

class AccountController extends Controller {
	/**
	 * @return void
	 */
	public function login(): void {
		if (isset($_POST['submitButton'])) {
			extract($_POST);

			if (!empty($email) && !empty($password)) {
				$userManager = new UserManager();

				$user = $userManager->findOneBy([
					'email' => $email, 
				]);

				if (!is_null($user)) {
					if (password_verify($password, $user->getPassword())) {
						(new UserLogged)->redirectUser($user);

						exit();
					} else {
						$error = "Mot de passe incorrect.";
					}
				} else {
					$error = "Aucun compte n'existe avec cette adresse email.";
				}
			} else {
				$error = "Un champ n'est pas correctement remplie.";
			}

			$this->render("@client/pages/login.html.twig", [
				'error' => $error, 
				'form' => [
					'email' => $email, 
				], 
			]);

			exit();
		}

		$this->render("@client/pages/login.html.twig");
	}

	/**
	 * @return void
	 */
	public function logout(): void {
		session_unset();
		session_destroy();

		header("Location: /login");

		exit();
	}

	/**
	 * @return void
	 */
	public function register(): void {
		if (isset($_POST['submitButton'])) {
			extract($_POST);

			if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password) && !empty($confirmPassword)) {
				if ($password === $confirmPassword) {
					$userManager = new UserManager();

					$user = $userManager->findOneBy([
						'email' => $email, 
					]);

					if (is_null($user)) {
						$options = [
							'cost' => 12,
						];

						$user = new User([
							'firstName' => $firstName, 
							'lastName' => $lastName, 
							'email' => $email, 
							'password' => password_hash($password, PASSWORD_BCRYPT, $options), 
						]);

						$userManager->create($user);

						$user = $userManager->findOneBy([
							'email' => $email, 
						]);

						(new UserLogged)->redirectUser($user);

						exit();
					} else {
						$error = "Un compte existe déjà avec cette adresse email.";
					}
				} else {
					$error = "Le mot de passe et la confirmation du mot de passe doivent être identique.";
				}
			} else {
				$error = "Un champ n'est pas correctement remplie.";
			}

			$this->render("@client/pages/register.html.twig", [
				'error' => $error, 
				'form' => [
					'firstName' => $firstName, 
					'lastName' => $lastName, 
					'email' => $email, 
				], 
			]);

			exit();
		}

		$this->render("@client/pages/register.html.twig");
	}
}