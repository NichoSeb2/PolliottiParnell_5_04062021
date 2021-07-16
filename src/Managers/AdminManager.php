<?php
namespace App\Managers;

use App\Model\User;
use App\Core\Entity;
use App\Core\Manager;
use App\Controllers\ErrorController;

class AdminManager extends Manager {
	/**
	 * @param int $userId
	 * 
	 * @return Entity|null
	 */
	public function findById(int $userId) {
		$sql = "SELECT * FROM admin AS a JOIN user AS u ON a.user_id = u.id WHERE a.user_id = ". $userId;

		$request = $this->pdo->query($sql);
		$results = $request->fetchAll();

		$entities = $this->convertEntities($results);

		if (empty($entities)) {
			return null;
		} else {
			return $entities[0];
		}
	}

	/**
	 * @param User $user
	 * 
	 * @return bool
	 */
	public function isAdmin(User $user): bool {
		return !is_null($this->findOneBy([
			'user_id' => $user->getId(), 
		]));
	}

	/**
	 * @param callable $function
	 * 
	 * @return void
	 */
	public function adminLogged($function): void {
		if (is_callable($function)) {
			$userManager = new UserManager();
			$adminManager = new AdminManager();

			$user = $userManager->findConnected();

			if (!is_null($user) && $adminManager->isAdmin($user)) {
				$admin = $adminManager->findById($user->getId());

				$function($admin);
			} else {
				$controller = new ErrorController("show403");

				$controller->execute();
			}
		}
	}
}
