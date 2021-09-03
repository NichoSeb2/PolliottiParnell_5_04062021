<?php
namespace App\Managers;

use App\Core\Entity;
use App\Core\Manager;

class AdminManager extends Manager {
	public function __construct($excludeGetterForInsert = ['getId', 'getCreatedAt', 'getUpdatedAt'], $excludeGetterForUpdate = ['getId', 'getCreatedAt', 'getRole', 'getFirstName', 'getLastName', 'getEmail', 'getPassword', 'getVerified', 'getVerificationToken', 'getForgotPasswordToken', 'getUpdatedAt']) {
		parent::__construct();

		$this->excludeGetterForInsert = $excludeGetterForInsert;

		$this->excludeGetterForUpdate = $excludeGetterForUpdate;
	}

	/**
	 * Return an admin based on its user-id
	 * 
	 * @param int $userId
	 * 
	 * @return Entity|null
	 */
	public function findById(int $userId) {
		$sql = "SELECT * FROM admin AS a JOIN user AS u ON a.user_id = u.id WHERE a.user_id = :id";

		$request = $this->pdo->prepare($sql);
		$request->execute([
			'id' => $userId, 
		]);
		$results = $request->fetchAll();

		$entities = $this->convertEntities($results);

		if (empty($entities)) {
			return null;
		} else {
			return $entities[0];
		}
	}

	/**
	 * Return the connected admin
	 * 
	 * @return Admin|null
	 */
	public function findConnected() {
		if (isset($_SESSION['id']) && is_numeric($_SESSION['id'])) {
			return $this->findById($_SESSION['id']);
		} else {
			return null;
		}
	}
}
