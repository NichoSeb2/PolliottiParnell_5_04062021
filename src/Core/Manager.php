<?php
namespace App\Core;

use PDO;
use DateTime;
use App\Core\Entity;
use App\Exceptions\SQLException;
use ReflectionClass;

class Manager {
	protected string $dateFormat = "Y-m-d H:i:s";

	protected PDO $pdo;

	protected string $tableName;

	protected string $entity;

	/**
	 * @param string $sql
	 * @param array $where
	 * @param array $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return string
	 */
	protected function _appendIfCorrect(string $sql, array $where = [], array $orderBy = [], int $limit = null, int $offset = null): string {
		if (!empty($where)) {
			$sql .= " ". $this->_computeWhere($where);
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

		return $sql;
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
	 * @return string
	 */
	protected function _computeWhere(array $where): string {
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
	 * 
	 * @return array
	 */
	protected function _extractFromEntity(Entity $entity, array $excludeGetter = ['getId', 'getCreatedAt', 'getUpdatedAt']): array {
		$result = [];

		foreach (get_class_methods($entity) as $function) {
			if (strpos($function, "get") !== false && !in_array($function, $excludeGetter)) {
				// Convert PascalCase to snake_case
				$key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace("get", "", $function)));

				$result[$key] = $entity->$function();
			}
		}

		return $result;
	}

	/**
	 * @param string $value
	 * 
	 * @return string
	 */
	protected function _escapeString(string $value): string {
		return "\"". $value. "\"";
	}

	/**
	 * @param bool $value
	 * 
	 * @return int
	 */
	protected function _escapeBool(bool $value): int {
		return $value ? 1 : 0;
	}

	/**
	 * @param mixed $value
	 * 
	 * @return mixed
	 */
	protected function _escapeValue($value) {
		if (is_string($value)) {
			$value = $this->_escapeString($value);
		} else if (is_bool($value)) {
			$value = $this->_escapeBool($value);
		} else if (is_null($value)) {
			$value = "NULL";
		}

		return $value;
	}

	/**
	 * @param array $values
	 * 
	 * @return array
	 */
	protected function _escapeArray(array $values): array {
		foreach ($values as $key => $value) {
			$values[$key] = $this->_escapeValue($value);
		}

		return $values;
	}

	/**
	 * @param array $data
	 * 
	 * @return array
	 */
	protected function _mergeKeyValue(array $data): array {
		$result = [];

		foreach ($data as $key => $value) {
			// this conversion need to be in first and separated so the date formatted can be escaped as a string
			if ($value instanceof DateTime) {
				$value = $value->format($this->dateFormat);
			}

			$value = $this->_escapeValue($value);

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

		$sql = $this->_appendIfCorrect($sql, $where, $orderBy, $limit, $offset);

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

		// set updatedAt to the current time
		$entity->setUpdatedAt(date($this->dateFormat));

		// keys and values to update
		$data = $this->_mergeKeyValue($this->_extractFromEntity($entity, ['getId', 'getCreatedAt']));

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

	/**
	 * @return int
	 */
	public function count(): int {
		$sql = "SELECT COUNT(*) FROM ". $this->tableName;

		$request = $this->pdo->query($sql);
		$result = $request->fetch();

		return (int) $result[0];
	}
}
