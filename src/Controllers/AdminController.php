<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller {
	public function addPost() {
		$this->render("@admin/pages/post_add.html.twig");
	}

	public function editPost() {
		$slug = $this->params['slug'];

		$this->render("@admin/pages/post_edit.html.twig", [
			'slug' => $slug
		]);
	}

	public function deletePost() {
		$slug = $this->params['slug'];

		$this->render("@admin/pages/post_delete.html.twig", [
			'slug' => $slug
		]);
	}
}