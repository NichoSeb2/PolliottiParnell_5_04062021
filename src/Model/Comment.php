<?php
namespace App\Model;

use App\Model\Post;
use App\Model\User;
use App\Core\Entity;
use App\Managers\PostManager;
use App\Managers\UserManager;

class Comment extends Entity {
	private int $userId;

	private User $user;

	private int $postId;

	private Post $post;

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

		$userManager = new UserManager();

		$this->setUser($userManager->findOneBy([
			'id' => $userId, 
		]));
	}

	/**
	 * @return User
	 */
	public function getUser(): User {
		return $this->user;
	}

	/**
	 * @param User $user
	 * 
	 * @return void
	 */
	public function setUser(User $user): void {
		$this->user = $user;
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

		$postManager = new PostManager();

		$this->setPost($postManager->findOneBy([
			'id' => $postId, 
		]));
	}

	/**
	 * @return Post
	 */
	public function getPost(): Post {
		return $this->post;
	}

	/**
	 * @param Post $post
	 * 
	 * @return void
	 */
	public function setPost(Post $post): void {
		$this->post = $post;
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
