<?php
namespace App\Managers;

use App\Core\Manager;
use App\Model\User;

class UserManager extends Manager {
	public function __construct($excludeGetterForInsert = ['getId', 'getCreatedAt', 'getUpdatedAt'], $excludeGetterForUpdate = ['getId', 'getCreatedAt']) {
		parent::__construct();

		$this->excludeGetterForInsert = $excludeGetterForInsert;

		$this->excludeGetterForUpdate = $excludeGetterForUpdate;
	}

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
