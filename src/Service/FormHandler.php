<?php
namespace App\Service;

use App\Model\User;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use App\Managers\PostManager;
use App\Managers\UserManager;
use App\Managers\CommentManager;
use App\Exceptions\FormException;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\RequestedEntityNotFound;
use App\Model\Social;

class FormHandler {
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

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function contact(array $data): void {
		extract($data);

		if (isset($name, $email, $message, $subject)) {
			(new SendMail)->sendContactMail($name, $email, $subject, $message);
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
	public function editAccount(array $data): void {
		extract($data);

		if (isset($firstName, $lastName, $email)) {
			$userManager = new UserManager();

			// omit verification if user null because function called by the admin controller
			$user = $userManager->findConnected();

			$user->setFirstName($firstName);
			$user->setLastName($lastName);
			$user->setEmail($email);

			$userManager->update($user);
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	public function editPassword(array $data): void {
		extract($data);

		if (isset($oldPassword, $password, $confirmPassword)) {
			$userManager = new UserManager();

			// omit verification if user null because function called by the admin controller
			$user = $userManager->findConnected();

			if (password_verify($oldPassword, $user->getPassword())) {
				if ($password === $confirmPassword) {
					$options = [
						'cost' => 12,
					];

					$user->setPassword(password_hash($password, PASSWORD_BCRYPT, $options));

					(new UserManager)->update($user);
				} else {
					throw new FormException(FormReturnMessage::PASSWORD_CPASSWORD_NOT_MATCH);
				}
			} else {
				throw new FormException("Ancien mot de passe incorrect.");
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
	public function addComment(array $data, string $slug): void {
		extract($data);

		$post = (new PostManager)->findOneBy([
			'slug' => $slug, 
		]);

		if (!is_null($post)) {
			$user = (new UserManager)->findConnected();

			if (!is_null($user)) {
				if (isset($content)) {
					$comment = new Comment([
						'userId' => $user->getId(), 
						'postId' => $post->getId(), 
						'content' => $content, 
					]);

					$commentManager = new CommentManager();

					$commentManager->create($comment);
				} else {
					throw new FormException(FormReturnMessage::MISSING_FIELD);
				}
			} else {
				throw new AccessDeniedException();
			}
		} else {
			throw new RequestedEntityNotFound();
		}
	}

	function editSocial(array $data, Social $social = null): Social {
		extract($data);

		if (is_null($social)) {
			$social = new Social();
		}

		if (isset($name, $url, $icon)) {
			$social->setName($name);
			$social->setUrl($url);
			$social->setIcon($icon);

			return $social;
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}
}
