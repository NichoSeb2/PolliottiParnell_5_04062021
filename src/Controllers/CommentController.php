<?php
namespace App\Controllers;

use App\Core\Controller;

class CommentController extends Controller {
	public function showComment() {
		$comments = [[
			'id' => 1, 
			'author' => "Jane Doe", 
			'content' => "Anim dolor mollit nulla Lorem esse laborum aliqua amet ex enim.", 
			'status' => "offline", 
			'createdAt' => "22/06/2021", 
			'updatedAt' => "25/06/2021", 
		], [
			'id' => 2, 
			'author' => "John Doe", 
			'content' => "Sint deserunt in dolore duis non eu fugiat fugiat et cillum mollit culpa.", 
			'status' => "online", 
			'createdAt' => "24/06/2021", 
			'updatedAt' => "25/06/2021", 
		]];

		$this->render("@admin/pages/comment.html.twig", [
			'active' => "showComment", 
			'comments' => $comments, 
		]);
	}

	public function putOnline() {
		$id = $this->params['id'];

		// no render. action and after redirect
	}

	public function putOffline() {
		$id = $this->params['id'];

		// no render. action and after redirect
	}
}