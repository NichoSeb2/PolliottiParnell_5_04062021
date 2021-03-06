<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\UserManager;
use App\Exceptions\FormException;
use App\Service\FormReturnMessage;
use App\Service\RegisterProcessHandler;

class AccountController extends Controller {
	/**
	 * Display the login form
	 * 
	 * @return void
	 */
	public function login(): void {
		$message = [];
		$form = [];

		if (isset($_POST['submitButton'])) {
			try {
				(new RegisterProcessHandler)->login($_POST);
			} catch (FormException $e) {
				extract($_POST);

				$message = [
					'error' => $e->getMessage(), 
				];

				$form = [
					'email' => $email, 
				];
			}
		}

		$this->render("@client/pages/login.html.twig", [
			'message' => $message, 
			'form' => $form, 
		]);
	}

	/**
	 * Logout an user
	 * 
	 * @return void
	 */
	public function logout(): void {
		session_unset();
		session_destroy();

		header("Location: /login");

		exit();
	}

	/**
	 * Display the register form
	 * 
	 * @return void
	 */
	public function register(): void {
		$template = "@client/pages/register.html.twig";

		$message = [];
		$form = [];

		if (isset($_POST['submitButton'])) {
			try {
				(new RegisterProcessHandler)->register($_POST);

				$message = [
					'success' => true, 
				];
			} catch (FormException $e) {
				extract($_POST);

				$message = [
					'error' => $e->getMessage(), 
				];

				$form = [
					'firstName' => $firstName, 
					'lastName' => $lastName, 
					'email' => $email, 
				];
			}
		}

		$this->render($template, [
			'message' => $message, 
			'form' => $form, 
		]);
	}

	/**
	 * Display the resend form
	 * 
	 * @return void
	 */
	public function resend(): void {
		$template = "@client/pages/resend.html.twig";

		$message = [];

		if (isset($_POST['submitButton'])) {
			try {
				(new RegisterProcessHandler)->resend($_POST);

				$message = [
					'success' => FormReturnMessage::VERIFICATION_MAIL_RESEND, 
				];
			} catch (FormException $e) {
				$message = [
					'error' => $e->getMessage(), 
				];
			}
		}

		$this->render($template, [
			'message' => $message, 
		]);
	}

	/**
	 * Verify an user
	 * 
	 * @return void
	 */
	public function verify(): void {
		$verificationToken = $this->params['verificationToken'];

		$message = [];

		try {
			(new RegisterProcessHandler)->verify($verificationToken);

			$message = [
				'success' => FormReturnMessage::ACCOUNT_SUCCESSFULLY_VERIFIED, 
			];
		} catch (FormException $e) {
			$message = [
				'error' => $e->getMessage(), 
			];
		}

		$this->render("@client/pages/verify.html.twig", [
			'message' => $message, 
		]);
	}

	/**
	 * Display the forget password form
	 * 
	 * @return void
	 */
	public function forget(): void {
		$template = "@client/pages/forget.html.twig";

		$message = [];

		if (isset($_POST['submitButton'])) {
			try {
				(new RegisterProcessHandler)->forget($_POST);

				$message = [
					'success' => FormReturnMessage::FORGOT_PASSWORD_MAIL_SEND, 
				];
			} catch (FormException $e) {
				$message = [
					'error' => $e->getMessage(), 
				];
			}
		}

		$this->render($template, [
			'message' => $message, 
		]);
	}

	/**
	 * Display the new password form
	 * 
	 * @return void
	 */
	public function newPassword(): void {
		$template = "@client/pages/newPassword.html.twig";

		$message = [];

		$forgotPasswordToken = $this->params['forgotPasswordToken'];

		$userManager = new UserManager();

		$user = $userManager->findOneBy([
			'forgot_password_token' => $forgotPasswordToken, 
		]);

		if (!is_null($user)) {
			if (isset($_POST['submitButton'])) {
				try {
					(new RegisterProcessHandler)->newPassword($_POST, $user);

					$message = [
						'success' => FormReturnMessage::PASSWORD_SUCCESSFULLY_CHANGED, 
					];
				} catch (FormException $e) {
					$message = [
						'error' => $e->getMessage(), 
					];
				}
			}
		} else {
			$message = [
				'error' => FormReturnMessage::NO_ACCOUNT_FOR_FORGOT_PASSWORD_TOKEN, 
			];
		}

		$this->render($template, [
			'forgotPasswordToken' => $forgotPasswordToken, 
			'message' => $message, 
		]);
	}
}