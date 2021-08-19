<?php
namespace App\Service;

use App\Model\User;
use App\Model\Admin;
use App\Managers\UserManager;
use App\Managers\AdminManager;
use App\Exceptions\AccessDeniedException;

class AdminLogged {
	/**
	 * @param User $user
	 * 
	 * @return bool
	 */
	public function isAdmin(User $user): bool {
		return !is_null((new AdminManager)->findOneBy([
			'user_id' => $user->getId(), 
		]));
	}

	/**
	 * @param mixed $function
	 * 
	 * @return void
	 */
	public function adminLogged($function): void {
		if (is_callable($function)) {
			$userManager = new UserManager();
			$adminManager = new AdminManager();

			$user = $userManager->findConnected();

			if (!is_null($user) && $this->isAdmin($user)) {
				$admin = $adminManager->findById($user->getId());

				$function($admin);
			} else {
				throw new AccessDeniedException();
			}
		}
	}
}
