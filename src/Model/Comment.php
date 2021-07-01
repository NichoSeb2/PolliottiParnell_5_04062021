<?php
namespace App\Model;

use App\Core\Entity;

class Comment extends Entity {
	private string $content;

	private bool $status;

	/**
	 * @return string
	 */
	public function getContent(): string {
		return $this->content;
	}

	/**
	 * @param string $content
	 * 
	 * @return void
	 */
	public function setContent(string $content): void {
		$this->content = $content;
	}

	/**
	 * @return bool
	 */
	public function getStatus(): bool {
		return $this->status;
	}

	/**
	 * @param bool $status
	 * 
	 * @return void
	 */
	public function setStatus(bool $status): void {
		$this->status = $status;
	}
}
