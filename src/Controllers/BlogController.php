<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Managers\PostManager;
use App\Managers\AdminManager;
use App\Managers\SocialManager;
use App\Managers\CommentManager;
use App\Controllers\ErrorController;

class BlogController extends Controller {
	private $minPage = 1;
	private $nbPostPerPage = 3;

	/**
	 * @param int $page
	 * @param int $minPage
	 * @param int $maxPage
	 * 
	 * @return int
	 */
	private function _validatePage(int $page, int $minPage, int $maxPage): int {
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
	private function _filterPost(array $post): array {
		$post = array_filter($post, function($index) {
			return $index >= $this->firstPostToDisplay && $index <= $this->lastPostToDisplay;
		}, ARRAY_FILTER_USE_KEY);

		return $post;
	}

	/**
	 * @return void
	 */
	public function showPost(): void {
		$slug = $this->params['slug'];

		$postManager = new PostManager();

		$post = $postManager->findOneBy([
			'slug' => $slug
		]);

		if (is_null($post)) {
			$controller = new ErrorController("show404");

			$controller->execute();

			return;
		}

		$commentManager = new CommentManager();

		$comments = $commentManager->findBy([
			'post_id' => $post->getId(), 
			'status' => 1, 
		], [
			'created_at' => "DESC", 
		]);

		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/post.html.twig", [
			'connected' => !empty($_SESSION['id']) && is_numeric($_SESSION['id']), 
			'admin' => $admin, 
			'post' => $post, 
			'comments' => $comments, 
			'socials' => $socials, 
		]);
	}

	/**
	 * @return void
	 */
	public function showBlog(): void {
		$page = 0;

		if (isset($this->params['page'])) {
			$page = (int) $this->params['page'];
		}

		$postManager = new PostManager();

		$post = $postManager->findBy([], [
			'created_at' => "DESC", 
		]);

		$maxPage = (int) ceil(sizeof($post) / $this->nbPostPerPage);

		$page = $this->_validatePage($page, $this->minPage, $maxPage);

		$this->firstPostToDisplay = ($page - 1) * $this->nbPostPerPage;
		$this->lastPostToDisplay = ($page * $this->nbPostPerPage) - 1;

		$post = $this->_filterPost($post);

		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/blog.html.twig", [
			'connected' => !empty($_SESSION['id']) && is_numeric($_SESSION['id']), 
			'admin' => $admin, 
			'post' => $post, 
			'firstPage' => $this->minPage, 
			'lastPage' => $maxPage, 
			'previousPage' => $this->_validatePage($page - 1, $this->minPage, $maxPage), 
			'currentPage' => $page, 
			'nextPage' => $this->_validatePage($page + 1, $this->minPage, $maxPage), 
			'socials' => $socials, 
		]);
	}
}