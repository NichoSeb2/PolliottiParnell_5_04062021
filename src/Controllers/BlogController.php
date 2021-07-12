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

	private function _validatePage($page, $minPage, $maxPage) {
		if ($page < $minPage) {
			$page = $minPage;
		}

		if ($page > $maxPage) {
			$page = $maxPage;
		}

		return $page;
	}

	public function showPost() {
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
			'post' => $post, 
			'comments' => $comments, 
			'socials' => $socials, 
			'catchPhrase' => $admin->getCatchPhrase(), 
		]);
	}

	public function showBlog() {
		if (isset($this->params['page'])) {
			$page = (int) $this->params['page'];
		} else {
			$page = 0;
		}

		$postManager = new PostManager();

		$rawPost = $postManager->findBy([], [
			'created_at' => "DESC", 
		]);

		$this->maxPage = (int) ceil(sizeof($rawPost) / $this->nbPostPerPage);

		$page = $this->_validatePage($page, $this->minPage, $this->maxPage);

		$this->firstPostToDisplay = ($page - 1) * $this->nbPostPerPage;
		$this->lastPostToDisplay = ($page * $this->nbPostPerPage) - 1;

		$post = array_filter($rawPost, function($index) {
			return $index >= $this->firstPostToDisplay && $index <= $this->lastPostToDisplay;
		}, ARRAY_FILTER_USE_KEY);

		$adminManager = new AdminManager();

		$admin = $adminManager->findById(1);

		$socialManager = new SocialManager();

		$socials = $socialManager->findAll();

		$this->render("@client/pages/blog.html.twig", [
			'post' => $post, 
			'firstPage' => $this->minPage, 
			'lastPage' => $this->maxPage, 
			'previousPage' => $this->_validatePage($page - 1, $this->minPage, $this->maxPage), 
			'currentPage' => $page, 
			'nextPage' => $this->_validatePage($page + 1, $this->minPage, $this->maxPage), 
			'socials' => $socials, 
			'catchPhrase' => $admin->getCatchPhrase(), 
		]);
	}
}