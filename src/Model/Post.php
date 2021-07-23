<?php
namespace App\Model;

use App\Core\Entity;
use App\Managers\AdminManager;

class Post extends Entity {
	private int $adminId;

	private Admin $admin;

	private string $slug;

	private string $title;

	private string $content;

	private string $urlCoverageImage;

	private string $altCoverageImage;

	private array $comments;

	/**
	 * @return int
	 */
	public function getAdminId(): int {
		return $this->adminId;
	}

	/**
	 * @param int $adminId
	 * 
	 * @return void
	 */
	public function setAdminId(int $adminId): void {
		$this->adminId = $adminId;

		$adminManager = new AdminManager();

		$this->setAdmin($adminManager->findById($adminId));
	}

	/**
	 * @return Admin
	 */
	public function getAdmin(): Admin {
		return $this->admin;
	}

	/**
	 * @param Admin $admin
	 * 
	 * @return void
	 */
	public function setAdmin(Admin $admin): void {
		$this->admin = $admin;
	}

	/**
	 * @return string
	 */
	public function getSlug(): string {
		return $this->slug;
	}

	/**
	 * @param string $slug
	 * 
	 * @return void
	 */
	public function setSlug(string $slug): void {
		$this->slug = $slug;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * @param string $title
	 * 
	 * @return void
	 */
	public function setTitle(string $title): void {
		$this->title = $title;
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
	 * @return string
	 */
	public function getUrlCoverageImage(): string {
		return $this->urlCoverageImage;
	}

	/**
	 * @param string $urlCoverageImage
	 * 
	 * @return void
	 */
	public function setUrlCoverageImage(string $urlCoverageImage): void {
		$this->urlCoverageImage = $urlCoverageImage;
	}

	/**
	 * @return string
	 */
	public function getAltCoverageImage(): string {
		return $this->altCoverageImage;
	}

	/**
	 * @param string $altCoverageImage
	 * 
	 * @return void
	 */
	public function setAltCoverageImage(string $altCoverageImage): void {
		$this->altCoverageImage = $altCoverageImage;
	}

	/**
	 * @return array
	 */
	public function getComments(): array {
		return $this->comments;
	}

	/**
	 * @param array $comments
	 * 
	 * @return void
	 */
	public function setComments(array $comments): void {
		$this->comments = $comments;
	}

	/**
	 * @param Comment $comment
	 * 
	 * @return void
	 */
	public function addComment(Comment $comment): void {
		$this->comments[] = $comment;
	}
}
