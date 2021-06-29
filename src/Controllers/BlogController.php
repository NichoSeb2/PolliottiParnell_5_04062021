<?php
namespace App\Controllers;

use App\Core\Controller;

class BlogController extends Controller {
	private $minPage = 1;
	private $nbPostPerPage = 3;

	private function validatePage($page, $minPage, $maxPage) {
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

		$this->render("@client/pages/post.html.twig", [
			'slug' => $slug, 
		]);
	}

	public function showBlog() {
		if (isset($this->params['page'])) {
			$page = (int) $this->params['page'];
		} else {
			$page = 0;
		}

		$rawPost = [[
			'slug' => 7, 
			'title' => "Exercitation non commodo elit ea est elit", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		],[
			'slug' => 6, 
			'title' => "Ea consectetur commodo dolore quis deserunt", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		],[
			'slug' => 5, 
			'title' => "Commodo proident nulla ut veniam exercitation", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		], [
			'slug' => 4, 
			'title' => "Irure laborum reprehenderit sint irure", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		], [
			'slug' => 3, 
			'title' => "Magna proident do deserunt tempor nostrud", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		], [
			'slug' => 2, 
			'title' => "Saepe nostrum ullam eveniet pariatur voluptates odit", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
		], [
			'slug' => 1, 
			'title' => "Laborum aute elit cillum commodo minim occaecat", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "18/06/2021", 
		]];

		$this->maxPage = (int) ceil(sizeof($rawPost) / $this->nbPostPerPage);

		$page = $this->validatePage($page, $this->minPage, $this->maxPage);

		$this->firstPostToDisplay = ($page - 1) * $this->nbPostPerPage;
		$this->lastPostToDisplay = ($page * $this->nbPostPerPage) - 1;

		$post = array_filter($rawPost, function($index) {
			return $index >= $this->firstPostToDisplay && $index <= $this->lastPostToDisplay;
		}, ARRAY_FILTER_USE_KEY);

		$this->render("@client/pages/blog.html.twig", [
			'post' => $post, 
			'firstPage' => $this->minPage, 
			'lastPage' => $this->maxPage, 
			'previousPage' => $this->validatePage($page - 1, $this->minPage, $this->maxPage), 
			'currentPage' => $page, 
			'nextPage' => $this->validatePage($page + 1, $this->minPage, $this->maxPage), 
		]);
	}
}