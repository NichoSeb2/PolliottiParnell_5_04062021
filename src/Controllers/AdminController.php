<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller {
	public function addPost() {
		$this->render("@admin/pages/postAdd.html.twig");
	}

	public function editPost() {
		$slug = $this->params['slug'];

		$this->render("@admin/pages/postEdit.html.twig", [
			'slug' => $slug
		]);
	}

	public function deletePost() {
		$slug = $this->params['slug'];

		$this->render("@admin/pages/postDelete.html.twig", [
			'slug' => $slug
		]);
	}
}