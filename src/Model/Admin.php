<?php
namespace App\Model;

use App\Model\User;

class Admin extends User {
	private int $userId;

	private string $catchPhrase;

	private string $urlCv;

	private string $urlPicture;

	private string $altPicture;

	/**
	 * @return int
	 */
	public function getUserId(): int {
		return $this->userId;
	}

	/**
	 * @param int $userId
	 * 
	 * @return void
	 */
	public function setUserId(int $userId): void {
		$this->userId = $userId;
	}

	/**
	 * @return string
	 */
	public function getCatchPhrase(): string {
		return $this->catchPhrase;
	}

	/**
	 * @param string $catchPhrase
	 * 
	 * @return void
	 */
	public function setCatchPhrase(string $catchPhrase): void {
		$this->catchPhrase = $catchPhrase;
	}

	/**
	 * @return string
	 */
	public function getUrlCv(): string {
		return $this->urlCv;
	}

	/**
	 * @param string $urlCv
	 * 
	 * @return void
	 */
	public function setUrlCv(string $urlCv): void {
		$this->urlCv = $urlCv;
	}

	/**
	 * @return string
	 */
	public function getUrlPicture(): string {
		return $this->urlPicture;
	}

	/**
	 * @param string $urlPicture
	 * 
	 * @return void
	 */
	public function setUrlPicture(string $urlPicture): void {
		$this->urlPicture = $urlPicture;
	}

	/**
	 * @return string
	 */
	public function getAltPicture(): string {
		return $this->altPicture;
	}

	/**
	 * @param string $altPicture
	 * 
	 * @return void
	 */
	public function setAltPicture(string $altPicture): void {
		$this->altPicture = $altPicture;
	}
}
