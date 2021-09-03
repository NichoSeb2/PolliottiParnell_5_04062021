<?php
namespace App\Core;

use DateTime;

class Entity {
	private int $id;

	private DateTime $createdAt;

	private DateTime $updatedAt;

	public function __construct(array $data = []) {
		if (!empty($data)) {
			$this->hydrate($data);
		}
	}

	/**
	 * Hydrate the entity
	 * 
	 * @param array $data
	 * 
	 * @return void
	 */
	public function hydrate(array $data): void {
		foreach ($data as $key => $value) {
			// create setter name by converting snake case to camel case
			$method = "set". str_replace("_", "", ucwords($key, "_"));

			if (is_callable([$this, $method])) {
				$this->$method($value);
			}
		}
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @param int $id
	 * 
	 * @return void
	 */
	public function setId(int $id): void {
		$this->id = $id;
	}

	/**
	 * @return DateTime
	 */
	public function getCreatedAt(): DateTime {
		return $this->createdAt;
	}

	/**
	 * @param string $createdAt
	 * 
	 * @return void
	 */
	public function setCreatedAt(string $createdAt): void {
		$this->createdAt = new DateTime($createdAt);
	}

	/**
	 * @return DateTime
	 */
	public function getUpdatedAt(): DateTime {
		return $this->updatedAt;
	}

	/**
	 * @param string $updatedAt
	 * 
	 * @return void
	 */
	public function setUpdatedAt(string $updatedAt): void {
		$this->updatedAt = new DateTime($updatedAt);
	}
}
