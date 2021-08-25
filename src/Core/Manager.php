<?php
namespace App\Core;

use PDO;
use DateTime;
use App\Core\Entity;
use ReflectionClass;
use App\Exceptions\SQLException;

class Manager {
	protected string $dateFormat = "Y-m-d H:i:s";

	protected PDO $pdo;

	protected string $tableName;

	protected string $entity;

	protected array $excludeGetterForInsert = ['getId', 'getCreatedAt', 'getUpdatedAt'];

	protected array $excludeGetterForUpdate = ['getId', 'getCreatedAt'];

	/**
	 * @param string $sql
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return array
	 */
	protected function _appendIfCorrect(string $sql, array $where = [], array $orderBy = [], int $limit = null, int $offset = null): array {
		$data = [];

		if (!empty($where)) {
			$result = $this->_computeWhere($where);

			$sql .= " ". $result[0];
			$data = array_merge($data, $result[1]);
		}

		if (!empty($orderBy)) {
			$sql .= " ". $this->_computeOrderBy($orderBy);
		}

		if (!is_null($limit) && is_numeric($limit)) {
			$sql .= " LIMIT ". ((int) $limit);

			if (!is_null($offset) && is_numeric($offset)) {
				$sql .= " OFFSET ". ((int) $offset);
			}
		}

		return [$sql, $data];
	}

	/**
	 * @param array $field
	 * @param string|null $table
	 * 
	 * @return string
	 */
	protected function _computeField(array $field, string $table = null): string {
		$groups = [];

		foreach ($field as $key => $value) {
			$groups[] = (!is_null($table) ? $table. "." : ""). (is_string($key) ? $key. " AS " : ""). $value;
		}

		return implode(", ", $groups);
	}

	/**
	 * @param array $where
	 * 
	 * @return array
	 */
	protected function _computeWhere(array $where): array {
		$groups = [];
		$values = [];

		foreach ($where as $key => $value) {
			$placeholderKey = explode(".", $key);
			$placeholderKey = end($placeholderKey);

			$groups[] = $key. " = :". $placeholderKey;
			$values[$placeholderKey] = $value;
		}

		return ["WHERE ". implode(" AND ", $groups), $values];
	}

	/**
	 * @param array $orderBy
	 * 
	 * @return string
	 */
	protected function _computeOrderBy(array $orderBy): string {
		$groups = [];

		foreach ($orderBy as $key => $value) {
			$value = strtoupper($value);

			if (!in_array($value, ['ASC', 'DESC'])) {
				throw new SQLException("\"$value\" isn't a valid order value");
			}

			$groups[] = $key. " ". $value;
		}

		return "ORDER BY ". implode(", ", $groups);
	}

	/**
	 * @param Entity $entity
	 * @param array $excludeGetter
	 * 
	 * @return array
	 */
	protected function _extractFromEntity(Entity $entity, array $excludeGetter = ['getId', 'getCreatedAt', 'getUpdatedAt']): array {
		$result = [];

		foreach (get_class_methods($entity) as $function) {
			if (strpos($function, "get") !== false && !in_array($function, $excludeGetter)) {
				// Convert PascalCase to snake_case
				$key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace("get", "", $function)));

				$value = $entity->$function();

				// discard all entity
				if ($value instanceof Entity) {
					continue;
				}

				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * @param array $data
	 * 
	 * @return array
	 */
	protected function _computeKeyForPreparedSet(array $data): array {
		$result = [];

		foreach ($data as $key => $value) {
			// discard all entity
			if ($value instanceof Entity) {
				continue;
			}

			$result[] = $key. " = :". $key;
		}

		return $result;
	}

	/**
	 * @param array $data
	 * 
	 * @return array
	 */
	protected function _filterValue(array $data): array {
		return array_map(function($value) {
			if ($value instanceof DateTime) {
				$value = $value->format($this->dateFormat);
			}

			return $value;
		}, array_filter($data, function($value) {
			return !($value instanceof Entity);
		}));
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

		$request = $this->pdo->prepare($sql);
		$request->execute();
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

		$result = $this->_appendIfCorrect($sql, $where, $orderBy, $limit, $offset);

		$sql = $result[0];
		$data = $result[1];

		$request = $this->pdo->prepare($sql);
		$request->execute($data);
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

		if (empty($results)) {
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
		$data = $this->_extractFromEntity($entity, $this->excludeGetterForInsert);

		// key and value split
		$keys = array_keys($data);
		$values = array_values($data);

		$sql .= " (". implode(", ", $keys). ") VALUES (". implode(", ", array_map(function($key) {
			return ":". $key;
		}, $keys)). ")";

		$data = array_combine($keys, $values);

		$request = $this->pdo->prepare($sql);
		$request->execute($data);
	}

	/**
	 * @param Entity $entity
	 * 
	 * @return void
	 */
	public function update(Entity $entity): void {
		$sql = "UPDATE ". $this->tableName. " SET ";

		// set updatedAt to the current time
		$entity->setUpdatedAt(date($this->dateFormat));

		// keys and values to update
		$data = $this->_computeKeyForPreparedSet($this->_extractFromEntity($entity, $this->excludeGetterForUpdate));

		$sql .= implode(", ", $data). " WHERE id = :id";

		$data = $this->_filterValue($this->_extractFromEntity($entity, $this->excludeGetterForUpdate));
		$data['id'] = $entity->getId();

		$request = $this->pdo->prepare($sql);
		$request->execute($data);
	}

	/**
	 * @param Entity $entity
	 * 
	 * @return void
	 */
	public function delete(Entity $entity): void {
		$sql = "DELETE FROM ". $this->tableName. " WHERE id = :id";

		$request = $this->pdo->prepare($sql);
		$request->execute([
			'id' => $entity->getId(), 
		]);
	}

	/**
	 * @return int
	 */
	public function count(): int {
		$sql = "SELECT COUNT(*) FROM ". $this->tableName;

		$request = $this->pdo->prepare($sql);
		$request->execute();
		$result = $request->fetch();

		return (int) $result[0];
	}
}
