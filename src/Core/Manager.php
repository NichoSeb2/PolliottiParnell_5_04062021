<?php
namespace App\Core;

use PDO;
use App\Core\Entity;
use ReflectionClass;

class Manager {
	private PDO $pdo;

	private string $tableName;

	private string $entity;

	/**
	 * @param array $where
	 * 
	 * @return string
	 */
	private function _computeWhere(array $where): string {
		$groups = [];

		foreach ($where as $key => $value) {
			$groups[] = $key. " = \"". $value. "\"";
		}

		return "WHERE ". implode(" AND ", $groups);
	}

	/**
	 * @param array $orderBy
	 * 
	 * @return string
	 */
	private function _computeOrderBy(array $orderBy): string {
		$groups = [];

		foreach ($orderBy as $key => $value) {
			$groups[] = $key. " ". $value;
		}

		return "ORDER BY ". implode(", ", $groups);
	}

	/**
	 * @param Entity $entity
	 * 
	 * @return array
	 */
	private function _extractFromEntity(Entity $entity): array {
		$result = [];

		foreach (get_class_methods($entity) as $function) {
			if (strpos($function, "get") !== false) {
				if (!in_array($function, ['getId', 'getCreatedAt', 'getUpdatedAt'])) {
					// Convert PascalCase to snake_case
					$key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace("get", "", $function)));

					$result[$key] = $entity->$function();
				}
			}
		}

		return $result;
	}

	/**
	 * @param string $value
	 * 
	 * @return string
	 */
	private function _escapeString(string $value): string {
		return "\"". $value. "\"";
	}

	/**
	 * @param array $values
	 * 
	 * @return array
	 */
	private function _escapeArray(array $values): array {
		foreach ($values as $key => $value) {
			if(is_string($value)) {
				$values[$key] = $this->_escapeString($value);
			}
		}

		return $values;
	}

	/**
	 * @param array $data
	 * 
	 * @return array
	 */
	private function _mergeKeyValue(array $data): array {
		$result = [];

		foreach ($data as $key => $value) {
			if(is_string($value)) {
				$value = $this->_escapeString($value);
			}

			$result[] = $key. " = ". $value;
		}

		return $result;
	}

	public function __construct() {
		$this->tableName = $this->getTableName();

		$this->entity = "\\App\\Model\\". ucfirst($this->tableName);

		$this->pdo = (new PDOFactory())->getMYSQLConnection();
	}

	/**
	 * @param array $results
	 * 
	 * @return array
	 */
	public function convertEntities(array $results) : array {
		$entities = [];

		foreach ($results as $result) {
			$entities[] = new $this->entity($result);
		}

		return $entities;
	}

	/**
	 * @return string
	 */
	public function getTableName(): string {
		$manager = (new ReflectionClass($this))->getShortName();

		return strtolower(str_replace("Manager", "", $manager));
	}

	/**
	 * @return array
	 */
	public function findAll(): array {
		$sql = "SELECT * FROM ". $this->tableName;

		$request = $this->pdo->query($sql);
		$results = $request->fetchAll();

		return $this->convertEntities($results);
	}

	/**
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return array
	 */
	public function findBy(array $where = [], array $orderBy = [], int $limit = null, int $offset = null): array {
		$sql = "SELECT * FROM ". $this->tableName;

		if (!empty($where)) {
			$sql .= " ". $this->_computeWhere($where);
		}

		if (!empty($orderBy)) {
			$sql .= " ". $this->_computeOrderBy($orderBy);
		}

		if (!is_null($limit)) {
			$sql .= " LIMIT ". $limit;

			if (!is_null($offset)) {
				$sql .= " OFFSET ". $offset;
			}
		}

		$request = $this->pdo->query($sql);
		$results = $request->fetchAll();

		return $this->convertEntities($results);
	}

	/**
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return Entity|null
	 */
	public function findOneBy(array $where = [], array $orderBy = [], int $limit = null, int $offset = null) {
		// Call findBy and return[0]

		$results = $this->findBy($where, $orderBy, $limit, $offset);

		if (sizeof($results) == 0) {
			return null;
		} else {
			return $results[0];
		}
	}

	/**
	 * @param Entity $entity
	 * 
	 * @return void
	 */
	public function create(Entity $entity): void {
		$sql = "INSERT INTO ". $this->tableName;

		// keys, values pair to insert
		$data = $this->_extractFromEntity($entity);

		// key and value split
		$keys = array_keys($data);
		$values = $this->_escapeArray(array_values($data));

		$sql .= " (". implode(", ", $keys). ") VALUES (". implode(", ", $values). ")";

		$this->pdo->query($sql);
	}

	/**
	 * @param Entity $entity
	 * 
	 * @return void
	 */
	public function update(Entity $entity): void {
		$sql = "UPDATE ". $this->tableName. " SET ";

		// keys and values to update
		$data = $this->_mergeKeyValue($this->_extractFromEntity($entity));

		$sql .= implode(", ", $data). " WHERE id = ". $entity->getId();

		$this->pdo->query($sql);
	}

	/**
	 * @param Entity $entity
	 * 
	 * @return void
	 */
	public function delete(Entity $entity) {
		$sql = "DELETE FROM ". $this->tableName. " WHERE id = ". $entity->getId();

		$this->pdo->query($sql);
	}
}
