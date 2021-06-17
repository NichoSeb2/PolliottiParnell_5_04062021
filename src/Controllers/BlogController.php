<?php
namespace App\Controllers;

use App\Core\Controller;

class BlogController extends Controller {
	public function showPost() {
		$slug = $this->params['slug'];

		$this->render("@client/pages/post.html.twig", [
			'slug' => $slug
		]);
	}

	public function showBlog() {
		$this->render("@client/pages/blog.html.twig");
	}
}