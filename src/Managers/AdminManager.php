<?php
namespace App\Managers;

use App\Core\Entity;
use App\Core\Manager;

class AdminManager extends Manager {
	/**
	 * @param int $adminId
	 * 
	 * @return Entity|null
	 */
	public function findById(int $adminId) {
		$sql = "SELECT * FROM admin AS a JOIN user AS u ON a.user_id = u.id WHERE a.id = ". $adminId;

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
