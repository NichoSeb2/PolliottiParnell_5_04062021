<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller {
	public function addPost() {
		$this->render("@admin/pages/post.html.twig", [
			'action' => "add"
		]);
	}

	public function editPost() {
		$slug = $this->params['slug'];

		$this->render("@admin/pages/post.html.twig", [
			'action' => "edit",
			'slug' => $slug
		]);
	}

	public function deletePost() {
		$slug = $this->params['slug'];

		$this->render("@admin/pages/post.html.twig", [
			'action' => "delete",
			'slug' => $slug
		]);
	}
}