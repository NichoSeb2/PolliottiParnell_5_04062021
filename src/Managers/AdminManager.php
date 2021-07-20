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
}
