<?php
namespace App\Controllers;

use App\Core\Controller;

class CommentController extends Controller {
	public function putOnline() {
		$id = $this->params['id'];

		$this->render("@admin/pages/comment.html.twig", [
			'action' => "putOnline",
			'id' => $id
		]);
	}

	public function putOffline() {
		$id = $this->params['id'];

		$this->render("@admin/pages/comment.html.twig", [
			'action' => "putOffline",
			'id' => $id
		]);
	}
}