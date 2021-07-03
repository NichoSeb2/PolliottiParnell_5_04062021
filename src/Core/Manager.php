<?php
namespace App\Core;

use PDO;
use App\Core\Entity;
use ReflectionClass;

class Manager {
	private PDO $pdo;

	private string $tableName;

	private Entity $entity;

	public function __construct() {
		$this->tableName = $this->getTableName();

		$this->entity = "\\App\\Model\\". ucfirst($this->tableName);

		$this->pdo = (new PDOFactory())->getMYSQLConnection();
	}

	/**
	 * @return string
	 */
	public function getTableName(): string {
		$manager = (new ReflectionClass($this))->getShortName();

		return strtolower(str_replace("Manager", "", $manager));
	}

	public function findAll() {

	}

	public function findBy($where, $orderBy, $limit, $offset) {

	}

	public function findOneBy($where, $orderBy, $limit, $offset) {
		// Call findBy and return[0]

		return $this->findBy($where, $orderBy, $limit, $offset)[0];
	}

	public function create($entity) {

	}

	public function update($entity) {

	}

	public function delete($entity) {

	}
}
