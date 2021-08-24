<?php
namespace App\Service;

use App\Model\Post;
use App\Model\User;
use App\Model\Admin;
use App\Model\Social;
use Ramsey\Uuid\Uuid;
use App\Model\Comment;
use Cocur\Slugify\Slugify;
use App\Managers\PostManager;
use App\Managers\UserManager;
use App\Managers\AdminManager;
use App\Managers\CommentManager;
use App\Exceptions\FileException;
use App\Exceptions\FormException;
use App\Exceptions\FileServerException;
use App\Exceptions\FileTooBigException;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\RequestedEntityNotFound;

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
	 * @return Admin|null
	 */
	public function editAccount(array $data): Admin {
		extract($data);

		if (isset($firstName, $lastName, $email)) {
			$userManager = new UserManager();

			// omit verification if user null because function called by the admin controller
			$user = $userManager->findConnected();

			$user->setFirstName($firstName);
			$user->setLastName($lastName);
			$user->setEmail($email);

			$userManager->update($user);

			return (new AdminManager)->findById($user->getId());
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * 
	 * @return void
	 */
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
				throw new FormException(FormReturnMessage::OLD_PASSWORD_INCORRECT);
			}
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * @param array $file
	 * 
	 * @return Admin|null
	 */
	public function editAdminInfo(array $data, array $file): Admin {
		extract($data);

		$adminManager = new AdminManager();

		// omit verification if admin null because function called by the admin controller
		$admin = $adminManager->findConnected();

		try {
			if (isset($file['CVFile']) && $file['CVFile']['error'] != 4) {
				$targetFile = (new FileUploader)->upload($file['CVFile'], "uploads/", "cv", ['application/pdf']);

				$admin->setUrlCv($targetFile);

				$adminManager->update($admin);
			}

			if (isset($file['pictureFile']) && $file['pictureFile']['error'] != 4) {
				$targetFile = (new FileUploader)->upload($file['pictureFile'], "uploads/", "pdp", FileUploader::IMAGE_TYPE);

				$admin->setUrlPicture($targetFile);

				$adminManager->update($admin);
			}
		} catch (FileTooBigException $e) {
			throw new FormException($e->getMessage());
		} catch (FileServerException $e) {
			throw new FileServerException($e->getMessage());
		} catch (FileException $e) {
			throw new FormException(FormReturnMessage::ERROR_WHILE_UPLOADING_FILE_RETRY);
		}

		if (isset($catchPhrase, $pictureAlt)) {
			$admin->setCatchPhrase($catchPhrase);
			$admin->setAltPicture($pictureAlt);

			$adminManager->update($admin);

			return $admin;
		} else {
			throw new FormException(FormReturnMessage::MISSING_FIELD);
		}
	}

	/**
	 * @param array $data
	 * @param array $file
	 * @param Post|null $post
	 * 
	 * @return Post
	 */
	public function editPost(array $data, array $file, Post $post = null) : Post {
		extract($data);

		if (is_null($post)) {
			$post = new Post();
		}

		$post->setTitle($title);

		if (!$post->issetSlug()) {
			$slug = (new Slugify())->slugify($post->getTitle());

			$slugDuplicator = 0;

			while (!is_null((new PostManager)->findOneBy(['slug' => $slug]))) {
				$slugDuplicator++;

				$slug = (new Slugify())->slugify($post->getTitle(). " ". $slugDuplicator);
			}

			$post->setSlug($slug);
		}

		$post->setContent($content);
		$post->setAltCoverageImage($coverImageAlt);

		if (!isset($post->adminId)) {
			$post->setAdminId((new AdminManager)->findConnected()->getId());
		}

		if ($file['error'] != 4) {
			try {
				$targetFile = (new FileUploader)->upload($file, "uploads/post/", $post->getSlug(), FileUploader::IMAGE_TYPE);

				$post->setUrlCoverageImage($targetFile);
			} catch (FileTooBigException $e) {
				throw new FormException($e->getMessage());
			} catch (FileServerException $e) {
				throw new FileServerException($e->getMessage());
			} catch (FileException $e) {
				throw new FormException(FormReturnMessage::ERROR_WHILE_UPLOADING_FILE_RETRY);
			}
		}

		return $post;
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

	/**
	 * @param array $data
	 * @param Social|null $social
	 * 
	 * @return Social
	 */
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
