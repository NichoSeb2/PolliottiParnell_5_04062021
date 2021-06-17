<?php
namespace App\Controllers;

use App\Core\Controller;

class CommentController extends Controller {
	public function putOnline() {
		$id = $this->params['id'];

		$this->render("@admin/pages/putOnline.html.twig", [
			'id' => $id
		]);
	}

	public function putOffline() {
		$id = $this->params['id'];

		$this->render("@admin/pages/putOffline.html.twig", [
			'id' => $id
		]);
	}
}