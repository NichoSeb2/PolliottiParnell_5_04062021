<?php
namespace App\Service;

class PostDisplay {
	/**
	 * @param int $page
	 * @param int $minPage
	 * @param int $maxPage
	 * 
	 * @return int
	 */
	public function validatePage(int $page, int $minPage, int $maxPage): int {
		if ($page < $minPage) {
			$page = $minPage;
		}

		if ($page > $maxPage) {
			$page = $maxPage;
		}

		return $page;
	}

	/**
	 * @param array $post
	 * 
	 * @return array
	 */
	public function filterPost(array $post, int $firstPostToDisplay, int $lastPostToDisplay): array {
		$this->firstPostToDisplay = $firstPostToDisplay;
		$this->lastPostToDisplay = $lastPostToDisplay;

		$post = array_filter($post, function($index) {
			return $index >= $this->firstPostToDisplay && $index <= $this->lastPostToDisplay;
		}, ARRAY_FILTER_USE_KEY);

		return $post;
	}
}
