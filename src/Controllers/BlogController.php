<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Service\PostDisplay;
use App\Managers\PostManager;
use App\Exceptions\RequestedEntityNotFound;

class BlogController extends Controller {
	private $minPage = 1;
	private $nbPostPerPage = 3;

	/**
	 * @return void
	 */
	public function showPost(): void {
		$slug = $this->params['slug'];

		$postManager = new PostManager();

		$post = $postManager->findOneByWithComment([
			'p.slug' => $slug, 
		]);

		if (is_null($post)) {
			throw new RequestedEntityNotFound("Post not found");
		}

		$this->render("@client/pages/post.html.twig", [
			'post' => $post, 
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

		$maxPage = (int) ceil((new PostManager)->count() / $this->nbPostPerPage);

		$postDisplay = new PostDisplay();

		$page = $postDisplay->validatePage($page, $this->minPage, $maxPage);

		$post = (new PostManager)->findBy([], [
			'created_at' => "DESC", 
		], $this->nbPostPerPage, ($page - 1) * $this->nbPostPerPage);

		$this->render("@client/pages/blog.html.twig", [
			'post' => $post, 
			'firstPage' => $this->minPage, 
			'lastPage' => $maxPage, 
			'previousPage' => $postDisplay->validatePage($page - 1, $this->minPage, $maxPage), 
			'currentPage' => $page, 
			'nextPage' => $postDisplay->validatePage($page + 1, $this->minPage, $maxPage), 
		]);
	}
}