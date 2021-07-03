<?php
namespace App\Model;

use App\Core\Entity;

class Comment extends Entity {
	private int $userId;

	private int $postId;

	private string $content;

	private bool $status;

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
	 * @return int
	 */
	public function getPostId(): int {
		return $this->postId;
	}

	/**
	 * @param int $postId
	 * 
	 * @return void
	 */
	public function setPostId(int $postId): void {
		$this->postId = $postId;
	}

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
