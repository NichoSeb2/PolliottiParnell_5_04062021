<?php
namespace App\Service;

use App\Model\Admin;
use App\Managers\UserManager;
use App\Managers\AdminManager;
use App\Exceptions\FileException;
use App\Exceptions\FormException;
use App\Exceptions\FileServerException;
use App\Exceptions\FileTooBigException;

class EditAccountProcessHandler {
	/**
	 * Handle the edition of the firstName, lastName and email for an admin
	 * 
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
	 * Handle the edition of the password of an admin
	 * 
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
	 * Handle the edition of the admin specific information
	 * 
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
}
