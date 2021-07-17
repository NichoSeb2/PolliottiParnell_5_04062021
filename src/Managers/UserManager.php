<?php
namespace App\Managers;

use App\Core\Manager;
use App\Model\User;

class UserManager extends Manager {
	/**
	 * @return User|null
	 */
	public function findConnected() {
		if (isset($_SESSION['id']) && is_numeric($_SESSION['id'])) {
			return $this->findOneBy([
				'id' => $_SESSION['id'], 
			]);
		} else {
			return null;
		}
	}
}
