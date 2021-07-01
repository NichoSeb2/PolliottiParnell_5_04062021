<?php
namespace App\Model;

use App\Model\User;

class Admin extends User {
	private string $catchPhrase;

	private string $urlCV;

	private string $urlPicture;

	private string $altPicture;

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
	public function getUrlCV(): string {
		return $this->urlCV;
	}

	/**
	 * @param string $urlCV
	 * 
	 * @return void
	 */
	public function setUrlCV(string $urlCV): void {
		$this->urlCV = $urlCV;
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
