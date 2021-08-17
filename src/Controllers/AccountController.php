<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\FormHandler;
use App\Managers\UserManager;
use App\Exceptions\FormException;
use App\Service\FormReturnMessage;

class AccountController extends Controller {
	/**
	 * @return void
	 */
	public function login(): void {
		if (!isset($_POST['submitButton'])) {
			$this->render("@client/pages/login.html.twig");

			exit();
		}

		try {
			(new FormHandler)->login($_POST);
		} catch (FormException $e) {
			extract($_POST);

			$this->render("@client/pages/login.html.twig", [
				'error' => $e->getMessage(), 
				'form' => [
					'email' => $email, 
				], 
			]);
		}
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
		$template = "@client/pages/register.html.twig";

		if (!isset($_POST['submitButton'])) {
			$this->render($template);

			exit();
		}

		try {
			(new FormHandler)->register($_POST);

			$this->render($template, [
				'success' => true, 
			]);
		} catch (FormException $e) {
			extract($_POST);

			$this->render($template, [
				'error' => $e->getMessage(), 
				'form' => [
					'firstName' => $firstName, 
					'lastName' => $lastName, 
					'email' => $email, 
				], 
			]);
		}
	}

	/**
	 * @return void
	 */
	public function resend(): void {
		$template = "@client/pages/resend.html.twig";

		if (!isset($_POST['submitButton'])) {
			$this->render($template);

			exit();
		}

		try {
			(new FormHandler)->resend($_POST);

			$this->render($template, [
				'success' => FormReturnMessage::VERIFICATION_MAIL_RESEND, 
			]);
		} catch (FormException $e) {
			$this->render($template, [
				'error' => $e->getMessage(), 
			]);
		}
	}

	/**
	 * @return void
	 */
	public function verify(): void {
		$verificationToken = $this->params['verificationToken'];

		try {
			(new FormHandler)->verify($verificationToken);

			$this->render("@client/pages/verify.html.twig", [
				'success' => FormReturnMessage::ACCOUNT_SUCCESSFULLY_VERIFIED, 
			]);
		} catch (FormException $e) {
			$this->render("@client/pages/verify.html.twig", [
				'error' => $e->getMessage(), 
			]);
		}
	}

	/**
	 * @return void
	 */
	public function forget(): void {
		$template = "@client/pages/forget.html.twig";

		if (!isset($_POST['submitButton'])) {
			$this->render($template);

			exit();
		}

		try {
			(new FormHandler)->forget($_POST);

			$this->render($template, [
				'success' => FormReturnMessage::FORGOT_PASSWORD_MAIL_SEND, 
			]);
		} catch (FormException $e) {
			$this->render($template, [
				'error' => $e->getMessage(), 
			]);
		}
	}

	/**
	 * @return void
	 */
	public function newPassword(): void {
		$template = "@client/pages/newPassword.html.twig";

		$forgotPasswordToken = $this->params['forgotPasswordToken'];

		$userManager = new UserManager();

		$user = $userManager->findOneBy([
			'forgot_password_token' => $forgotPasswordToken, 
		]);

		if (!is_null($user)) {
			if (!isset($_POST['submitButton'])) {
				$this->render($template, [
					'forgotPasswordToken' => $forgotPasswordToken, 
				]);

				exit();
			}

			try {
				(new FormHandler)->newPassword($_POST, $user);

				$this->render($template, [
					'forgotPasswordToken' => $forgotPasswordToken, 
					'success' => FormReturnMessage::PASSWORD_SUCCESSFULLY_CHANGED, 
				]);
			} catch (FormException $e) {
				$this->render($template, [
					'forgotPasswordToken' => $forgotPasswordToken, 
					'error' => $e->getMessage(), 
				]);
			}

			exit();
		} else {
			$error = FormReturnMessage::NO_ACCOUNT_FOR_FORGOT_PASSWORD_TOKEN;
		}

		$this->render($template, [
			'forgotPasswordToken' => $forgotPasswordToken, 
			'error' => $error, 
		]);
	}
}