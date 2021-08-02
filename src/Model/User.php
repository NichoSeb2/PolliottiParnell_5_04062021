<?php
namespace App\Model;

use App\Core\Entity;
use TypeError;

class User extends Entity {
	private string $role;

	private string $firstName;

	private string $lastName;

	private string $email;

	private string $password;

	private bool $verified;

	private $verificationToken;

	/**
	 * @return string
	 */
	public function getRole(): string {
		if (!isset($this->role)) {
			$this->setRole();
		}

		return $this->role;
	}

	/**
	 * @param string $role
	 * 
	 * @return void
	 */
	public function setRole(string $role = null): void {
		if (is_null($role)) {
			$role = "user";
		}

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

	/**
	 * @return bool
	 */
	public function getVerified() : bool {
		return $this->verified;
	}

	/**
	 * @param bool $verified
	 * 
	 * @return void
	 */
	public function setVerified(bool $verified): void {
		$this->verified = $verified;
	}

	/**
	 * @return string
	 */
	public function getVerificationToken() : string {
		return $this->verificationToken;
	}

	/**
	 * @param string|null $verificationToken
	 * 
	 * @return void
	 */
	public function setVerificationToken($verificationToken): void {
		if (!is_null($verificationToken) && !is_string($verificationToken)) {
			throw new TypeError("verificationToken must be of the type string or null");
		}

		$this->verificationToken = $verificationToken;
	}
}
