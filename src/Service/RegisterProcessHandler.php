<?php
namespace App\Service;

use App\Model\User;
use App\Model\Admin;
use Ramsey\Uuid\Uuid;
use App\Managers\UserManager;
use App\Managers\AdminManager;
use App\Exceptions\FileException;
use App\Exceptions\FormException;
use App\Exceptions\FileServerException;
use App\Exceptions\FileTooBigException;

class RegisterProcessHandler {
	/**
	 * @param array $data
	 * @param array $file
	 * 
	 * @return void
	 */
	public function setup(array $data, array $file): void {
		extract($data);
		extract($file);

		if (!isset($firstName, $lastName, $email, $password, $confirmPassword, $catchPhrase, $cvFile, $pictureAlt, $pictureFile) || $cvFile['error'] == 4 || $pictureFile['error'] == 4) {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}

		if ($password === $confirmPassword) {
			$userManager = new UserManager(['getCreatedAt', 'getUpdatedAt']);
			$adminManager = new AdminManager([
				'getCreatedAt', 
				'getUpdatedAt', 
				'getRole', 
				'getFirstName', 
				'getLastName', 
				'getEmail', 
				'getPassword', 
				'getVerified', 
				'getVerificationToken', 
				'getForgotPasswordToken', 
			]);

			// to prevent error at creation
			$userManager->delete(new User([
				'id' => 1, 
			]));
			$adminManager->delete(new Admin([
				'id' => 1, 
			]));

			$options = [
				'cost' => 12, 
			];

			// id is set because the main admin and user need to be the id 1
			$userManager->create(new User([
				'id' => 1, 
				'role' => "admin", 
				'firstName' => $firstName, 
				'lastName' => $lastName, 
				'email' => $email, 
				'password' => password_hash($password, PASSWORD_BCRYPT, $options), 
				'verified' => false, 
				'verificationToken' => Uuid::uuid4()->toString(), 
			]));

			$user = $userManager->findOneBy([
				'email' => $email, 
			]);

			// id is set because the main admin and user need to be the id 1
			$admin = new Admin([
				'id' => 1, 
				'userId' => $user->getId(), 
				'catchPhrase' => $catchPhrase, 
				'altPicture' => $pictureAlt, 
			]);

			try {
				$targetFile = (new FileUploader)->upload($file['cvFile'], "uploads/", "cv", ['application/pdf']);
				$admin->setUrlCv($targetFile);

				$targetFile = (new FileUploader)->upload($file['pictureFile'], "uploads/", "pdp", FileUploader::IMAGE_TYPE);
				$admin->setUrlPicture($targetFile);
			} catch (FileTooBigException $e) {
				throw new FormException($e->getMessage());
			} catch (FileServerException $e) {
				throw new FileServerException($e->getMessage());
			} catch (FileException $e) {
				throw new FormException(FormReturnMessage::ERROR_WHILE_UPLOADING_FILE_RETRY);
			}

			$adminManager->create($admin);

			(new SendMail)->sendVerificationMail($user);
		} else {
			throw new FormException(FormReturnMessage::PASSWORD_CPASSWORD_NOT_MATCH);
		}
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function login(array $data): void {
		extract($data);

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
						throw new FormException(FormReturnMessage::WRONG_PASSWORD);
					}
				} else {
					throw new FormException(FormReturnMessage::ACCOUNT_NOT_VERIFIED);
				}
			} else {
				throw new FormException(FormReturnMessage::NO_ACCOUNT_FOR_EMAIL);
			}
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function register(array $data): void {
		extract($data);

		if (isset($firstName, $lastName, $email, $password, $confirmPassword)) {
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
				} else {
					throw new FormException(FormReturnMessage::ACCOUNT_ALREADY_EXIST);
				}
			} else {
				throw new FormException(FormReturnMessage::PASSWORD_CPASSWORD_NOT_MATCH);
			}
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function resend(array $data): void {
		extract($data);

		if (isset($email)) {
			$userManager = new UserManager();

			$user = $userManager->findOneBy([
				'email' => $email, 
			]);

			if (!is_null($user)) {
				if (!$user->getVerified()) {
					(new SendMail)->sendVerificationMail($user);
				} else {
					throw new FormException(FormReturnMessage::ACCOUNT_ALREADY_VERIFIED);
				}
			} else {
				throw new FormException(FormReturnMessage::NO_ACCOUNT_FOR_EMAIL);
			}
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param string $verificationToken
	 * 
	 * @return void
	 */
	public function verify(string $verificationToken): void {
		$userManager = new UserManager();

		$user = $userManager->findOneBy([
			'verification_token' => $verificationToken, 
		]);

		if (!is_null($user)) {
			if (!$user->getVerified()) {
				$user->setVerified(true);

				$userManager->update($user);
			} else {
				throw new FormException(FormReturnMessage::VERIFICATION_TOKEN_ALREADY_USED);
			}
		} else {
			throw new FormException(FormReturnMessage::NO_ACCOUNT_FOR_VERIFICATION_TOKEN);
		}
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function forget(array $data): void {
		extract($data);

		if (isset($email)) {
			$userManager = new UserManager();

			$user = $userManager->findOneBy([
				'email' => $email, 
			]);

			if (!is_null($user)) {
				$user->setForgotPasswordToken(Uuid::uuid4()->toString());

				$userManager->update($user);

				(new SendMail)->sendForgotPasswordMail($user);
			} else {
				throw new FormException(FormReturnMessage::NO_ACCOUNT_FOR_EMAIL);
			}
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * @param User $user
	 * 
	 * @return void
	 */
	public function newPassword(array $data, User $user): void {
		extract($data);

		if (isset($password, $confirmPassword)) {
			if ($password === $confirmPassword) {
				$options = [
					'cost' => 12,
				];

				$user->setPassword(password_hash($password, PASSWORD_BCRYPT, $options));

				$user->setForgotPasswordToken(null);

				(new UserManager)->update($user);
			} else {
				throw new FormException(FormReturnMessage::PASSWORD_CPASSWORD_NOT_MATCH);
			}
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}
}
