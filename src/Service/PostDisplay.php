<?php
namespace App\Service;

class PostDisplay {
	/**
	 * Verify and correct if needed a page number
	 * 
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
}
