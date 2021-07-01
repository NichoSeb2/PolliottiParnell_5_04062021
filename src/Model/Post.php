<?php
namespace App\Model;

use App\Core\Entity;

class Post extends Entity {
	private string $slug;

	private string $title;

	private string $content;

	private string $urlCoverageImage;

	private string $altCoverageImage;

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
}
