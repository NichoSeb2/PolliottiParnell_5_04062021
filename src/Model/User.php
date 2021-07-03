<?php
namespace App\Model;

use App\Core\Entity;

class User extends Entity {
	private string $role;

	private string $firstName;

	private string $lastName;

	private string $email;

	private string $password;

	/**
	 * @return string
	 */
	public function getRole(): string {
		return $this->role;
	}

	/**
	 * @param string $role
	 * 
	 * @return void
	 */
	public function setRole(string $role): void {
		$this->role = $role;
	}

	/**
	 * @return string
	 */
	public function getFirstName(): string {
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 * 
	 * @return void
	 */
	public function setFirstName(string $firstName): void {
		$this->firstName = $firstName;
	}

	/**
	 * @return string
	 */
	public function getLastName(): string {
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 * 
	 * @return void
	 */
	public function setLastName(string $lastName): void {
		$this->lastName = $lastName;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * @param string $email
	 * 
	 * @return void
	 */
	public function setEmail(string $email): void {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string {
		return $this->password;
	}

	/**
	 * @param string $password
	 * 
	 * @return void
	 */
	public function setPassword(string $password): void {
		$this->password = $password;
	}
}
