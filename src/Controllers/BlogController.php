<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\PostDisplay;
use App\Managers\PostManager;
use App\Managers\CommentManager;
use App\Controllers\ErrorController;

class BlogController extends Controller {
	private $minPage = 1;
	private $nbPostPerPage = 3;

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

		$this->render("@client/pages/post.html.twig", [
			'post' => $post, 
			'comments' => $comments, 
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

		$page = (new PostDisplay)->validatePage($page, $this->minPage, $maxPage);

		$post = (new PostDisplay)->filterPost($post, ($page - 1) * $this->nbPostPerPage, ($page * $this->nbPostPerPage) - 1);

		$this->render("@client/pages/blog.html.twig", [
			'post' => $post, 
			'firstPage' => $this->minPage, 
			'lastPage' => $maxPage, 
			'previousPage' => (new PostDisplay)->validatePage($page - 1, $this->minPage, $maxPage), 
			'currentPage' => $page, 
			'nextPage' => (new PostDisplay)->validatePage($page + 1, $this->minPage, $maxPage), 
		]);
	}
}