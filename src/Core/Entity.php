<?php
namespace App\Core;

use DateTime;

class Entity {
	private int $id;

	private DateTime $createdAt;

	private DateTime $updatedAt;

	/**
	 * @return int
	 */
	protected function getId(): int {
		return $this->id;
	}

	/**
	 * @param int $id
	 * 
	 * @return void
	 */
	protected function setId(int $id): void {
		$this->id = $id;
	}

	/**
	 * @return DateTime
	 */
	protected function getCreatedAt(): DateTime {
		return $this->createdAt;
	}

	/**
	 * @param DateTime $createdAt
	 * 
	 * @return void
	 */
	protected function setCreatedAt(DateTime $createdAt): void {
		$this->createdAt = $createdAt;
	}

	/**
	 * @return DateTime
	 */
	protected function getUpdatedAt(): DateTime {
		return $this->updatedAt;
	}

	/**
	 * @param DateTime $updatedAt
	 * 
	 * @return void
	 */
	protected function setUpdatedAt(DateTime $updatedAt): void {
		$this->updatedAt = $updatedAt;
	}
}
