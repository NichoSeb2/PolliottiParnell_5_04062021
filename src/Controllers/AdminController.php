<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller {
	public function showProfile() {
		$userDate = [
			'catchPhrase' => "Lorem elit !", 
			'pictureAlt' => "Est ut proident est nulla", 
		];

		$this->render("@admin/pages/profile.html.twig", [
			'active' => "profile", 
			'userData' => $userDate, 
		]);
	}

	public function showPost() {
		$post = [[
			'slug' => 4, 
			'title' => "In sint dolore quis sint aliqua nostrud quis", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "24/06/2021", 
			'updatedAt' => "25/06/2021", 
		], [
			'slug' => 3, 
			'title' => "Mollit ad laboris occaecat veniam laborum", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "23/06/2021", 
			'updatedAt' => "25/06/2021", 
		], [
			'slug' => 2, 
			'title' => "Saepe nostrum ullam eveniet pariatur voluptates odit", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "22/06/2021", 
			'updatedAt' => "25/06/2021", 
		], [
			'slug' => 1, 
			'title' => "Laborum aute elit cillum commodo minim occaecat", 
			'author' => "Parnell Polliotti", 
			'createdAt' => "18/06/2021", 
			'updatedAt' => "18/06/2021", 
		]];

		$this->render("@admin/pages/post.html.twig", [
			'active' => "showPost", 
			'post' => $post, 
		]);
	}

	public function addPost() {
		$this->render("@admin/pages/post_add.html.twig", [
			'active' => "addPost"
		]);
	}

	public function editPost() {
		$slug = $this->params['slug'];

		$post = [
			'slug' => 1, 
			'title' => "Laborum aute elit cillum commodo minim occaecat", 
			'content' => "Ea id est aliqua commodo minim anim commodo aliqua laborum. Sunt cillum enim irure duis nisi commodo proident esse excepteur.\nConsequat ad sit sunt Lorem irure dolore.\nTempor ut sit est adipisicing irure non exercitation nulla quis mollit dolor officia voluptate cillum.\nCommodo ut eiusmod consectetur minim nulla laboris est enim velit nisi esse amet minim do.", 
			'author' => "Parnell Polliotti", 
			'coverageImageAlt' => "Anim amet aute dolor amet", 
			'createdAt' => "18/06/2021", 
			'updatedAt' => "18/06/2021", 
		];

		$this->render("@admin/pages/post_edit.html.twig", [
			'active' => "editPost", 
			'post' => $post, 
		]);
	}

	public function deletePost() {
		$slug = $this->params['slug'];

		// no render. action and after redirect
	}
}