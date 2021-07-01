<?php
namespace App\Model;

use App\Core\Entity;

class SocialNetwork extends Entity {
	private string $name;

	private string $icon;

	private string $url;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * 
	 * @return void
	 */
	public function setName(string $name): void {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getIcon(): string {
		return $this->icon;
	}

	/**
	 * @param string $icon
	 * 
	 * @return void
	 */
	public function setIcon(string $icon): void {
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}

	/**
	 * @param string $url
	 * 
	 * @return void
	 */
	public function setUrl(string $url): void {
		$this->url = $url;
	}
}
