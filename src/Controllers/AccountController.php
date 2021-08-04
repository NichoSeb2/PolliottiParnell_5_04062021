<?php
namespace App\Controllers;

use App\Model\User;
use Ramsey\Uuid\Uuid;
use App\Core\Controller;
use App\Service\SendMail;
use App\Service\UserLogged;
use App\Managers\UserManager;
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

		extract($_POST);

		if (isset($email, $password)) {
			$userManager = new UserManager();

			$user = $userManager->findOneBy([
				'email' => $email, 
			]);

			if (!is_null($user)) {
				if ($user->getVerified()) {
					if (password_verify($password, $user->getPassword())) {
						(new UserLogged)->redirectUser($user);

						exit();
					} else {
						$error = FormReturnMessage::WRONG_PASSWORD;
					}
				} else {
					$error = FormReturnMessage::ACCOUNT_NOT_VERIFIED;
				}
			} else {
				$error = FormReturnMessage::NO_ACCOUNT_FOR_EMAIL;
			}
		} else {
			$error = FormReturnMessage::MISSING_FIELD;
		}

		$this->render("@client/pages/login.html.twig", [
			'error' => $error, 
			'form' => [
				'email' => $email, 
			], 
		]);
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
							'verified' => false, 
							'verificationToken' => Uuid::uuid4()->toString(), 
							'forgotPasswordToken' => null, 
						]);

						$userManager->create($user);

						(new SendMail)->sendVerificationMail($user);

						$this->render($template, [
							'success' => true, 
						]);

						exit();
					} else {
						$error = FormReturnMessage::ACCOUNT_ALREADY_EXIST;
					}
				} else {
					$error = FormReturnMessage::PASSWORD_CPASSWORD_NOT_MATCH;
				}
			} else {
				$error = FormReturnMessage::MISSING_FIELD;
			}

			$this->render($template, [
				'error' => $error, 
				'form' => [
					'firstName' => $firstName, 
					'lastName' => $lastName, 
					'email' => $email, 
				], 
			]);

			exit();
		}

		$this->render($template);
	}

	/**
	 * @return void
	 */
	public function resend(): void {
		$template = "@client/pages/resend.html.twig";

		if (isset($_POST['submitButton'])) {
			extract($_POST);

			if (!empty($email)) {
				$userManager = new UserManager();

				$user = $userManager->findOneBy([
					'email' => $email, 
				]);

				if (!is_null($user)) {
					if (!$user->getVerified()) {
						(new SendMail)->sendVerificationMail($user);

						$success = FormReturnMessage::VERIFICATION_MAIL_RESEND;

						$this->render($template, [
							'success' => $success, 
						]);

						exit();
					} else {
						$error = FormReturnMessage::ACCOUNT_ALREADY_VERIFIED;
					}
				} else {
					$error = FormReturnMessage::NO_ACCOUNT_FOR_EMAIL;
				}
			} else {
				$error = FormReturnMessage::MISSING_FIELD;
			}

			$this->render($template, [
				'error' => $error, 
			]);

			exit();
		}

		$this->render($template);
	}

	/**
	 * @return void
	 */
	public function verify(): void {
		$verificationToken = $this->params['verificationToken'];

		$userManager = new UserManager();

		$user = $userManager->findOneBy([
			'verification_token' => $verificationToken, 
		]);

		if (!is_null($user)) {
			if (!$user->getVerified()) {
				$user->setVerified(true);

				$userManager->update($user);

				$this->render("@client/pages/verify.html.twig", [
					'success' => FormReturnMessage::ACCOUNT_SUCCESSFULLY_VERIFIED, 
				]);

				exit();
			} else {
				$error = FormReturnMessage::VERIFICATION_TOKEN_ALREADY_USED;
			}
		} else {
			$error = FormReturnMessage::NO_ACCOUNT_FOR_VERIFICATION_TOKEN;
		}

		$this->render("@client/pages/verify.html.twig", [
			'error' => $error, 
		]);
	}

	/**
	 * @return void
	 */
	public function forget(): void {
		$template = "@client/pages/forget.html.twig";

		if (isset($_POST['submitButton'])) {
			extract($_POST);

			if (!empty($email)) {
				$userManager = new UserManager();

				$user = $userManager->findOneBy([
					'email' => $email, 
				]);

				if (!is_null($user)) {
					$user->setForgotPasswordToken(Uuid::uuid4()->toString());

					$userManager->update($user);

					(new SendMail)->sendForgotPasswordMail($user);

					$this->render($template, [
						'success' => FormReturnMessage::FORGOT_PASSWORD_MAIL_SEND, 
					]);

					exit();
				} else {
					$error = FormReturnMessage::NO_ACCOUNT_FOR_EMAIL;
				}
			} else {
				$error = FormReturnMessage::MISSING_FIELD;
			}

			$this->render($template, [
				'error' => $error, 
			]);

			exit();
		}

		$this->render($template);
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
			if (isset($_POST['submitButton'])) {
				extract($_POST);

				if (!empty($password) && !empty($confirmPassword)) {
					if ($password === $confirmPassword) {
						$options = [
							'cost' => 12,
						];

						$user->setPassword(password_hash($password, PASSWORD_BCRYPT, $options));

						$user->setForgotPasswordToken(null);

						$userManager->update($user);

						$this->render($template, [
							'forgotPasswordToken' => $forgotPasswordToken, 
							'success' => FormReturnMessage::PASSWORD_SUCCESSFULLY_CHANGED, 
						]);

						exit();
					} else {
						$error = FormReturnMessage::PASSWORD_CPASSWORD_NOT_MATCH;
					}
				} else {
					$error = FormReturnMessage::MISSING_FIELD;
				}
			} else {
				$this->render($template, [
					'forgotPasswordToken' => $forgotPasswordToken, 
				]);

				exit();
			}
		} else {
			$error = FormReturnMessage::NO_ACCOUNT_FOR_FORGOT_PASSWORD_TOKEN;
		}

		$this->render($template, [
			'forgotPasswordToken' => $forgotPasswordToken, 
			'error' => $error, 
		]);
	}
}